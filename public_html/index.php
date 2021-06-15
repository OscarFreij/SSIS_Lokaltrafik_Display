<?php


include "../private_html/Container.php";
$container = new Container();

foreach ($container->DB()->GetTimeTableData() as $key => $value) {
    $data = @simplexml_load_string($value['xmlData']);
    echo "<pre style='border-bottom: dotted black 8px; padding-bottom: 8px;'>"; print_r($data); exit;        
}