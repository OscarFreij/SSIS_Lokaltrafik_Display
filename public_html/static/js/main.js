var idArray = Array();
var currentId;
var interval = 10000;

var clock = setInterval(GetData, interval);

idArray = ["7,8,12","9,11,12"];
GetData();

function GetData() {
    $.get('/callback.php?id='+idArray[0]+'&maxCount=6',  // url
      function (data, textStatus, jqXHR) {  // success callback
          console.log('status: ' + textStatus + ' | collection: ' + idArray[0]);
          $("#dataBox").empty();
          $("#dataBox").append(data);
          idArray.push(idArray.shift());
    });
}
