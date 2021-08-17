<div class="dataColumn">
    <div class="titel">
        <?=$stationName?>
    </div>
    <div class="cellColumn">
        <?php
            foreach ($data as $elementNr => $element) {
                include "../private_html/templates/dataCell.php";
            }
        ?>
    </div>
</div>
<br>