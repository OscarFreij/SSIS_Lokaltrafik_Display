<?php

use function PHPSTORM_META\type;

include "../private_html/Container.php";
$container = new Container();

$dbReturn = $container->DB()->GetTimeTableData();
echo("<pre>");
foreach ($dbReturn as $key => $value)
{
    echo("<div style='border: black dashed 5px; padding: 10px; margin: 10px;'>");
    var_dump($container->Functions()->GenerateDepartureObjects($value));
    echo("</div><br>");
}
exit;
