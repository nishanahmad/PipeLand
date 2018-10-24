<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';			
	require '../functions/targetFormula.php';	

	if(isset($_GET['id']))
		$urlId = $_GET['id'];
	else
		$urlId = 1;
	
	if(isset($_GET['year']))
		$urlYear = $_GET['year'];
	else
		$urlYear = date("Y");	
	
	$arMap = array();
	$arList = mysqli_query($con, "SELECT id,name,isActive FROM ar_details ORDER BY name ASC" ) or die(mysqli_error($con));		
	foreach($arList as $ar) 
	{
		if($ar['id'] == $urlId)
		{
			$arName = $ar['name'];
			$isActive = $ar['isActive'];			
		}
	}
	$yearList = mysqli_query($con, "SELECT DISTINCT YEAR(entry_date) FROM nas_sale WHERE ar_id = '$urlId' ORDER BY entry_date DESC" ) or die(mysqli_error($con));
	foreach($yearList as $year) 
	{
		$yearMap[] = $year['YEAR(entry_date)'];
	}	
	
	$targetObjects = mysqli_query($con,"SELECT month, target, payment_perc,rate FROM target WHERE Year='$urlYear' AND ar_id = '$urlId' ") or die(mysqli_error($con));		 
	foreach($targetObjects as $target)
	{
		$targetMap[$target['month']]['target'] = $target['target'];
		$targetMap[$target['month']]['rate'] = $target['rate'];
		$targetMap[$target['month']]['payment_perc'] = $target['payment_perc'];
	}
	
	$saleMap = array();	
	$salesList = mysqli_query($con, "SELECT SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag),MONTH(entry_date) FROM nas_sale WHERE YEAR(entry_date) = '$urlYear' AND ar_id = '$urlId' GROUP BY MONTH(entry_date) ORDER BY MONTH(entry_date) ASC" ) or die(mysqli_error($con));
	foreach($salesList as $sale) 
	{
		$saleMap[$sale['MONTH(entry_date)']] = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
	}
	
	$mainArray = array();
	foreach($saleMap as $month => $total)
	{
		$mainArray[$month]['points'] = null;
		$mainArray[$month]['actual_perc'] = null;
		$mainArray[$month]['point_perc'] = null;
		$mainArray[$month]['achieved_points'] = null;
		$mainArray[$month]['payment_points'] = null;					

		if(isset($targetMap[$month]['target']) && $isActive && $targetMap[$month]['target'] >0)
		{
			$points = round($total * $targetMap[$month]['rate'],0);
			$actual_perc = round($total * 100 / $targetMap[$month]['target'],0);
			$point_perc = getPointPercentage($actual_perc,$urlYear,$month);			 
			$achieved_points = round($points * $point_perc/100,0);
			
			if($total > 0)		
				$payment_points = round($achieved_points * $targetMap[$month]['payment_perc']/100,0);
			else
				$payment_points = 0;			

			$mainArray[$month]['points'] = $points;
			$mainArray[$month]['actual_perc'] = $actual_perc;
			$mainArray[$month]['point_perc'] = $point_perc;
			$mainArray[$month]['achieved_points'] = $achieved_points;
			$mainArray[$month]['payment_points'] = $payment_points;			
		}		
	}
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../css/loader.css">
<link rel="stylesheet" type="text/css" href="../css/responstable.css">
<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
function rerender()
{
	var ar = document.getElementById("ar").options[document.getElementById("ar").selectedIndex].value;
	var year = document.getElementById("year").options[document.getElementById("year").selectedIndex].value;

	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));

	window.location.href = hrf +"?id="+ ar + "&year=" + year;
}
</script>
<title>Ledger</title>
</head>
<body>
<div id="main" class="main">
<div align="center" style="padding-bottom:5px;">
<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='60px' height='60px'/> </a>
<br><br>
	<select id="ar" name="ar" onchange="return rerender();">																							<?php	
	foreach($arList as $ar) 
	{																																										?>			
		<option <?php if($urlId == $ar['id']) echo 'selected';?> value="<?php echo $ar['id'];?>"><?php echo $ar['name'];?></option>															<?php	
	}																																									?>	
	</select>					
	&nbsp;&nbsp;
	<select id="year" name="year" onchange="return rerender();">																							<?php	
	foreach($yearMap as $index => $year) 
	{																																										?>			
		<option <?php if($urlYear == $year) echo 'selected';?> value="<?php echo $year;?>"><?php echo $year;?></option>															<?php	
	}																																									?>	
	</select>						
<br/><br/>	
<h1><?php echo $arName . ', ' .$urlYear ;?></h1>
</div>
<table align="center" class="responstable" style="width:25%;">
<tr>
	<th style="text-align:left;width:40%">Month</th>
	<th>Target</th>
	<th>Sale</th>
	<th>Points</th>
	<th></th>	
</tr>
<?php
$totalPercentage =0;
$count =0;
foreach($saleMap as $month => $sale) 
{																																		?>
	<tr>
		<td style="text-align:left;"><?php echo getMonth($month);?></th>
		<td><?php if(isset($targetMap[$month]['target'])) echo $targetMap[$month]['target'];?></th>
		<td><?php echo $sale;?></th>
		<td><?php echo $mainArray[$month]['payment_points'];?></td>															<?php 
		if(isset($targetMap[$month]['target']) && $targetMap[$month]['target'] >0)
		{			
			$count++;
			$totalPercentage = 	$totalPercentage + $sale/$targetMap[$month]['target'] *100;									?>
			<td><?php echo round($sale/$targetMap[$month]['target'] *100,0);?>%</td>													<?php
		}
		else
		{																													?>
			<td></td>																										<?php
		}																													?>

	</tr>																													<?php																		
}																															?>
	<tr>
		<td colspan="4"></td>
		<td><b><?php if($count >0 ) echo round($totalPercentage/$count,0);?>%</b></td>
	<tr>
</table>
<br><br>
</div>
</body>
</html>																														<?php

}
else
	header("Location:../index.php");
