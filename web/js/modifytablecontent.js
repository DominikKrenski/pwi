function showTableContent(event)
{
  event.preventDefault();

  var target = event.target;
  var href = target.href;
  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert ("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText != null) {
        document.getElementById('main-content').innerHTML = this.responseText;
      }
    }
  }
  ajaxRequest.send(null);
}

function editTableContent(event, dataTypes, primaryKey)
{
  event.preventDefault();
  var link = event.target;
  var tableRow = link.parentNode.parentNode;

  var oldData = {};

  /* Wstawienie formularza edycji elementu tabeli */
  if (!document.getElementById('modify-table-content-div')) {
    var contentTable = document.getElementById('content-table');
    var mainContent = document.getElementById('main-content');
    var formDiv = document.createElement('div');
    var formDivHeader = document.createElement('div');
    var formHeaderText = document.createElement('p');
    var formHeaderTextContent = document.createTextNode("Edytuj wpis:");
    var form = document.createElement('form');

    formDivHeader.setAttribute('id', 'modify-table-content-header');
    formDiv.setAttribute('id', 'modify-table-content-div');
    form.setAttribute('id', 'modify-content-form');
    form.setAttribute('action', link.href);
    form.setAttribute('method', 'POST');

    for (var i = 0; i < tableRow.cells.length - 1; i++) {
      var p = document.createElement('p');
      p.innerHTML = contentTable.rows[0].cells[i].innerHTML;
      form.appendChild(p);

      if (dataTypes[contentTable.rows[0].cells[i].innerHTML] == "tinyint") {
        var select = document.createElement('select');
        select.setAttribute('id', contentTable.rows[0].cells[i].innerHTML);
        var option1 = document.createElement('option');
        var option2 = document.createElement('option');

        option1.setAttribute('value', '1');
        option1.text = 'true';

        option2.setAttribute('value', '0');
        option2.text = 'false';

        if (tableRow.cells[i].innerHTML == 'true') {
          option1.selected = true;
        }
        else {
          option2.selected = true;
        }

        select.appendChild(option1);
        select.appendChild(option2);
        form.appendChild(select);
      }
      else {
        var input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('value', tableRow.cells[i].innerHTML);
        input.setAttribute('id', contentTable.rows[0].cells[i].innerHTML);

        if (primaryKey[contentTable.rows[0].cells[i].innerHTML] == 'auto_increment') {
          input.disabled = true;
          input.style.backgroundColor = 'rgb(122, 166, 209)';
        }

        form.appendChild(input);
      }
    }

    var updateButton = document.createElement('input');
    updateButton.setAttribute('type', 'submit');
    updateButton.setAttribute('value', 'Zapisz');
    updateButton.setAttribute('onclick', 'updateTableRow(event,' + JSON.stringify(dataTypes) + ', ' + JSON.stringify(primaryKey) + ')');

    form.appendChild(updateButton);

    formHeaderText.appendChild(formHeaderTextContent);
    formDivHeader.appendChild(formHeaderText);
    formDiv.appendChild(formDivHeader);
    formDiv.appendChild(form);
    mainContent.insertBefore(formDiv, contentTable);

    for (var i = 0; i < tableRow.cells.length - 1; i++) {
      oldData[contentTable.rows[0].cells[i].innerHTML] = form.elements[i].value;
    }

    /* Wykorzystanie localStorage do przechowania aktualnych wartości w bazie danych */
    localStorage.setItem('oldData', JSON.stringify(oldData));
  }
  else {
    var form = document.getElementById('modify-content-form');

    var errorMessages = document.getElementsByClassName('field-required-error');

    if (errorMessages.length > 0) {
      for (var i = 0; i < errorMessages.length; i++) {
        errorMessages[i].style.display = "none";
      }
    }

    for (var i = 0; i < tableRow.cells.length - 1; i++) {
      if (form.elements[i].type == 'select-one') {
        if (form.elements[i][0].text == tableRow.cells[i].innerHTML) {
          form.elements[i][0].selected = true;
        }
        else {
          form.elements[i][1].selected = true;
        }
      }
      else {
        form.elements[i].value = tableRow.cells[i].innerHTML;
      }
    }
    for (var i = 0; i < tableRow.cells.length - 1; i++) {
      oldData[contentTable.rows[0].cells[i].innerHTML] = form.elements[i].value;
    }

    /* Tutaj ponowne wykorzystanie localStorage */
    localStorage.setItem('oldData', JSON.stringify(oldData));
  }
}

function updateTableRow(event, dataTypes, primaryKey)
{
  event.preventDefault();
  event.stopPropagation();

  var errorFlag = validateUpdateTableContent(dataTypes, primaryKey);

  if (!errorFlag) {
    var ajaxRequest = createAjaxRequest();

    if (ajaxRequest == false) {
      alert("Przeglądarka nie wspiera technologii Ajax");
    }

    var form = document.forms[0];

    ajaxRequest.open("POST", "updaterow.php", true);

    ajaxRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }
    var data = new FormData();

    for (var i = 0; i < form.elements.length - 1; i++) {
      data.append(form.elements[i].id, form.elements[i].value);
    }

    /* Pobranie danych z localStorage i dołączenie ich do obiektu FormData */
    var oldData = JSON.parse(localStorage.getItem('oldData'));
    localStorage.removeItem('oldData');

    for (var key in oldData) {
      if(oldData.hasOwnProperty(key)) {
        data.append("o"+key, oldData[key]);
      }
    }

    ajaxRequest.send(data);
  }
}


function removeRow(event)
{
  event.preventDefault();
  event.stopPropagation();

  var data = new FormData();
  var contentTable = document.getElementById('content-table');
  var link = event.target;
  var tableRow = link.parentNode.parentNode;

  for (var i = 0; i < tableRow.cells.length - 1; i++) {
    var key = contentTable.rows[0].cells[i].innerHTML;
    var value = tableRow.cells[i].innerHTML;

    if (value == 'true') {
      data.append(key, 1);
    }
    else if (value == 'false') {
      data.append(key, 0);
    }
    else {
      data.append(key, value);
    }
  }

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest == false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("POST", link.href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText != null) {
        document.getElementById('main-content').innerHTML = this.responseText;
      }
    }
  }
  ajaxRequest.send(data);
}

function validateUpdateTableContent(dataTypes, primaryKey)
{
  event.preventDefault();
  event.stopPropagation();

  var errorFlag = false;

  var form = document.forms[0];

  for (var i = 0; i < form.elements.length - 1; i++) {
    if (dataTypes[form.elements[i].id] == 'int') {
      if (!/^\d+$/.test(form.elements[i].value)) {
        var nextElement = form.elements[i].nextElementSibling;
        var errorText = document.createTextNode("Pole może zawierać tylko cyfry");
        var paragraphError = document.createElement('P');
        paragraphError.setAttribute('class', 'field-required-error');
        paragraphError.appendChild(errorText);
        form.insertBefore(paragraphError, nextElement);
        errorFlag = true;
      }
    }
  }
  return errorFlag;
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
