<?php

include "../private_html/Container.php";
$container = new Container();

$dbReturn = $container->DB()->GetTimeTableData();

$data = array();
foreach ($dbReturn as $key => $value)
{
    $domObejct = "<pre>".print_r($container->Functions()->GenerateDepartureObjects($value), true)."</pre>";
    array_push($data, $domObejct);
}
echo(json_encode($data,JSON_UNESCAPED_UNICODE));
exit;

?>