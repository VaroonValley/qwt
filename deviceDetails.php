<?php
session_start();
if (!$_SESSION['userLogin']) {
    header("Location: login.php");
}
$uid = $_SESSION['cid'];
include "classes/User.php";
$user               = new User();
$did                = $_REQUEST['did'];
$_SESSION['did']    = $did;
$active_time_in_sec = 60;
if ($uid == "" || $did == "") {
    header("Location: qualityfirst-home.php?$did&$uid&done");
    exit;
}

$submit        = $_REQUEST['submit'];
$condevId      = $_REQUEST['connected_device'];
$changeNameBtn = $_REQUEST['cn'];

// print_r("$condevId dec connected");
$newValue = "";

if ($condevId != "" && $submit == "update") {
    for ($i = 0; $i < $condevId; $i++) {
        $devNo     = $i + 1;
        $device_nm = "switch_" . $devNo;
        if ($_REQUEST[$device_nm] == "1") {
            $newValue = $newValue . "1";
        } else {
            $newValue = $newValue . "0";
        }
    }
    $fill_value = "0000000000000000";
    $to_fill    = 16 - $condevId;
    $fill_value = substr($fill_value, 0, $to_fill);
    $newValue   = $newValue . $fill_value;
    $approved   = true;
    // print_r("New updated value : $newValue for $did");
    $ip = $_SESSION['remote_ip'];
    $user->changeDeviceStatus($did, $newValue, $ip);
    header("Location: qualityfirst-home.php");
} else if ($changeNameBtn == "change") {
    $device_new_name = trim($_REQUEST['device_new_name']);
    if ($device_new_name != "") {
        // print_r("New updated name : $device_new_name for $did");
        $user->changeDeviceName($did, $device_new_name);
    }
}

