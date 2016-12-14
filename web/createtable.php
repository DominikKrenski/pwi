<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if (isset($_POST['tableName']) && isset($_POST['fieldName']) && isset($_POST['dataType']) && isset($_POST['dataSize']) && isset($_POST['defaultValue']) &&
    isset($_POST['allowNull']) && isset($_POST['primary']) && isset($_POST['increment']))
{
  $tableName = $_POST['tableName'];
  $fieldName = $_POST['fieldName'];
  $dataType = $_POST['dataType'];
  $dataSize = $_POST['dataSize'];
  $defaultValue = $_POST['defaultValue'];
  $allowNull = $_POST['allowNull'];
  $primary = $_POST['primary'];
  $increment = $_POST['increment'];

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = "CREATE TABLE $tableName (";

    for ($i = 0; $i < count($dataType); $i++) {
      $statement .= $fieldName[$i] . " " . $dataType[$i] . " ";

      if (!empty($dataSize[$i])) {
        $statement .= "(" . $dataSize[$i] . ") ";
      }

      if (!empty($defaultValue[$i]) && $dataType[$i] == "varchar") {
        $statement .= "default " . "\"$defaultValue[$i]\"" . " ";
      }
      elseif (!empty($defaultValue[$i])) {
        $statement .= "default " . $defaultValue[$i] . " ";
      }

      if ($allowNull[$i] == "no") {
        $statement .= "NOT NULL ";
      }
      if ($primary[$i] == "yes") {
        $statement .= "PRIMARY KEY ";
      }
      if ($increment[$i] == "yes") {
        $statement .= "AUTO_INCREMENT ";
      }
      $statement .= ",";
    }

    $statement = rtrim($statement, ",");
    $statement .= ");";

    $stmt = $connection->prepare($statement);
    $stmt->execute();
    echo "OK";

  } catch (PDOException $ex) {
    $message = $ex->getMessage();
      echo "<div class=\"connection-error\">
      <div class=\"connection-error-header\">
      <h2>" . $langArray['errorHeader'] . "</h2>
      </div>
      <div class=\"connection-error-content\">
      <p>$message</p>
      </div>
      </div>";
  }
}

if (isset($_GET['errorString'])) {
  $errorString = $_GET['errorString'];
  $errorStrings = explode("$", $errorString);

  switch ($lang)
  {
    case "pl":
    $errorMessage = $errorStrings[0];
    break;
    case "en":
    $errorMessage = $errorStrings[1];
    break;
    default:
    $errorMessage = $errorStrings[0];
  }

  if (!preg_match('/^<ul>(<li>[^<>\/]+<\/li>)+<\/ul>$/', $errorMessage)) {
    echo "Niedozwolone znaki";
    die();
  }
  echo $errorMessage;
}

/*if (isset($_GET['errorStringPL'])) {
  $errorString = $_GET['errorStringPL'];
  if (!preg_match('/^<ul>(<li>[^<>\/]+<\/li>)+<\/ul>$/', $errorString)) {
    echo "Niedozwolone znaki";
    die();
  }
  echo $errorString;
}*/
