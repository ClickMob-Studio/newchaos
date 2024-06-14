<?php

use Item as GameItem;
use PayPal\Api\Amount;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment as PayPalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use SendGrid\Mail\TypeException;

class Payment
{
    const UPGRADE_PACK_PRICE = 6.00;

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                getenv('PAYPAL_CLIENT_ID'),
                getenv('PAYPAL_CLIENT_SECRET')
            )
        );

        if (!getenv('PAYPAL_SANDBOX')) {
            $this->apiContext->setConfig([
                'mode' => 'live',
            ]);
        }
    }

    /**
     * Retrieve the API context.
     *
     * @return ApiContext
     */
    public function getApiContext()
    {
        return $this->apiContext;
    }

    /**
     * Create a new single payment.
     *
     * @param $price
     * @param null $points
     * @param null $subscription
     *
     * @throws Exception
     *
     * @return string
     */
    public function beginPayment(User $user, $price, string $description, $points = null, $subscription = null, $money = null)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($description)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $total = number_format($price, 2, '.', '');
        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency('USD');

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($description);

        // Create a transaction in the database
        $transactionId = $this->createTransaction($user, $total, $points,$money, $subscription);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(getenv('URL') . 'upgrade.php?txnId=' . $transactionId)
            ->setCancelUrl(getenv('URL') . 'upgrade.php?cancel=true');

        $payment = new PayPalPayment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setExperienceProfileId($this->createPresentation())
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            // Store information in DB for response
            $payment->create($this->apiContext);

            return $payment->getApprovalLink();
        } catch (PayPalConnectionException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Capture a transaction back from PayPal.
     *
     * @throws SoftException
     *
     * @return bool
     */
    public function captureTransaction(string $transactionId, string $paymentId, string $token, string $payerId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `upgrade_transaction` WHERE `transaction_id` = ? AND `paid_at` IS NULL');
        $stmt->bind_param(
            's',
            $transactionId
        );
        $stmt->execute();

        $results = $stmt->get_result();
        if ($results->num_rows === 0) {
            throw new \SoftException('Unable to find transaction.');
        }

        $upgradeTransaction = $results->fetch_assoc();

        $payment = PayPalPayment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $transaction = new Transaction();
        $amount = new Amount();

        $amount->setCurrency('USD');
        $amount->setTotal($upgradeTransaction['price']);
        $transaction->setAmount($amount);

        try {
            $result = $payment->execute($execution, $this->apiContext);

            // Verify the transaction was a success
            if ($result->getIntent() === 'sale' && $result->getState() === 'approved') {
                $capturedTransaction = $result->getTransactions()[0];

                if ($capturedTransaction->getAmount()->total != $upgradeTransaction['price']) {
                    throw new \Exception('Invalid transaction amount.');
                }

                if ($capturedTransaction->getAmount()->currency !== 'USD') {
                    throw new \Exception('Invalid currency.');
                }

                $payerInfo = $result->getPayer()->getPayerInfo();
                $fullName = $payerInfo->getFirstName() . ' ' . $payerInfo->getLastName();
                $email = $payerInfo->getEmail();
                $invoiceNumber = $result->getId();
                $fee = $capturedTransaction->getRelatedResources()[0]->getSale()->getTransactionFee()->value;
                $profit = number_format($upgradeTransaction['price'] - $fee, 2, '.', '');
                $paidAt = time();

                $stmt = $conn->prepare('UPDATE `upgrade_transaction` SET 
                    payment_id = ?,
                    token = ?,
                    payer_id = ?,
                    paypal_invoice_id = ?,
                    payer_name = ?,
                    payer_email = ?,
                    fees = ?,
                    profit = ?,
                    capture_ip_address = ?,
                    paid_at = ?
                    WHERE `transaction_id` = ?');
                $stmt->bind_param(
                    'sssssssssss',
                    $paymentId,
                    $token,
                    $payerId,
                    $invoiceNumber,
                    $fullName,
                    $email,
                    $fee,
                    $profit,
                    $_SERVER['REMOTE_ADDR'],
                    $paidAt,
                    $transactionId
                );

                if ($stmt->execute()) {
                    return true;
                }

                throw new \Exception('Unable to capture payment and update transaction.');
            }
        } catch (\Exception $e) {
            // Log error
            echo $e->getMessage();
            echo $e->getTraceAsString();
            throw new \SoftException('Unable to capture transaction.');
        }
    }

    /**
     * Credit the user.
     *
     * @param $transactionId
     *
     * @throws Exception
     * @throws FailedResult
     *
     * @return bool
     */
    public static function creditUser($transactionId, User $user)
    {
        global $conn;
        $transaction = self::getTransaction($transactionId);
        if (!$transaction) {
            throw new \Exception('Unable to find transaction');
        }

        if ($transaction['upgrade_packs'] > 0) {
            $user->AddItems(GameItem::getUpgradePackId(), $transaction['upgrade_packs']);
            User::SNotify($user->id, 'Your purchase of ' . $transaction['upgrade_packs'] . ' Upgrade Pack(s) was successful, they have been credited to your inventory.', 'Upgrade');
            Payment::sendPurchaseEmail($user, 'x' . $transaction['upgrade_packs'], 'Upgrade Packs', $transaction['price']);

            return true;
        }

        if ($transaction['awake'] > 0) {
            $user->AddItems(GameItem::getAwakePillId(), $transaction['awake']);
            User::SNotify($user->id, 'Your purchase of ' . $transaction['awake'] . 'x Awake Pills was successful, they have been credited to your inventory.', 'Upgrade');
            Payment::sendPurchaseEmail($user, 'x' . $transaction['awake'], 'Awake Pills', $transaction['price']);

            return true;
        }

        if ($transaction['points'] > 0) {
            $stmt = $conn->prepare('UPDATE grpgusers SET credits = credits + ? WHERE id = '.$user->id);
            $stmt->bind_param(
                'i',
                $transaction['points']
            );
            $stmt->execute();
            User::SNotify($user->id, 'Your purchase of ' . $transaction['points'] . ' credits was successful, they have been credited to your account.', 'Upgrade');
            Payment::sendPurchaseEmail($user, $transaction['points'], 'Credits', $transaction['price']);

            return true;
        }
        if ($transaction['money'] > 0) {
            $user->AddMoney($transaction['money']);
            User::SNotify($user->id, 'Your purchase of $' . $transaction['money'] . '  was successful, they have been credited to your account.', 'Upgrade');
            Payment::sendPurchaseEmail($user, $transaction['money'], 'Money', $transaction['price']);

            return true;
        }
        if ($transaction['subscription'] > 0) {

            $user->AddPoints(100);
            $newtime = time() + 2678400;
            $check = DBi::$conn->query("SELECT * FROM temp_items_use WHERE userid = ".$user->id);
            if(mysqli_num_rows($check) > 0) {
                $check = $check->fetch_assoc();
                $newtime = time() + 2678400;
                DBi::$conn->query("UPDATE temp_items_use SET xpboost = ".$newtime." WHERE userid = ".$user->id);
            } else {
                DBi::$conn->query("INSERT INTO temp_items_use (userid, xpboost) VALUES (".$user->id.", ".$newtime.")");
            }
            $user->AddItems(271, 4);
            $user->AddItems(174, 2);
            $user->AddPoints(20000);


            User::SNotify($user->id, 'Your subscription payment has been captured.', 'Upgrade');
            Payment::sendSubscriberEmail($user, $transaction['price']);

            return true;
        }

        return false;
    }

    /**
     * Send an email about their purchase.
     *
     * @param User $user
     * @param $qty
     * @param $productName
     * @param $total
     *
     * @throws TypeException
     */
    public static function sendPurchaseEmail(User $user, $qty, $productName, $total)
    {
        $sendGridEmail = new \SendGrid\Mail\Mail();
        $sendGridEmail->setTemplateId('d-9f39117655274d85a9377a9d53ebc0f5');
        $sendGridEmail->setFrom('support@generalforces.com', 'General Forces');
        $sendGridEmail->addTo(
            $user->email,
            $user->username,
            [
                'name' => $user->username,
                'qty' => $qty,
                'productName' => $productName,
                'total' => '$' . number_format($total, 2),
            ]
        );
        $sendGrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $sendGrid->send($sendGridEmail);
    }

    /**
     * Send the subscriber email.
     *
     * @param User $user
     * @param $total
     *
     * @throws TypeException
     */
    public static function sendSubscriberEmail(User $user, $total)
    {
        $sendGridEmail = new \SendGrid\Mail\Mail();
        $sendGridEmail->setTemplateId('d-9f39117655274d85a9377a9d53ebc0f5');
        $sendGridEmail->setFrom('support@generalforces.com', 'GeneralForces');
        $sendGridEmail->addTo(
            $user->email,
            $user->username,
            [
                'name' => $user->username,
                'subscriptionCost' => '$' . number_format($total, 2),
            ]
        );
        $sendGrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $sendGrid->send($sendGridEmail);
    }

    /**
     * Retrieve a transaction by ID.
     *
     * @param $transactionId
     *
     * @return array|null
     */
    public static function getTransaction(string $transactionId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `upgrade_transaction` WHERE `transaction_id` = ? AND `paid_at` IS NOT NULL');
        $stmt->bind_param(
            's',
            $transactionId
        );
        $stmt->execute();

        $results = $stmt->get_result();
        if ($results->num_rows === 0) {
            return null;
        }

        return $results->fetch_assoc();
    }

    /**
     * Capture a checkout request.
     *
     * @throws Exception
     * @throws SoftException
     *
     * @return bool
     */
    public static function captureCheckout(User $user, string $orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');

        $client = PayPalClient::client();
        $response = $client->execute($request);
        Logger::log((array) $response);

        if ($response->result->status !== 'COMPLETED' || $response->result->intent !== 'CAPTURE') {
            Logger::error((array) $response->result);
            throw new \SoftException('Transaction response was not completed / captured.');
        }

        $invoiceId = $response->result->id;
        $payer = $response->result->payer;
        $payerEmail = $payer->email_address;
        $payerName = $payer->name->given_name . ' ' . $payer->name->surname;
        $purchaseUnit = $response->result->purchase_units[0];

        $item = $purchaseUnit->items[0];
        $sku = $item->sku;
        if (!in_array($sku, self::getValidSkus())) {
            throw new \SoftException('Invalid SKU was purchased.');
        }

        $pointsAmount = 0;
        $moneyAmount = 0;
        $awakeAmount = 0;
        $upgradePacks = 0;

        if ($sku === 'UPGRADE') {
            $upgradePacks = $item->quantity;
            $totalCost = $upgradePacks * self::UPGRADE_PACK_PRICE;
            $itemAmount = $item->unit_amount->value;
            $totalAmount = $purchaseUnit->amount->value;

            if (!self::compareAmount(self::UPGRADE_PACK_PRICE, $itemAmount)
                || !self::compareAmount($totalCost, $totalAmount)
            ) {
                throw new \Exception('Compared amount is different to expected.');
            }
        } elseif (strpos($sku, 'POINTS') !== false) {
            $pointsAmount = str_replace('POINTS-', '', $sku);

            $amount = $item->unit_amount->value;
            $upgradePoints = UpgradePoints::get($pointsAmount);
            if (!self::compareAmount($upgradePoints['price'], $amount)
                || !self::compareAmount($upgradePoints['price'], $purchaseUnit->amount->value)
            ) {
                throw new \Exception('Compared amount is different to expected.');
            }
        } elseif (strpos($sku, 'AWAKE') !== false) {
            $awakeAmount = str_replace('AWAKE-', '', $sku);

            $amount = $item->unit_amount->value;
            $upgradeAwake = UpgradeAwake::get($awakeAmount);
            if (!self::compareAmount($upgradeAwake['price'], $amount)
                || !self::compareAmount($upgradeAwake['price'], $purchaseUnit->amount->value)
            ) {
                throw new \Exception('Compared amount is different to expected.');
            }
        }elseif (strpos($sku, 'MONEY') !== false) {
            $moneyAmount = str_replace('MONEY-', '', $sku);

            $amount = $item->unit_amount->value;
            $upgradeMoney = UpgradeMoney::get($moneyAmount);
            if (!self::compareAmount($upgradePoints['price'], $amount)
                || !self::compareAmount($upgradePoints['price'], $purchaseUnit->amount->value)
            ) {
                throw new \Exception('Compared amount is different to expected.');
            }
        }

        $capture = $purchaseUnit->payments->captures[0];
        $amount = $capture->amount->value;
        $fees = '0.00';
        if (isset($capture->seller_receivable_breakdown->paypal_fee->value)) {
            $fees = $capture->seller_receivable_breakdown->paypal_fee->value;
        }

        return self::createFullTransaction(
            $user->id,
            $pointsAmount,
            $moneyAmount,
            $awakeAmount,
            $upgradePacks,
            0,
            $amount,
            $payerName,
            $payerEmail,
            $fees,
            $invoiceId
        );
    }

    /**
     * Create a new subscription entry.
     *
     * @throws Exception
     */
    public static function createSubscription(User $user, string $orderId, string $payPalSubscriptionId): bool
    {
        global $conn;

        $request = new OrdersGetRequest($orderId);

        $client = PayPalClient::client();
        $response = $client->execute($request);
        Logger::log((array) $response);

        if (!isset($response->result) || $response->statusCode !== 200) {
            throw new \Exception('Unable to find subscription by order ID.');
        }

        if ($response->result->status !== 'APPROVED') {
            throw new \Exception('Order is not approved.');
        }

        if (!isset($response->result->id)) {
            throw new \Exception('No result transaction present.');
        }

        $stmt = $conn->prepare('SELECT `id` FROM `subscriptions` WHERE `paypal_subscription_id` = ?');
        $stmt->bind_param('s', $subscriptionId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows !== 0) {
            throw new \SoftException('This subscription already exists and cannot be processed again.');
        }

        $payer = $response->result->payer;
        $payerEmail = $payer->email_address;
        $payerName = $payer->name->given_name . ' ' . $payer->name->surname;
        $createTime = strtotime($response->result->create_time);

        $stmt = $conn->prepare('
            INSERT INTO `subscriptions`
                (paypal_subscription_id, paypal_order_id, user_id, start_time, payer_name, payer_email, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?);
        ');
        $stmt->bind_param(
            'ssiisss',
            $payPalSubscriptionId,
            $orderId,
            $user->id,
            $createTime,
            $payerName,
            $payerEmail,
            $_SERVER['REMOTE_ADDR']
        );

        return $stmt->execute();
    }

    /**
     * Activate the subscription.
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function activateSubscription(string $payPalSubscriptionId, int $activeTime)
    {
        global $conn;

        $subscription = self::getSubscription($payPalSubscriptionId);

        if ($subscription && !$subscription['active_time']) {
            $stmt = $conn->prepare('UPDATE `subscriptions` SET `active_time` = ?, `active` = 1 WHERE `paypal_subscription_id` = ?');
            $stmt->bind_param('is', $activeTime, $payPalSubscriptionId);
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            return true;
        }

        return false;
    }

    /**
     * Cancel a subscription.
     *
     * @param string $payPalSubscriptionId
     * @param int    $endTime
     *
     * @throws TypeException
     *
     * @return bool
     */
    public static function cancelSubscription(string $payPalSubscriptionId, int $endTime)
    {
        global $conn;

        $subscription = self::getSubscription($payPalSubscriptionId);

        if ($subscription) {
            $stmt = $conn->prepare('UPDATE `subscriptions` SET `end_time` = ?, `active` = 0 WHERE `paypal_subscription_id` = ?');
            $stmt->bind_param('is', $endTime, $payPalSubscriptionId);
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            User::SNotify(
                $subscription['user_id'],
                'Your subscription was successfully cancelled. If you could let us know why by <a href="pms.php?action=compose&to=1">sending me a direct message</a> we\'ll be more than glad to take your feedback on board.',
                'Upgrade'
            );

            $user = new User($subscription['user_id']);
            $sendGridEmail = new \SendGrid\Mail\Mail();
            $sendGridEmail->setTemplateId('d-e4524c08b3714372a77f24878da4eb3e');
            $sendGridEmail->setFrom('support@generalforces.com', 'General Forces');
            $sendGridEmail->addTo(
                $user->email,
                $user->username,
                [
                    'name' => $user->username,
                ]
            );
            $sendGrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
            $sendGrid->send($sendGridEmail);

            return true;
        }

        return false;
    }

    /**
     * Retrieve a subscription from the database.
     *
     * @throws Exception
     *
     * @return array|null
     */
    public static function getSubscription(string $payPalSubscriptionId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `subscriptions` WHERE `paypal_subscription_id` = ?');
        $stmt->bind_param('s', $payPalSubscriptionId);
        $stmt->execute();
        $results = $stmt->get_result();

        if ($results->num_rows === 0) {
            return null;
        }

        return $results->fetch_assoc();
    }

    /**
     * Credit a subscription from a web hook request.
     *
     * @param $request
     *
     * @throws Exception
     *
     * @return string
     */
    public static function creditSubscription(string $payPalSubscriptionId, $request)
    {
        $subscription = Payment::getSubscription($payPalSubscriptionId);

        if (!$subscription) {
            throw new \Exception('Unable to find subscription: ' . $payPalSubscriptionId);
        }

        if ($request->resource->state !== 'completed') {
            throw new \Exception('Subscription payment was not completed.');
        }

        if ($request->resource->amount->total !== '4.99' && $request->resource->amount->currency !== 'USD') {
            throw new \Exception('Subscription amount is incorrect, received ' . $request->resource->amount->total . $request->resource->amount->currency);
        }

        $existingTransaction = self::getTransactionByInvoiceId($request->resource->id);
        if ($existingTransaction) {
            throw new \Exception('Transaction already processed.');
        }

        self::activateSubscription($payPalSubscriptionId, time());

        $transactionId = self::createFullTransaction(
            $subscription['user_id'],
            0,
            0,
            0,
            0,
            1,
            $request->resource->amount->total,
            $subscription['payer_name'],
            $subscription['payer_email'],
            $request->resource->transaction_fee->value,
            $request->resource->id,
            $subscription['ip_address'],
            $subscription['id']
        );

        if ($transactionId) {
            $user = new User($subscription['user_id']);

            return self::creditUser($transactionId, $user);
        }
    }

    /**
     * Does a user have a pending subscription?
     *
     * @return bool
     */
    public static function hasPendingSubscription(int $userId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT id FROM `subscriptions` WHERE `user_id` = ? AND (`active` = 0 OR `active` IS NULL) AND `end_time` IS NULL;');
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $results = $stmt->get_result();

            return $results->num_rows >= 1;
        }

        return false;
    }

    /**
     * Does a user have an active subscription?
     *
     * @return bool
     */
    public static function hasActiveSubscription(int $userId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT id FROM `subscriptions` WHERE `user_id` = ? AND `active` = 1');
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $results = $stmt->get_result();

            return $results->num_rows >= 1;
        }

        return false;
    }

    /**
     * Get the subscription for a user.
     *
     * @return array|null
     */
    public static function getSubscriptionForUser(int $userId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `subscriptions` WHERE `user_id` = ? AND `active` = 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $results = $stmt->get_result();

        return $results->fetch_assoc();
    }

    /**
     * Retrieve all valid skus.
     *
     * @return array
     */
    public static function getValidSkus()
    {
        $points = UpgradePoints::getAll();
        $money = UpgradeMoney::getAll();
        $awake = UpgradeAwake::getAll();
        $items = array_merge($points, $awake, $money);

        $skus = array_map(function ($item) {
            return $item['sku'];
        }, $items);

        $skus[] = 'UPGRADE';

        return $skus;
    }

    /**
     * Retrieve a transaction by invoice ID.
     *
     * @return array|null
     */
    public static function getTransactionByInvoiceId(string $invoiceId)
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `upgrade_transaction` WHERE `paypal_invoice_id` = ?');
        $stmt->bind_param('s', $invoiceId);
        $stmt->execute();
        $results = $stmt->get_result();

        if ($results->num_rows === 0) {
            return null;
        }

        return $results->fetch_assoc();
    }

    /**
     * Get all transactions.
     *
     * @return array|null
     */
    public static function getAllTransactions()
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `upgrade_transaction` ORDER BY `created_at` DESC');
        $stmt->execute();
        $results = $stmt->get_result();

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Retrieve all subscriptions.
     *
     * @return array|null
     */
    public static function getAllSubscriptions()
    {
        global $conn;

        $stmt = $conn->prepare('SELECT * FROM `subscriptions`');
        $stmt->execute();
        $results = $stmt->get_result();

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Compare two monetary amounts.
     *
     * @param $expected
     * @param $actual
     *
     * @return bool
     */
    public static function compareAmount($expected, $actual)
    {
        return number_format($expected, '2', '.', '')
            === number_format($actual, '2', '.', '');
    }

    /**
     * Retrieve and update fees from PayPal.
     */
    public function updateFees()
    {
        $transactionQuery = BaseObject::createQueryBuilder();
        $transactionQuery->select('paypal_invoice_id')
            ->from('upgrade_transaction')
            ->where('fees = 0.00');

        $transactions = $transactionQuery->execute()->fetchAll();

        if (count($transactions)) {
            foreach ($transactions as $transaction) {
                try {
                    $request = new OrdersGetRequest($transaction['paypal_invoice_id']);
                    $client = PayPalClient::client();
                    $response = $client->execute($request);

                    $captures = $response->result->purchase_units[0]->payments->captures[0];
                    if (isset($captures->seller_receivable_breakdown->paypal_fee->value)) {
                        $fee = $captures->seller_receivable_breakdown->paypal_fee->value;
                        $netAmount = $captures->seller_receivable_breakdown->net_amount->value;

                        $updateTransaction = BaseObject::createQueryBuilder();
                        $updateTransaction->update('upgrade_transaction')
                            ->set('fees', $fee)
                            ->set('profit', $netAmount)
                            ->where('paypal_invoice_id = :id')
                            ->setParameter('id', $transaction['paypal_invoice_id']);

                        $updateTransaction->execute();
                    }
                } catch (\Exception $e) {
                    Logger::log($e);
                }
            }
        }
    }

    /**
     * Create a full transaction.
     *
     * @param $userId
     * @param $amount
     *
     * @throws Exception
     *
     * @return string
     */
    private static function createFullTransaction(
        $userId,
        int $points,
        int $money,
        int $awake,
        int $upgradePacks,
        int $subscription,
        $amount,
        string $payerName,
        string $payerEmail,
        string $fees,
        string $invoiceId,
        string $ipAddress = null,
        int $subscriptionId = null
    ) {
        global $conn;

        if (!$ipAddress) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        $profit = (float) $amount - (float) $fees;

        $transactionId = self::generateTransactionId();
        $stmt = $conn->prepare('
            INSERT INTO `upgrade_transaction`
                (transaction_id, user_id, ip_address, capture_ip_address, points, awake, upgrade_packs, subscription, price, payer_name, payer_email, fees, paypal_invoice_id, created_at, paid_at, subscription_id, profit)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
        ');
        $createdAtTime = time();
        $stmt->bind_param(
            'sissiiiisssssiiis',
            $transactionId,
            $userId,
            $ipAddress,
            $ipAddress,
            $points,
            $awake,
            $upgradePacks,
            $subscription,
            $amount,
            $payerName,
            $payerEmail,
            $fees,
            $invoiceId,
            $createdAtTime,
            $createdAtTime,
            $subscriptionId,
            $profit
        );

        if ($stmt->execute()) {
            return $transactionId;
        }

        throw new \Exception($stmt->error);
    }

    /**
     * Create a custom presentation to improve checkout.
     *
     * @throws PayPalConnectionException
     *
     * @return string
     */
    private function createPresentation()
    {
        $flowConfig = new FlowConfig();
        $flowConfig->setLandingPageType('Billing')
            ->setUserAction('commit');

        $presentation = new Presentation();
        $presentation->setLogoImage('https://generalforces.com/images/paypal-header.png')
            ->setBrandName('General Forces');

        $inputFields = new InputFields();
        $inputFields->setAllowNote(1)
            ->setAddressOverride(0)
            ->setNoShipping(1);

        $webProfile = new WebProfile();

        $webProfile->setName('General Forces ' . uniqid())
            ->setFlowConfig($flowConfig)
            ->setPresentation($presentation)
            ->setInputFields($inputFields)
            ->setTemporary(true);

        try {
            $createProfileResponse = $webProfile->create($this->apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            // Log errors
            throw $e;
        }

        return $createProfileResponse->getId();
    }

    /**
     * Create a transaction.
     *
     * @param $price
     * @param null $points
     * @param null $subscription
     *
     * @throws Exception
     *
     * @return string
     */
    private function createTransaction(User $user, $price, $points = null,$money = null, $subscription = null)
    {
        global $conn;

        $transactionId = self::generateTransactionId();
        $stmt = $conn->prepare('
            INSERT INTO `upgrade_transaction` 
                (transaction_id, user_id, ip_address, points, subscription, price, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?); 
        ');
        $createdAtTime = time();
        $stmt->bind_param(
            'sisiiss',
            $transactionId,
            $user->id,
            $_SERVER['REMOTE_ADDR'],
            $points,
            $subscription,
            $price,
            $createdAtTime
        );
        if (!$stmt->execute()) {
            throw new \Exception('Unable to create transaction.');
        }

        return $transactionId;
    }

    /**
     * Generate a transaction ID.
     *
     * @return string
     */
    private static function generateTransactionId()
    {
        return strtoupper(uniqid());
    }
}