$resultObj   = $user->getDeviceDetails($did);
$hideDisplay = true;
if ($resultObj->status == false) {
    if ($resultObj->error == "NoDevice") {
        $messag = "User does not have any registered Modules";
    } else {
        $messag = "Issue fetching device information kindly contact Site admin";
    }
} else {
    $countOfDevice = 1;
    if ($resultObj->name == "") {
        $device_display = "Module Id: " . $resultObj->device_id;
    } else {
        $device_display = $resultObj->name . "(" . $resultObj->device_id . ")";
    }

    if ($resultObj->device_status == "1") {
        $displayData      = true;
        $dispayString     = "Active";
        $dev_no           = $resultObj->no_of_connected_devices;
        $connDev          = "Connected Device: $dev_no";
        $bulb             = "";
        $deviceToggleText = "";
        $pin_status       = $resultObj->pin_status;
        $timerIsSet       = $resultObj->timerIsSet;
        $timerPinArray    = $resultObj->pinArray;
        for ($j = 0; $j < $dev_no; $j++) {
            $device_no = $j + 1;
            $ledState  = substr($pin_status, $j, 1);
            $clockCol  = " text-secondary ";
            if ($timerIsSet) {
                $searchRes = array_search($device_no, $timerPinArray);
                if ($searchRes === false) {
                    $clockCol = " text-secondary ";
                } else {
                    $clockCol = " text-success ";
                }
            }
            $timerClock = "<a href='deviceTimings.php?device_sno=" . $device_no . "&dno=" . $resultObj->device_id . "&did=" . $did . "'>&nbsp;<button  type='button' class='btn btn-sm btn-outline-info'><i class='fas fa-clock $clockCol pt-1' style='font-size:20px'></i></button></a>";

            if ($device_no == 1) {
                $blankSp = "&nbsp; ";
            } else if ($device_no < 10) {
                $blankSp = "&nbsp;";
            } else {
                $blankSp = "";
            }

            if ($ledState == "0") {
                $bulb             = $bulb . "<span class='dot'></span>";
                $deviceToggleText = $deviceToggleText . "<div class='input-group mb-1'><div class='input-group-prepend'><span class='input-group-text' style='font-family:courier;'>Device " . $device_no . "$blankSp</span></div><input class='clsBtn' name='switch_" . $device_no . "' value='1' type='checkbox' data-toggle='toggle' data-onstyle='success' data-offstyle='danger'>$timerClock</div>";
            } else {
                $bulb             = $bulb . "<span class='dot bg-success'></span>";
                $deviceToggleText = $deviceToggleText . "<div class='input-group mb-1'><div class='input-group-prepend'><span class='input-group-text' style='font-family:courier;'>Device " . $device_no . "$blankSp</span></div><input class='clsBtn' name='switch_" . $device_no . "' value='1' type='checkbox' checked data-toggle='toggle' data-onstyle='success' data-offstyle='danger'>$timerClock</div>";
            }
        }
        $connDev    = $connDev . "<br>" . $bulb;
        $footerText = 1;
    } else {
        $displayData  = false;
        $dispayString = "Pending Approval";
        $footerText   = 2;
        $connDev      = "";
    }
    if ($active_time_in_sec > $resultObj->last_relay_signal_in_sec) {
        $onlineStatus = "$dispayString<div class='text-success'>Online</div>";
    } else {
        $onlineStatus = "$dispayString<div class='text-danger'>Offline</div>";
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <title>User Details & Registered Devices</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./dist/bootstrap/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link href='https://fonts.googleapis.com/css?family=Red Hat Display' rel='stylesheet'>
    <style>
        .error{
            color:red;
        }
    </style>
    <link href="dist/toggle/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="dist/jquery/jquery-3.6.1.min.js"></script>
</head>
<body>
        <?php
include_once "inc_header_logged.php";
?>

        <script>
	window.oncontextmenu = function () {
				return false;
			}
			$(document).keydown(function (event) {
				if (event.keyCode == 123) {
					return false;
				}
				else if ((event.ctrlKey && event.shiftKey && event.keyCode == 73) || (event.ctrlKey && event.shiftKey && event.keyCode == 74)) {
					return false;
				}
			});
</script>

        <!-- <img src="images/banner6.png" class="img-fluid mx-auto d-block"> -->
    <div class="container-fluid">
        <div class="container heading">
            <div class='row'>
                <div class='col-sm-6'>Manage Details for Module</div>
                <div class='col-sm-6' align="right"><a href='qualityfirst-home.php'><i class="fas fa-arrow-left"></i>&nbsp;Back</a></div>
            </div>
        </div>

        <div class="d-flex  flex-column justify-content-center align-items-center">
            <?php if ($messag != "") {
    ?>
                <div class="d-flex p-2 bd-highlight justify-content-center align-items-center">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php
print $messag;
    ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            <?php
}
?>

            <div class="row">
                <div class="row d-flex justify-content-center bd-highlight responDetail">
                    <?php
if ($countOfDevice > 0) {
    ?>
                        <input type='hidden' id='user_keyID' value='<?php print $resultObj->device_key;?>'>
                        <input type='hidden' id='moduleID' value='<?php print $resultObj->device_id;?>'>
                        <div class="card m-1" style="width:300px">
                            <div class="card-header bg-info text-white p-3" >
                                <?php print $device_display;?>
                                <button id='changeNameShowBtnId' class='btn btn-sm text-light'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form id="formId" style="display:none;" onsubmit="return FormValidation();">
                                    <input name='did' value='<?php print $did;?>' type='hidden'>
                                    <input type='hidden' value='<?php print $dev_no;?>' name="connected_device" id='condevId'>
                                    <div class='input-group mb-1'>
                                        <div class='input-group-prepend'>
                                            <span class='input-group-text'>Name</span>
                                        </div>
                                        <input class='form-control' type='text' name="device_new_name" id="device_new_name" size='15'>
                                    </div>
                                    <button name="cn" type="submit" class="btn btn-primary btn-sm" value="change">Change Name</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php print $onlineStatus;?></h5>
                                <h6 class="card-subtitle text-muted"><?php print $connDev;?></h6>
                            </div>
                            <div class="card-footer bg-gary">
                                <form class="form-signin" action="" method="post">
                                    <input name='did' value='<?php print $did;?>' type='hidden'>
                                    <input type='hidden' value='<?php print $dev_no;?>' name="connected_device" id='condevId'>

                                    <label for="">Devices Status</label>
                                        <?php print $deviceToggleText;?>
                                    <div class="input-group pt-1">
                                        <button id='submitID' name='submit' class='btn btn-success' disabled value='update' type='submit'>&nbsp;Save Changes</button>
                                    </div>
                                    <div class="input-group pt-2 text-sm-left" id='smsDetailsID'>
                                      <div class="d-flex flex-row pt-2">
                                        <div>SMS Used:&nbsp;<span id='usedID'></span> &nbsp;&nbsp;&nbsp;</div>
                                        <div>SMS Left:&nbsp;<span id='leftID'></span> </div>
                                    </div>
                                    </div>
                                    <div class="alert alert-danger" role="alert" id="msgTextID" style="display: none"></div>
                                </form>
                            </div>
                        </div>

                    <?php

} //close if
else {
    ?>

                    <?php
} //close else
?>
                </div>
            </div>
        </div>
    </div>

        <?php
include_once "inc_footer.php";
?>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="dist/jquery/jquery-3.6.1.min.js" ></script>
    <script src="dist/poper/popper.min.js" ></script>
    <script src="dist/bootstrap/bootstrap.min.js" ></script>
    <script src="https://kit.fontawesome.com/1c0e96ca9b.js" crossorigin="anonymous"></script>
    <script src="dist/toggle/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="custom_two.js?v=<?php echo date('YmdHis'); ?>"></script>
</body>
</html>