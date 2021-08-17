var idArray = Array();
var currentId;
var interval = 10000;

var clock = setInterval(GetData, interval);

idArray = ["7,8,12","9,10,11"];
GetData();

function GetData() {
    $.get('/callback.php?id='+idArray[0]+'&maxCount=6',  // url
      function (data, textStatus, jqXHR) {  // success callback
          console.log('status: ' + textStatus + ' | collection: ' + idArray[0]);
          $("#dataBox").empty();
          $("#dataBox").append(data);
          $("title").text("SSIS_LD:"+idArray[0]);
          idArray.push(idArray.shift());
    });
}
