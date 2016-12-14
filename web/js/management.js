function showTableStructure(event)
{
  event.preventDefault();

  var target = event.target;
  var tableName = target.text;

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", "tablestructure.php?name=" + tableName, true);

  ajaxRequest.onreadystatechange = function () {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }
  }

  ajaxRequest.send(null);
}

function dropColumnFromTable(event)
{
  event.preventDefault();

  var target = event.target;
  var href = target.href;

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }
  }

  ajaxRequest.send(null);
}

function dropTable(event)
{
  event.preventDefault();
  event.stopPropagation();

  var target = event.target;
  var href = target.href;
  var tableName = href.substring(53);

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert ("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {
          if (this.responseText == "OK") {
            var tableList = document.getElementById('table-list');
            var ulList = document.querySelectorAll('#table-list li');
            for (var i = 0; i < ulList.length; i++) {
              if ((ulList[i].innerText == tableName) || (ulList[i].textContent == tableName)) {
                tableList.removeChild(ulList[i]);
                break;
              }
            }
            var parent = document.getElementById('main-content');
            var child = document.getElementById('structure-table');
            parent.removeChild(child);
          }
        }
      }
    }
  }
  ajaxRequest.send(null);
}

function createTable(event)
{
  event.preventDefault();

  var form = document.forms[0];

  var tableName = form.elements['tableName'];
  var fieldName = form.elements['fieldName[]'];
  var dataType = form.elements['dataType[]'];
  var dataSize = form.elements['dataSize[]'];
  var defaultValue = form.elements['defaultValue[]'];
  var allowNull = form.elements['allowNull[]'];
  var primary = form.elements['primary[]'];
  var increment = form.elements['increment[]'];

  var queryString = "tableName=" + tableName.value + "&";

  for (var i = 0; i < fieldName.length; i++) {
    queryString += "fieldName[]=" + fieldName[i].value + "&";
  }

  for (var i = 0; i < dataType.length; i++) {
    queryString += "dataType[]=" + dataType[i].value + "&";
  }

  for (var i = 0; i < dataType.length; i++) {
    queryString += "dataSize[]=" + dataSize[i].value + "&";
  }

  for (var i = 0; i < defaultValue.length; i++) {
    queryString += "defaultValue[]=" + defaultValue[i].value + "&";
  }

  for (var i = 0; i < allowNull.length; i++) {
    if (!allowNull[i].checked) {
      allowNull[i].value = "no";
    }
    queryString += "allowNull[]=" + allowNull[i].value + "&";
  }

  for (var i = 0; i < primary.length; i++) {
    if (!primary[i].checked) {
      primary[i].value = "no";
    }
    queryString += "primary[]=" + primary[i].value + "&";
  }

  for (var i = 0; i < increment.length; i++) {
    if (!increment[i].checked) {
      increment[i].value = "no";
    }
    queryString += "increment[]=" + increment[i].value + "&";
  }

  var ajaxRequest = createAjaxRequest();
  if (ajaxRequest === false) {
    alert ("Przeglądarka nie wspiera technologii Ajax");
  }

  var errorStringPL = validateTableForm(tableName, fieldName, dataType, dataSize, defaultValue);
  if (errorStringPL.length == 9) {
    ajaxRequest.open("POST", "createtable.php", true);
    ajaxRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    ajaxRequest.onreadystatechange = function() {
      addLinkTable(this, tableName);
    }
    ajaxRequest.send(queryString);
  }
  else {
    ajaxRequest.open("GET", "createtable.php?errorStringPL=" + errorStringPL);
    ajaxRequest.onreadystatechange = function() {
      if (this.readyState == 4) {
        if (this.status == 200) {
          if (this.responseText != null) {
            if (document.getElementById('error-message')) {
              document.getElementById('error-message').innerHTML = this.responseText;
            }
            else {
              var errorDiv = document.createElement("div");
              errorDiv.setAttribute("id", "error-message");
              errorDiv.className = "connection-error";
              errorDiv.innerHTML = this.responseText;
              var createForm = document.getElementById("create-table-div");
              var mainContent = document.getElementById("main-content");
              mainContent.insertBefore(errorDiv, createForm);
            }
          }
        }
      }
    }
  }
  ajaxRequest.send(null);
}

