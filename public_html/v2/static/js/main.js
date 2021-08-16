var idArray = Array();
var currentId;
var interval = 10000;

var clock = setInterval(Tick, interval);

async function GetData(params) {
    $.ajax({
        url: "https://ssis_ld.offthegridcg.me/callback2.php/?id="+params,
        beforeSend: function( xhr ) {
            xhr.overrideMimeType( "text/plain; charset=UTF-8" );
        }
    })
    .done(function( data ) {
        if (data != false)
        {
            try {
                console.log("New Data retrived :)\nSize: "+data.length);
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

function GenerateElement(a,b,c)
{
    $('#dataBox').empty();
    GetData(a);
    GetData(b);
    GetData(c);
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
        currentId = idArray.shift();
        idArray.push(currentId);
        currentId = idArray.shift();
        idArray.push(currentId);
        GenerateElement(idArray[0], idArray[1], idArray[2])
    }
}

function SetInterval(newIntervalms)
{
    interval = newIntervalms;
    clearInterval(clock);
    clock = setInterval(Tick, interval);
}