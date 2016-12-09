<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";


if (isset($_GET['name'])) {
  $tableName = $_GET['name'];

  // Sprawdzenie, czy przekazana nazwa tabeli zawiera jedynie dozwolone znaki
  if (preg_match('/[^A-Za-z0-9_.]/', $tableName)) {
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

    /* Zmienna przechowująca łańcuch znaków potrzebny do wygenerowania tabeli */
    $result = [];

    /* Zmienna przechowująca wartość pola w kolumnie 'Field' */
    $fieldValue = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[] = $row;
    }

    /* Pobranie nazw kolumn */
    $columnNames = array_keys($result[0]);
    $structureTable = "<table id=\"structure-table\"><caption>" . $langArray['structureTableHeading'] . " '$tableName'</caption><tr>";

    for ($i = 0; $i < count($columnNames); $i++) {
      $structureTable .= "<th>$columnNames[$i]</th>";
    }

    $structureTable .= '</tr>';

    /* Wypełnienie wierszy tabeli */
    foreach ($result as $key => $table) {
      $structureTable .= '<tr>';
      foreach ($table as $key => $value) {
        if ($key == 'Field') {
          $fieldValue = $value;
        }
        if ($key == "Extra") {
          $structureTable .= "<td><a href=\"removetablecolumn.php?table=$tableName&column=$fieldValue\" onclick=\"dropColumnFromTable(event)\">" .$langArray['drop'] . "</a></td>";
        }
        else {
          $structureTable .= "<td>$value</td>";
        }
      }
      $structureTable .= '</tr>';
    }

    $structureTable .= "</table>";
    echo $structureTable;

  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();
    echo "<h2>$message</h2>";
  }
}
