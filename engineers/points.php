<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require '../navbar.php';
	
	$mainArray = array();
	if(isset($_GET['year']) && isset($_GET['month']))
	{
		$urlYear = (int)$_GET['year'];
		$urlMonth = (int)$_GET['month'];
	}	
	else
	{
		$urlYear = (int)date("Y");
		$urlMonth = (int)date("m");
	}
	
	$engObjects =  mysqli_query($con,"SELECT id,name,mobile,shop_name,bag_report FROM ar_details WHERE type LIKE '%Engineer%' AND isActive = 1 ORDER BY name ASC ") or die(mysqli_error($con));
	foreach($engObjects as $eng)
	{
		$engMap[$eng['id']]['name'] = $eng['name'];
		$engMap[$eng['id']]['mobile'] = $eng['mobile'];
		$engMap[$eng['id']]['shop'] = $eng['shop_name'];
		$engMap[$eng['id']]['bag_report'] = $eng['bag_report'];
	}				
	
	$prevMap = getPrevPoints(array_keys($engMap),$urlYear,$urlMonth);
	
	//var_dump($prevMap);
	
	$engIds = implode("','",array_keys($engMap));
	
	$sales = mysqli_query($con,"SELECT ar_id,product,SUM(qty),SUM(return_bag) FROM nas_sale WHERE '$urlYear' = year(`entry_date`) AND '$urlMonth' = month(`entry_date`) AND ar_id IN ('$engIds') GROUP BY ar_id,product") or die(mysqli_error($con));	
	foreach($sales as $sale)
	{
		$engId = $sale['ar_id'];
		
		if($sale['product'] == 4)
			$total = ($sale['SUM(qty)'] - $sale['SUM(return_bag)'])*3;
		else
			$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];		
		
		if(isset($pointMap[$engId]['points']))
			$pointMap[$engId]['points'] = $pointMap[$engId]['points'] + $total;
		else
			$pointMap[$engId]['points'] = $total;
	}			
	
	$sales = mysqli_query($con,"SELECT eng_id,product,SUM(qty),SUM(return_bag) FROM nas_sale WHERE '$urlYear' = year(`entry_date`) AND '$urlMonth' = month(`entry_date`) AND eng_id IN ('$engIds') GROUP BY eng_id,product") or die(mysqli_error($con));	
	foreach($sales as $sale)
	{
		$engId = $sale['eng_id'];
		
		if($sale['product'] == 4)
			$total = ($sale['SUM(qty)'] - $sale['SUM(return_bag)'])*3;
		else
			$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];		
		
		if(isset($pointMap[$engId]['points']))
			$pointMap[$engId]['points'] = $pointMap[$engId]['points'] + $total;
		else
			$pointMap[$engId]['points'] = $total;
	}				
	
	$currentRedemption = mysqli_query($con,"SELECT ar_id,SUM(points) FROM redemption WHERE '$urlYear' = year(`date`) AND '$urlMonth' = month(`date`) AND ar_id IN ('$engIds') GROUP BY ar_id") or die(mysqli_error($con));	
	foreach($currentRedemption as $redemption)
	{
		$redemptionMap[$redemption['ar_id']] = $redemption['SUM(points)'];
	}
