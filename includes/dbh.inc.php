<?php

$dsn = "mysql:host=localhost;dbname=schulbibliothek";
$dbusername = "root";
$dbpassword = "";

try{
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // in case of an error this makes an exception so we can grab it in handle it in catch
} catch(PDOException $e){

    echo "Connection failed: " . $e->getMessage();
    die();

}
