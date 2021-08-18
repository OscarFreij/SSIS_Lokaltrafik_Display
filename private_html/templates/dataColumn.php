<div class="dataColumn">
    <div class="titel">
        <?=$stationName?>
    </div>
    <div class="cellColumn">
        <?php
        if ($data != false)
        {
            foreach ($data as $elementNr => $element) {
                include "../private_html/templates/dataCell.php";
            }
        }
        else
        {
            ?>
            <span>Ingen Data!</span>
            <?php
        } 
        ?>
    </div>
</div>
<br>