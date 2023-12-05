<?php

class User{

    // Inside your User class
public function getUserEmail($from_src) {
    $tableName = "q_user";
    $conditionArray = ["customer_key='$from_src'"];
    
    $userRes = $this->newUser->displayCondition($tableName, $conditionArray);

    if ($userRes->state == true) {
        $userDataArr = $userRes->data;
        $no_of_row2 = count($userDataArr);
        
        if ($no_of_row2 > 0) {
            $tempDataUser = $userDataArr[0];
            return $tempDataUser->email;
        }
    }

    return null; 
}

}