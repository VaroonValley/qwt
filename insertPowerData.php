<?php 
class User {

    public function insertPowerData($reader_id, $pvoltage, $pcurrent) {
    $tableName = "q_power";
    $fieldNames = ["device_id", "voltage", "current", "timestamp"];
    $fieldValues = [$reader_id, $pvoltage, $pcurrent, date("Y-m-d H:i:s")];

    return $this->newUser->insertData($tableName, $fieldNames, $fieldValues);
}

}
