<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $postData = [];
  $tableName = $_SESSION['updateRowTableName'];
  unset($_SESSION['updateRowTableName']);

  foreach ($_POST as $key => $value) {
    $postData[$key] = sanitizeData($value);
  }

  /* Przygotowanie zapytania */
  $queryString = "DELETE FROM $tableName WHERE ";

  foreach ($postData as $key => $value) {
    $queryString .= "$key = ? AND ";
  }

  $queryString = rtrim($queryString);
  $queryString = substr($queryString, 0, strlen($queryString) - 3);

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare($queryString);

    $tmpArray = [];

    foreach ($postData as $key => $value) {
      $tmpArray[] = $value;
    }

    $stmt->execute($tmpArray);

    $connection = null;
  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();
    echo $message;
  }
}

function sanitizeData($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = strip_tags($data);
  $data = htmlentities($data);
  return $data;
}
