<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../functions/monthMap.php';
	require '../functions/targetFormula.php';
	require 'getTargetMap.php';
	require 'getTargetExtrasMap.php';
	require 'getSaleMap.php';
	require 'getSpecialTargetMap.php';	
	require 'getBoosterMap.php';	
	require '../SpecialTarget/dropDownGenerator.php';
	require '../Target/functions/latestYear.php';
	require '../Target/functions/latestMonth.php';
	
	$latestYear = getLatestYear($con);
	$latestMonth = getLatestMonth($con);
	
	if($latestMonth == 12)
	{
		$nextMonth = 1;
		$nextYear = $latestYear + 1;
	}
	else
	{
		$nextMonth = $latestMonth + 1;
		$nextYear = $latestYear;		
	}	
	
	
	$mainArray = array();
	if(isset($_GET['year']) && isset($_GET['month']) && isset($_GET['dateString']))
	{
		$year = (int)$_GET['year'];
		$month = (int)$_GET['month'];
		$dateString = $_GET['dateString'];
	}	
	else
	{
		$year = $latestYear;
		$month = $latestMonth;
		$dateString = 'FULL';
	}
	
	if($year == $latestYear && $month > $latestMonth)
	{
		$URL='mainPage.php?';
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
	}
	
	$arObjects =  mysqli_query($con,"SELECT id,name,mobile,whatsapp,shop_name,sap_code FROM ar_details WHERE Type != 'Engineer' ORDER BY name ASC ") or die(mysqli_error($con));		 
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']]['name'] = $ar['name'];
		$arMap[$ar['id']]['mobile'] = $ar['mobile'];
		$arMap[$ar['id']]['whatsapp'] = $ar['whatsapp'];
		$arMap[$ar['id']]['shop'] = $ar['shop_name'];
		$arMap[$ar['id']]['sap'] = $ar['sap_code'];
	}				
	
	$prevMap = getPrevPoints(array_keys($arMap),$year,$month,$dateString);
	
	$arIds = implode("','",array_keys($arMap));
	
	if($dateString == 'FULL')
	{	
		$targetObjects = mysqli_query($con,"SELECT ar_id, target, payment_perc,rate FROM target WHERE  month = '$month' AND Year='$year' AND target > 0 AND ar_id IN('$arIds')") or die(mysqli_error($con));		 
		foreach($targetObjects as $target)
		{
			$targetMap[$target['ar_id']]['target'] = $target['target'];
			$targetMap[$target['ar_id']]['rate'] = $target['rate'];
			$targetMap[$target['ar_id']]['payment_perc'] = $target['payment_perc'];
			$monthTgtDetails[$target['ar_id']][$year][$month] = $target['target'];
		}
		
		$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE deleted IS NULL AND '$year' = year(`entry_date`) AND '$month' = month(`entry_date`) AND ar_id IN ('$arIds') GROUP BY ar_id") or die(mysqli_error($con));	
		foreach($sales as $sale)
		{
			$arId = $sale['ar_id'];
			$targetBagsQuery = mysqli_query($con,"SELECT SUM(qty) FROM targetbags WHERE '$year' = year(`date`) AND '$month' = month(`date`) AND ar_id = $arId") or die(mysqli_error($con));
			$row=mysqli_fetch_array($targetBagsQuery,MYSQLI_ASSOC);
			if($row['SUM(qty)'] == null)
				$targetBags = 0;
			else
				$targetBags = $row['SUM(qty)'];			

			$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];
			if(isset($targetMap[$arId]))
			{
				$points = round($total * $targetMap[$arId]['rate'],0);
				$actual_perc = round(($total + $targetBags) * 100 / $targetMap[$arId]['target'],0);
				$point_perc = getPointPercentage($actual_perc,$year,$month);			
				$achieved_points = round($points * $point_perc/100,0);
				
				if($total > 0)		
					$payment_points = round($achieved_points * $targetMap[$arId]['payment_perc']/100,0);
				else
					$payment_points = 0;			
				
				$pointMap[$arId]['points'] = $payment_points;
				$pointMap[$arId]['point_perc'] = $point_perc;
			}
			else
			{
				$pointMap[$arId]['points'] = 0;
				$pointMap[$arId]['point_perc'] = 0;
			}	
		}			
	}
	else
	{
		$dateArray = explode(" to ",$dateString);
		$from = $dateArray[0];
		$to = $dateArray[1];
		$toString = $to.'-'.$month.'-'.$year;		
		$toDate = date("Y-m-d",strtotime($toString));	
		
		$fromString = $from.'-'.$month.'-'.$year;		
		$fromDate = date("Y-m-d",strtotime($fromString));			
		
		$boostQuery = mysqli_query($con,"SELECT * FROM special_target_booster WHERE  fromDate = '$fromDate' AND toDate = '$toDate'") or die(mysqli_error($con));		 
		$boost = mysqli_fetch_array($boostQuery,MYSQLI_ASSOC);
		
		$specialTargetObjects = mysqli_query($con,"SELECT ar_id, fromDate, toDate,special_target FROM special_target WHERE  toDate = '$toDate' AND fromDate = '$fromDate' AND special_target >0 AND ar_id IN('$arIds')") or die(mysqli_error($con));		 
		foreach($specialTargetObjects as $specialTarget)
		{
			$arId = $specialTarget['ar_id'];
			$start = $specialTarget['fromDate'];
			$end = $specialTarget['toDate'];
			$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE deleted IS NULL AND entry_date >= '$start' AND entry_date <= '$end' AND ar_id = '$arId' GROUP BY ar_id") or die(mysqli_error($con));	
			foreach($sales as $sale)
			{
				$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];
				$totalWithExtra = $total;
				$extraBags = mysqli_query($con,"SELECT ar_id,SUM(qty) FROM extra_bags WHERE date >= '$start' AND date <= '$end' AND ar_id = '$arId' GROUP BY ar_id") or die(mysqli_error($con));	
				foreach($extraBags as $extraBag)
					$totalWithExtra = $totalWithExtra + $extraBag['SUM(qty)'];
					
				if(isset($boost))
				{
					$actualPercentage = round(  $total * 100 / $specialTarget['special_target'],0);
					if($actualPercentage >= $boost['ifAchieved'])
						$pointMap[$arId]['points'] = $total + round($total * $boost['boost']/100);
					else if($totalWithExtra >= ($specialTarget['special_target']))
						$pointMap[$arId]['points'] = $total;
				}					
				else if($totalWithExtra >= ($specialTarget['special_target']))
				{
					$pointMap[$arId]['points'] = $total;
				}
				else
				{
					$pointMap[$arId]['points'] = 0;			
				}
			}
		}		
	}
	
	$currentRedemption = mysqli_query($con,"SELECT ar_id,SUM(points) FROM redemption WHERE '$year' = year(`date`) AND '$month' = month(`date`) AND ar_id IN ('$arIds') GROUP BY ar_id") or die(mysqli_error($con));	
	foreach($currentRedemption as $redemption)
	{
		$redemptionMap[$redemption['ar_id']] = $redemption['SUM(points)'];
	}
