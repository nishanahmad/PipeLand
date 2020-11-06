<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require 'dropDownGenerator.php';
	require '../navbar.php';
	
	$today = date("Y-m-d");
	
	if(isset($_GET['year']))
	{
		$year = (int)$_GET['year'];		

		$monthList = getMonths($year);
		if(isset($_GET['month']))
		{
			$month = (int)$_GET['month'];
			if(isset($_GET['dateString']))
				$dateString = $_GET['dateString'];
			else	
			{
				$stringList = getStrings($year,$month);					
				$dateString = end($stringList);					
			}				
		}
		else
		{
			$month = end($monthList);
			$stringList = getStrings($year,$month);					
			$dateString = end($stringList);					
		}
	}
	else
	{
		$sql = mysqli_query($con,"SELECT YEAR(from_date) FROM special_target_date ORDER BY from_date DESC LIMIT 1") or die(mysqli_error($con));	
		$row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
		$year = (int)$row['YEAR(from_date)'];

		$monthList = getMonths($year);		
		$month = end($monthList);

		$stringList = getStrings($year,$month);					
		$dateString = end($stringList);							
	}
	
	$dateArray = explode(" to ",$dateString);
	$from = $dateArray[0];
	$to = $dateArray[1];
	$toString = $to.'-'.$month.'-'.$year;		
	$toDate = date("Y-m-d",strtotime($toString));	
	
	$fromString = $from.'-'.$month.'-'.$year;		
	$fromDate = date("Y-m-d",strtotime($fromString));
		
	$zeroTargetMap = null;
	$zeroTargetList = mysqli_query($con,"SELECT ar_id FROM special_target WHERE  fromDate <= '$fromDate' AND toDate>='$toDate' AND special_target = 0") or die(mysqli_error($con));		 
	foreach($zeroTargetList as $zeroTarget)
	{
		$zeroTargetMap[$zeroTarget['ar_id']] = null;
	}
	
	$zeroTargetIds = implode("','",array_keys($zeroTargetMap));		
	
	$arList = mysqli_query($con,"SELECT id, name, mobile, shop_name FROM ar_details WHERE isActive = 1 AND id NOT IN ('$zeroTargetIds') AND Type LIKE '%AR%' ") or die(mysqli_error($con));		 
	foreach($arList as $arObject)
	{
		$arNameMap[$arObject['id']] = $arObject['name'];
		$arMobileMap[$arObject['id']] = $arObject['mobile'];
		$arShopMap[$arObject['id']] = $arObject['shop_name'];
		$arExtraMap[$arObject['id']] = 0;
	}
	$extraBagsList = mysqli_query($con,"SELECT ar_id,SUM(qty) FROM extra_bags WHERE date >= '$fromDate' AND date <= '$toDate' GROUP BY ar_id") or die(mysqli_error($con));											
	foreach($extraBagsList as $extraBag)
	{
		$arExtraMap[$extraBag['ar_id']] = $extraBag['SUM(qty)'];
	}	
	
	$array = implode("','",array_keys($arNameMap));	
	
	$arTarget = mysqli_query($con,"SELECT ar_id, special_target FROM special_target WHERE  fromDate <= '$fromDate' AND toDate>='$toDate' AND ar_id IN ('$array')") or die(mysqli_error($con));		 
	foreach($arTarget as $splTarget)
	{
		$arTargetMap[$splTarget['ar_id']] = $splTarget['special_target']; 
	}
	
	$ar_detail = mysqli_query($con,"SELECT ar_id, special_target FROM special_target WHERE  fromDate <= '$fromDate' AND toDate>='$toDate' AND ar_id IN ('$array')") or die(mysqli_error($con));		 
	
	if(isset($_GET['removeToday']) && $_GET['removeToday'] == 'true')
	{
		$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$fromDate'
											AND entry_date <= '$toDate' AND entry_date < CURDATE() 
											AND ar_id IN ('$array')
											GROUP BY ar_id")
											or die(mysqli_error($con));												
	}
	else
		$sales = mysqli_query($con,"SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$fromDate'
											AND entry_date <= '$toDate'
											AND ar_id IN ('$array')
											GROUP BY ar_id")
											or die(mysqli_error($con));								
	foreach($sales as $sale)
	{
		$qty = $sale['SUM(qty)'];
		$return_bag = $sale['SUM(return_bag)'];
		$total = $qty - $return_bag;
		$arSaleMap[$sale['ar_id']] = $total;
	}
	if($noticeFlag)
		$dateString = null;
	
	//Calculate boost percentage
	$boostQuery = mysqli_query($con,"SELECT * FROM special_target_booster WHERE fromDate = '$fromDate' AND toDate = '$toDate'") or die(mysqli_error($con));	
	$boost = mysqli_fetch_array($boostQuery,MYSQLI_ASSOC);
	
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
	<title>Special Target</title>
</head>
<body>		
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">AR List</a></li>
					<li><a href="../Target/monthlyPointsList.php">Target</a></li>
					<li class="active"><a href="#">Special Target</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">		
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							View
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
							<li id="update"><a href="updatePage.php" class="dropdown-item">Update</a></li>							
						</ul>
					</div>
				</div>					
				<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-chart-pie"></i> Special Target View</span>
				<a href="special_target_date.php" class="btn btn-sm" style="background-color:#54698D;color:white;float:right;margin-right:5%;"><i class="fa fa-chart-pie"></i> Create New</a>			
			</nav>
			<div id="snackbar"><i class="fa fa-chart-pie"></i>&nbsp;&nbsp;Special target list inserted succesfully !!!</div>
			<br><br>
			<select name="grouping" id="grouping" class="form-control" style="margin-left:40%;width:150px;" onchange="location.href= this.value ">
				 <option selected value="#">No Grouping</option>   								
				 <option value="list_user.php?">User Wise</option>
			</select>		
			<br><br>			
			<div class="row" style="margin-left:20%">
				<div style="width:100px;">
					<div class="input-group">
						<select id="jsYear" name="jsYear" class="form-control" onchange="return refreshYear();">																<?php	
							$yearList = getYears();	
							foreach($yearList as $yr)
							{																																				?>
								<option value="<?php echo $yr;?>" <?php if($year == $yr) echo 'selected';?>><?php echo $yr;?></option>									<?php										
							} 			?>		
						</select>
					</div>
				</div>
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsMonth" name="jsMonth" class="form-control" onchange="return refreshMonth();">																<?php	
							if(!isset($monthList))
								$monthList = getMonths($year);	
							foreach($monthList as $mnth) 
							{																																				?>			
								<option value="<?php echo $mnth;?>" <?php if($month == $mnth) echo 'selected';?>><?php echo getMonth($mnth);?></option>																<?php						
							}?>	
						</select>
					</div>
				</div>
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsDateString" name="jsDateString" class="form-control" onchange="return refreshString();">																	<?php	
							if(!isset($stringList))
								$stringList = getStrings($year,$month);
							foreach($stringList as $string) 
							{																																															?>
								<option value="<?php echo $string;?>" <?php if($dateString == $string) echo 'selected';?>><?php echo $string;?></option>																			<?php						
							}																																					?>																										
						</select>
					</div>
				</div>																																							<?php	
				if($today >= $fromDate && $today <= $toDate)
				{																																						?>
					<div style="width:50px;">
						<div class="form-check">
							<input type="checkbox" name="removeToday" id="removeToday" class="form-check-input" onchange="refresh();">
							<label class="form-check-label" for="removeToday">Yesterday's closing</label>
						</div>
						
					</div>																																				<?php	
				}																																						?>	
			</div>			
			<br><br>
			<table class="maintable table table-hover table-bordered" style="width:90%;margin-left:15%;">
				<thead>
					<tr class="table-success">
						<th style="text-align:left;width:24%;">AR</th>
						<th style="text-align:left;width:27%;">SHOP</th>
						<th style="width:14%;">MOBILE</th>
						<th style="width:8%;">Spcl Target</th>
						<th style="width:8%;">Actual Sale</th>
						<th style="width:8%;">Balance</th>
						<th style="width:8%;">Actual%</th>
						<th style="width:8%;">Extra Bags</th>				
						<th style="width:3%;">Achieved%</th>
						<th style="width:3%;">Points</th>
					</tr>																																
				</thead>
				<tbody>				<?php
				$targetTotal = 0;
				$saleTotal = 0;
				$extraTotal = 0;
				$balanceTotal = 0;
				$pointTotal = 0;
				foreach($arNameMap as $arId =>$arName)
				{		
					if(isset($arTargetMap[$arId]))
						$spclTarget = $arTargetMap[$arId];
					else
						$spclTarget = 0;
					if(isset($arSaleMap[$arId]))
						$sale = $arSaleMap[$arId];
					else
						$sale = 0;
					if(isset($arExtraMap[$arId]))
						$extraBags = $arExtraMap[$arId];
					else
						$extraBags = 0;																													
					
					if($spclTarget != 0)
					{
						$actualPercentage = round(  $sale * 100 / $spclTarget,0);
						$percentage = round(  ($sale + $extraBags) * 100 / $spclTarget,0);
					}
					else
					{
						$actualPercentage = 0;
						$percentage = 0;					
					}
					$balance = $spclTarget-$sale-$extraBags;
					if($balance < 0)
						$balance = 0;																													?>
					<tr align="center">
						<td style="text-align:left;"><?php echo $arName;?></td>
						<td style="text-align:left;"><?php echo $arShopMap[$arId];?></td>
						<td><?php echo $arMobileMap[$arId];?></td>
						<td><?php echo $spclTarget;?></td>
						<td><?php echo $sale;?></td>
						<td><?php echo $balance ?></td>			
						<td><?php echo $actualPercentage.'%'; ?></td>			
						<td><?php echo $extraBags;?></td>
						<td><?php echo $percentage.'%';?></td>																								<?php 
						if($percentage >= 100)
						{
							if(isset($boost) && $actualPercentage >= (float)$boost['ifAchieved'])
							{	
								$pointTotal = $pointTotal + $sale  + round($sale * $boost['boost']/100);																												?>
								<td><?php echo $sale + round($sale * $boost['boost']/100);?></td>																	<?php
							}	
							else	
							{																																
								$pointTotal = $pointTotal + $sale;																							?>
								<td><?php echo $sale;?></td>																								<?php
							}
						}
						else
						{																																	?>
							<td>0</td>																														<?php
						}																																	?>
					</tr>																																	<?php
					$targetTotal = $targetTotal + $spclTarget;
					$saleTotal = $saleTotal + $sale;
					$extraTotal = $extraTotal + $arExtraMap[$arId];
					$balanceTotal = $balanceTotal + $balance;																														
				}
				$actualPercentageTotal = round(  $saleTotal * 100 / $targetTotal,0);																
				$percentageTotal = round(  ($saleTotal + $extraTotal) * 100 / $targetTotal,0);																?>
				</tbody>
				<tfoot>
					<tr style="line-height:50px;background-color:#BEBEBE !important;font-family: Arial Black;">
						<td colspan="3" style="text-align:right;font-size:20px;">Total</td>
						<td style="font-size:15px;"><?php echo $targetTotal;?></td>
						<td style="font-size:15px;"><?php echo $saleTotal;?></td>
						<td style="font-size:15px;"><?php echo $balanceTotal;?></td>
						<td style="font-size:15px;"><?php echo $actualPercentageTotal.'%';?></td>
						<td style="font-size:15px;"><?php echo $extraTotal;?></td>			
						<td style="font-size:15px;"><?php echo $percentageTotal.'%';?></td>						
						<td style="font-size:15px;"><?php echo $pointTotal;?></td>						
					</tr>
				</tfoot>
			</table>
			<br><br><br><br>
		</div>
	</div>
</body>
<script type="text/javascript" language="javascript" >
$(document).ready(function() {		
	$(".maintable tbody tr").each(function(){
		var extra = $(this).find("td:eq(7)").text();   
		if (extra != '0'){
		$(this).addClass('selected');
		}
	});

	$(".maintable").tablesorter({
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	});

	var checkbox = getUrlParameter('removeToday');
	if(checkbox =='true')
		$('#removeToday').prop('checked', true);
	else
		$('#removeToday').prop('checked', false);	
	
			
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
	}			
} );
function refresh()
{
	var removeToday = $('#removeToday').is(':checked');
	
	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	window.location.href = hrf + "?removeToday=" + removeToday;
}

function refreshYear()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
	
	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	
	window.location.href = hrf +"?year="+ year;
}	

function refreshMonth()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
	var month=document.getElementById("jsMonth").value;
	
	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	
	window.location.href = hrf +"?year="+ year + "&month=" + month;
}

function refreshString()
{
	var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
	var month=document.getElementById("jsMonth").value;
	var dateString = document.getElementById("jsDateString").options[document.getElementById("jsDateString").selectedIndex].value;
	
	var hrf = window.location.href;
	hrf = hrf.slice(0,hrf.indexOf("?"));
	
	window.location.href = hrf +"?year="+ year + "&month=" + month + "&dateString=" + dateString;
}

var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;
	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};
</script>
</html>
<?php
}
else
	header("Location:../index/home.php");
?>