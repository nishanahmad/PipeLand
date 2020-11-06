<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require '../navbar.php';
	require 'functions/latestYear.php';
	require 'functions/latestMonth.php';
	
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
	
	if(isset($_GET['year']))
	{
		$year = $_GET['year'];
		$month = $_GET['month'];		
	}
	else
	{
		$year = $latestYear;
		$month = $latestMonth;
	}
		
	if($year == $latestYear && $month > $latestMonth)
	{
		$URL='edit.php?';
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
	}
		
		
	$arObjects = mysqli_query($con, "SELECT * FROM ar_details WHERE isActive = 1 AND Type LIKE '%AR%' ORDER BY name ASC") or die(mysqli_error($con));
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']] = $ar['name'];
		$shopMap[$ar['id']] = $ar['shop_name'];
		$codeMap[$ar['id']] = $ar['sap_code'];
		$phoneMap[$ar['id']] = $ar['mobile'];
	}	
	
	$array = implode("','",array_keys($arMap));	
	
	$sql = "SELECT ar_id, target, rate, payment_perc FROM target WHERE year='$year' AND month='$month' AND ar_id IN ('$array')";
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));		

	$yearObjects = mysqli_query($con,"SELECT DISTINCT year FROM target ORDER BY year DESC");	
	foreach($yearObjects as $yearObj)
		$yearList[] = (int)$yearObj['year'];																																					?>

<script type="text/javascript">
function rerender()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;

	var month=document.getElementById("jsMonth").value;

	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));

	window.location.href = hrf +"?year="+ year + "&month=" + month;
}
</script>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="../css/styles.css" rel="stylesheet" type="text/css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" ></script>
	<title><?php echo getMonth($month);?> Target</title>
</head>
<body>
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">AR List</a></li>
					<li class="active"><a href="#">Target</a></li>
					<li><a href="../SpecialTarget/list.php?">Special Target</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">		
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Update Target
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">
							<li><a href="monthlyPointsList.php?" class="dropdown-item">Monthly Points</a></li>
							<li><a href="../points_full/mainPage.php?" class="dropdown-item">Accumulated Points</a></li>
						</ul>
					</div>
				</div>								
				<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-chart-line"></i> Update Target</span>
				<a href="new.php?year=<?php echo $nextYear;?>&month=<?php echo $nextMonth;?>" class="btn btn-sm" style="background-color:#54698D;color:white;float:right;margin-right:5%;"><i class="fa fa-chart-line"></i> Generate <?php echo getmonth($nextMonth);?> Target</a>
			</nav>
			<div id="snackbar"><i class="fa fa-chart-pie"></i>&nbsp;&nbsp;Target generated succesfully !!!</div>
			<br/><br/>
			<div class="row" style="margin-left:50%">
				<div style="width:100px;">
					<div class="input-group">
						<select id="jsYear" name="jsYear" class="form-control" onchange="return rerender();">																				<?php
						foreach($yearList as $yearIterator)
						{																																			?>
							<option  <?php if($yearIterator == $year) echo 'selected';?> value="<?php echo $yearIterator;?>"> <?php echo $yearIterator;?> </option>															<?php 
						}																																				?>
						</select>
					</div>
				</div>				
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsMonth" name="jsMonth" class="form-control" onchange="return rerender();">																				<?php
							$monthObjects = mysqli_query($con,"SELECT DISTINCT month FROM target WHERE year = $year ORDER BY month ASC");	
							foreach($monthObjects as $mnth)
							{
								$m = (int)$mnth['month'];																												?>
								<option value="<?php echo $m;?>" <?php if($month == $m) echo 'selected';?>><?php echo getMonth($m);?></option>								<?php
							}																																				?>
						</select>
					</div>
				</div>
			</div>	
			<br/><br/>			
			<form name="arBulkUpdate" method="post" action="updateServer.php">
				<table class="table table-hover table-bordered offset-2" style="margin-left:25%;width:70%">
					<thead>
						<tr class="table-success">
							<th style="width:20%">AR NAME</th>
							<th style="width:30%">SHOP</th>
							<th style="width:30%">MOBILE</th>
							<th style="width:10%">SAP</th>
							<th style="width:10%;text-align:center;">TARGET</th>
							<th style="width:10%;text-align:center;">RATE</th>
							<!--th style="width:10%;text-align:center;">PAYMENT %</th--> 
						</tr>	
					</thead>
					<tbody>	<?php
						$total = 0;	
						while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) 
						{
							$arId = $row['ar_id'];
							$target = $row['target'];
							$total = $total + $target;
							$rate = $row['rate'];
							$pp = $row['payment_perc'];																							?>				
							<tr>
								<td><label align="center"><?php echo $arMap[$arId]; ?></td>	
								<td><label align="center"><?php echo $shopMap[$arId]; ?></td>	
								<td><label align="center"><?php echo $phoneMap[$arId]; ?></td>
								<td><label align="center"><?php echo $codeMap[$arId]; ?></td>						
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-target';?>" value="<?php echo $target; ?>"></td>	
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-rate';?>" value="<?php echo $rate; ?>"></td>
								<!--td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php //echo $arId.'-pp';?>" value="<?php //echo $pp; ?>"></td-->		
							</tr>																												<?php
						}																														?>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th><?php echo $total;?></th>
							<th></th>
							<th></th>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="year" value="<?php echo $year;?>">
				<input type="hidden" name="month" value="<?php echo $month;?>">		
				<br><br>
					<div align="center"><input type="submit" name="submit" value="Submit" onclick="return confirm('Are you sure you want to update?')"></div>		
				<br><br> 
			</form>
		</div>
	</div>
</body>
<script>
$(function(){		
	$(".download-table").tablesorter({
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	});	
});
</script>																																						<?php
}
else
	header("Location:../index/home.php");
