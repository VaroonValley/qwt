<?php
require_once 'path_to_user.php';  // Replace with the actual path to the 'user.php' file
require_once 'path_to_mailsender.php';  // Replace with the actual path to the 'mailsender.php' file

// Assuming your request comes via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get values from the POST request
    $customer_key = $_POST['k'] ?? '';
    $device_id    = $_POST['id'] ?? '';
    $pin_status   = $_POST['pin'] ?? '';
    $voltage      = $_POST['pvoltage'] ?? '';
    $amp          = $_POST['pcurrent'] ?? '';

    // Validate inputs (You may need to add more validation)
    if (empty($customer_key) || empty($device_id) || empty($pin_status) || !is_numeric($voltage) || !is_numeric($amp)) {
        // Handle invalid input
        die("Invalid input");
    }

    // Instantiate the User class
    $user = new User();

    // Insert voltage and amp values into q_power table
    $tableName  = "q_power";
    $filedName   = ["device_id", "voltage", "amp", "date_time"];
    $fieldValue  = [$device_id, $voltage, $amp, date("Y-m-d H:i:s")];
    $user->newUser->insertData($tableName, $filedName, $fieldValue);

    // Define your voltage and amp threshold values
    $voltageThreshold = 10;  // Replace with your actual threshold value
    $ampThreshold     = 5;   // Replace with your actual threshold value

    // Check if voltage and amp are within defined values
    if ($voltage <= $voltageThreshold && $amp <= $ampThreshold) {
        // Update or overwrite the pin using existing function
        $user->overwritePin($customer_key, $device_id, $pin_status);

        // Prepare email content
        $subject = "Device Update Notification";
        $body    = "Your device with ID: $device_id has been updated successfully.";

        // Get user email from q_user table (Assuming customer_key is the user identifier)
        $tableName      = "q_user";
        $conditionArray = ["customer_key='$customer_key'"];
        $userRes        = $user->newUser->displayCondition($tableName, $conditionArray);

        if ($userRes->state && !empty($userRes->data)) {
            $userEmail = $userRes->data[0]->email;

            // Send email
            $mailer = new MailSender();
            $mailer->sendMail($userEmail, $subject, $body);
        } else {
            // Handle the case where user email is not found
            die("User email not found");
        }
    } else {
        // Voltage or amp is not within defined values

        // Create new pin value based on the length of the received pin
        $newPin = strlen($pin_status) === 6 ? '000000' : (strlen($pin_status) === 8 ? '00000000' : '');

        // Update or overwrite the pin with the new value
        $user->overwritePin($customer_key, $device_id, $newPin);

        // Prepare email content
        $subject = "Device Update Notification";
        $body    = "Your device with ID: $device_id has been updated. However, the voltage or amp values are not within the defined range.";

        // Get user email from q_user table (Assuming customer_key is the user identifier)
        $tableName      = "q_user";
        $conditionArray = ["customer_key='$customer_key'"];
        $userRes        = $user->newUser->displayCondition($tableName, $conditionArray);

        if ($userRes->state && !empty($userRes->data)) {
            $userEmail = $userRes->data[0]->email;

            // Send email
            $mailer = new MailSender();
            $mailer->sendMail($userEmail, $subject, $body);
        } else {
            // Handle the case where user email is not found
            die("User email not found");
        }
    }

    // Output a success message (you might want to handle this differently based on your application logic)
    echo "Device update successful!";
} else {
    // Handle non-POST requests
    die("Invalid request method");
}
?>
