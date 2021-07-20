var dataArray = Array();

async function GetData(params) {
    $.ajax({
        url: "https://ssis_ld.offthegridcg.me/callback.php",
        beforeSend: function( xhr ) {
            xhr.overrideMimeType( "text/plain; charset=UTF-8" );
        }
    })
    .done(function( data ) {
        if (data != false)
        {
            try {
                console.log("New Data retrived :)\nSize: "+data.length);
                jsonData = JSON.parse(data);
                jsonData.forEach(element => {
                    dataArray.push(element);
                });    
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

setInterval(Tick, 10000);

function Tick()
{
    if (dataArray.length <= 1)
    {
        GetData();
    }
    NextDataSet();
}

function NextDataSet()
{
    $('#dataBox').empty();
    $('#dataBox').append(JSON.stringify(dataArray[0]));   
    dataArray.shift();
}

