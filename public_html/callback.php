<?php

include "../private_html/Container.php";
$container = new Container();


if (!isset($_GET['id']))
{
    echo("<h1>Missing argument: [GET] --> id : Number</h1>");
}
else if (!isset($_GET['maxCount']))
{
    echo("<h1>Missing argument: [GET] --> maxCount : Number</h1>");
}
else
{
    echo("<pre>");
    print_r($container->DB()->GetTimeTableData($_GET['id'], $_GET['maxCount']));
}

exit;

?>