<?php
session_start();
if (!$_SESSION['userLogin']) {
    header("Location: login.php");
}
$uid = $_SESSION['cid'];
include "classes/User.php";
$active_time_in_sec = 7;   //  originally it was checking in 60 seconds

$user        = new User();
$resultObj   = $user->getDeviceList($uid);
$hideDisplay = true;

if ($resultObj->status == false) {
    if ($resultObj->error == "NoDevice") {
        $messag = "You do not have any registered device";
    } else {
        $messag = "Issue fetching device information kindly contact Site admin";
    }
} else {
    $deviceArray   = $resultObj->data;
    $countOfDevice = count($deviceArray);
}

if (isset($_POST['submit'])) {
    $resultObjNew = $user->validateConfirmUser($_POST['password']);
    if ($resultObjNew >= 1) {
        $url = "shareDetails.php?id=" . $_POST['deviceId'] . "&did=" . $_POST['Did'];
        header("Location: " . $url);
    } else {
        $messag = "Password not match";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <title>User Home Page</title>
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
</head>
<body>
        <?php
include_once "inc_header_logged.php";
?>



        <img src="images/samplebanner.jpg" class="img-fluid mx-auto d-block">
    <div class="container-fluid">
        <div class="container heading">Registered Module List</div>
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
            <?php }?>
            <div class="row">
                <div class="row d-flex justify-content-center bd-highlight responDevice">
                    <?php
if ($countOfDevice > 0) {
    for ($i = 0; $i < $countOfDevice; $i++) {
        $data                     = $deviceArray[$i];
        $device_status            = $data->device_status;
        $did                      = $data->auto_inc;
        $pin_status               = $data->pin_status;
        $no_of_connected_devices  = $data->approved_devices;
        $device_name              = $data->device_name;
        $device_id                = $data->device_id;
        $device_key               = $data->device_key;
        $last_relay_signal_in_sec = $data->secs;
        $regDate                  = $data->reg_date;

        if ($device_id == "") {
            continue;
        }
        if ($device_name == "") {
            $device_display = "Module Id: " . $device_id;
        } else {
            $device_display = $device_name . "(" . $device_id . ")";
        }
        $bulb = "";
        if ($device_status == "1") {
            $displayData  = true;
            $dispayString = "Active";
            // $connDev="Connected Device: $no_of_connected_devices";
            $connDev = "Connected Device: " . $no_of_connected_devices . "<br>Reg Date: $regDate";
            $dev_no  = $no_of_connected_devices;
            for ($j = 0; $j < $dev_no; $j++) {
                $ledState = substr($pin_status, $j, 1);
                if ($ledState == "0") {
                    $bulb = $bulb . "<span class='dot'></span>";
                } else {
                    $bulb = $bulb . "<span class='dot bg-success'></span>";
                }
                // print "counting $bulb";
            }
            $connDev    = $connDev . "<br>" . $bulb;
            $footerText = "<a href='deviceDetails.php?id=$device_id&k=$device_key&did=$did' title='Device Control'><button type='button' class='btn btn-warning btn-sm pull-left'><i class='fa fa-solid fa-server'></i></button></a> <a title='Shared Device' class='openModal' data-device='" . $device_id . "' data-did='" . $did . "' href='javascript:void(0)'><button type='button' class='btn btn-success btn-sm pull-right'><i class='fa fa-solid fa-share'></i></button></a>";
        } else {
            $displayData  = false;
            $dispayString = "Pending Approval";
            $connDev      = "";
            $footerText   = "<div class='text-light'>Activating your device may take upto 24Hrs</div>";
        }
        if ($active_time_in_sec > $last_relay_signal_in_sec) {
            $onlineStatus = "$dispayString<div class='text-success'>Online</div>";
        } else {
            $onlineStatus = "$dispayString<div class='text-danger'>Offline</div>";
        }
        ?>
                                                <div class="card m-1" style="width:240px">
                                                    <div class="card-header bg-info text-white p-3" >
                                                        <?php print $device_display;?>
                                                    </div>
                                                    <div class="card-body bg-light">
                                                        <h5 class="card-title"><?php print $onlineStatus;?></h5>
                                                        <h6 class="card-subtitle text-muted"><?php print $connDev;?></h6>

                                                    </div>
                                                    <div class="card-footer bg-info">
                                                        <?php print $footerText;?>
                                                    </div>

                                                </div>
                                            <?php
} //close for loop
} //close if
else {
    ?>
                            <ul class="mx-4"><h5>Please follow the steps below to connect/register your smart device</h5>
                                <li>Power On your Device</li>
                                <li>Connect to the wifi connection 'QualityFirst' on your smart phone/Computer</li>
                                <li>Open the IP http://192.168.4.1/</li>
                                <li>Enter the Default User Name: admin and Password: admin </li>
                                <li>Select your Wifi Connection Name from the Wifi Dropdown</li>
                                <li>Enter your Device Key (You can find it under Manage Account->Get Device Key)</li>
                                <li>Enter a new password for your device </li>
                                <li>Click on Save & re-start</li>
                            </ul>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Password Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form name="frm" method="post" action="qualityfirst-home.php">
          <div class="modal-body">
              <div class="form-group">
                <label for="recipient-name" class="col-form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Your Password">
                <input type="hidden" id="deviceId" name="deviceId" value="">
                <input type="hidden" id="Did" name="Did" value="">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <input type="submit" name="submit" value="Confirm" class="btn btn-primary">
          </div>
        </form>
    </div>
  </div>
</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="dist/jquery/jquery-3.6.1.min.js"></script>
    <script src="dist/poper/popper.min.js" ></script>
    <script src="dist/bootstrap/bootstrap.min.js" ></script>
    <script src="https://kit.fontawesome.com/1c0e96ca9b.js" crossorigin="anonymous"></script>
     <script type="text/javascript" src="custom.js?v=<?php echo date('YmdHis'); ?>"></script> 
    <script type="text/javascript">

        $(document).on("click", ".openModal", function (e) {
            e.preventDefault();
            $("#Did").val($(this).data('did'));
            $("#deviceId").val($(this).data('device'));
            $("#exampleModal").modal('show');
        });

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
    <style>
        .openModal {
            float: right;
        }
    </style>
</body>
</html>