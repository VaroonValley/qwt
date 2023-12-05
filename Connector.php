<?php
/**
 * summary
 */
class Connector
{
    /**
     * D connection class
     */
    private $hostName;
    private $host     = "";
    private $dbuser   = "";
    private $dbpass   = "";
    private $dbnameIs = "";
    private $myDBcon;

    public function __construct()
    {
        $this->hostName = $_SERVER['SERVER_NAME'];
        if ($this->hostName == "localhost" || $this->hostName == "192.168.0.100") {
            $this->host     = "localhost:3306";
            $this->dbuser   = "root";
            $this->dbpass   = "root";
            $this->dbnameIs = "q_first";
        } else {
            $this->host     = "localhost";
            $this->dbuser   = "qwebit_iot_sva";
            $this->dbpass   = "00760076A*a*";
            $this->dbnameIs = "qwebit_iot_sva";
        }

        $this->myDBcon = mysqli_connect($this->host, $this->dbuser, $this->dbpass, $this->dbnameIs);
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to Db: $this->hostName"; // .
            // mysqli_connect_error();
            exit;
        } else {
            //echo "SQL connected<br>";
        }
    }

    public function __destruct()
    {
        //echo "destructor called<br>";
        $this->myDBcon->close();
    }

    public function displayAll($table)
    {
        $result = new \stdClass();
        $qry    = "select * from $table ";
        $res    = $this->filterData($qry);
        if ($res === false) {
            $result->state = false;
            $result->error = "Error in query string";
        } else {
            $result->state = true;
            $result->data  = $res;
        }
        return $result;
    }

    public function filterData($qry)
    {
        $result = new \stdClass();
        //$qry = $this->myDBcon->real_escape_string($qry);
        $result = $this->myDBcon->query($qry);
        if ($result === false) {
            return false;
            //echo "Invalid query";
        } else {
            $resultArray = array();
            $no_rows     = $result->num_rows;
            if ($no_rows > 0) {
                for ($i = 0; $i < $no_rows; $i++) {
                    $obj = $result->fetch_object();
                    if (isset($resultArray)) {
                        array_push($resultArray, $obj);
                    } else {
                        $resultArray = array($obj);
                    }
                }
                //print_r($resultArray);
                //print "<br> Count :". count($resultArray) ."<br>";
                //$result->free();
                //$result->close();
            }
            return $resultArray;
        }
    }

    public function displayCondition($table, $filterArray)
    {
        $result      = new \stdClass();
        $filterCount = count($filterArray);
        if ($filterCount < 1) {
            $result->state = false;
            $result->error = "Filter Param is not an array";
            return $result;
        } else {
            for ($i = 0; $i < $filterCount; $i++) {
                $filterData = $filterArray[$i];
                if ($i == 0) {
                    $condition = " $filterData";
                } else {
                    $condition = $condition . " and $filterData";
                }
            }
            $qry = "select * from $table where $condition";
            $res = $this->filterData($qry);
            if ($res === false) {
                $result->state = false;
                $result->error = "Error in query condition or table name";
            } else {
                $result->state = true;
                $result->data  = $res;
            }
            return $result;
        }
    }

    public function displayConditionOr($table, $filterArray)
    {
        $result      = new \stdClass();
        $filterCount = count($filterArray);
        if ($filterCount < 1) {
            $result->state = false;
            $result->error = "Filter Param is not an array";
            return $result;
        } else {
            for ($i = 0; $i < $filterCount; $i++) {
                $filterData = $filterArray[$i];
                if ($i == 0) {
                    $condition = " $filterData";
                } else {
                    $condition = $condition . " or $filterData";
                }
            }
            $qry = "select * from $table where $condition";
            $res = $this->filterData($qry);
            if ($res === false) {
                $result->state = false;
                $result->error = "Error in query condition or table name";
            } else {
                $result->state = true;
                $result->data  = $res;
            }
            return $result;
        }
    }

    public function displayConditionOrder($table, $filterArray, $orderBy)
    {
        $result      = new \stdClass();
        $filterCount = count($filterArray);
        if ($filterCount < 1) {
            $result->state = false;
            $result->error = "Filter Param is not an array";
            return $result;
        } else {
            for ($i = 0; $i < $filterCount; $i++) {
                $filterData = $filterArray[$i];
                if ($i == 0) {
                    $condition = " $filterData";
                } else {
                    $condition = $condition . " and $filterData";
                }
            }
            $qry = "select * from $table where $condition order by $orderBy";
            $res = $this->filterData($qry);
            if ($res === false) {
                $result->state = false;
                $result->error = "Error in query condition or table name";
            } else {
                $result->state = true;
                $result->data  = $res;
            }
            return $result;
        }
    }

    public function countCondition($table, $filterArray)
    {
        $result      = new \stdClass();
        $filterCount = count($filterArray);
        if ($filterCount < 1) {
            $result->state = false;
            $result->error = "Filter Param is not an array";
            return $result;
        } else {
            for ($i = 0; $i < $filterCount; $i++) {
                $filterData = $filterArray[$i];
                if ($i == 0) {
                    $condition = " $filterData";
                } else {
                    $condition = $condition . " and $filterData";
                }
            }
            $qry = "select count(*) as returnrows from $table where $condition";
            //echo $qry;
            //$res=$this->myDBcon->query($qry);
            $res = $this->filterData($qry);
            if ($res === false) {
                $result->state = false;
                $result->error = "Error in query condition or table name";
            } else {
                $result->state = true;
                $result->data  = $res;
            }
            return $result;
        }
    }

    public function execQuery($qry)
    {
        $result = new \stdClass();
        $result = $this->myDBcon->query($qry);
        return $result;
    }

    public function insertAll($insertSQL)
    {
        //echo "the var db is set " . isset($this->myDBcon);
        //$insertSQL = $this->myDBcon->real_escape_string($insertSQL);
        if ($this->myDBcon->query($insertSQL) === false) {
            return false;
        } else {
            return true;
        }
    }
    public function insertData($tableName, $fieldName, $fieldValues)
    {
        $returnObj  = new stdClass();
        $tableName  = $this->myDBcon->real_escape_string($tableName);
        $insertQry  = "insert into $tableName ";
        $fieldCount = count($fieldName);
        $valueCount = count($fieldValues);
        if ($fieldCount != $valueCount) {
            $returnObj->input = "Field Count : $fieldCount Field Value Count : $valueCount";
            $returnObj->state = false;
            return $returnObj;
        } else {
            $fieldList = "";
            for ($i = 0; $i < $fieldCount; $i++) {
                if ($i == 0) {
                    $fieldList = $fieldName[$i];
                } else {
                    $fieldList = $fieldList . ", " . $fieldName[$i];
                }
            }
            $valueList = "";
            for ($i = 0; $i < $fieldCount; $i++) {
                if ($i == 0) {
                    $valueList = "'" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                } else {
                    $valueList = $valueList . ", '" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                }
            }
            $insertQry        = "$insertQry ($fieldList) values($valueList)";
            $returnObj->input = $insertQry;
            if ($this->myDBcon->query($insertQry) === false) {
                $returnObj->error = $this->myDBcon->error;
                $returnObj->state = false;
            } else {
                $returnObj->insertId = $this->myDBcon->insert_id;
                $returnObj->state    = true;
            }
            return $returnObj;
        }
    }

    public function deleteData($tableName, $fieldName, $fieldValues)
    {
        $returnObj  = new stdClass();
        $tableName  = $this->myDBcon->real_escape_string($tableName);
        $deleteQry  = "delete from $tableName ";
        $fieldCount = count($fieldName);
        $valueCount = count($fieldValues);
        if ($fieldCount != $valueCount) {
            $returnObj->input = "Field Count : $fieldCount Field Value Count : $valueCount";
            $returnObj->state = false;
            return $returnObj;
        } else {
            $fieldList = "";
            for ($i = 0; $i < $fieldCount; $i++) {
                if ($i == 0) {
                    $fieldList = $fieldName[$i] . " = '" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                } else {
                    $fieldList = $fieldList . " and " . $fieldName[$i] . " = '" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                }
            }

            $deleteQry        = "$deleteQry  where $fieldList";
            $returnObj->input = $deleteQry;
            if ($this->myDBcon->query($deleteQry) === false) {
                $returnObj->error = $this->myDBcon->error;
                $returnObj->state = false;
            } else {
                //$returnObj->insertId = $this->myDBcon->insert_id;
                $returnObj->state = true;
            }
            return $returnObj;
        }
    }

    public function updateData($tableName, $updateQry, $fieldName, $fieldValues)
    {
        $returnObj  = new stdClass();
        $tableName  = $this->myDBcon->real_escape_string($tableName);
        $updateQry  = "update  $tableName set $updateQry ";
        $fieldCount = count($fieldName);
        $valueCount = count($fieldValues);
        if ($fieldCount != $valueCount) {
            $returnObj->input = "Field Count : $fieldCount Field Value Count : $valueCount";
            $returnObj->state = false;
            return $returnObj;
        } else {
            $fieldList = "";
            for ($i = 0; $i < $fieldCount; $i++) {
                if ($i == 0) {
                    $fieldList = $fieldName[$i] . " = '" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                } else {
                    $fieldList = $fieldList . " and " . $fieldName[$i] . " = '" . $this->myDBcon->real_escape_string($fieldValues[$i]) . "'";
                }
            }

            $updateQry        = "$updateQry  where $fieldList";
            $returnObj->input = $updateQry;
            if ($this->myDBcon->query($updateQry) === false) {
                $returnObj->error = $this->myDBcon->error;
                $returnObj->state = false;
            } else {
                //$returnObj->insertId = $this->myDBcon->insert_id;
                $returnObj->state = true;
            }
            return $returnObj;
        }
    }
}

/////////////////////////////
///       Test class.     ///
/////////////////////////////
// $con=new Connector();
// $tableName="q_user";
// $conditionArray=array("u_id='9'","email = 'totography.drive10@gmail.com'");
// $a1= $userRes = $con->displayCondition($tableName,$conditionArray);
print_r($a1);
/*$fld=array("id", "email", "mobile_no", "pass", "type");
$val = array("","test@tet","989876765","56544","2");
$res = $con->insertData("admin", $fld, $val);
print_r($res);*/
// print_r($con->countCondition("phase_grade",array("phase_id='1'")));