?>
<head>
	<link href="../css/styles.css" rel="stylesheet" type="text/css">	
	<link rel="stylesheet" type="text/css" href="../css/loader.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.min.js" integrity="sha512-mWSVYmb/NacNAK7kGkdlVNE4OZbJsSUw8LiJSgGOxkb4chglRnVfqrukfVd9Q2EOWxFp4NfbqE3nDQMxszCCvw==" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.widgets.min.js" integrity="sha512-6I1SQyeeo+eLGJ9aSsU43lGT+w5HYY375ev/uIghqqVgmSPSDzl9cqiQC4HD6g8Ltqz/ms1kcf0takjBfOlnig==" crossorigin="anonymous"></script>
	</style>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$("#loaderbody").hide();
		$("#Points").tablesorter({
			dateFormat : "ddmmyyyy",
			theme : 'bootstrap',
			widgets: ['filter'],
			filter_columnAnyMatch: true
		});
	} );
	function rerender()
	{
		var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
		var month=document.getElementById("jsMonth").value;
		var hrf = window.location.href;
		hrf = hrf.slice(0,hrf.indexOf("?"));
		$("#mainbody").hide();
		$("#loaderbody").show();
		window.location.href = hrf +"?year="+ year + "&month=" + month + "&dateString=FULL";
	}
	function rerender2()
	{
		var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
		var month=document.getElementById("jsMonth").value;		
		var dateString = document.getElementById("jsDateString").options[document.getElementById("jsDateString").selectedIndex].value;
		var hrf = window.location.href;
		hrf = hrf.slice(0,hrf.indexOf("&dateString"));
		$("#mainbody").hide();
		$("#loaderbody").show();
		window.location.href = hrf +"?year="+ year + "&month=" + month + "&dateString=" + dateString;
	}
	</script>
	<title><?php echo getMonth($month); echo " "; echo $year; ?></title>
