<?php

// connect to the database
$dsn = 'mysql:host=localhost;dbname=Techshop';
$user = 'root';
$pass = '';
$options = array (
  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);

try {
  $db = new PDO($dsn, $user, $pass, $options);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
}
  catch(PDOException $e) {
  echo $e;
}

?>