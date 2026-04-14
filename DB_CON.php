<?php
$DB_HOST = 'localhost';
$DB_USER = 'u707770306_adomi';
$DB_PASSWORD = "9nSExs>X";

$DB_NAME = "u707770306_dantra";

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if (!$con) {
    echo "<script>alert('NO CONNECTION')</script>";
    die();
}

?>