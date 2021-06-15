<?php


include "../private_html/Container.php";
$container = new Container();

$data = simplexml_load_string($container->DB()->GetTimeTableData()[0]['xmlData']);
echo "<pre>"; print_r($data); exit;    