?>
<html>
<head>
<title><?php echo getMonth($urlMonth); echo " "; echo $urlYear; ?></title>
<link href="../css/styles.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
</head>
<body>
<div class="main">
	<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
		<span class="navbar-brand" style="font-size:25px;margin-left:45%;"><i class="fa fa-hard-hat"></i> Engineers</span>
	</nav>
	<div align="center">
		<br><br>
		<div class="form-group row">
			<div style="width:200px;margin-left:40%">
				<div class="input-group mb-3">
						<select id="jsMonth" name="jsMonth" class="form-control col-md-4" onchange="return rerender();">																								<?php	
							$monthList = mysqli_query($con, "SELECT DISTINCT month FROM target ORDER BY month ASC" ) or die(mysqli_error($con));	
							foreach($monthList as $month) 
							{																																											?>			
								<option <?php if($urlMonth == (int)$month['month']) echo 'selected';?> value="<?php echo $month['month'];?>"><?php echo getMonth($month['month']);?></option>															<?php	
							}																																											?>	
						</select>					
				</div>
			</div>
			<div style="width:150px">
				<div class="input-group mb-3">
					<select id="jsYear" name="jsYear" class="form-control" onchange="return rerender();">																				<?php	
						$yearList = mysqli_query($con, "SELECT DISTINCT year FROM target ORDER BY year DESC") or die(mysqli_error($con));	
						foreach($yearList as $year) 
						{																																								?>
							<option <?php if($urlYear == (int)$year['year']) echo 'selected';?> value="<?php echo $year['year'];?>"><?php echo $year['year'];?></option>																			<?php	
						}																																								?>		
					</select>
				</div>
			</div>
		</div>
		<br/><br/>
		<table class="maintable table table-hover table-bordered" style="width:70%;margin-left:40px;">
		<thead>
			<tr class="table-success">
				<th style="width:20%;text-align:left;">Engineer</th>
				<th style="width:12%;">Mobile</th>
				<th style="width:25%;text-align:left;">Shop</th>
				<th style="width:10%;">ReportedTo</th>
				<th>Opng Pnts</th>
				<th>Current Pnts</th>	
				<th>Redeemed Pnts</th>	
				<th>Balance</th>	
			</tr>
		</thead>
		<tbody>																																						<?php
			$openingTotal = 0;
			$currentTotal = 0;
			$redeemedTotal = 0;
			$balanceTotal = 0;																																												
			foreach($engMap as $arId => $detailMap)
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
					<td style="text-align:left;"><?php echo $detailMap['shop'];?></b></td>
					<td><?php echo $detailMap['bag_report'];?></b></td>
					<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'];?></b></td>
					<td><?php echo $pointMap[$arId]['points'];?></td>
					<td><?php echo $redemptionMap[$arId];?></td>
					<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'] + $pointMap[$arId]['points'] - $redemptionMap[$arId];?></td>
				</tr>																																							<?php
				$openingTotal = $openingTotal + $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'];
				$currentTotal = $currentTotal + $pointMap[$arId]['points'];
				$redeemedTotal = $redeemedTotal + $redemptionMap[$arId];
				$balanceTotal = $balanceTotal + $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'] + $pointMap[$arId]['points'] - $redemptionMap[$arId];			
			}																																									?>
			</tbody>
			<tfoot>
				<tr>
					<th style="width:20%;text-align:left;"></th>
					<th style="width:12%;"></th>
					<th style="width:25%;text-align:left;"></th>
					<th style="width:10%;"></th>
					<th><?php echo $openingTotal;?></th>
					<th><?php echo $currentTotal;?></th>	
					<th><?php echo $redeemedTotal;?></th>	
					<th><?php echo $balanceTotal;?></th>	
				</tr>
			</tfoot>																															
		</table>
		<br/><br/><br/><br/>
	</div>
</div>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$(".maintable").tablesorter({
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
		window.location.href = hrf +"?year="+ year + "&month=" + month;
	}
	</script>	
</body>
</html>																																											<?php
}
else
	header("../Location:index.php");


function getPrevPoints($engList,$endYear,$endMonth)
{
	require '../connect.php';
	
	$startDate = date("Y-m-d",strtotime('2018-04-01'));
	$urlEndMonth = $endMonth;
	
	foreach($engList as $engId)
	{
		$engMap[$engId]['prevPoints'] = 0;	
		$engMap[$engId]['prevRedemption'] = 0;			
	}
	
	if($endYear >= 2018)
	{
		if($endMonth > 1)
			$endMonth = $endMonth - 1;
		else
		{
			$endMonth = 12;
			$endYear = $endYear - 1;
		}

		$days = cal_days_in_month(CAL_GREGORIAN,$endMonth,$endYear);
		$endDate = date("Y-m-d",strtotime($endYear.'-'.$endMonth.'-'.$days));
		
		$engIds = implode("','",array_keys($engMap));	
		
		$sales = mysqli_query($con,"SELECT ar_id,product,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$startDate' AND entry_date <= '$endDate' AND ar_id IN ('$engIds') GROUP BY ar_id,product" ) or die(mysqli_error($con));		 	 
		foreach($sales as $sale)
		{
			$engId = $sale['ar_id'];				
			
			if($sale['product'] == 4)
				$total = ($sale['SUM(qty)'] - $sale['SUM(return_bag)'])*3;
			else
				$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];		
		
			$engMap[$engId]['prevPoints'] = $engMap[$engId]['prevPoints'] + $total;	
		}
		$sales = mysqli_query($con,"SELECT eng_id,product,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$startDate' AND entry_date <= '$endDate' AND eng_id IN ('$engIds') GROUP BY eng_id,product" ) or die(mysqli_error($con));		 	 
		foreach($sales as $sale)
		{
			$engId = $sale['eng_id'];				
			
			if($sale['product'] == 4)
				$total = ($sale['SUM(qty)'] - $sale['SUM(return_bag)'])*3;
			else
				$total = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];		
			
			$engMap[$engId]['prevPoints'] = $engMap[$engId]['prevPoints'] + $total;	
		}		
		
		$redemptionList = mysqli_query($con,"SELECT ar_id,SUM(points) FROM redemption WHERE  ( (YEAR(date) = '$endYear' AND MONTH(date) <= '$endMonth') OR (YEAR(date) < '$endYear')) AND ar_id IN('$engIds') GROUP BY ar_id") or die(mysqli_error($con));		 	
		foreach($redemptionList as $redemption)
		{
			$engMap[$redemption['ar_id']]['prevRedemption'] = $engMap[$redemption['ar_id']]['prevRedemption'] + $redemption['SUM(points)'];			
		}
	}
	return $engMap;
}
?>