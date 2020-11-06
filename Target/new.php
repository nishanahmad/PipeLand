<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../functions/monthMap.php';
	require '../functions/targetIds.php';
	require '../functions/zeroTargetIds.php';
	
	$year = $_GET['year'];
	$month = $_GET['month'];

	if($month == 1)
	{
		$lastTargetYear = $year -1;
		$lastTargetMonth = 12;	
	}
	else
	{
		$lastTargetYear = $year;
		$lastTargetMonth = $month - 1;
	}

	$arObjects = mysqli_query($con, "SELECT id,name FROM ar_details WHERE type = 'AR/SR' ORDER BY name asc") or die(mysqli_error($con));
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']] = $ar['name'];
	}
	
	$targetIds = getTargetIds($lastTargetYear,$lastTargetMonth,$con);
	$targetIds = implode("','",$targetIds);
	
	$zeroTargetIds = getZeroTargetIds($lastTargetYear,$lastTargetMonth,$con);
	
?>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/styles.css" />
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>		
	<title>AR List</title>
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
				<span class="navbar-brand" style="font-size:25px;margin-right:25%"><i class="fa fa-chart-line"></i> Generate Target - <font style="color:limegreen"><b><?php echo getMonth($month).' '.$year;?><b></font></span>
			</nav>
			<br/><br/>
			<form name="arBulkUpdate" method="post" action="insert.php">
				<table class="table table-hover table-bordered offset-2" style="margin-left:35%;width:45%">
					<thead>
						<tr class="table-success">
							<th style="width:40%">AR NAME</th>
							<th style="width:20%;text-align:center;">TARGET</th>
							<th style="width:20%;text-align:center;">RATE</th>
							<th style="width:20%;text-align:center;">PAYMENT %</th> 
						</tr>
					</thead>
					<tbody>																													<?php
						$targetList = mysqli_query($con, "SELECT * FROM target t LEFT JOIN ar_details a ON t.ar_id = a.id WHERE t.year = $lastTargetYear AND t.month = $lastTargetMonth AND t.ar_id IN ('$targetIds') ORDER BY a.name") or die(mysqli_error($con));
						foreach($targetList as $row) 
						{
							$arId = $row['ar_id'];
							$target = $row['target'];
							$rate = $row['rate'];
							$pp = $row['payment_perc'];																						?>				
							<tr>
								<td><label align="center"><?php echo $arMap[$arId]; ?></td>	
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-target';?>" value="<?php echo $target; ?>"></td>	
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-rate';?>" value="<?php echo $rate; ?>"></td>		
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-pp';?>" value="<?php echo $pp; ?>"></td>		
							</tr>																												<?php
						}
						foreach($zeroTargetIds as $arId) 
						{																							?>				
							<tr>
								<td><label align="center"><?php echo $arMap[$arId]; ?></td>	
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-target';?>" value="0"></td>	
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-rate';?>" value="0"></td>		
								<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-pp';?>" value="100"></td>		
							</tr>																												<?php
						}																													?>						
						<input type="hidden" name="year" value="<?php echo $year;?>">
						<input type="hidden" name="month" value="<?php echo $month;?>">
					</tbody>
				</table>
				<br/>
				<input type="submit" class="btn btn-success" style="margin-left:54%" name="submit" value="Generate">
				<br/><br/><br/><br/>
			</form>
		</div>
	</div>	
</body>																														<?php
}
else
	header("Location:../index/home.php");

?>