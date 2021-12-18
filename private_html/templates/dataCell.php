<?php

$state = "normal";
$late = false;

$directionName = $element['direction'];
$lineName = $element['lineName'];
$timeUntillDeparture = "Ingen Data!";
$departureTime = "Ingen Data!";

$actualDepartureTime = "";

$a = new DateTime();
$a->setTimestamp($element['unixTimeStamp']);
$a = $a->format('H:i');
if ($element['rtUnixTimeStamp'] != "")
{
    
    $actualDepartureTime = $element['rtUnixTimeStamp'];
    if ($element['rtUnixTimeStamp'] > $element['unixTimeStamp'])
    {
        $late = true;
        $b = new DateTime();
        $b->setTimestamp($element['rtUnixTimeStamp']);
        $b = $b->format('H:i');
    }
}
else
{
    $actualDepartureTime = $element['unixTimeStamp'];
}
$dateTimeNow = new DateTime();
$timeDiffDeparture =  $dateTimeNow->diff(new DateTime("@".$actualDepartureTime));
$travelTime = intval($element['travelTime']);

if ($timeDiffDeparture->h != 0 && !$timeDiffDeparture->invert)
{
    $timeUntillDeparture = "60+ min";
}
else
{
    $timeUntillDeparture = ($timeDiffDeparture->i+1)." min";

    if ($timeDiffDeparture->i <= 0 + $travelTime)
    {
        $state = "critical";
        if ($travelTime == 0)
        {
            $timeUntillDeparture = "NU";
        }
    }
    else if ($timeDiffDeparture->i > 0 + $travelTime && $timeDiffDeparture->invert)
    {
        $state = "passed";
    }
    else if ($timeDiffDeparture->i <= 5 + $travelTime)
    {
        $state = "high";
    }
    else if ($timeDiffDeparture->i <= 15 + $travelTime)
    {
        $state = "low";
    }
}

?>

<div class="dataCell" state="<?=$state?>" late="<?=$late?>">
    <span><?=$directionName?></span>
    <span class="time"><?=$timeUntillDeparture?></span>
    <span><?=$lineName?></span>
    <?php 
    if ($late)
    {
        ?>
        <span class="time"><span class="time time-old"><?=$a?></span> - <span class="time time-new"><?=$b?></span></span>
        <?php
    }
    else
    {
        ?>
        <span class="time"><?=$a?></span>
        <?php
    }
    ?>
</div>