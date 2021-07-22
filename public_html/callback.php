<?php

include "../private_html/Container.php";
$container = new Container();

if (isset($_GET['id']))
{
    $dbReturn = $container->DB()->GetTimeTableData($_GET['id']);

    $domObejct = $container->Functions()->GenerateDisplayHTML($container->Functions()->GenerateDepartureObjects($dbReturn[0]));
    
    echo($domObejct);
}
else
{
    echo("<h1>Missing argument: [GET] --> id : Number</h1>");
}

exit;

?>