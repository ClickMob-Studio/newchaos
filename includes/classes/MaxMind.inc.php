<?php

    /**
     * discription: This class is used to manage User Information for payment.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: UserTries
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class MaxMind extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'maxmind'; // table implemented

        /**
         * Constructor.
         */
        public function __construct($id = null)
        {
            if (!empty($id) && is_numeric($id)) {
                parent::__construct($id);
            }
        }

        public static function CountUnSeen($refused = true)
        {
            return MySQL::GetSingle('SELECT COUNT(id) FROM ' . self::GetDataTable() . ' WHERE seen=0 AND ' . ($refused ? 'refused = 1' : 'refused = 0'));
        }

        /**
         * Funtions return all records.
         *
         * @return array
         */
        public static function GetAll($where = '', $order = false, $dir = 'ASC', $page = false, $limit = false)
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, $page, $limit, $order, $dir);
        }

        public static function GetAllAccepted($order = '', $dir = 'DESC')
        {
            return self::GetAllWhere('refused = 0', $order, $dir);
        }

        public static function GetAllRefused($order = '', $dir = 'DESC')
        {
            return self::GetAllWhere('refused = 1', $order, $dir);
        }

        public static function GetAllWhere($where, $order = '', $dir = 'DESC')
        {
            if (empty($order)) {
                $order = 'checkedtime';
            }

            if (empty($dir)) {
                $dir = 'DESC';
            }

            //$where = 'refused = 1';

            $objs = self::GetAll($where, $order, $dir);
            $ids = [];

            $records = [];
            foreach ($objs as $obj) {
                $obj->payment = COM_NONE;
                $records[$obj->id] = $obj;

                $ids[] = $obj->id;
            }

            unset($objs);

            if (empty($ids)) {
                return $records;
            }

            $sql = 'SELECT id_pagamento, txn, maxmind FROM pagamentos WHERE maxmind IN (\'' . implode('\',\'', $ids) . '\')';
            $res = DBi::$conn->query($sql);

            while ($row = mysqli_fetch_object($res)) {
                $records[$row->maxmind]->id_pagamento = $row->id_pagamento;
                $records[$row->maxmind]->txn = $row->txn;
                $records[$row->maxmind]->payment = $row->txn . '/' . $row->id_pagamento;
            }

            return $records;
        }

        /**
         * Funtions used to add user tries.
         *
         * @return Boleean
         */
        public static function Check(User $user)
        {
            $userinfo = UserInfo::GetCurrentForUser($user);

            $ccfs = new CreditCardFraudDetection();

            // Set inputs and store them in a hash
            // See http://www.maxmind.com/app/ccv for more details on the input fields

            // Enter your license key here (Required)
            $h['license_key'] = MM_PWD;

            // Required fields
            $h['i'] = $_SERVER['REMOTE_ADDR'];             // set the client ip address
            $h['city'] = $userinfo->city;             // set the billing city
            $h['region'] = $userinfo->region;                 // set the billing state
            $h['postal'] = $userinfo->postal;              // set the billing zip code
            $h['country'] = $userinfo->country;                // set the billing country
            $h['emailMD5'] = md5(strtolower($userinfo->email));
            $email_arr = split('@', $userinfo->email);
            $h['domain'] = $email_arr[1];

            // Recommended fields
            if (!empty($userinfo->ccbin)) {
                $h['bin'] = $userinfo->ccbin;
            }            // bank identification number
            //$h["forwardedIP"] = "24.24.24.25";    // X-Forwarded-For or Client-IP HTTP Header
            // CreditCardFraudDetection.php will take

            $h['sessionID'] = session_id();        // Session ID

            $h['accept_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $h['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            // If you want to disable Secure HTTPS or don't have Curl and OpenSSL installed
            // uncomment the next line
            // $ccfs->isSecure = 0;

            // set the timeout to be five seconds
            $ccfs->timeout = 20;

            // uncomment to turn on debugging
            // $ccfs->debug = 1;

            // how many seconds to cache the ip addresses
            // $ccfs->wsIpaddrRefreshTimeout = 3600*5;

            // file to store the ip address for minfraud3.maxmind.com, minfraud1.maxmind.com and minfraud2.maxmind.com
            // $ccfs->wsIpaddrCacheFile = "/tmp/maxmind.ws.cache";

            // if useDNS is 1 then use DNS, otherwise use ip addresses directly
            $ccfs->useDNS = 0;

            $ccfs->isSecure = 0;

            // next we set up the input hash
            $ccfs->input($h);

            // then we query the server
            $ccfs->query();

            // then we get the result from the server
            $h = $ccfs->output();

            $info = '';

            foreach ($h as $key => $value) {
                $info .= $key . ': ' . $value . "\n";
            }

            $data = [];
            $riskScore = $h['score'];
            if (empty($userinfo->ccbin)) {
                ++$riskScore;
            }

            if (trim($h['binMatch']) == 'NotFound') {
                ++$riskScore;
            }
            if (trim($h['err']) == 'CITY_NOT_FOUND') {
                $riskScore += .05;
            }

            $riskscoreper = (float) Variable::GetValue('riskScore');

            $data['user_id'] = $user->id;
            $data['user_info_id'] = $userinfo->id;
            $data['checkedtime'] = time();
            $data['riskscore'] = $riskScore; //riskScore
            $data['refused'] = ($riskScore > $riskscoreper ? 1 : 0);
            $data['data'] = Utility::SmartEscape($info);

            $id = parent::AddRecords($data, self::GetDataTable());

            if ($h['riskScore'] > $riskscoreper) {
                throw new FailedResult(sprintf(MAXMIND_REJECT_MSG, $id));
            }

            return $id;
        }

        /**
         * Funtions used to delete user tries.
         *
         * @return Boleean
         */
        public static function Delete($id)
        {
            return parent::sDelete(self::GetDataTable(), ['id' => $id]);
        }

        public static function MarkAllSeen($refused = 1)
        {
            return self::sUpdate(self::GetDataTable(), ['seen' => 1], ['seen' => 0,'refused' => $refused]);
        }

        public static function form($view)
        {
            ?>
                <div id='div_drag' class="dialog" style="width:400px; display:;">
                <div id='div_handler' class='dialog_title'><a href="#" class="close">[ x ]</a><?php echo ADD_INFO_REQ; ?></div>
                <div id="dialog_body" class="dialog_body">
                    <form method="post" action="" onsubmit="return ValidateForm(this)">
                        <table cellspacing="0" cellpadding="3">
                            <tr>
                                <td><?php echo FIRST_NAME; ?> <span class="darkred">*</span></td>
                                <td><input name="firstname" value="<?php echo $view->userinfo->firstname; ?>" maxlength="100"></td>
                            </tr>
                            <tr>
                                <td><?php echo LAST_NAME; ?> <span class="darkred">*</span></td>
                                <td><input name="lastname" value="<?php echo $view->userinfo->lastname; ?>" maxlength="100"></td>
                            </tr>
                            <tr>
                                <td><?php echo CITY; ?> <span class="darkred">*</span></td>
                                <td><input name="city" value="<?php echo $view->userinfo->city; ?>" maxlength="100"></td>
                            </tr>
                            <tr>
                                <td><?php echo STATE; ?> <span class="darkred">*</span></td>
                                <td><input name="region" value="<?php echo $view->userinfo->region; ?>" maxlength="50"></td>
                            </tr>
                            <tr>
                                <td><?php echo ZIP; ?> <span class="darkred">*</span></td>
                                <td><input name="postal" value="<?php echo $view->userinfo->postal; ?>" maxlength="20" size="10"></td>
                            </tr>
                            <tr>
                                <td><?php echo COUNTRY; ?> <span class="darkred">*</span></td>
                                <td>
                                    <select name="country" style='width:200px;'>
                                    <?php foreach ($view->countries as $country):?>
                                    <option value="<?php echo $country->code; ?>" <?php echo $view->userinfo->country == $country->code ? 'selected' : ''; ?>><?php echo $country->name; ?></option>
                                    <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <!--tr>
                                <td><?php echo CCBIN; ?> <span class="darkred">[1]</span></td>
                                <td><input name="ccbin" value="<?php echo $view->userinfo->ccbin; ?>" maxlength="6" size="6"></td>
                            </tr-->
                            <tr>
                                <td></td>
                                <td><input type="submit" value="<?php echo COM_CONTINUE; ?>" class='button'></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="darkred">
                                    <span class="darkred">*</span> = <?php echo REQUIRED; ?><br>
                                     <?php //echo PAYMET_USER_FORM_NOTE_2;?><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="darkred">
                                    <?php echo PAYMET_USER_FORM_NOTE_1; ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <span style='display:none' class="message"></span>
                </div>
            </div>
            <script>

            var requestSent = false;
            var paymentForm = null;

            $(document).ready(function(){
                $("#div_drag").draggable({ handle: '#div_handler', cursor:'move' });
                $("body").append('<div class="modeltrans" id="divoverlay"></div>');
                $("body").append($("#div_drag"));

                $("#div_drag").css({'position':'fixed','top':'30%','left':'40%'});

                $("#divoverlay").css('opacity',0.3);
                $("#divoverlay").hide();

                $("#div_drag").css('z-index',1000);
                $("#div_drag").hide();

                $("#div_drag a.close").click(function(){

                    if(requestSent)
                    {
                        alert("<?php echo PAYMET_USER_FORM_WAIT; ?>");
                        return false;
                    }

                     $("#divoverlay").hide();
                     $("#div_drag").hide();
                     return false;
                });

            });

            function ValidateForm(form)
            {
                if(jQuery.Validation.is_empty(form.firstname.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, FIRST_NAME); ?>");
                    form.firstname.focus();
                    return false;
                }
                if(jQuery.Validation.is_empty(form.lastname.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, LAST_NAME); ?>");
                    form.lastname.focus();
                    return false;
                }
                if(jQuery.Validation.is_empty(form.city.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, CITY); ?>");
                    form.city.focus();
                    return false;
                }
                if(jQuery.Validation.is_empty(form.region.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, STATE); ?>");
                    form.region.focus();
                    return false;
                }
                if(jQuery.Validation.is_empty(form.postal.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, ZIP); ?>");
                    form.postal.focus();
                    return false;
                }
                if(jQuery.Validation.is_empty(form.country.value))
                {
                    alert("<?php printf(FIELD_EMPTY_ERR, COUNTRY); ?>");
                    form.country.focus();
                    return false;
                }

               

                var data = {
                            'firstname':form.firstname.value
                            ,'lastname':form.lastname.value
                            ,'city':form.city.value
                            ,'region':form.region.value
                            ,'postal':form.postal.value
                            ,'country':form.country.value
                            //,'ccbin':form.ccbin.value
                            ,'action':'saveUserInfo'
                            };

                requestSent = true;

                $("#div_drag table").hide();
                $("#div_drag span.message").html('<img src="images/wait1.gif" width="16px"> <?php echo AJAX_WAITING; ?>');
                $("#div_drag span.message").show();

                $.post('rpstore.php',data, function(data, textStatus){
                    requestSent = false;

                    data = data.split('|');

                    if(data[0] != 'success')
                    {
                        alert(data[0]);

                        location.href= 'rpstore.php';
                        return false;
                    }

                    //alert(data[1]);

                    $("#divoverlay").hide();
                    $("#div_drag").hide();

                    paymentForm.custom.value = data[1];
                    paymentForm.submit();

                });

                return false;
            }

            function ShowMaxMindForm(form)
            {
                <?php if (120 < $view->user_class->signuptime / DAY_SEC && 0 < User::GetPayment($view->user_class->id)):?>
                    form.submit();
                    return;
                <?php endif; ?>

                $("#divoverlay").show();
                $("#div_drag").show();
                $("#div_drag table").show();
                $("#div_drag span.message").hide();

                paymentForm = form;
                //alert(form);

                /**/

                return false;
            }
            </script>
            <?php
        }

        /**
         * Function used to get the data table name which is implemented by class.
         *
         * @return string
         */
        protected static function GetDataTable()
        {
            return self::$dataTable;
        }

        /**
         * Returns the fields of table.
         *
         * @return array
         */
        protected static function GetDataTableFields()
        {
            return [
                self::$idField,
                'user_id',
                'user_info_id',
                'checkedtime',
                'riskscore',
                'refused',
                'data',
                'seen',
            ];
        }

        /**
         * Returns the identifier field name.
         *
         * @return mixed
         */
        protected function GetIdentifierFieldName()
        {
            return self::$idField;
        }

        /**
         * Function returns the class name.
         *
         * @return string
         */
        protected function GetClassName()
        {
            return __CLASS__;
        }
    }
