<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if (isset($_GET['page'])) {

  $counter = 0;
  $tableKeys = [];
  $resultTable = "<div id=\"result-div\"><table id=\"result-table\"><tr>";
  $tmpTable = "";

  $pageNumber = intval(sanitizeData($_GET['page']));
  $rowsPerPage = $_SESSION['rowsPerPage'];
  $numberOfRows = $_SESSION['numberOfRows'];
  $numberOfPages = $_SESSION['numberOfPages'];
  $startingPoint = ($pageNumber - 1) * $rowsPerPage;

  $query = $_SESSION['customQuery'] . " limit $startingPoint, $rowsPerPage;";

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_NUM
    ]);

    $result = $connection->query($query, PDO::FETCH_ASSOC);

    foreach ($result as $inner) {
      if ($counter == 0) {
        $tableKeys = array_keys($inner);
      }
      $tmpTable .= '<tr>';

      foreach ($inner as $key => $value) {
        $tmpTable .= "<td>$value</td>";
      }

      $tmpTable .= "</tr>";
      $counter++;
    }

    foreach ($tableKeys as $value) {
      $resultTable .= "<th>$value</th>";
    }

    $resultTable .= "</tr>";
    $resultTable .= $tmpTable;
    $resultTable .= "</table>";

    echo $resultTable;

    $navigation = '<ul id="navigation">';

    for ($i = 1; $i <= $numberOfPages; $i++) {
      $navigation .= "<li><a href=\"pagination.php?page=$i\" onclick=\"handlePagination(event)\">$i</li>";
    }

    $navigation .= '</ul>';

    echo $navigation;
  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();

    echo "<div class=\"connection-error\">
            <div class=\"connection-error-header\">
              <h2>". $langArray['errorHeader'] . "</h2>
            </div>
            <div class=\"connection-error-content\">
              <p>$message</p>
            </div>
          </div>";
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