</head>
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">AR List</a></li>
					<li class="active"><a href="#">Target</a></li>
					<li><a href="../SpecialTarget/list.php?">Special Target</a></li>
					<li><a href="../redemption/list.php?">Redemption</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Accumulated Points
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">
							<li><a href="../Target/list.php?" class="dropdown-item">Monthly Points</a></li>
							<li><a href="../Target/edit.php?" class="dropdown-item">Update Target</a></li>
						</ul>
					</div>
				</div>					
				<span class="navbar-brand" style="font-size:25px;margin-right:7%"><i class="fa fa-chart-line"></i> Accumulated Points</span>
				<a href="../Target/new.php?year=<?php echo $nextYear;?>&month=<?php echo $nextMonth;?>" class="btn btn-sm" style="background-color:#54698D;color:white;float:right;margin-right:5%;"><i class="fa fa-chart-line"></i> Generate <?php echo getmonth($nextMonth);?> Target</a>
			</nav>
			<div id="loaderbody">
				<div id="loader">
				  <div class="divider" aria-hidden="true"></div>
				  <p class="loading-text" aria-label="Loading">
					<span class="letter" aria-hidden="true">L</span>
					<span class="letter" aria-hidden="true">o</span>
					<span class="letter" aria-hidden="true">a</span>
					<span class="letter" aria-hidden="true">d</span>
					<span class="letter" aria-hidden="true">i</span>
					<span class="letter" aria-hidden="true">n</span>
					<span class="letter" aria-hidden="true">g</span>
				  </p>
				</div>
			</div>		
			<div id="mainbody">	
				<br/><br/>		
				<div class="row">
					<div style="width:120px;margin-left:42%">
						<div class="input-group">
							<select id="jsYear" name="jsYear" class="form-select" onchange="return rerender();">																				<?php	
								$yearList = mysqli_query($con, "SELECT DISTINCT year FROM target ORDER BY year DESC") or die(mysqli_error($con));	
								foreach($yearList as $yearObj) 
								{																																								?>
									<option value="<?php echo $yearObj['year'];?>" <?php if($yearObj['year'] == $year) echo 'selected';?>><?php echo $yearObj['year'];?></option>																			<?php	
								}																																								?>
							</select>
						</div>
					</div>						
					<div style="width:150px;">
						<div class="input-group">
							<select id="jsMonth" name="jsMonth" class="form-select" onchange="return rerender();">																				<?php	
								$monthList = mysqli_query($con, "SELECT DISTINCT month FROM target WHERE year = $year ORDER BY month ASC" ) or die(mysqli_error($con));	
								foreach($monthList as $monthObj) 
								{																																		?>
									<option value="<?php echo $monthObj['month'];?>" <?php if($monthObj['month'] == $month) echo 'selected';?>><?php echo getMonth($monthObj['month']);?></option>															<?php	
								}																																		?>	
							</select>					
						</div>
					</div>
					<div style="width:150px;">
						<div class="input-group">
							<select id="jsDateString" name="jsDateString" class="form-select" onchange="return rerender2();">											<?php	
								if(!isset($stringList))
									$stringList = getStrings($year,$month);
									$stringList[] = 'FULL';
								foreach($stringList as $string) 
								{																																															?>
									<option value="<?php echo $string;?>" <?php if($dateString == $string) echo 'selected';?>><?php echo $string;?></option>																			<?php						
								}																																					?>																								
							</select>
						</div>
					</div>				
				</div>	
				<br/><br/>
				<table id="Points" class="maintable table table-hover table-bordered" style="width:92%;margin-left:15%;">
				<thead>
					<tr class="table-success">
						<th style="width:20%;text-align:left;">AR</th>
						<th>Mobile</th>
						<th>Whatsapp</th>
						<th style="width:20%;text-align:left;">Shop</th>
						<th>target</th>
						<th>Opng Pnts</th>
						<th>Current Pnts</th>	
						<th>Current%</th>	
						<th>Redeemed Pnts</th>	
						<th>Balance</th>	
					</tr>
				</thead>																										<?php
				
					$openingTotal = 0;
					$currentTotal = 0;
					$redeemedTotal = 0;
					$balanceTotal = 0;
					
					foreach($arMap as $arId => $detailMap)
					{		
						if(!isset($targetMap[$arId]))
							$targetMap[$arId]['target'] = 0;						
						if(!isset($pointMap[$arId]))	
							$pointMap[$arId]['points'] = 0;
						if(!isset($redemptionMap[$arId]))	
							$redemptionMap[$arId] = 0;																																	?>
						
						
						<tr align="center">
							<td style="text-align:left;"><?php echo $detailMap['name'];?></b></td>
							<td><?php echo $detailMap['mobile'];?></b></td>
							<td><?php echo $detailMap['whatsapp'];?></b></td>
							<td style="text-align:left;"><?php echo $detailMap['shop'];?></b></td>
							<td><?php if(isset($monthTgtDetails[$arId][$year][$month]))echo $monthTgtDetails[$arId][$year][$month]; else echo '0';?></b></td>
							<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'];?></b></td>
							<td><?php echo $pointMap[$arId]['points'];?></td>
							<td><?php if(isset($pointMap[$arId]['point_perc'])) echo $pointMap[$arId]['point_perc'].'%'; else echo '0%';?></td>
							<td><?php echo $redemptionMap[$arId];?></td>
							<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'] + $pointMap[$arId]['points'] - $redemptionMap[$arId];?></td>
						</tr>																																							<?php
						$openingTotal = $openingTotal + $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'];
						$currentTotal = $currentTotal + $pointMap[$arId]['points'];
						$redeemedTotal = $redeemedTotal + $redemptionMap[$arId];
						$balanceTotal = $balanceTotal + $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'] + $pointMap[$arId]['points'] - $redemptionMap[$arId];
					}																																									?>
				<thead>
					<tr style="text-align:center">
						<th colspan="4"></th>
						<th><?php echo $openingTotal;?></th>
						<th><?php echo $currentTotal;?></th>	
						<th><?php echo $redeemedTotal;?></th>	
						<th><?php echo $balanceTotal;?></th>	
					</tr>
				</thead>																													
				</table>
			</div>
		</div>
		<br/><br/>
	</div>
