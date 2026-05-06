<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "finance_app";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>