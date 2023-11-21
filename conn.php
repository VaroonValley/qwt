<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$database = 'qwebit_iot_a';
$port = 3306;
$socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$connection = mysqli_connect($host, $user, $pass, $database, $port, $socket);

if (mysqli_connect_errno()) {
    echo '' . mysqli_connect_error();
}
