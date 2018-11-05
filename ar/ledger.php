<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require '../functions/ledger.php';

	if(isset($_GET['id']))
		$urlId = $_GET['id'];
	else
		$urlId = 1;
	
	if(isset($_GET['year']))
		$urlYear = $_GET['year'];
	else
		$urlYear = date("Y");
	
	$arQuery = mysqli_query($con, "SELECT id,name,isActive FROM ar_details WHERE id = '$urlId' " ) or die(mysqli_error($con));		
	$ar = mysqli_fetch_array($arQuery,MYSQLI_ASSOC);
	$arName = $ar['name'];
	$isActive = $ar['isActive'];
	
	$targetMap = getTargets($urlYear,$urlId);
	$specialTargetMap = getSpecialTargets($urlYear,$urlId);
	$redemptionMap = getRedemptions($urlYear,$urlId);
	$saleMap = getSales($urlYear,$urlId);
	$pointsMap = getPoints($urlYear,$saleMap,$isActive,$targetMap);
	$openingPoints = getOpeningPoints($urlYear,$urlId,$isActive);
	var_dump($openingPoints);
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
			$arList = mysqli_query($con, "SELECT id,name,isActive FROM ar_details ORDER BY name ASC" ) or die(mysqli_error($con));		
			foreach($arList as $ar) 
			{																																										?>			
				<option <?php if($urlId == $ar['id']) echo 'selected';?> value="<?php echo $ar['id'];?>"><?php echo $ar['name'];?></option>															<?php	
			}																																									?>	
			</select>					
			&nbsp;&nbsp;
			<select id="year" name="year" onchange="return rerender();">																							<?php	
			$yearList = mysqli_query($con, "SELECT DISTINCT YEAR(entry_date) FROM nas_sale ORDER BY entry_date DESC" ) or die(mysqli_error($con));	
			foreach($yearList as $yearObj) 
			{							
				$year = $yearObj['YEAR(entry_date)'];																				?>			
				<option <?php if($urlYear == $year) echo 'selected';?> value="<?php echo $year;?>"><?php echo $year;?></option>															<?php	
			}																																									?>	
			</select>						
		<br/><br/>	
		<h1><?php echo $arName . ', ' .$urlYear ;?></h1>
		</div>
		<table align="center" class="responstable" style="width:50%;">
		<tr>
			<th style="text-align:left;">Month</th>
			<th style="width:10%;">Target</th>
			<th style="width:10%;">Sale</th>
			<th style="width:10%;">Points</th>
			<th>Remarks</th>
		</tr>
		<?php
		foreach($targetMap as $month => $target) 
		{
			if(isset($specialTargetMap[$month]))
			{
				foreach($specialTargetMap[$month] as $dateString => $subArray)
				{																														?>
					<tr>
						<td style="text-align:left;"><?php echo getMonth($month).' '.$dateString;?></td>
						<td><?php echo $subArray['target'];?></td>
						<td><?php echo $subArray['sale'];?></td>
						<td><?php if($subArray['sale'] + $subArray['extra'] >= $subArray['target']) echo $subArray['sale'];else echo '0';?></td>
						<td></td>
					</tr>																												<?php			
				}	
			}																															?>
			<tr>
				<td style="text-align:left;"><?php echo getMonth($month);?></td>
				<td><?php echo $target['target'];?></td>
				<td><?php if(isset($saleMap[$month]))echo $saleMap[$month]; else echo '0';?></td>
				<td><?php if(isset($pointsMap[$month]['payment_points'])) echo $pointsMap[$month]['payment_points']; else echo '0';?></td>															
				<td></td>
			</tr>																													<?php
			if(isset($redemptionMap[$month]))
			{
				foreach($redemptionMap[$month] as $redemption)
				{																														?>
					<tr>
						<td style="text-align:left;"><?php echo date('F d',strtotime($redemption['date']));?></td>
						<td colspan="2">Redemption</td>
						<td><?php echo $redemption['points'];?></td>
						<td><?php echo $redemption['remarks'];?></td>
					</tr>																												<?php			
				}	
			}																															?>	
			<tr><td colspan="5" style="background-color:#167F92;"></td></tr>															<?php																		
		}																																?>
		</table>
		<br><br>
		</div>
	</body>
</html>																														<?php

}
else
	header("Location:../index.php");
