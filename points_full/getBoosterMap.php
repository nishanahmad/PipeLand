<?php
function getBoosterMap($endDate)
{
	require '../connect.php';
	
	$boosterMap = array();
	
	$boosterObjects = mysqli_query($con,"SELECT * FROM special_target_booster WHERE  toDate <= '$endDate'") or die(mysqli_error($con));		 
	foreach($boosterObjects as $booster)
	{
		$boosterMap[$booster['fromDate']]['achieved'] = $booster['ifAchieved'];
		$boosterMap[$booster['fromDate']]['boost'] = $booster['boost'];
	}	

	return $boosterMap;
}
?>	