<?php
function getZeroTargetIds($year,$month,$con)
{
	$arIds = null;	
	$tempIds = null;
	
	$targetObjects = mysqli_query($con, "SELECT ar_id FROM target WHERE year = $year AND month = $month") or die(mysqli_error($con));
	foreach($targetObjects as $target)
	{
		$tempIds[] = $target['ar_id'];
	}	
	
	$array = implode("','",$tempIds);	
	$arObjects = mysqli_query($con, "SELECT id FROM ar_details WHERE type = 'AR/SR' AND id NOT IN ('$array') ORDER BY name") or die(mysqli_error($con));
	foreach($arObjects as $ar)
	{
		$arIds[] = $ar['id'];
	}		

	$zeroTargetObjects = mysqli_query($con, "SELECT ar_id FROM target t LEFT JOIN ar_details a ON t.ar_id = a.id WHERE t.year = $year AND t.month = $month AND t.target = 0 ORDER BY a.name") or die(mysqli_error($con));
	foreach($zeroTargetObjects as $target)
	{
		$arIds[] = $target['ar_id'];
	}	
	
	return $arIds;
}
?>