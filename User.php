<?php
error_reporting(E_ERROR);
date_default_timezone_set("Asia/Kolkata");
require_once 'Connector.php';
class User
{
    private $newUser;
    private $cron_interval_in_sec = -61;
    public function __construct()
    {
        $this->newUser = new Connector();
    }

    public function getMaxTimer(String $dev_id, String $day, String $timer_no)
    {
        $returnRes = new stdClass();

        $tableName = "view_device_timer";

        if ($day == "all") {
            $conditionArray = ["auto_inc_f='$dev_id'", "dev_no='$timer_no'"];
        } else {
            $conditionArray = ["auto_inc_f='$dev_id'", "day_name='$day'", "dev_no='$timer_no'"];
        }

        $userRes = $this->newUser->displayCondition($tableName, $conditionArray);
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                //User Email Already exists
                $userRow           = $dataArr[$no_of_row - 1];
                $returnRes->status = true;
                $returnRes->max    = $userRow->timer_no;
                $returnRes->data   = $dataArr;
            } else {
                $returnRes->status = true;
                $returnRes->max    = 0;
            }
        }
        $returnRes->qryArray = $conditionArray;
        return $returnRes;
    }

    public function delTimer($auto_id, $day_id, $timer_no, $day_display)
    {
        $tableName   = "q_device_timer";
        $fieldName   = ["auto_inc_f", "day_name", "timer_no"];
        $fieldValues = [$auto_id, $day_id, $timer_no];
        $this->newUser->deleteData($tableName, $fieldName, $fieldValues);
    }

    public function editTimer($auto_id, $day_id, $timer_no)
    {
        $tableName      = "q_device_timer";
        $conditionArray = ["auto_inc_f=$auto_id", "day_name=$day_id", "timer_no=$timer_no"];
        return $result  = (array) $this->newUser->displayCondition($tableName, $conditionArray);
        // if ($result['state'] == "1" && count($result['data']) > 0) {
        //     return $result['data']['0'];
        // }
        // return null;
    }

    public function autoUpdateDevice()
    {
        $returnRes = new stdClass();

        $tableName      = "view_device_timer";
        $conditionArray = ["day_name=WEEKDAY(now())", "secs<=0", "secs > $this->cron_interval_in_sec"];
        //$conditionArray            = array("day_name=WEEKDAY(now())");
        $userRes                   = $this->newUser->displayCondition($tableName, $conditionArray);
        $returnRes->conditionArray = $conditionArray;
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                $returnRes->status = true;
                $returnRes->data   = $dataArr;
            } else {
                $returnRes->status = false;
                $returnRes->error  = "No data";
            }
        }
        return $returnRes;
    }

    public function autoSendSms()
    {
        $returnRes                 = new stdClass();
        $tableName                 = "view_sms_tracker";
        $conditionArray            = ["sms_sent='0'", "sms_full='0'"];
        $userRes                   = $this->newUser->displayCondition($tableName, $conditionArray);
        $returnRes->conditionArray = $conditionArray;
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                $returnRes->status = true;
                $returnRes->data   = $dataArr;
            } else {
                $returnRes->status = false;
                $returnRes->error  = "No data";
            }
        }
        return $returnRes;
    }

    public function changePinState($auto_id, $pin_no, $action_type)
    {
        $returnRes = new stdClass();

        $tableName                 = "q_devices";
        $conditionArray            = ["auto_inc='$auto_id'"];
        $userRes                   = $this->newUser->displayCondition($tableName, $conditionArray);
        $returnRes->conditionArray = $conditionArray;
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                $tempData            = $dataArr[0];
                $pin_status          = $tempData->pin_status;
                $customer_key        = $tempData->customer_key;
                $device_id           = $tempData->device_id;
                $device_name         = $tempData->device_name;
                $pin_initial         = substr($pin_status, 0, $pin_no - 1);
                $pin_end             = substr($pin_status, $pin_no);
                $pin_status          = $pin_initial . $action_type . $pin_end;
                $conditionfieldName  = ["auto_inc"];
                $conditionfieldValue = [$auto_id];
                $updateVal           = " pin_status = '$pin_status'";
                $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);

                $tableName  = " q_device_timer_log ";
                $filedName  = ["cust_key", "device_id", "device_name", "pin_no", "pin_status"];
                $fieldValue = [$customer_key, $device_id, $device_name, $pin_no, $action_type];
                $this->newUser->insertData($tableName, $filedName, $fieldValue);
            }

        }
    }

    public function setTimer($timer1, $timer2, $state1ID, $state2ID, $maxRec, $dev_auto_id, $dev_index_id, $day_id, $mode)
    {
        $tableName = " q_device_timer ";
        $filedName = ["auto_inc_f", "dev_no", "day_name", "action_type", "action_time", "timer_no", "mode"];
        if ($day_id != "all") {
            $fieldValue  = [$dev_auto_id, $dev_index_id, $day_id, $state1ID, $timer1, $maxRec, $mode];
            $returnRes1  = $this->newUser->insertData($tableName, $filedName, $fieldValue);
            $fieldValue2 = [$dev_auto_id, $dev_index_id, $day_id, $state2ID, $timer2, $maxRec, $mode];
            $returnRes2  = $this->newUser->insertData($tableName, $filedName, $fieldValue2);
        } else {
            for ($i = 0; $i < 7; $i++) {
                $fieldValue  = [$dev_auto_id, $dev_index_id, $i, $state1ID, $timer1, $maxRec, $mode];
                $returnRes1  = $this->newUser->insertData($tableName, $filedName, $fieldValue);
                $fieldValue2 = [$dev_auto_id, $dev_index_id, $i, $state2ID, $timer2, $maxRec, $mode];
                $returnRes2  = $this->newUser->insertData($tableName, $filedName, $fieldValue2);
            }
        }
        return true;
    }

    public function storeShareDetail(String $module_id, String $auto_inc, String $email, String $devices, String $pwd, String $mode, String $start_time, String $end_time)
    {
        $returnRes  = new stdClass();
        $tableName  = "share_detail";
        $filedName  = ["module_id", "auto_inc", "email", "password", "devices", "mode", "start_time", "end_time"];
        $fieldValue = [$module_id, $auto_inc, $email, $pwd, $devices, $mode, $start_time, $end_time];
        $returnRes  = $this->newUser->insertData($tableName, $filedName, $fieldValue);
        return $returnRes;
    }

    public function registerUser(String $email, String $mobile, String $pwd)
    {
        $returnRes = new stdClass();
        if ($email == "" || $mobile == "" || $pwd == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $conditionArray = ["email='$email'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User Email Already exists
                    $userRow                        = $dataArr[0];
                    $returnRes->status              = false;
                    $returnRes->error               = "exists";
                    $returnRes->pendingVerification = $userRow->e_verified;
                    $returnRes->suspendedUser       = $userRow->u_status;
                    $returnRes->userID              = $userRow->u_id;
                } else {
                    //proceed ith registration
                    $tableName  = " q_user ";
                    $pwd        = md5($pwd);
                    $userKey    = md5($email);
                    $filedName  = ["email", "mobile", "pwd", "device_key", "u_status"];
                    $fieldValue = [$email, $mobile, $pwd, $userKey, '1'];
                    $returnRes  = $this->newUser->insertData($tableName, $filedName, $fieldValue);
                }
            }
        }
        return $returnRes;
    }

    public function getRegisterUserData(String $id)
    {
        $returnRes = new stdClass();
        if ($id == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $conditionArray = ["u_id='$id'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $returnRes->status = true;
                    $returnRes->data   = $dataArr[0];
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function verifyUserEmail(String $id, String $email)
    {
        $returnRes = new stdClass();
        if ($id == "" || $email == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $conditionArray = ["u_id='$id'", "email = '$email'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            // print_r($userRes);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // print_r($dataArr);
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row               = $dataArr[0];
                    $validEmail        = $row->e_verified;
                    $validUser         = $row->u_status;
                    $returnRes->status = true;
                    if ($validEmail == "1" && $validUser != "1") {
                        //Email vefified but user blocked
                        $returnRes->userType = "blocked";
                    } else if ($validEmail == "1" && $validUser == "1") {
                        //Email verified & User active - nothing to do
                        $returnRes->userType = "verified";
                    } else {
                        //verify email
                        $tableName           = " q_user ";
                        $conditionfieldName  = ["u_id"];
                        $conditionfieldValue = [$id];
                        $updateVal           = " e_verified = '1', u_status = '1'";
                        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                        $returnRes->userType = "ok";
                    }
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function validateUser($email, $pwd)
    {
        $returnRes = new stdClass();
        if ($pwd == "" || $email == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $pwd            = md5($pwd);
            $conditionArray = ["pwd='$pwd'", "email = '$email'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row                   = $dataArr[0];
                    $validEmail            = $row->e_verified;
                    $validUser             = $row->u_status;
                    $userId                = $row->u_id;
                    $returnRes->status     = true;
                    $returnRes->uid        = $userId;
                    $returnRes->device_key = $row->device_key;
                    $returnRes->email      = $row->email;
                    $returnRes->mobile     = $row->mobile;

                    if ($validEmail == "1" && $validUser != "1") {
                        //Email vefified but user blocked
                        $returnRes->userType = "blocked";
                    } else if ($validEmail == "1" && $validUser == "1") {
                        //Email verified & User active - nothing to do
                        $returnRes->userType  = "verified";
                        $returnRes->mobile    = $row->mobile;
                        $returnRes->deviceKey = $row->device_key;
                    } else {
                        //verify email
                        $returnRes->userType = "pending";
                    }
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function getSMSStatus(String $cust_key)
    {
        $returnRes = new stdClass();
        if ($cust_key == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName = "view_user_sms";

            $conditionArray = ["cust_key_f='$cust_key'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row                    = $dataArr[0];
                    $returnRes->sms_enabled = $row->sms_enabled;
                    if ($returnRes->sms_enabled == 1) {
                        $returnRes->number_verified = $row->number_verified;
                        if ($returnRes->number_verified == 1) {
                            $returnRes->status        = true;
                            $returnRes->sms_left      = $row->sms_left;
                            $returnRes->extra_sms_cnt = $row->extra_sms_cnt;
                            $returnRes->mobile        = $row->mobile;
                        } else {
                            $returnRes->status = false;
                            $returnRes->error  = 'not verified';
                        }
                    } else {
                        $returnRes->status = false;
                        $returnRes->error  = 'disabled';
                    }

                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function update_sms_count($cust_key, $device_id, $sms_cnt, $sms_extra)
    {
        $tableName           = " q_user_sms ";
        $conditionfieldName  = ["cust_key_f", "module_id"];
        $conditionfieldValue = [$cust_key, $device_id];
        $updateVal           = " sms_used = sms_used + $sms_cnt, extra_sms_cnt = $sms_extra ";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function resetWeeklySMSLimit()
    {
        $tableName           = " q_user_sms ";
        $conditionfieldName  = ["1"];
        $conditionfieldValue = ["1"];
        $updateVal           = " sms_used = 0, extra_sms_cnt = 0 ";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function update_sms_log($auto_log, $sms_sent, $sms_full)
    {
        $tableName           = " q_device_timer_log ";
        $conditionfieldName  = ["auto_log"];
        $conditionfieldValue = [$auto_log];
        $updateVal           = " sms_sent = '$sms_sent' , sms_full = '$sms_full'";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function validateUserMobile($mobile, $otp)
    {
        $returnRes = new stdClass();
        if ($mobile == "" || $otp == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName = "view_user_sms_status";

            $conditionArray = ["mobile='$mobile'", "verification_code = '$otp'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row                   = $dataArr[0];
                    $returnRes->status     = true;
                    $returnRes->uid        = $row->u_id;
                    $returnRes->device_key = $row->cust_key_f;
                    $returnRes->email      = $row->email;
                    $returnRes->mobile     = $row->mobile;
                    $tableName             = " q_user_sms_status ";
                    $conditionfieldName    = ["cust_key_f"];
                    $conditionfieldValue   = [$row->cust_key_f];
                    $updateVal             = " number_verified = '1'";
                    $result                = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function setPwd($email)
    {
        $returnRes = new stdClass();
        if ($email == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $conditionArray = ["email = '$email'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row               = $dataArr[0];
                    $validEmail        = $row->e_verified;
                    $validUser         = $row->u_status;
                    $id                = $row->u_id;
                    $returnRes->status = true;
                    if ($validEmail == "1" && $validUser != "1") {
                        //Email vefified but user blocked
                        $returnRes->userType = "blocked";
                    } else if ($validEmail == "1" && $validUser == "1") {
                        //Email verified & User active - send pwd
                        $returnRes->userType = "verified";
                        $tableName           = " q_user ";
                        $conditionfieldName  = ["u_id"];
                        $conditionfieldValue = [$id];
                        $pwd                 = mt_rand(10000, 99999);
                        $md5_pwd             = md5($pwd);
                        $updateVal           = " pwd = '$md5_pwd'";
                        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                        $returnRes->userType = "ok";
                        $returnRes->pwd      = $pwd;

                    } else {
                        // email not verified
                        $returnRes->userType = "pending";
                    }
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function getDeviceList($uid)
    {
        $returnRes = new stdClass();
        if ($uid == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "view_devices";
            $conditionArray = ["u_id='$uid'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $returnRes->status = true;
                    $returnRes->data   = $dataArr;
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "NoDevice";
                }
            }
        }
        return $returnRes;
    }

    public function getDeviceListByModuleId($uid)
    {
        $returnRes = new stdClass();
        if ($uid == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "view_devices";
            $conditionArray = ["device_id='$uid'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $returnRes->status = true;
                    $returnRes->data   = $dataArr;
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "NoDevice";
                }
            }
        }
        return $returnRes;
    }

    public function getShareListData($id)
    {
        $returnRes      = new stdClass();
        $tableName      = "share_detail";
        $conditionArray = ["module_id='$id'"];
        $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                //User ID exists
                $returnRes->status = true;
                $returnRes->data   = $dataArr;
            } else {
                $returnRes->status = false;
                $returnRes->error  = "NoDevice";
            }
        }
        return $returnRes;
    }

    public function getShareListDataById($id)
    {
        $returnRes      = new stdClass();
        $tableName      = "share_detail";
        $conditionArray = ["id='$id'"];
        $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
        if ($userRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $userRes->data;
            // $returnRes->userRes = $userRes;
            $no_of_row = count($dataArr);
            if ($no_of_row > 0) {
                //User ID exists
                $returnRes->status = true;
                $returnRes->data   = $dataArr;
            } else {
                $returnRes->status = false;
                $returnRes->error  = "NoDevice";
            }
        }
        return $returnRes;
    }

    public function reSetPwd($uid, $pwd)
    {
        $returnRes = new stdClass();
        if ($uid == "" || $pwd == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "q_user";
            $conditionArray = ["u_id = '$uid'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    $returnRes->status   = true;
                    $tableName           = " q_user ";
                    $conditionfieldName  = ["u_id"];
                    $conditionfieldValue = [$uid];
                    $md5_pwd             = md5($pwd);
                    $updateVal           = " pwd = '$md5_pwd'";
                    $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                    $returnRes->res      = $result;
                    $returnRes->updt     = $updateVal;
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "Nouser";
                }
            }
        }
        return $returnRes;
    }

    public function overwritePin($from_src, $reader_id, $pins)
    {
        $tableName           = " q_devices ";
        $conditionfieldName  = ["customer_key", "device_id"];
        $conditionfieldValue = [$from_src, $reader_id];
        $updateVal           = "pin_status='$pins'";
        $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);

        // Update access_time
        $dt                  = date("Y-m-d H:i:s");
        $accessTimeUpdateVal = "access_time='$dt'";
        $this->newUser->updateData($tableName, $accessTimeUpdateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function checkDevice($from_src, $reader_id)
    {
        $returnRes = new stdClass();
        if ($from_src == "" || $reader_id == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "view_devices_mini";
            $conditionArray = ["customer_key='$from_src'", "device_id='$reader_id'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //Check if the device is approved
                    $data          = $dataArr[0];
                    $device_status = $data->device_status;
                    if ($device_status == "1") {
                        //device is approved and send back the status of the pins to device
                        $no_of_devices_approved = $data->approved_devices;
                        $did                    = $data->auto_inc;
                        $pin_status             = $data->q_devices;
                        $device_status          = substr($pin_status, 0, $no_of_devices_approved);
                        $tableName              = " q_devices ";
                        $conditionfieldName     = ["auto_inc"];
                        $conditionfieldValue    = [$did];
                        $dt                     = date("Y-m-d H:i:s");
                        $updateVal              = "access_time='$dt'";
                        $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                        $returnRes->status        = true;
                        $returnRes->ip            = $data->user_ip;
                        $returnRes->device_status = $device_status;
                    } else {
                        //device is not approved
                        $tableName           = " q_devices ";
                        $did                 = $data->auto_inc;
                        $conditionfieldName  = ["auto_inc"];
                        $conditionfieldValue = [$did];
                        $dt                  = date("Y-m-d H:i:s");
                        $updateVal           = "access_time='$dt'";
                        $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
                        $returnRes->status = false;
                    }
                } else {
                    //register the device if the from_src has a valid customer ID
                    $returnRes->status = false;
                    $tableName         = "q_user";
                    $conditionArray    = ["device_key='$from_src'"];
                    $validUserRes      = $this->newUser->displayCondition($tableName, $conditionArray);
                    if ($validUserRes->state == true) {
                        $userDataArr = $validUserRes->data;
                        $no_of_row2  = count($userDataArr);
                        if ($no_of_row2 > 0) {
                            $dt                    = date("Y-m-d H:i:s");
                            $tableName             = " q_devices ";
                            $filedName             = ["customer_key", "device_id", "device_status", "approved_devices", "pin_status", "reg_date"];
                            $fieldValue            = [$from_src, $reader_id, '0', '0', '0000000000000000', $dt];
                            $userRes               = $this->newUser->insertData($tableName, $filedName, $fieldValue);
                            $returnRes->sendMail   = true;
                            $tempDataUser          = $userDataArr[0];
                            $returnRes->sendMailTo = $tempDataUser->email;
                        } else {
                            $returnRes->sendMail = false;
                        }
                    } else {
                        $returnRes->sendMail = false;
                    }
                }
            }
        }
        return $returnRes;
    }

    public function getAdminEmail()
    {
        $returnRes = new stdClass();
        $table     = "admin_user";
        $returnRes = $this->newUser->displayAll($table);
        if ($returnRes === false) {
            $returnRes->status = false;
        } else {
            $data       = $returnRes->data;
            $countAdmin = count($data);
            $emails     = "";
            for ($i = 0; $i < $countAdmin; $i++) {
                $row = $data[$i];
                if ($countAdmin - $i == 1) {
                    $emails = $emails . $row->email;
                } else {
                    $emails = $emails . $row->email . ",";
                }
            }
            $returnRes = $emails;
        }
        return $returnRes;
    }

    public function getDeviceDetails(String $device_auto_id)
    {
        $returnRes = new stdClass();
        if ($device_auto_id == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "view_devices";
            $conditionArray = ["auto_inc='$device_auto_id'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //Check if the device is approved
                    $data          = $dataArr[0];
                    $device_status = $data->device_status;
                    //device is approved and send back the status of the pins to device
                    $returnRes->no_of_devices_approved   = $data->approved_devices;
                    $device_id                           = $data->device_id;
                    $returnRes->pin_status               = $data->pin_status;
                    $returnRes->status                   = true;
                    $returnRes->name                     = $data->device_name;
                    $returnRes->device_key               = $data->customer_key;
                    $returnRes->device_id                = $device_id;
                    $returnRes->device_status            = $device_status;
                    $returnRes->last_relay_signal_in_sec = $data->secs;
                    $returnRes->no_of_connected_devices  = $data->approved_devices;
                    $timerRes                            = $this->getTimerStatus($device_id, "device", "all");
                    $returnRes->timerIsSet               = $timerRes->timerIsSet;
                    $returnRes->pinArray                 = $timerRes->pinArray;
                } else {
                    //register the device
                    $returnRes->status = false;
                    $returnRes->id     = $device_auto_id;
                    $returnRes->error  = "nodevice";
                }
            }
        }
        return $returnRes;
    }

    public function getTimerStatus(String $device_id, String $searchType, String $dev_no)
    {
        $timer_pinArray = [];
        $returnRes      = new stdClass();
        $tableName      = "view_timer_mini";
        if ($dev_no == "all") {
            $conditionArray = ["device_id='$device_id'"];
        } else {
            $conditionArray = ["device_id='$device_id'", "dev_no='$dev_no'"];
        }
        $userRes2 = $this->newUser->displayCondition($tableName, $conditionArray);
        if ($userRes2->state) {
            $dataArr2    = $userRes2->data;
            $no_of_timer = count($dataArr2);
            if ($no_of_timer > 0) {
                $returnRes->timerIsSet = true;
                for ($j = 0; $j < $no_of_timer; $j++) {
                    $tmpRow = $dataArr2[$j];
                    if ($searchType == "device") {
                        if ($timer_pinArray == "") {
                            $timer_pinArray = [$tmpRow->dev_no];
                        } else {
                            array_push($timer_pinArray, $tmpRow->dev_no);
                        }
                    } else if ($searchType == "device_day") {
                        if ($timer_pinArray == "") {
                            $timer_pinArray = [$tmpRow->day_name];
                        } else {
                            array_push($timer_pinArray, $tmpRow->day_name);
                        }
                    }
                }
                $returnRes->pinArray = array_unique($timer_pinArray);
            } else {
                $returnRes->timerIsSet = false;
            }
        } else {
            $returnRes->timerIsSet = false;
        }
        return $returnRes;
    }

    public function changeDeviceStatus(String $did, String $devices, $ip)
    {
        $tableName           = " q_devices ";
        $conditionfieldName  = ["auto_inc"];
        $conditionfieldValue = [$did];
        $updateVal           = "pin_status='$devices', user_ip = '$ip'";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function changeDeviceName(String $did, String $devices)
    {
        $tableName           = " q_devices ";
        $conditionfieldName  = ["auto_inc"];
        $conditionfieldValue = [$did];
        $updateVal           = "device_name='$devices'";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function updateTimer($a_inc, $timer, $stateID, $maxRec, $dev_auto_id, $dev_index_id, $day_id, $mode)
    {
        $tableName  = " q_device_timer ";
        $filedName  = ["a_inc"];
        $fieldValue = ["$a_inc"];
        $updateVal  = "auto_inc_f='$dev_auto_id',dev_no='$dev_index_id',action_type='$stateID',action_time='$timer',timer_no='$maxRec',mode='$mode'";
        $this->newUser->updateData($tableName, $updateVal, $filedName, $fieldValue);
        return true;
    }

    public function delTimerNew($temprow)
    {
        $auto_inc_f = $temprow->auto_inc_f;
        $dev_no     = $temprow->dev_no;
        $a_inc      = $temprow->a_inc;
        $day_name   = $temprow->day_name;
        $timer_no   = $temprow->timer_no;
        $mode       = $temprow->mode;

        $tableName   = "q_device_timer";
        $fieldNames  = ["auto_inc_f", "dev_no", "day_name", "timer_no", "mode"];
        $fieldValues = [$auto_inc_f, $dev_no, $day_name, $timer_no, $mode];

        $filedName  = ["a_inc"];
        $fieldValue = ["$a_inc"];
        $updateVal  = "status='complete'";
        $this->newUser->updateData($tableName, $updateVal, $filedName, $fieldValue);

        $conditionArray = ["auto_inc_f='$auto_inc_f'", "dev_no='$dev_no'", "timer_no='$timer_no'", "mode='$mode'"];
        $userRes2       = $this->newUser->displayCondition($tableName, $conditionArray);
        if (isset($userRes2->data[0]) && isset($userRes2->data[1]) && $userRes2->data[0]->status == "complete" && $userRes2->data[1]->status == "complete") {
            $this->newUser->deleteData($tableName, $fieldNames, $fieldValues);
        }
    }

    public function checkOldPwd($uid, $pwd)
    {
        $tableName      = "q_user";
        $pwd            = md5($pwd);
        $conditionArray = ["pwd='$pwd'", "u_id='$uid'"];
        $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
        if (isset($userRes->data) && count($userRes->data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteShareDetail($auto_id)
    {
        $tableName   = "share_detail";
        $fieldName   = ["id"];
        $fieldValues = [$auto_id];
        $this->newUser->deleteData($tableName, $fieldName, $fieldValues);
        $_SESSION['sharedUserLogin'] = false;
        $_SESSION['sharedId']        = '';
    }

    public function updateShareDetails($uid, $devices, $mode, $start_time, $end_time)
    {
        $tableName           = "share_detail";
        $conditionfieldName  = ["id"];
        $conditionfieldValue = [$uid];
        $updateVal           = " devices = '$devices',mode='$mode',start_time = '$start_time',end_time = '$end_time'";
        $result              = $this->newUser->updateData($tableName, $updateVal, $conditionfieldName, $conditionfieldValue);
    }

    public function validateSharedUser($email, $pwd)
    {
        $returnRes = new stdClass();
        if ($pwd == "" || $email == "") {
            $returnRes->status = false;
            $returnRes->error  = "blank";
        } else {
            $tableName      = "share_detail";
            $pwd            = md5($pwd);
            $conditionArray = ["password='$pwd'", "email = '$email'"];
            $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
            if ($userRes->state == false) {
                $returnRes->status = false;
                $returnRes->error  = "Db issue";
            } else {
                $dataArr = $userRes->data;
                // $returnRes->userRes = $userRes;
                $no_of_row = count($dataArr);
                if ($no_of_row > 0) {
                    //User ID exists
                    $row                   = $dataArr[0];
                    $returnRes->status     = true;
                    $returnRes->id         = $row->id;
                    $returnRes->module_id  = $row->module_id;
                    $returnRes->email      = $row->email;
                    $returnRes->mode       = $row->mode;
                    $returnRes->start_time = $row->start_time;
                    $returnRes->end_time   = $row->end_time;
                } else {
                    $returnRes->status = false;
                    $returnRes->error  = "No user";
                }
            }
        }
        return $returnRes;
    }

    public function validateConfirmUser($pwd)
    {
        $returnRes      = new stdClass();
        $tableName      = "q_user";
        $pwd            = md5($pwd);
        $conditionArray = ["pwd='$pwd'"];
        $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
        $dataArr        = $userRes->data;
        $no_of_row      = count($dataArr);
        return $no_of_row;
    }
    
    public function getSharedUser($userId)
    {
        $tableName      = "share_detail";
        $conditionArray = ["id='$userId'"];
        $userRes        = $this->newUser->displayCondition($tableName, $conditionArray);
        $dataArr        = $userRes->data;
        $no_of_row      = count($dataArr);
        if ($no_of_row > 0) {
            $row = $dataArr[0];
            return $row;
        } else {
            return false;
        }
    }
}
