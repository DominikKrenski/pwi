<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

echo "<div id=\"create-table-div\">
  <form id=\"create-table-form\" name=\"create-table-form\" action=\"createtable.php\" method=\"POST\">
    <table id=\"create-table\">
      <caption>Nazwa tabeli: <input type=\"text\" name=\"tableName\" onfocus=\"removeHighlight(event)\"></caption>
      <tr><th>Nazwa pola</th><th>Typ</th><th>Rozmiar</th><th>Wart. domyślna</th><th>Dozw. null</th><th>K G</th><th>A Z</th></tr>
      <tr>
        <td><input type=\"text\" name=\"fieldName[]\" onfocus=\"removeHighlight(event)\"></td>
        <td>
          <select name=\"dataType[]\">
            <option value=\"integer\">INTEGER</option>
            <option value=\"varchar\">VARCHAR</option>
            <option value=\"boolean\">BOOLEAN</option>
          </select>
        </td>
        <td><input type=\"text\" name=\"dataSize[]\" onfocus=\"removeHighlight(event)\"></td>
        <td><input type=\"text\" name=\"defaultValue[]\" onfocus=\"removeHighlight(event)\"></td>
        <td><input type=\"checkbox\" name=\"allowNull[]\" value=\"yes\"></td>
        <td><input type=\"checkbox\" name=\"primary[]\" value=\"yes\"></td>
        <td><input type=\"checkbox\" name=\"increment[]\" value=\"yes\"></td>
      </tr>
      <tr>
        <td><input type=\"text\" name=\"fieldName[]\" onfocus=\"removeHighlight(event)\"></td>
        <td>
          <select name=\"dataType[]\">
            <option value=\"integer\">INTEGER</option>
            <option value=\"varchar\">VARCHAR</option>
            <option value=\"boolean\">BOOLEAN</option>
          </select>
        </td>
        <td><input type=\"text\" name=\"dataSize[]\" onfocus=\"removeHighlight(event)\"></td>
        <td><input type=\"text\" name=\"defaultValue[]\" onfocus=\"removeHighlight(event)\"></td>
        <td><input type=\"checkbox\" name=\"allowNull[]\" value=\"yes\"></td>
        <td><input type=\"checkbox\" name=\"primary[]\" value=\"yes\"></td>
        <td><input type=\"checkbox\" name=\"increment[]\" value=\"yes\"></td>
      </tr>
      <tr>
        <td id=\"submit-row\" colspan=\"7\"><input type=\"submit\" value=\"Utwórz\" onclick=\"createTable(event)\"></td>
      </tr>
    </table>
  </form>
  <div id=\"add-remove-button\">
    <div id=\"add-remove-button-inner\">
      <button type=\"button\" id=\"add-button\" onclick=\"addRow()\">Dodaj wiersz</button>
      <button type=\"button\" id=\"remove-button\" onclick=\"removeRow()\">Usuń wiersz</button>
    </div>
  </div>
</div>";