</body>
</html>																																											<?php
}
else
	header("../Location:index.php");


function getPrevPoints($arList,$endYear,$endMonth,$dateString)
{
	require '../connect.php';
	
	$startYear = 2018; 
	$startMonth = 1;
	
	if($dateString == 'FULL')
	{
		$string = cal_days_in_month(CAL_GREGORIAN,$endMonth,$endYear).'-'.$endMonth.'-'.$endYear;		
	}
	else
	{
		$dateArray = explode(" to ",$dateString);
		$from = $dateArray[0];
		$to = $dateArray[1];
		$string = $to.'-'.$endMonth.'-'.$endYear;		
	}
	$endDate = date("Y-m-d",strtotime($string));	
	
	foreach($arList as $arId)
	{
		$arMap[$arId]['prevPoints'] = 0;	
		$arMap[$arId]['prevRedemption'] = 0;			
	}
	
	$arIds = implode("','",array_keys($arMap));	
	
	
	//call targetMap and saleMap from helper functions 
	
	$targetMap = getTargetMap($arIds,$startYear);		// arId => year => month => target
	$targetExtrasMap = getTargetExtrasMap($arIds,$startYear);		// arId => year => month => targetExtraBags
	$saleMap = getSaleMap($arIds,$startYear,$endYear);	    // arId => year => month = sale

	// Add points based on monthly targets
	foreach($targetMap as $arId => $yearMonthArray)
	{
		foreach($yearMonthArray as $year => $monthArray)
		{
			if($year <= $endYear)
			{
				foreach($monthArray as $month => $detailArray)
				{
					if(($month < $endMonth && $year == $endYear) || $year < $endYear)
					{
						if(isset($saleMap[$arId][$year][$month]))
							$sale = $saleMap[$arId][$year][$month];
						else
							$sale = 0;
						
						if(isset($targetExtrasMap[$arId][$year][$month]))
							$targetExtras = $targetExtrasMap[$arId][$year][$month];
						else
							$targetExtras = 0;						
						
						

						$points = round($sale * $detailArray['rate'],0);
						$actual_perc = round(($sale + $targetExtras)* 100 / $detailArray['target'],0);
						$point_perc = getPointPercentage($actual_perc,$year,$month);			
						$achieved_points = round($points * $point_perc/100,0);
						
						if($sale > 0)		
							$payment_points = round($achieved_points * $detailArray['payment_perc']/100,0);
						else if(isset($detailArray))
							$payment_points = 0;			
						
						$arMap[$arId]['prevPoints'] = $arMap[$arId]['prevPoints'] + $payment_points;												
					}
				}				
			}
		}
	}
	
	// Add points based on special targets
	$specialTargetMap = getSpecialTargetMap($arIds,$endDate);		// arId => fromDate => special_target
	$boosterMap = getBoosterMap($endDate);
	
	if($dateString != 'FULL')
		$stDates = mysqli_query($con,"SELECT from_date,to_date FROM special_target_date WHERE to_date < '$endDate' AND from_date > '2018-01-01' ") or die(mysqli_error($con));	
	else
		$stDates = mysqli_query($con,"SELECT from_date,to_date FROM special_target_date WHERE to_date <= '$endDate' AND from_date > '2018-01-01' ") or die(mysqli_error($con));	
	
	foreach($stDates as $stDate)
	{
		$start = $stDate['from_date'];
		$end = $stDate['to_date'];
		$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE deleted IS NULL AND entry_date >= '$start' AND entry_date <= '$end' AND ar_id IN ('$arIds') GROUP BY ar_id") or die(mysqli_error($con));	
		foreach($sales as $sale)
		{
			$arId = $sale['ar_id'];
			$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];
			$totalWithExtra = $total;
			$extraBags = mysqli_query($con,"SELECT ar_id,SUM(qty) FROM extra_bags WHERE date >= '$start' AND date <= '$end' AND ar_id = '$arId' GROUP BY ar_id") or die(mysqli_error($con));	
			foreach($extraBags as $extraBag)
				$totalWithExtra = $totalWithExtra + $extraBag['SUM(qty)'];

			
			if(isset($specialTargetMap[$arId][$start]))			
			{
				if(isset($boosterMap[$start]))
				{
					$actualPercentage = round(  $total * 100 / $specialTargetMap[$arId][$start],0);
					if($actualPercentage >= $boosterMap[$start]['achieved'])
						$arMap[$arId]['prevPoints'] = $arMap[$arId]['prevPoints'] + $total + round($total * $boosterMap[$start]['boost']/100);												
					else if($totalWithExtra >= ($specialTargetMap[$arId][$start]))
						$arMap[$arId]['prevPoints'] = $arMap[$arId]['prevPoints'] + $total;												
				}
				else
				{
					if($totalWithExtra >= ($specialTargetMap[$arId][$start]))
						$arMap[$arId]['prevPoints'] = $arMap[$arId]['prevPoints'] + $total;												
				}	
			}
		}			
	}
	
	$redemptionList = mysqli_query($con,"SELECT ar_id,SUM(points) FROM redemption WHERE  ( (YEAR(date) = '$endYear' AND MONTH(date) < '$endMonth') OR (YEAR(date) < '$endYear')) AND ar_id IN('$arIds') GROUP BY ar_id") or die(mysqli_error($con));		 	
	foreach($redemptionList as $redemption)
	{
		$arMap[$redemption['ar_id']]['prevRedemption'] = $arMap[$redemption['ar_id']]['prevRedemption'] + $redemption['SUM(points)'];			
	}

	return $arMap;
}
?>