function addLinkTable(that, tableName)
{
  if (that.readyState == 4) {
    if (that.status == 200) {
      if (that.responseText != null) {
        if (that.responseText == "OK") {
          var tableList = document.getElementById('table-list');
          var li = document.createElement('li');
          var link = document.createElement('a');
          link.innerHTML = tableName.value;
          link.setAttribute('href', '#');
          link.setAttribute('onclick', 'showTableStructure(event)');
          li.appendChild(link);
          tableList.appendChild(li);
          document.getElementById('main-content').innerHTML = "";
        }
        else {
          document.getElementById('main-content').innerHTML = that.responseText;
        }
      }
    }
  }
}

function showCreateTableForm(event)
{
  event.preventDefault();

  var target = event.target;
  var href = target.href;

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }
  }
  ajaxRequest.send(null);
}

function validateTableForm(tableName, fieldName, dataType, dataSize, defaultValue)
{
  var errorFlag = false;
  var errorStringPL = "<ul>";

  /*Sprawdzenie czy pole tableName jest puste.
   * Jeśli tak wyświetlenie informacji o błędzie.
   * Jeśli nie, sprawdzenie czy nazwa tabeli nie zawiera niedozwolonych znaków,
   * jeśli zawiera -> wyświetlenie informacji o błędzie
  */
  if (checkIfFieldIsEmpty(tableName.value)) {
    highlightErrorField(tableName);
    errorStringPL += "<li>Nazwa tabeli nie może być pusta</li>";
    errorFlag = true;
  }
  else {
    if (checkIfContainsIllegalCharacters(tableName.value)) {
      highlightErrorField(tableName);
      errorStringPL += "<li>Nazwa tabeli może zawierać jedynie znaki [A-Z a-z . _]</li>";
      errorFlag = true;
    }
  }

  /* Sprawdzenie, czy poszczególne nazwy pól nie są puste
   * oraz czy nie zawierają niedozwolonych znaków
   * Jeśli tak -> wyświetlenie informacji o błędzie.
  */
  for (var i = 0; i < fieldName.length; i++) {
    if (checkIfFieldIsEmpty(fieldName[i].value)) {
      highlightErrorField(fieldName[i]);
      errorStringPL += "<li>Nazwa pola nie może być pusta</li>";
      errorFlag = true;
    }
    else {
      if (checkIfContainsIllegalCharacters(fieldName[i].value)) {
        highlightErrorField(fieldName[i]);
        errorStringPL += "<li>Nazwa pola może zawierać jedynie znaki [A-Z a-z . _]</li>";
      }
    }
  }

  /* Sprawdzenie czy pola dataSize zawierają jedynie cyfry.
   * Jeśli nie -> wyświetlenie informacji o błędzie.
  */
  for (var i = 0; i < dataSize.length; i++) {
    if (!checkIfFieldIsEmpty(dataSize[i].value)) {
      if (!checkIfNumber(dataSize[i].value)) {
        highlightErrorField(dataSize[i]);
        errorStringPL += "<li>Rozmiar danych musi być liczbą</li>"
        errorFlag = true;
      }
    }
  }

  /* Sprawdzenie, czy jeśli użytkownik wybrał typ danych VARCHAR
   * została wprowadzona długość pola.
   * Jeśli nie -> wyświetlenie informacji o błędzie
   * Oraz, czy jeśli użytkownik wybrał typ danych BOOLEAN
   * pole dataSize jest puste.
   * Jeśli nie -> wyświetlenie informacji o błędzie.
  */
  for (var i = 0; i < dataSize.length; i++) {
    if (dataType[i].value == "varchar") {
      if (checkIfFieldIsEmpty(dataSize[i].value)) {
        highlightErrorField(dataSize[i]);
        errorStringPL += "<li>Nie podano rozmiaru dla pola typu VARCHAR</li>";
        errorFlag = true;
      }
    }
    if (dataType[i].value == "boolean") {
      if (!checkIfFieldIsEmpty(dataSize[i].value)) {
        errorStringPL += "<li>Nie można ustawić długości zmiennej typu BOOLEAN</li>"
        highlightErrorField(dataSize[i]);
        errorFlag = true;
      }
    }
  }

  /* Sprawdzenie, czy jeśli użytkownik wybrał typ danych INTEGER
   * wartość domyślna zawiera jedynie cyfry.
   * Jeśli nie -> wyświetlenie informacji o błędzie
   * Oraz, czy jeśli użytkownik wybrał typ danych BOOLEAN
   * wartość domyślna przyjmuje jedną z wartości: 0, 1, true, false
   * Jeśli nie -> wyświetlenie informacji o błędzie
  */
  for (var i = 0; i < defaultValue.length; i++) {
    if (dataType[i].value == "integer" && !checkIfFieldIsEmpty(defaultValue[i].value)) {
      if (!checkIfNumber(defaultValue[i].value)) {
        errorStringPL += "<li>Wartością domyślną dla typu INTEGER może być tylko liczba całkowita</li>";
        highlightErrorField(defaultValue[i]);
        errorFlag = true;
      }
    }

    if (dataType[i].value == "boolean") {
      if (!checkIfFieldIsEmpty(defaultValue[i].value)) {
        if (defaultValue[i].value != "1" && defaultValue[i].value != "0" && defaultValue[i].value != "true" && defaultValue[i].value != "false") {
          highlightErrorField(defaultValue[i]);
          errorStringPL += "<li>Dozwolonymi wartościami dla typu BOOLEAN są: true, false, 1 lub 0</li>";
          errorFlag = true;
        }
      }
    }
  }
  errorStringPL += "</ul>";
  return errorStringPL;
}

