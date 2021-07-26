<?php

include "../private_html/Container.php";
$container = new Container();

if (isset($_GET['id']))
{
    $dbReturn = $container->DB()->GetTimeTableData($_GET['id'])[0];    
    echo($container->Functions()->GenerateDisplayHTML($container->Functions()->GenerateDepartureObjects($dbReturn)));
}
else
{
    echo("<h1>Missing argument: [GET] --> id : Number</h1>");
}

exit;

?>