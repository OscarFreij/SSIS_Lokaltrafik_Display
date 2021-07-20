<?php

include "../private_html/Container.php";
$container = new Container();

if (isset($_GET['id']))
{
    $dbReturn = $container->DB()->GetTimeTableData($_GET['id']);

    $domObejct = "<pre>".print_r($container->Functions()->GenerateDepartureObjects($dbReturn[0]), true)."</pre>";
    
    echo($domObejct);
}
else
{
    echo("<h1>Missing argument: [GET] --> id : Number</h1>");
}

exit;

?>