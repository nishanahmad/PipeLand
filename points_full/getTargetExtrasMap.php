<?php
function getTargetExtrasMap($arIds,$startYear)
{
	require '../connect.php';
	
	$targetExtrasObjects = mysqli_query($con,"SELECT ar_id, qty, YEAR(date), MONTH(date) FROM targetBags WHERE  YEAR(date) >='$startYear' AND ar_id IN('$arIds')") or die(mysqli_error($con));
	foreach($targetExtrasObjects as $bags)
	{
		if(isset($targetExtrasMap[$bags['ar_id']][$bags['YEAR(date)']][$bags['MONTH(date)']]))
			$targetExtrasMap[$bags['ar_id']][$bags['YEAR(date)']][$bags['MONTH(date)']] = $targetExtrasMap[$bags['ar_id']][$bags['YEAR(date)']][$bags['MONTH(date)']] + $bags['qty'];
		else
			$targetExtrasMap[$bags['ar_id']][$bags['YEAR(date)']][$bags['MONTH(date)']] = $bags['qty'];
	}	

	return $targetExtrasMap;
}

?>