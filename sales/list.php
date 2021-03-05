<!DOCTYPE html>
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require 'listHelper.php';
	require '../navbar.php';
	require 'newModal.php';
	require 'rateModal.php';
	require 'filterModal.php';

	$currentRateMap = getCurrentRates($con);
	$clientNamesMap = getClientNames($con);
	$productDetailsMap = getProductDetails($con);
	$discountMap = getDiscounts($con);
	$truckNumbersMap = getTruckNumbers($con);
	
	$filterSql = null;
	if(isset($_GET['sql']))
		$filterSql = $_GET['sql'];
	
	if(isset($_GET['range']))
		$range = $_GET['range'];
	else
		$range = 'Custom Filter';

	$mainMap = array();
	if(isset($filterSql))
	{
		$productSumMap = getProductSum($con,$filterSql);
		$mainMap = getSales($con,$filterSql);
	}

	$rateMap = getRateMap();
	$cdMap = getCDMap();
	$wdMap = getWDMap();
	
	$productDates = mysqli_query($con, "SELECT * FROM rate ORDER BY date") or die(mysqli_error($con));				 	 
	foreach($productDates as $rate)
		$productDateMap[$rate['product']][] = strtotime($rate['date']);

	$arObjects = mysqli_query($con,"SELECT id,name,type,shop_name FROM ar_details ORDER BY name") or die(mysqli_error($con));	
	foreach($arObjects as $ar)
	{
		if($ar['type'] != 'Engineer Only')
			$arMap[$ar['id']] = $ar['name']; 
		if($ar['type'] == 'Engineer' || $ar['type'] == 'Contractor' || $ar['type'] == 'Engineer Only')
			$engMap[$ar['id']] = $ar['name'];
		
		$shopName = strip_tags($ar['shop_name']); 
		$shopNameMap[$ar['id']] = $shopName;

		$shopNameArray = json_encode($shopNameMap);
		$shopNameArray = str_replace('\n',' ',$shopNameArray);
		$shopNameArray = str_replace('\r',' ',$shopNameArray);		
	};																																				?>	
	
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" media="screen and (max-device-width: 768px)" href="../css/neomorphism.css"/>
		<link href="../css/navbarMobile.css" media="screen and (max-device-width: 768px)" rel="stylesheet" type="text/css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.min.js" integrity="sha512-mWSVYmb/NacNAK7kGkdlVNE4OZbJsSUw8LiJSgGOxkb4chglRnVfqrukfVd9Q2EOWxFp4NfbqE3nDQMxszCCvw==" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.widgets.min.js" integrity="sha512-6I1SQyeeo+eLGJ9aSsU43lGT+w5HYY375ev/uIghqqVgmSPSDzl9cqiQC4HD6g8Ltqz/ms1kcf0takjBfOlnig==" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
		<title>Sales</title>
		<style>
			.select2-selection__rendered {
				line-height: 33px !important;
			}
			.select2-container .select2-selection--single {
				height: 38px !important;
			}
			.select2-selection__arrow {
				height: 37px !important;
			}
			#line{
			   display:block;
			   width:220px;
			   border-top: 1px solid #D3D3D3;
			   margin-top:5px;
			   margin-bottom:5px;
			}			
		</style>			
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div class="btn-group" role="group" style="float:left;margin-left:2%;">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="far fa-calendar-alt"></i> <?php echo $range;?>
					</button>
					<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
						<li id="todayFilter"><a class="dropdown-item">Today</a></li>							
						<li id="10DaysFilter"><a class="dropdown-item">10 Days</a></li>
						<li id="customFilter" class="dropdown-item">Custom Filter</a></li>				
					</ul>
				</div>
			</div>					
			<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-bolt"></i> Sales</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:3%;" data-toggle="modal" data-target="#saleModal"><i class="fa fa-bolt"></i> New Sale</a>			
		</nav>
		<div style="width:100%;" class="mainbody">	
			<div id="snackbar"><i class="fa fa-bolt"></i>&nbsp;&nbsp;Sale detail saved successfully !!!</div>
			<div align="center">
				<br/><br/>
				<table class="ratetable table table-hover table-bordered" <?php if($range == 'Today') echo 'style="width:35%;"'; else echo 'style="width:20%;"';?> id="ratetable">
					<thead>
						<tr class="table-info">
							<th><i class="fa fa-shield"></i> Product</th>
							<th style="width:90px;"><i class="fab fa-buffer"></i> Qty</th>															<?php 
							if($range == 'Today')
							{																														?>
								<th style="width:90px;"><i class="fa fa-rupee-sign"></i> Rate</th>
								<th style="width:110px;"><i class="fa fa-tags"></i> Discount</th>													<?php
							}																														?>
						</tr>
					</thead>
					<tbody id="ratebody"><?php				
						foreach($currentRateMap as $product=>$rate)
						{
							if(isset($productSumMap[$product]))
							{																														?>
								<tr>
									<td><?php echo $productDetailsMap[$product]['name'];?></td>
									<td><?php echo $productSumMap[$product];?></td><?php 
									if($range == 'Today')
									{																												?>
										<td><?php echo $rate.'/-';?></td>
										<td><?php if(isset($discountMap[$product])) echo $discountMap[$product].'/-';?></td>						<?php
									}																												?>	
								</tr>																												<?php
							}	
						}																															?>
					</tbody>																														<?php 
					if($range == 'Today')
					{																																?>
						<tfoot id="ratefoot">
							<tr>
								<td colspan="4" style="text-align:center"><a href="#" class="link-success" data-toggle="modal" data-target="#rateModal">All Product Rates</a></td>
							</tr>			
						</tfoot>																														<?php
					}																																?>
				</table>
			</div>
			<div id="content-desktop">
				<br/><br/>
				<table class="maintable table table-hover table-bordered" style="width:95%;margin-left:2%;">
					<thead>
						<tr class="table-success">
							<th style="min-width:110px;"><i class="far fa-calendar-alt"></i> Date</th>
							<th><i class="fa fa-address-card-o"></i> AR</th>
							<th style="width:70px;"><i class="fa fa-shield"></i> PRO</th>
							<th style="width:70px;"><i class="fab fa-buffer"></i> QTY</th>
							<th style="width:70px;"><i class="fa fa-rupee-sign"></i> RATE</th>
							<th style="width:120px;"><i class="far fa-file-alt"></i> BILL NO</th>
							<th style="width:95px;"><i class="fas fa-truck-moving"></i> TRUCK</th>
							<th style="width:180px;"><i class="far fa-user"></i> CUSTOMER</th>
							<th><i class="far fa-comment-dots"></i> REMARKS</th>
							<th><i class="fas fa-map-marker-alt"></i> ADDRESS</th>
						</tr>	
					</thead>
					<tbody>	<?php
						foreach($mainMap as $index => $sale) 
						{
							$date = $productDateMap[$sale['product']][closestDate($productDateMap[$sale['product']],strtotime($sale['date']))];
							$date = date('Y-m-d',$date);
							
							if(isset($rateMap[$sale['product']][$date]))
								$rate = $rateMap[$sale['product']][$date];
							else
								$rate = 0;
							
							if(isset($cdMap[$sale['product']][$sale['client']][$sale['date']]))
								$cd = $cdMap[$sale['product']][$sale['client']][$sale['date']];
							else
								$cd = 0;
							
							if(isset($wdMap[$sale['product']][$sale['date']]) && $clientTypeMap[$sale['client']] == 'AR/SR')
								$wd = $wdMap[$sale['product']][$sale['date']];
							else
								$wd = 0;
							
							$finalRate = $rate - $cd - $wd - $sale['discount'];																					?>	
							
							<tr data-id="<?php echo $sale['id'];?>" data-params="<?php echo explode('?',$_SERVER['REQUEST_URI'])[1];?>" class="saleId" style="cursor:pointer;">
								<td><?php echo date('d-m-Y',strtotime($sale['date'])); ?></td>
								<td><?php echo $clientNamesMap[$sale['client']]; ?></td>
								<td><?php echo $productDetailsMap[$sale['product']]['name'];?></td>
								<td><?php echo $sale['qty']; ?></td>
								<td><?php if($finalRate > 0 ) echo $finalRate.'/-';?></td>							
								<td><?php echo $sale['bill']; ?></td>
								<td><?php if(isset($truckNumbersMap[$sale['truck_no']])) echo $truckNumbersMap[$sale['truck_no']]; ?></td>
								<td><?php echo $sale['name'].'<br/><font>'.$sale['phone'].'</font>'; ?></td>
								<td><?php echo $sale['remarks']; ?></td>
								<td><?php echo $sale['address']; ?></td>
							</tr>																																		<?php				
						}																																				?>
					</tbody>	
				</table>
			</div>

			<div id="content-mobile">
				<br/><br/>
				<table class="maintable table table-hover table-bordered table-sm" style="width:95%;margin-left:2%;">
					<thead>
						<tr class="table-success">
							<th style="min-width:110px;"><i class="far fa-calendar-alt"></i> Date</th>
							<th><i class="fa fa-address-card-o"></i> AR</th>
							<th style="width:70px;"><i class="fa fa-shield"></i> PRO</th>
							<th style="width:70px;"><i class="fab fa-buffer"></i> QTY</th>
							<th style="width:70px;"><i class="fa fa-rupee-sign"></i> RATE</th>
							<th style="width:120px;"><i class="far fa-file-alt"></i> BILL NO</th>
							<th style="width:95px;"><i class="fas fa-truck-moving"></i> TRUCK</th>
							<th style="width:180px;"><i class="far fa-user"></i> CUSTOMER</th>
						</tr>	
					</thead>
					<tbody>	<?php
						foreach($mainMap as $index => $sale) 
						{
							$date = $productDateMap[$sale['product']][closestDate($productDateMap[$sale['product']],strtotime($sale['date']))];
							$date = date('Y-m-d',$date);
							
							if(isset($rateMap[$sale['product']][$date]))
								$rate = $rateMap[$sale['product']][$date];
							else
								$rate = 0;
							
							if(isset($cdMap[$sale['product']][$sale['client']][$sale['date']]))
								$cd = $cdMap[$sale['product']][$sale['client']][$sale['date']];
							else
								$cd = 0;
							
							if(isset($wdMap[$sale['product']][$sale['date']]) && $clientTypeMap[$sale['client']] == 'AR/SR')
								$wd = $wdMap[$sale['product']][$sale['date']];
							else
								$wd = 0;
							
							$finalRate = $rate - $cd - $wd - $sale['discount'];																					?>	
							
							<tr data-id="<?php echo $sale['id'];?>" data-params="<?php echo explode('?',$_SERVER['REQUEST_URI'])[1];?>" class="saleId" style="cursor:pointer;">
								<td><?php echo date('d-m-Y',strtotime($sale['date'])); ?></td>
								<td><?php echo $clientNamesMap[$sale['client']]; ?></td>
								<td><?php echo $productDetailsMap[$sale['product']]['name'];?></td>
								<td><?php echo $sale['qty']; ?></td>
								<td><?php if($finalRate > 0 ) echo $finalRate.'/-';?></td>							
								<td><?php echo $sale['bill']; ?></td>
								<td><?php if(isset($truckNumbersMap[$sale['truck_no']])) echo $truckNumbersMap[$sale['truck_no']]; ?></td>
								<td><?php echo $sale['name'].'<br/><font>'.$sale['phone'].'</font>'; ?></td>
							</tr>																																		<?php				
						}																																				?>
					</tbody>	
				</table>
			</div>			
			<br/><br/><br/>
		</div>
		<script src="list.js"></script>
		<script src="newModal.js"></script>
		<script>	
			var shopNameList = '<?php echo $shopNameArray;?>';
			var shopName_array = JSON.parse(shopNameList);
			var shopNameArray = shopName_array;											
		</script>
	</body>
</html>																																					<?php
}
else
	header("Location:../index/home.php");																													?>