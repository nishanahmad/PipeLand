<?php
function getTargetIds($year,$month,$con)
{
	$arIds = null;
		
	$targetObjects = mysqli_query($con, "SELECT ar_id FROM target WHERE year = $year AND month = $month AND target > 0") or die(mysqli_error($con));

	foreach($targetObjects as $target)
	{
		$arIds[] = $target['ar_id'];
	}	
			
	return $arIds;
}
?>


