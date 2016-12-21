<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $postData = [];
  $oldPostData = [];
  $tmpArray = [];
  $tableName = $_SESSION['updateRowTableName'];
  unset($_SESSION['updateRowTableName']);

  foreach ($_POST as $key => $value) {
    $tmpArray[$key] = sanitizeData($value);
  }

  /* Dane przesyłane są w formacie tableName=nazwa field=value field1=value ...
   * ofield = value ofield1 = value ...
   * Najpierw pobierana i usuwana jest nazwa tablicy.
   * Następnie pobierane są kolejne dane, w przypadku indeksów zaczynających się
   * od 'o' (dane znajdujące się początkowo w formularzu aktualizacji), jest ono usuwane
  */
  $centerIndex = count($tmpArray) / 2;
  $counter = 0;

  foreach ($tmpArray as $key => $value) {
    if ($counter < $centerIndex) {
      $postData[$key] = $value;
    }
    else {
      $oldPostData[$key] = $value;
    }
    $counter++;
  }

  /* Przygotowanie zapytania */
  $queryString = "UPDATE $tableName SET ";

  foreach ($postData as $postKey => $postValue) {
    $queryString .= "$postKey= ?, ";
  }
  $queryString = rtrim($queryString);
  $queryString = rtrim($queryString, ',');

  $queryString .= " WHERE";

  foreach ($postData as $key => $value) {
    $queryString .= " $key= ? AND";
  }

  $queryString = substr($queryString, 0, strlen($queryString) - 3);
  $queryString = trim($queryString);

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare($queryString);

    $tmpArray = array_merge($postData, $oldPostData);
    $bla = [];
    foreach ($tmpArray as $key => $value) {
      $bla[] = $value;
    }
    $stmt->execute($bla);

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
