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
  if (!isset($postData['autoIncrement'])) {
    $queryString = "INSERT INTO $tableName (";
    $counter = 0;

    foreach ($postData as $key => $value) {
      if ($value != "") {
        $queryString .= "$key,";
        $counter++;
      }
    }
    $queryString = rtrim($queryString, ',');
    $queryString .= ") VALUES (";

    for ($i = 0; $i < $counter; $i++) {
      $queryString .= "?,";
    }

    $queryString = rtrim($queryString, ',');
    $queryString .= ");";
    /*foreach ($postData as $key => $value) {
      $queryString .= "?,";
    }

    $queryString = rtrim($queryString, ',');
    $queryString .= ');';*/
  }
  else {
    $increment = array_pop($postData);
    $queryString = "INSERT INTO $tableName(";
    $counter = 0;

    foreach ($postData as $key => $value) {
      if (($increment !== $key) && $value != "") {
        $queryString .= "$key,";
        $counter++;
      }
    }
    $queryString = rtrim($queryString, ',');
    $queryString .= ') VALUES(';

    for ($i = 0; $i < $counter; $i++) {
      $queryString .= "?,";
    }

    $queryString = rtrim($queryString, ',');
    $queryString .= ');';
  }

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare($queryString);

    $tmpArray = [];

    if (!isset($increment)) {
      foreach($postData as $key => $value) {
        if ($value != "") {
          $tmpArray[] = $value;
        }
      }
    }
    else {
      foreach ($postData as $key => $value) {
        if (($key !== $increment) && $value != "") {
          $tmpArray[] = $value;
        }
      }
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
