<?php
    error_reporting(0);
    session_start();
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)

    $voltageThresholdMin = 180;
    $voltageThresholdMax = 200;
    $currentThresholdMin = 20; 
    $currentThresholdMax = 50;
    $now = date('d-m-Y H:i:s');
   
 

    $response = array();

    if (isset($_REQUEST['k']) && isset($_REQUEST['id']) && isset($_REQUEST['pin']) && isset($_REQUEST['pvoltage']) && isset($_REQUEST['pcurrent'])) {
        $from_src   = trim($_REQUEST['k']);
        $reader_id  = trim($_REQUEST['id']);
        $pins       = trim($_REQUEST['pin']);
        $pvoltage   = floatval($_REQUEST['pvoltage']); // Assuming the voltage is a float
        $pcurrent   = floatval($_REQUEST['pcurrent']); // Assuming the current is a float
        $message = '';// Message which will be send to customer on alert.
        $abnomality = '';// Message which include specific values and time.

        require_once "classes/User.php";
        $user = new User();

        // Insert voltage and amp values into q_power table
        $user->insertPowerData($reader_id, $pvoltage, $pcurrent);
        

        // Check if voltage and amp are within defined values
        if ($pvoltage >= $voltageThresholdMin && $pvoltage <= $voltageThresholdMax 
            && $pcurrent >= $currentThresholdMin && $pcurrent <= $currentThresholdMax ) {
            // If yes, update or overwrite the pin using existing function
            $user->overwritePin($from_src, $reader_id, $pins);

            // Send email to user
            $userEmail = $user->getUserEmail($from_src); // Implement this function in your User class
            $subject = "Device Update Notification";
            $body = "Your device with ID $reader_id has been updated successfully.Check the details on <a href='$domain/qualityfirst-home.php'> qualityfirst-home.php</a>.";

            // Use the MailSender function from mailsender.php
            require_once "classes/MailSender.php";
            $mailer = new MailSender();
            $mailer->sendMail($userEmail, $subject, $body);

            $response["status"] = 1;
        } else {

            // Retrieve stored pin values
            $storedPinRes = $user->getPinValues($from_src, $reader_id);
            if($storedPinRes->status){
                $storedPin = $storedPinRes->data[0]->pin_status;

                $message .= "Previous Devices Status : <br>";
                $message .= "Devices            Status<br>";
                for($i=0; $i<strlen($len); $i++){
                    $device = $i +1;
                    $message .=($len[$i]== '1') ? "Device ". $device . "            <i style='color: red'>ON</i> <br>" :
                    "Device ". $device . "            <i style='color: red'>Off</i> <br>";
                }

                
            }


            // If not, change or overwrite the pin values
            $newPinLength = strlen($pins);
            $newPin = str_repeat('0', $newPinLength);
            $user->overwritePin($from_src, $reader_id, $newPin);

            // Send email to user
            $userEmail = $user->getUserEmail($from_src); // Implement this function in your User class
            
            // Phrasing the emails.
            if(($pvoltage < $voltageThresholdMin || $pvoltage > $voltageThresholdMax) && 
            ($pcurrent < $currentThresholdMin || $pcurrent > $currentThresholdMax)){

                $abnomality = "voltage of $pvoltage and current of $pcurrent";
            }else{

                $abnomality= ($pvoltage < $voltageThresholdMin || $pvoltage > $voltageThresholdMax ) ? 
                "voltage of $pvoltage" : "current of $pcurrent";
            }
            $message .= "<p><b>Regards,</b></p> <p>Quality First Home";
            
            
            $subject = "Device Update Notification";
            $body = "Your device with ID $reader_id has experienced abnormal $abnomality as on $today. 
            Your devices have been turned off. Check the details on qualityfirst-home.php <br><br>
            $message ";

            // Use the MailSender function from mailsender.php
            require_once "classes/MailSender.php";
            $mailer = new MailSender();
            $mailer->sendMail($userEmail, $subject, $body);

            $response["status"] = 2;
        }
    } else {
        // If required parameter is missing
        $response["status"] = 0;
        $response["message"] = "Parameter(s) are missing. Please check the request.";
    }

    echo json_encode($response);


