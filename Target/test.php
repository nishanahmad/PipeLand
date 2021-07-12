<?php
$monthNum = 2;
$dateObj   = DateTime::createFromFormat('!m', $monthNum);
$monthName = $dateObj->format('F'); // March
echo $monthName;
?>