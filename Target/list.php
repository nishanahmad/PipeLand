<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require '../functions/targetFormula.php';
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

	$mainArray = array();
	if(isset($_GET['year']) && isset($_GET['month']))
	{
		$year = (int)$_GET['year'];
		$month = (int)$_GET['month'];		
	}	
	else
	{
		$year = $latestYear;
		$month = $latestMonth;
	}

	if($year == $latestYear && $month > $latestMonth)
	{
		$URL='list.php?';
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
	}
	
	$zeroTargetMap = array();
	$zeroTargetList = mysqli_query($con,"SELECT ar_id FROM target WHERE year = '$year' AND month  = '$month' AND target = 0") or die(mysqli_error($con));		 
	foreach($zeroTargetList as $zeroTarget)
	{
		$zeroTargetMap[$zeroTarget['ar_id']] = null;
	}
	
	$zeroTargetIds = implode("','",array_keys($zeroTargetMap));	
	
	$arMap = array();
	$arObjects =  mysqli_query($con,"SELECT id,name,mobile,whatsapp,shop_name,sap_code FROM ar_details WHERE id NOT IN ('$zeroTargetIds') AND Type != 'Engineer' ORDER BY name ASC ") or die(mysqli_error($con));		 
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']]['name'] = $ar['name'];
		$arMap[$ar['id']]['mobile'] = $ar['mobile'];
		$arMap[$ar['id']]['whatsapp'] = $ar['whatsapp'];
		$arMap[$ar['id']]['shop'] = $ar['shop_name'];
		$arMap[$ar['id']]['sap'] = $ar['sap_code'];
	}				
	
	$targetMap = array();
	$arIds = implode("','",array_keys($arMap));
	$targetObjects = mysqli_query($con,"SELECT ar_id, target, payment_perc,rate FROM target WHERE  month = '$month' AND Year='$year' AND ar_id IN('$arIds')") or die(mysqli_error($con));		 
	foreach($targetObjects as $target)
	{
		$targetMap[$target['ar_id']]['target'] = $target['target'];
		$targetMap[$target['ar_id']]['rate'] = $target['rate'];
		$targetMap[$target['ar_id']]['payment_perc'] = $target['payment_perc'];
	}
	


	$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE deleted IS NULL AND '$year' = year(`entry_date`) AND '$month' = month(`entry_date`) AND ar_id IN ('$arIds') GROUP BY ar_id") or die(mysqli_error($con));	

	$mainArray = array();
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
			
			$mainArray[$arId]['target'] = $targetMap[$arId]['target'];
			$mainArray[$arId]['actual_sale'] = $total;
			$mainArray[$arId]['targetBags'] = $targetBags;
			$mainArray[$arId]['points'] = $points;
			$mainArray[$arId]['actual_perc'] = $actual_perc;
			$mainArray[$arId]['point_perc'] = $point_perc;
			$mainArray[$arId]['achieved_points'] = $achieved_points;
			$mainArray[$arId]['payment_points'] = $payment_points;			
			$mainArray[$arId]['whatsapp'] = $arMap[$arId]['whatsapp'];		
			$mainArray[$arId]['month'] = $month;
			$mainArray[$arId]['year'] = $year;
		}
	}	
	
	$whatsapp_status = true;
	$whatsappQuery =  mysqli_query($con,"SELECT * FROM whatsapp_status WHERE type = 'target' AND year = $year AND month = $month") or die(mysqli_error($con));
	foreach($whatsappQuery as $status)
		$whatsapp_status = false;
?>
<html>
<head>
<style>
.selected{
	background-color:#ffb3b3 !important;
}
</style>
<link href="../css/styles.css" rel="stylesheet" type="text/css">	
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.2.1/jquery.floatThead.min.js" integrity="sha512-q0XkdCnK0e3QLJgYrtENEEmAv+urSGCQs/xCXF4xs+NoLfNWD+j7iMqNYXtFOQfnYDsfE4Z7phZqaHgYJrGB/g==" crossorigin="anonymous"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	
	$(".maintable tr").each(function(){
		var extra = $(this).find("td:eq(7)").text();   
		if (extra != '0' && extra != ''){
		  $(this).addClass('selected');
		}
	});

	$(".maintable").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	}); 
	
	var $table = $('.maintable');
	
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);	
	}	
} );


