<?php

class User {
public function getPinValues($from_src, $reader_id)
{
    $returnRes = new stdClass();

    if ($from_src == "" || $reader_id == "") {
        $returnRes->status = false;
        $returnRes->error  = "blank";
    } else {
        $tableName      = "q_devices";
        $conditionArray = ["customer_key='$from_src'", "device_id='$reader_id'"];
        $pinRes         = $this->newUser->displayCondition($tableName, $conditionArray);

        if ($pinRes->state == false) {
            $returnRes->status = false;
            $returnRes->error  = "Db issue";
        } else {
            $dataArr = $pinRes->data;
            $no_of_row = count($dataArr);

            if ($no_of_row > 0) {
                // Retrieve pin values
                $returnRes->status   = true;
                $returnRes->data     = $dataArr;
            } else {
                $returnRes->status = false;
                $returnRes->error  = "NoDevice";
            }
        }
    }

    return $returnRes;
}
}