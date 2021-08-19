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
    ?>
    <div id="pageTitel">
        Avgångar
    </div>
    <?php
    foreach (explode(',',$_GET['id']) as $key => $id)
    {
        $stationName = $container->DB()->GetCallTimeName($id);
        $data = $container->DB()->GetTimeTableData($id, $_GET['maxCount']);
        include "../private_html/templates/dataColumn.php";
    }   
}

exit;

?>