<?php
/**
 * Class represent the  RP store Item.
 *
 * @author Harish <harish282@gmail.com>
 * @copyright http://www.prisionstruggle.com
 */
class RPItemNew
{
    public $id = 0; //Item ID
    public $item = []; //Item array
    public $disabled = false;
    public $usingVariable = '';

    /**
     * constructor.
     *
     * @param Number $id
     */
    public function __construct($id = null)
    {
        if (!empty($id)) {
            $this->setId($id);
        }
    }

    /**
     * Set the item id.
     *
     * @param Number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function GetId()
    {
        return $this->id;
    }

    /**
     * Set the pack name...
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->item['name'] = $name;
    }

    /**
     * return item pack name.
     *
     * @return string
     */
    public function GetName()
    {
        return $this->item['name'];
    }

    /**
     * Set the pack code.
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->item['code'] = $code;
    }

    /**
     * return item pack code.
     *
     * @return string
     */
    public function GetCode()
    {
        return $this->item['code'];
    }

    /**
     * Sets the item category.
     *
     * @param string $cat
     */
    public function setCategory($cat)
    {
        $this->item['cat'] = $cat;
    }

    /**
     * return the category.
     *
     * @return string
     */
    public function GetCategory()
    {
        return $this->item['cat'];
    }

    /**
     * Sets the item category.
     *
     * @param string $cat
     */
    public function setVariable($var)
    {
        $this->usingVariable = $var;
    }

    /**
     * return the category.
     *
     * @return string
     */
    public function GetVariable()
    {
        return $this->usingVariable;
    }

    /**
     * Add to attribute.
     *
     * @param string $attr
     * @param string $val
     */
    public function AddToAttribute($attr, $val)
    {
        $this->item['attr'][$attr] = $val;
    }

    /**
     * Add items into pack.
     *
     * @param Number $itemid
     * @param Number $qty
     */
    public function AddItems($itemid, $qty)
    {
        $this->item['item'][$itemid] = $qty;
    }

    /**
     * Add land to pack.
     *
     * @param Number $city
     * @param Number $qty
     */
    public function AddLand($city, $qty)
    {
        $this->item['land'][$city] = $qty;
    }

    public function AddQuantity($qty)
    {
        $this->item['qty'] = $qty;
    }

    /**
     * Add price for item.
     *
     * @param Number $price
     */
    public function AddPrice($price, $paymentGateway = 'paypal')
    {
        $this->item['price'][$paymentGateway] = $price;
    }

    /**
     * Return price for particular payment gateway. Returns false if payment gateway is not supported.
     *
     * @param string $paymentGateway
     *
     * @return booleab
     */
    public function GetPrice($paymentGateway = 'paypal')
    {
        return isset($this->item['price'][$paymentGateway]) ? $this->item['price'][$paymentGateway] : false;
    }

    /**
     * Return all applied payment methods.
     *
     * @return array
     */
    public function GetAllPrice()
    {
        return $this->item['price'];
    }

    /**
     * Required game money for item.
     *
     * @param Number $money
     */
    public function AddGameMoney($money)
    {
        $this->item['gameMoney'] = $money;
    }

    public function GetGameMoney()
    {
        return isset($this->item['gameMoney']) ? $this->item['gameMoney'] : false;
    }

    /**
     * Function used to disable on enable products.
     *
     * @param bool $disabled
     */
    public function Disable($disabled = true)
    {
        $this->disabled = $disabled;
    }

    /**
     * Return item is disabled or not.
     *
     * @return bool
     */
    public function IsDisabled()
    {
        return $this->disabled;
    }

    /**
     * Return quantity of rp item. false for unlimited.
     *
     * @return mixed
     */
    public function GetQuantity()
    {
        return isset($this->item['qty']) ? $this->item['qty'] : false;
    }

    /**
     * Add payment for pack.
     *
     * @param string $method
     * @param string $strItemName
     * @param string $strType
     * @param Number $intAmount
     */
    public function ShowPayment($method, User $user, array $options = [])
    {
        $for = $options['initFor'] . PAYPAL_SEPARATOR . $user->id;
        $for1 = $options['initFor'] . 'I' . $user->id;
        $forPBC = $options['initFor'] . PBC_SEPARATOR . $user->id;
        if (isset($options['forRpShop']) && $options['forRpShop']) {
            $for .= PAYPAL_SEPARATOR . 'rps';
            $for1 .= 'I' . 'rps';
            $forPBC .= PBC_SEPARATOR . 'rps';
        }

        if ($method == 'moneybooker') {
            return PaymentNew::RenderMoneyPayment('www.generalforces.com|DP|' . $this->id . '|' . $for, $this->item['code'], number_format($this->GetPrice($method), 2));
        } elseif ($method == 'paypal') {
            return PaymentNew::RenderPaypalPayment('www.generalforces.com|DP|' . $this->id . '|' . $for, $this->item['name'], number_format($this->GetPrice($method), 2));
        } elseif ($method == 'pbc') {
            return PaymentNew::RenderCashPayment($user->id, 'www.generalforces.com@DP@' . $this->id . '@' . $forPBC, number_format($this->GetPrice(), 2), $this->item['name']);
        } elseif ($method == 'phone') {
            return PaymentNew::RenderPhonePayment($user->id, $this->item['code'], $for1);
        }
    }

    public function UseItem(User $user)
    {
        $packname = $this->GetName();

        if (is_array($this->item['attr'])) {
            foreach ($this->item['attr'] as $attr => $val) {
                try {
                    $user->AddToAttribute($attr, $val);
                } catch (FailedResult $e) {
                    $error = $e->getView();
                    if (strpos($error, 'POINTS_ERR') !== false) {
                        $temp = explode('|', $error);
                        $pointsCredited = (int) $temp[1];
                    }

                    User::SNotify($user->id, sprintf(MAXPOINTS_USER_NOTIFY, $packname, MAX_POINTS, $pointsCredited), COM_ERROR);
                    User::SNotify(ADMIN_USER_ID, sprintf(MAXPOINTS_ADMIN_NOTIFY, $packname, $user->id, MAX_POINTS, $pointsCredited), COM_ERROR);
                }
            }
        }

        if (is_array($this->item['item'])) {
            foreach ($this->item['item'] as $itemid => $qnty) {
                $user->AddItems($itemid, $qnty);
            }
        }

        if (is_array($this->item['land'])) {
            foreach ($this->item['land'] as $city => $qnty) {
                $user->AddLand($city, $qnty);
            }
        }

        /*if($this->GetCategory() == 'rpnames')
        {
            $user->Notify ( PAYMENT_SOMEONECONTACT, 'Donation' );
            User::SNotify ( 2168, sprintf ( PAYMENT_NEWNAME, $user->id, $this->GetName() ) );
        }*/

        return true;
    }
}
