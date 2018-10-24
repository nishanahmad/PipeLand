<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	
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
	
	$engObjects =  mysqli_query($con,"SELECT id,name,mobile,shop_name,sap_code FROM ar_details WHERE type LIKE '%Engineer%' AND isActive = 1 ORDER BY name ASC ") or die(mysqli_error($con));		 
	foreach($engObjects as $eng)
	{
		$engMap[$eng['id']]['name'] = $eng['name'];
		$engMap[$eng['id']]['mobile'] = $eng['mobile'];
		$engMap[$eng['id']]['shop'] = $eng['shop_name'];
		$engMap[$eng['id']]['sap'] = $eng['sap_code'];
	}				
	
	$prevMap = getPrevPoints(array_keys($engMap),$urlYear,$urlMonth);
	
	//var_dump($prevMap);
	
	$engIds = implode("','",array_keys($engMap));
	
	$sales = mysqli_query($con,"SELECT ar_id,SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag) FROM nas_sale WHERE '$urlYear' = year(`entry_date`) AND '$urlMonth' = month(`entry_date`) AND ar_id IN ('$engIds') GROUP BY ar_id") or die(mysqli_error($con));	
	foreach($sales as $sale)
	{
		$engId = $sale['ar_id'];
		
		$total = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
		$pointMap[$engId]['points'] = $total;
	}			
	
	$sales = mysqli_query($con,"SELECT eng_id,SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag) FROM nas_sale WHERE '$urlYear' = year(`entry_date`) AND '$urlMonth' = month(`entry_date`) AND eng_id IN ('$engIds') GROUP BY eng_id") or die(mysqli_error($con));	
	foreach($sales as $sale)
	{
		$engId = $sale['eng_id'];
		
		$total = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
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
<link rel="stylesheet" type="text/css" href="../css/loader.css">	
<link rel="stylesheet" type="text/css" href="../css/responstable.css">
<link rel="stylesheet" type="text/css" href="../css/glow_box.css">
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../js/jquery.floatThead.min.js"></script>
<script src="../js/fileSaver.js"></script>
<script src="../js/tableExport.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$("#loader").hide();
 	$("#button").click(function(){
		$("table").tableExport({
				formats: ["xls"],    // (String[]), filetypes for the export
				bootstrap: false,
				ignoreCSS: ".ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
		});
	});		
	var $table = $('.responstable');
	$table.floatThead();				
} );
function rerender()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
	var month=document.getElementById("jsMonth").value;
	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	$("#main").hide();
	$("#loader").show();
	window.location.href = hrf +"?year="+ year + "&month=" + month;
}
</script>

<title><?php echo getMonth($urlMonth); echo " "; echo $urlYear; ?></title>
</head>
<body>
	<div id="loader" class="loader" align="center" style="background : #161616 url('../images/pattern_40.gif') top left repeat;height:100%">
		<br><br><br><br><br><br><br><br><br><br><br><br>
		<div class="circle"></div>
		<div class="circle1"></div>
		<br>
		<font style="color:white;font-weight:bold">Calculating ......</font>
	</div>
	<div align="center">
		<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='50px' height='50px'/> </a>
		<br><br>
		<select id="jsMonth" name="jsMonth" class="textarea" onchange="return rerender();">																								<?php	
			$monthList = mysqli_query($con, "SELECT DISTINCT month FROM target ORDER BY month ASC" ) or die(mysqli_error($con));	
			foreach($monthList as $month) 
			{	
	?>			<option <?php if($urlMonth == (int)$month['month']) echo 'selected';?> value="<?php echo $month['month'];?>"><?php echo getMonth($month['month']);?></option>															<?php	
			}
	?>	</select>					
			&nbsp;&nbsp;

		<select id="jsYear" name="jsYear" class="textarea" onchange="return rerender();">																				<?php	
			$yearList = mysqli_query($con, "SELECT DISTINCT year FROM target ORDER BY year DESC") or die(mysqli_error($con));	
			foreach($yearList as $year) 
			{
?>				<option <?php if($urlYear == (int)$year['year']) echo 'selected';?> value="<?php echo $year['year'];?>"><?php echo $year['year'];?></option>																			<?php	
			}
?>		</select>

		<br><br>
		
		<img src="../images/excel.png" id="button" height="50px" width="45px" />
		<br/><br/>

		<table id="Points" class="responstable" style="width:70% !important">
		<thead>
			<tr>
				<th style="width:20%;text-align:left;">AR</th>
				<th style="width:12%;">Mobile</th>
				<th style="width:25%;text-align:left;">Shop</th>
				<th style="width:10%;">SAP</th>
				<th>Opng Pnts</th>
				<th>Current Pnts</th>	
				<th>Redeemed Pnts</th>	
				<th>Balance</th>	
			</tr>
		</thead>	
							
																																												<?php
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
				<td><?php echo $detailMap['sap'];?></b></td>
				<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'];?></b></td>
				<td><?php echo $pointMap[$arId]['points'];?></td>
				<td><?php echo $redemptionMap[$arId];?></td>
				<td><?php echo $prevMap[$arId]['prevPoints'] - $prevMap[$arId]['prevRedemption'] + $pointMap[$arId]['points'] - $redemptionMap[$arId];?></td>
				</tr>																																							<?php
			}																																									?>
		</table>
		<br/><br/><br/><br/>
	</div>
</body>
</html>																																											<?php
}
else
	header("../Location:index.php");


function getPrevPoints($engList,$endYear,$endMonth)
{
	require '../connect.php';
	
	$startDate = date("Y-m-d",strtotime('2018-04-01'));
	
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
		
		$sales = mysqli_query($con,"SELECT ar_id,SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$startDate' AND entry_date <= '$endDate' AND ar_id IN ('$engIds') GROUP BY ar_id" ) or die(mysqli_error($con));		 	 
		foreach($sales as $sale)
		{
			$engId = $sale['ar_id'];				
			
			$total = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
			$engMap[$engId]['prevPoints'] = $engMap[$engId]['prevPoints'] + $total;	
		}
		$sales = mysqli_query($con,"SELECT eng_id,SUM(srp),SUM(srh),SUM(f2r),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$startDate' AND entry_date <= '$endDate' AND eng_id IN ('$engIds') GROUP BY eng_id" ) or die(mysqli_error($con));		 	 
		foreach($sales as $sale)
		{
			$engId = $sale['eng_id'];				
			
			$total = $sale['SUM(srp)'] + $sale['SUM(srh)'] + $sale['SUM(f2r)'] - $sale['SUM(return_bag)'];
			$engMap[$engId]['prevPoints'] = $engMap[$engId]['prevPoints'] + $total;	
		}		
		
		
		$redMonth = $endMonth - 1;
		$redemptionList = mysqli_query($con,"SELECT ar_id,SUM(points) FROM redemption WHERE  ( (YEAR(date) = '$endYear' AND MONTH(date) < '$redMonth') OR (YEAR(date) < '$endYear')) AND ar_id IN('$engIds') GROUP BY ar_id") or die(mysqli_error($con));		 	
		foreach($redemptionList as $redemption)
		{
			$engMap[$redemption['ar_id']]['prevRedemption'] = $engMap[$redemption['ar_id']]['prevRedemption'] + $redemption['SUM(points)'];			
		}
	}
	return $engMap;
}
?>