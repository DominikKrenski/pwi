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
        //input.innerHTML = contentTable.rows[0].cells[i].innerHTML;

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
    updateButton.setAttribute('onclick', 'validateUpdateTableContent(event,' + JSON.stringify(dataTypes) + ', ' + JSON.stringify(primaryKey) + ')');

    form.appendChild(updateButton);

    formHeaderText.appendChild(formHeaderTextContent);
    formDivHeader.appendChild(formHeaderText);
    formDiv.appendChild(formDivHeader);
    formDiv.appendChild(form);
    mainContent.insertBefore(formDiv, contentTable);
  }
  else {
    var form = document.getElementById('modify-content-form');

    for (var i = 0; i < tableRow.cells.length - 1; i++) {
      if (form.elements[i].type == 'select-one') {
        //console.log(form.elements[i][0]);
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
  }
}


function removeTableContent(event)
{
  event.preventDefault();
  alert('removeTableContent');
}

function validateUpdateTableContent(event, dataTypes, primaryKey)
{
  event.preventDefault();
  event.stopPropagation();
  alert('walidacja');
}
