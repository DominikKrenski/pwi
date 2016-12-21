<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if (isset($_GET['table'])) {
  $tableName = $_GET['table'];

  /* Sprawdzenie czy nazwa nie zawiera niedozwolonych znaków */
  if (preg_match('/[^A-Za-z0-9_.ĘęÓóĄąŚśŁłŻżŹźĆćŃń]/', $tableName)) {
    echo "<h2>Nazwa tabeli zawiera niedozwolone znaki</h2>";
    die();
  }

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare("SHOW COLUMNS FROM $tableName");
    $stmt->execute();

    /* Tablica przechowująca wynik zapytania o nazwy kolumn */
    $columnNames = [];

    /* Tablica przechowująca wszystkie wartości przechowywane w tabeli */
    $content = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $columnNames[] = $row;
    }

    $stmt = $connection->prepare("SELECT * FROM $tableName");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $content[] = $row;
    }

    $connection = null;

    createContentTable($tableName, $columnNames, $content, $lang, $langArray);
  }
  catch (PDOException $ex) {
    echo "BŁĄD";
  }
}

function createContentTable($tableName, $columnNames, $content, $lang, $langArray)
{
  if (count($content) > 0) {
    /* Tablica przechowująca nazwy pól wraz z typem pola w formacie nazwaPola => typPola */
    $temporaryArray = [];

    /* Tablica przechowująca nazwy pól wraz z zawartością pola EXTRA w formacie nazwaPola => EXTRA */
    $extraArray = [];

    foreach ($columnNames as $outerArray) {
      $pos = strpos($outerArray['Type'], '(');
      $temporaryArray[$outerArray['Field']] = substr($outerArray['Type'], 0, $pos);
    }

    foreach ($columnNames as $outerArray) {
      $extraArray[$outerArray['Field']] = $outerArray['Extra'];
    }

    $contentTable = "<table id=\"content-table\"><caption>". $langArray['contentTable'] ." '$tableName'</caption><tr>";

    for ($i = 0; $i < count($columnNames); $i++) {
      $contentTable .= "<th>".$columnNames[$i]['Field']."</th>";
    }
    $contentTable .= "<th>". $langArray['modify'] ."</th></tr>";

    foreach ($content as $outerTable) {
      $contentTable .= "<tr>";
      foreach ($outerTable as $key => $value) {
        if ($temporaryArray[$key] == 'tinyint') {
          if ($value == 1) {
            $value = "true";
          }
          else {
            $value = "false";
          }
        }
        $contentTable .= "<td>$value</td>";
      }
      $dataTypes = json_encode($temporaryArray);
      $primaryKey = json_encode($extraArray);
      $languages = json_encode($langArray);
      $contentTable .= '<td id="content-table-last-cell"><a href="edittablecontent.php" onclick="editTableContent(event,'.htmlentities($dataTypes). ',' .htmlentities($primaryKey). ','. htmlentities($languages) .')">'. $langArray['edit'] .'</a><a href="removerow.php" onclick="removeRow(event)">'. $langArray['remove'] .'</a></td>';
      $contentTable .= "</tr>";
    }
    $_SESSION['updateRowTableName'] = $tableName;
    $contentTable .= "</table>";
    $contentTable .= '<button style="position: relative; left: 50%; " onclick="addEntryForm(event,'. htmlentities($dataTypes). ',' . htmlentities($primaryKey). ','. htmlentities($languages) .')">'. $langArray['add'] .'</button>';
    echo $contentTable;
  }
  else {
    /* Tablica przechowująca nazwy pól wraz z typem pola w formacie nazwaPola => typPola */
    $temporaryArray = [];

    /* Tablica przechowująca nazwy pól wraz z zawartością pola EXTRA w formacie nazwaPola => EXTRA */
    $extraArray = [];

    foreach ($columnNames as $outerArray) {
      $pos = strpos($outerArray['Type'], '(');
      $temporaryArray[$outerArray['Field']] = substr($outerArray['Type'], 0, $pos);
    }

    foreach ($columnNames as $outerArray) {
      $extraArray[$outerArray['Field']] = $outerArray['Extra'];
    }

    $contentTable = "<table id=\"content-table\"><caption>Zawartość tabeli '$tableName'</caption><tr>";

    for ($i = 0; $i < count($columnNames); $i++) {
      $contentTable .= "<th>".$columnNames[$i]['Field']."</th>";
    }
    $contentTable .= "<th>Modyfikacja</th></tr>";
    $contentTable .= "</table>";

    $dataTypes = json_encode($temporaryArray);
    $primaryKey = json_encode($extraArray);
    $languages = json_encode($langArray);
    $_SESSION['updateRowTableName'] = $tableName;

    $contentTable .= '<button style="position: relative; left: 50%; " onclick="addEntryForm(event,'. htmlentities($dataTypes) . ', ' . htmlentities($primaryKey) . ',' . htmlentities($languages) .')">Dodaj wpis</button>';
    echo $contentTable;
  }
}
