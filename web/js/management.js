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

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert ("Przeglądarka nie wspiera technologii Ajax");
  }

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
  alert(queryString);
  ajaxRequest.open("POST", "createtable.php", true);
  ajaxRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRequest.onreadystatechange = function() {
    addLinkTable(this, tableName);
  }
  ajaxRequest.send(queryString);
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

function addRow()
{
  var table = document.getElementById('create-table');
  var index = table.rows.length - 1;
  var row = table.insertRow(index);
  var cells = [];

  for (var i = 0; i < 7; i++) {
    cells[i] = row.insertCell(i);
  }

  cells[0].innerHTML = '<input type="text" name="fieldName[]">';
  cells[1].innerHTML = '<select name="dataType[]"> <option value="integer">INTEGER</option><option value="varchar">VARCHAR</option><option value="boolean">BOOLEAN</option></select>';
  cells[2].innerHTML = '<input type="text" name="dataSize[]">';
  cells[3].innerHTML = '<input type="text" name="defaultValue[]">';
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
