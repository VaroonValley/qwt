<?php

$host = 'localhost';
$user = 'root';
$pass = 'root';
$database = 'qwebit_iot_a';
$port = 3306;
$socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$connection = mysqli_connect($host, $user, $pass, $database, $port);

if (mysqli_connect_errno()) {
    echo '' . mysqli_connect_error();
}
