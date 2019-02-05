<?php


function getTargets($year,$arId)
{
	require '../connect.php';
	
	$targetMap = array();
	$targetObjects = mysqli_query($con,"SELECT month, target, payment_perc,rate FROM target WHERE Year='$year' AND ar_id = '$arId' ") or die(mysqli_error($con));		 
	foreach($targetObjects as $target)
	{
		$targetMap[$target['month']]['target'] = $target['target'];
		$targetMap[$target['month']]['rate'] = $target['rate'];
		$targetMap[$target['month']]['payment_perc'] = $target['payment_perc'];
	}
			
	return $targetMap;
}



function getSpecialTargets($year,$arId)
{
	require '../connect.php';
	
	$specialTargetMap = array();
	$specialTargetObjects = mysqli_query($con,"SELECT * FROM special_target WHERE YEAR(fromDate)='$year' AND ar_id = '$arId' ORDER BY fromDate") or die(mysqli_error($con));		 
	foreach($specialTargetObjects as $target)
	{
		$month = (int)date("m",strtotime($target['fromDate']));
		$from = date("Y-m-d",strtotime($target['fromDate']));
		$to = date("Y-m-d",strtotime($target['toDate']));
		$dateString = date('d',strtotime($target['fromDate'])). ' to ' .date('d',strtotime($target['toDate']));
		$specialTargetMap[$month][$dateString]['target'] = $target['special_target'];
		
		$sql = mysqli_query($con, "SELECT ar_id,SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$from' AND entry_date <= '$to' AND ar_id = '$arId'" ) or die(mysqli_error($con));
		$sale = mysqli_fetch_array($sql,MYSQLI_ASSOC);
		$specialTargetMap[$month][$dateString]['sale'] = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
		
		$sql = mysqli_query($con, "SELECT SUM(qty) FROM extra_bags WHERE date >= '$from' AND date <= '$to' AND ar_id = '$arId'" ) or die(mysqli_error($con));
		$extraBags = mysqli_fetch_array($sql,MYSQLI_ASSOC);		
		$specialTargetMap[$month][$dateString]['extra'] =  $extraBags['SUM(qty)'];
	}	
	
	return $specialTargetMap;
}	


function getRedemptions($year,$arId)
{
	require '../connect.php';
	
	$redemptionMap = array();
	$redemptionObjects = mysqli_query($con,"SELECT * FROM redemption WHERE YEAR(date)='$year' AND ar_id = '$arId' ") or die(mysqli_error($con));		 
	foreach($redemptionObjects as $redemption)
	{
		$redMonth = (int)date('m',strtotime($redemption['date']));
		$redemptionMap[$redMonth][] = $redemption;
	}
			
	return $redemptionMap;
}


function getSales($year,$arId)
{
	require '../connect.php';
	
	$saleMap = array();	
	$salesList = mysqli_query($con, "SELECT SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag),MONTH(entry_date) FROM nas_sale WHERE YEAR(entry_date) = '$year' AND ar_id = '$arId' GROUP BY MONTH(entry_date) ORDER BY MONTH(entry_date) ASC" ) or die(mysqli_error($con));
	foreach($salesList as $sale) 
	{
		$saleMap[$sale['MONTH(entry_date)']] = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
	}
			
	return $saleMap;
}



function getPoints($year,$saleMap,$isActive,$targetMap)
{
	require '../connect.php';
	require 'targetFormula.php';
	
	$pointsMap = array();
	foreach($saleMap as $month => $total)
	{
		$pointsMap[$month]['points'] = null;
		$pointsMap[$month]['actual_perc'] = null;
		$pointsMap[$month]['point_perc'] = null;
		$pointsMap[$month]['achieved_points'] = null;
		$pointsMap[$month]['payment_points'] = null;					

		if(isset($targetMap[$month]['target']) && $isActive && $targetMap[$month]['target'] >0)
		{
			$points = round($total * $targetMap[$month]['rate'],0);
			$actual_perc = round($total * 100 / $targetMap[$month]['target'],0);
			$point_perc = getPointPercentage($actual_perc,$year,$month);			 
			$achieved_points = round($points * $point_perc/100,0);
			
			if($total > 0)		
				$payment_points = round($achieved_points * $targetMap[$month]['payment_perc']/100,0);
			else
				$payment_points = 0;			

			$pointsMap[$month]['points'] = $points;
			$pointsMap[$month]['actual_perc'] = $actual_perc;
			$pointsMap[$month]['point_perc'] = $point_perc;
			$pointsMap[$month]['achieved_points'] = $achieved_points;
			$pointsMap[$month]['payment_points'] = $payment_points;			
		}		
	}
			
	return $pointsMap;
}


function getOpeningPoints($year,$arId,$isActive)
{
	require '../connect.php';
	
	$opening = 0;
	if($year == 2018)
	{
		$redemptionObjects = mysqli_query($con,"SELECT * FROM redemption WHERE YEAR(date)<'$year' AND ar_id = '$arId' ") or die(mysqli_error($con));		 
		foreach($redemptionObjects as $redemption)
		{
			$opening = $opening - $redemption['points'];
		}
	}
	else if($year > 2018)
	{
		while($year >= 2018)
		{
			if($year == 2018)
			{
				$redemptionObjects = mysqli_query($con,"SELECT * FROM redemption WHERE YEAR(date)<'$year' AND ar_id = '$arId' ") or die(mysqli_error($con));		 
				foreach($redemptionObjects as $redemption)
				{
					$opening = $opening - $redemption['points'];
				}				
			}
			else
			{
				$targetMap = getTargets($year-1,$arId);
				$specialTargetMap = getSpecialTargets($year-1,$arId);
				$redemptionMap = getRedemptions($year-1,$arId);
				$saleMap = getSales($year-1,$arId);
				
				foreach($saleMap as $month => $total)
				{
					if(isset($targetMap[$month]['target']) && $isActive && $targetMap[$month]['target'] >0)
					{
							$points = round($total * $targetMap[$month]['rate'],0);
							$actual_perc = round($total * 100 / $targetMap[$month]['target'],0);
							$point_perc = getPointPercentage($actual_perc,$year-1,$month);			 
							$achieved_points = round($points * $point_perc/100,0);
							
							if($total > 0)		
								$payment_points = round($achieved_points * $targetMap[$month]['payment_perc']/100,0);
							else
								$payment_points = 0;			
							
							$opening = $opening + $payment_points;						
					}		
				}			
				
				foreach($specialTargetMap as $month => $subArray)
				{
					foreach($subArray as $dateString => $value)
					{
						if($value['sale'] + $value['extra'] >= $value['target'])
						{
							$opening = $opening + $value['sale'];					
						}
					}
				}			
				
				foreach($redemptionMap as $subArray)
				{
					foreach($subArray as $index => $redemption)
					{
						$opening = $opening - $redemption['points'];
					}
						
				}				
			}

			$year--;
		}
	}
			
	return $opening;
}


?>