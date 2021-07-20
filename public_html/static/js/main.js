var idArray = Array();
var currentId;
var interval = 10000;

var clock = setInterval(Tick, interval);

async function GetData(params) {
    $.ajax({
        url: "https://ssis_ld.offthegridcg.me/callback.php/?id="+params,
        beforeSend: function( xhr ) {
            xhr.overrideMimeType( "text/plain; charset=UTF-8" );
        }
    })
    .done(function( data ) {
        if (data != false)
        {
            try {
                console.log("New Data retrived :)\nSize: "+data.length);
                $('#dataBox').empty();
                $('#dataBox').append(data);
            } catch (error) {
                console.log(data);
            }
            
        }
        else
        {
            console.log("No data to retrive :(");
        }
    });
}

function Tick()
{
    if (idArray.length == 0)
    {
        $('#dataBox').empty();
        $('#dataBox').append("<h1>Missing idArray Data!</h1>"); 
        console.error("Missing idArray Data!");
    }
    else
    {
        currentId = idArray.shift();
        idArray.push(currentId);
        GetData(currentId);
    }
}

function SetInterval(newIntervalms)
{
    interval = newIntervalms;
    clearInterval(clock);
    clock = setInterval(Tick, interval);
}