function rerender()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;

	var month=document.getElementById("jsMonth").value;

	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	window.location.href = hrf +"?year="+ year + "&month=" + month;
}
</script>

<title><?php echo getMonth($month); echo " "; echo $year; ?></title>
<style>

</style>
</head>
<body>
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
			<div id="snackbar"><i class="fa fa-whatsapp" aria-hidden="true"></i>&nbsp;&nbsp;Message sent successfully !!!</div>
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Monthly Points
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">
							<li><a href="../points_full/mainPage.php?" class="dropdown-item">Accumulated Points</a></li>
							<li><a href="edit.php?" class="dropdown-item">Update Target</a></li>
							<li><a href="../targetBags/list.php" class="dropdown-item">Target Bags</a></li>
						</ul>
					</div>
				</div>					
				<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-chart-line"></i> Monthly Points</span>
				<a href="new.php?year=<?php echo $nextYear;?>&month=<?php echo $nextMonth;?>" class="btn btn-sm" style="background-color:#54698D;color:white;float:right;margin-right:5%;"><i class="fa fa-chart-line"></i> Generate <?php echo getmonth($nextMonth);?> Target</a>
			</nav>
			<br/><br/>			
			<div class="row">
				<div style="width:120px;margin-left:48%">
					<div class="input-group">
						<select id="jsYear" name="jsYear" class="form-select" style="width:200px;" onchange="return rerender();">																				<?php	
							$yearList = mysqli_query($con, "SELECT DISTINCT year FROM target ORDER BY year DESC") or die(mysqli_error($con));
							foreach($yearList as $yearObj) 
							{																																	  ?>				
								<option value="<?php echo $yearObj['year'];?>" <?php if($yearObj['year'] == $year) echo 'selected';?>><?php echo $yearObj['year'];?></option>											<?php	
							}																																	  ?>		
						</select>
					</div>
				</div>				
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsMonth" name="jsMonth" class="form-select" style="width:200px;" onchange="return rerender();">																				<?php	
							$monthObjects = mysqli_query($con,"SELECT DISTINCT month FROM target WHERE year = $year ORDER BY month ASC");	
							foreach($monthObjects as $mnth)
							{
								$m = (int)$mnth['month'];																												?>
								<option value="<?php echo $m;?>" <?php if($m == $month) echo 'selected';?>><?php echo getMonth($m);?></option>				<?php
							}																																?>
						</select>
					</div>
				</div>																																		<?php
				if($whatsapp_status)
				{																																			?>
					<div style="width:150px;">
						<div class="input-group" title="Dear AR, Ur {X} Month Target is {X} Bags. Achieve Ur Target & Earn Full Lakshya Benefits - AR HELP">
							<form method="post" action="whatsapp_target.php">
								<input type='hidden' name='input_name' value="<?php echo htmlentities(serialize($mainArray)); ?>" />
								<button id="whatapp" class="btn" style="width:100px;font-size:18px;background-color:#44C052;color:#F7F7F7" onclick="return confirm('This will send message on whatsapp. Are you sure you want to proceed?')"><i class="fa fa-whatsapp" aria-hidden="true"></i> Target</button>
							</form>
						</div>
					</div>																																	<?php
				}																																			?>
				<div style="width:200px;">
					<div class="input-group" title="DEAR AR, YOUR BALANCE TO ACHIEVE YOUR MONTHLY TARGET OF {MONTH YEAR} IS {X} BAGS. ACHIEVE YOUR TARGET & EARN SPECIAL BENEFITS - AR HELP">
						<form method="post" action="whatsapp_reamining_bags.php">
							<input type='hidden' name='input_name' value="<?php echo htmlentities(serialize($mainArray)); ?>" />
							<button id="whatapp" class="btn" style="width:180px;font-size:18px;background-color:#44C052;color:#F7F7F7" onclick="return confirm('This will send message on whatsapp. Are you sure you want to proceed?')"><i class="fa fa-whatsapp" aria-hidden="true"></i> Remaining Bags</button>
						</form>
					</div>
				</div>																																					
			</div>	
			<br/><br/>
			<table class="maintable table table-hover table-bordered ui-table-reflow" style="width:92%;margin-left:15%;">
			<thead>
				<tr class="table-success">
					<th style="text-align:left;">AR</th>
					<th>Mobile</th>
					<th>Whatsapp</th>
					<th style="text-align:left;width:20%">Shop</th>
					<th>SAP</th>
					<th>Target</th>
					<th>Sale</th>
					<th>Extra</th>
					<th>Rate</th>
					<th>Points</th>
					<th>Actual%</th>	
					<th>Point%</th>	
					<th>Achieved Pnts</th>
					<th>Points</th>	
				</tr>
			</thead>	
								
																																							<?php
				$totalTarget = 0;
				$totalSale = 0;	
				$totalPoints = 0;		
				$totalPaymentPoints = 0;					
				foreach($targetMap as $arId => $targetArray)
				{		
					$target = $targetArray['target'];
					$rate = $targetArray['rate'];
					$payment_perc = $targetArray['payment_perc'];
					$totalTarget = $totalTarget + $target;
					if(!isset($mainArray[$arId]))
					{
						$mainArray[$arId]['actual_sale'] = null;
						$mainArray[$arId]['targetBags'] = null;
						$mainArray[$arId]['points'] = null;
						$mainArray[$arId]['actual_perc'] = null;
						$mainArray[$arId]['point_perc'] = null;
						$mainArray[$arId]['achieved_points'] = null;
						$mainArray[$arId]['payment_points'] = null;
					}																																	
					$totalSale = $totalSale + $mainArray[$arId]['actual_sale'];
					$totalPoints = $totalPoints + $mainArray[$arId]['points'];
					$totalPaymentPoints = $totalPaymentPoints + $mainArray[$arId]['payment_points'];							?>
					<tr align="center">
						<td style="text-align:left;"><?php echo $arMap[$arId]['name'];?></b></td>
						<td><?php echo $arMap[$arId]['mobile'];?></b></td>
						<td><?php echo $arMap[$arId]['whatsapp'];?></b></td>
						<td style="text-align:left;"><?php echo $arMap[$arId]['shop'];?></b></td>
						<td><?php echo $arMap[$arId]['sap'];?></b></td>
						<td><?php echo $target;?></td>
						<td><?php echo $mainArray[$arId]['actual_sale'];?></td>
						<td><?php echo $mainArray[$arId]['targetBags'];?></td>
						<td><?php echo $rate;?></td>
						<td><?php echo $mainArray[$arId]['points'];?></td>
						<td><?php echo $mainArray[$arId]['actual_perc'].'%';?></td>
						<td><?php echo $mainArray[$arId]['point_perc'].'%';?></td>
						<td><?php echo $mainArray[$arId]['achieved_points'];?></td>
						<td><?php echo '<b>'.$mainArray[$arId]['payment_points'].'</b>';?></td>
					</tr>																															<?php
				}																																	?>
				<tfoot>
					<tr>
						<th><!-- AR --></th>
						<th><!-- MOBILE --></th>
						<th><!-- SHOP --></th>
						<th><!-- SAP --></th>
						<th><?php echo $totalTarget;?></th>
						<th><?php echo $totalSale;?></th>
						<th><!-- EXTRA BAGS --></th>
						<th><!-- RATE --></th>
						<th><?php echo $totalPoints;?></th>
						<th><?php if($totalTarget >0) echo round($totalSale/$totalTarget*100,1)?>%</th>
						<th></th>	
						<th></th>
						<th><?php echo $totalPaymentPoints;?></th>
					</tr>
				</tfoot>	
			</table>
		</div>
		<br/><br/><br/><br/>
	</div>
</body>
</html>
<?php
}
else
	header("../Location:index.php");
?>