function checkIfContainsIllegalCharacters(value)
{
  if (/[^A-Za-z0-9_.ĘęÓóĄąŚśŁłŻżŹźĆćŃń]/.test(value)) {
    return true;
  }
  return false;
}

function checkIfNumber(value)
{
  if (/^\d+$/.test(value)) {
    return true;
  }
  else {
    return false;
  }
}

function checkIfFieldIsEmpty(value)
{
  value = "" + value;
  value = value.replace(/ /g, "");
  if (value == "" || value.length == 0) {
    return true;
  } else {
    return false;
  }
}

function highlightErrorField(field)
{
  field.className = 'field-required-error';
}

function removeHighlight(event)
{
  var element = event.target;
  element.className = "";
}

function addRow()
{
  var table = document.getElementById('create-table');
  var index = table.rows.length - 1;
  var row = table.insertRow(index);
  var cells = [];

  for (var i = 0; i < 7; i++) {
    cells[i] = row.insertCell(i);
  }

  cells[0].innerHTML = '<input type="text" name="fieldName[]" onfocus="removeHighlight(event)">';
  cells[1].innerHTML = '<select name="dataType[]"> <option value="integer">INTEGER</option><option value="varchar">VARCHAR</option><option value="boolean">BOOLEAN</option></select>';
  cells[2].innerHTML = '<input type="text" name="dataSize[]" onfocus="removeHighlight(event)">';
  cells[3].innerHTML = '<input type="text" name="defaultValue[]" onfocus="removeHighlight(event)">';
  cells[4].innerHTML = '<input type="checkbox" name="allowNull[]" value="yes">';
  cells[5].innerHTML = '<input type="checkbox" name="primary[]" value="yes">';
  cells[6].innerHTML = '<input type="checkbox" name="increment[]" value="yes">';
}

function removeRow()
{
  var table = document.getElementById('create-table');
  var index = table.rows.length - 2;

  if (index > 2) {
    table.deleteRow(index);
  }
}

function createAjaxRequest()
{
  try {
    var request = new XMLHttpRequest();
  }
  catch (ex) {
    request = false;
  }
  return request;
}
