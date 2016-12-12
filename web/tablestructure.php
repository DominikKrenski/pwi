<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";


if (isset($_GET['table']) && isset($_GET['column'])) {
  removeTableColumn($_GET['table'], $_GET['column'], $langArray);
}


if (isset($_GET['name'])) {
  showTableStructure($_GET['name'], $langArray);
}

if (isset($_GET['dropTable'])) {
  dropTable($_GET['dropTable']);
}

function dropTable($tableName)
{
  /* Sprawdzenie czy nazwa nie zawiera niedozwolonych znaków */
  if (preg_match('/[^A-Za-z0-9_.]/', $tableName)) {
    echo "<h2>Nazwa tabeli zawiera niedozwolone znaki</h2>";
    die();
  }

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection ->prepare("DROP TABLE $tableName");
    $stmt->execute();
    echo "OK";
  }
  catch (PDOException $ex) {
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

function removeTableColumn($tableName, $columnName, $langArray)
{
  /* Sprawdzenie czy nazwy nie zawierają niedozwolonych znaków */
  if (preg_match('/[^A-Za-z0-9_.]/', $tableName) || preg_match('/[^A-Za-z0-9_.]/', $columnName)) {
    echo "<h2>Nazwa tabeli lub kolumny zawiera niedozwolone znaki</h2>";
    die();
  }

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare("ALTER TABLE $tableName DROP COLUMN $columnName");
    $stmt->execute();

    showTableStructure($tableName, $langArray);
  }
  catch (PDOException $ex) {
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


function showTableStructure($tableName, $langArray)
{
  /* Sprawdzenie, czy przekazana nazwa tabeli zawiera jedynie dozwolone znaki */
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

    /* Tablica przechowująca wynik zapytania */
    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[] = $row;
    }

    createStructureTable($result, $tableName, $langArray);
  }
  catch (PDOException $ex) {
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

function createStructureTable($result, $tableName, $langArray)
{

  /* Zmienna przechowująca wartość pola w kolumnie 'Field' */
  $fieldValue = '';

  /* Pobranie nazw kolumn */
  $columnNames = array_keys($result[0]);
  $structureTable = "<table id=\"structure-table\"><caption>" . $langArray['structureTableHeading'] . " '$tableName'</caption><tr>";

  for ($i = 0; $i < count($columnNames); $i++) {
    $structureTable .= "<th>$columnNames[$i]</th>";
  }

  $structureTable .= '</tr>';

  /* Wypełnienie wierszy tabeli */
  foreach($result as $key => $table) {
    $structureTable .= '<tr>';
    foreach($table as $key => $value) {
      if ($key == 'Field') {
        $fieldValue = $value;
      }
      if ($key == "Extra") {
        $structureTable .= "<td><a href=\"tablestructure.php?table=$tableName&column=$fieldValue\" onclick=\"dropColumnFromTable(event)\">" . $langArray['drop'] . "</a></td>";
      }
      else {
        $structureTable .= "<td>$value</td>";
      }
    }
    $structureTable .= '</tr>';
  }

  $structureTable .= "<tr><td colspan=\"6\"><a href=\"tablestructure.php?dropTable=$tableName\" onclick=\"dropTable(event)\">Usuń tabelę</a></td></tr>";
  $structureTable .= "</table>";
  echo $structureTable;

}
