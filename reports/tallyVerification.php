<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../sales/listHelper.php';
	require '../navbar.php';
  
	if(isset($_GET['date']))
		$date = date("Y-m-d", strtotime($_GET['date']));		

	$productMap = getProductDetails($con);
	$rateMap = getRateMap();
	$cdMap = getCDMap();
	$wdMap = getWDMap();	


	$arObjects = mysqli_query($con, "SELECT * FROM ar_details ORDER BY name ASC" ) or die(mysqli_error($con));	
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']]['name'] = $ar['name']; 
		$arMap[$ar['id']]['type'] = $ar['type']; 
	}
				
	function getVerificationStatus($saleId,$con) 
	{
		$result = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = '$saleId'");
		if(mysqli_num_rows($result) > 0)
		{
			$tally = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if($tally['status'] == 'LOCKED')
				return $tally['checked_by'];
			else
				return null;
		}
		else
			return null;
	}

	$productDates = mysqli_query($con, "SELECT * FROM rate ORDER BY date") or die(mysqli_error($con));				 	 
	foreach($productDates as $rate)
		$productDateMap[$rate['product']][] = strtotime($rate['date']);
			
	$userObjects = mysqli_query($con, "SELECT * FROM users" ) or die(mysqli_error($con));
	foreach($userObjects as $user)
		$userMap[$user['user_id']] = $user['user_name'];
		
	$salesList = mysqli_query($con, "SELECT * FROM nas_sale WHERE entry_date = '$date' ORDER BY bill_no ASC" ) or die(mysqli_error($con));
	
	if(isset($_POST['date']))
	{
		$URL='tallyVerification.php?date='.$_POST['date'];
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';		
	}
?>
<html>
<head>
	<title>Tally Verification Page</title>
	<link href="../css/styles.css" rel="stylesheet" type="text/css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>	
	<script>
	$(function() {
		var pickerOpts = { dateFormat:"dd-mm-yy"}; 
		$( "#date" ).datepicker(pickerOpts);
	});
	
	$(document).ready(function() {
		$(".maintable").tablesorter(); 
		var $table = $('.maintable');
	});		

	</script>	
    <style> 
        .header { 
            position: sticky; 
            top:0; 
        } 
		.green{
			font-weight:bold;
			font-style:italic;
			color:LimeGreen			
		}
	</style> 
	
</head>
<body>
<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
	<div style="float:left;margin-left:20px;">
		<form action="salesSummary.php">				
			<button type="submit" class="btn" style="background-color:#54698D;color:white;">
				<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Reports
			</button>				
		</form>
	</div>
	<span class="navbar-brand" style="font-size:25px;margin-right:45%;"><img src="../images/tallyLogo.jpg"/><span>
</nav>
<div align="center">
<br><br>
<form method="post" action="" autocomplete="off">
	<div class="row" style="margin-left:40%">
		<div class="col col-md-3">
			<div class="input-group">
				<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
				<input type="text" required name="date" id="date" class="form-control" value="<?php echo date('d-m-Y',strtotime($date)); ?>">
			</div>
		</div>
		<div class="col col-md-3">
			<div class="input-group">
				<input type="submit" class="btn" style="background-color:#54698D;color:white;" value="Search">		
			</div>
		</div>		
	</div>
</form>
<br>
<table class="maintable table table-hover table-bordered" style="width:60%;margin-left:40px;">
<thead style="position: sticky;top: 0">
	<tr class="table-success">
		<th style="text-align:left;" class="header" scope="col"><i class="far fa-file-alt"></i> Bill</th>
		<th style="text-align:left;" class="header" scope="col"><i class="fa fa-map-o"></i> AR</th>
		<th style="text-align:left;" class="header" scope="col"><i class="fa fa-address-card-o"></i> Customer</th>	
		<th style="width:100px;text-align:left;" class="header" scope="col"><i class="fa fa-shield"></i> Product</th>	
		<th style="width:70px;text-align:center" class="header" scope="col"><i class="fab fa-buffer"></i> Qty</th>
		<th style="width:12%;text-align:center" class="header" scope="col"><i class="fa fa-rupee-sign"></i> Approx.</th>
		<th class="header" scope="col">VerifiedBy</th>
	</tr>
</thead>
<?php
			
	foreach($salesList as $sale)
	{
		$date = $productDateMap[$sale['product']][closestDate($productDateMap[$sale['product']],strtotime($sale['entry_date']))];
		$date = date('Y-m-d',$date);
		
		if(isset($rateMap[$sale['product']][$date]))
			$rate = $rateMap[$sale['product']][$date];
		else
			$rate = 0;
		
		if(isset($cdMap[$sale['product']][$sale['ar_id']][$sale['entry_date']]))
			$cd = $cdMap[$sale['product']][$sale['ar_id']][$sale['entry_date']];
		else
			$cd = 0;
		
		if(isset($wdMap[$sale['product']][$sale['entry_date']]))
			$wd = $wdMap[$sale['product']][$sale['entry_date']];
		else
			$wd = 0;
		
		$finalRate = $rate - $cd - $wd - $sale['discount'];																					?>		
		
		<tr id="<?php echo $sale['sales_id'];?>">
			<td><?php echo $sale['bill_no'];?></td>
			<td><?php echo $arMap[$sale['ar_id']]['name'];?></td>
			<td><?php echo $sale['customer_name'];?></td>
			<td><?php echo $productMap[$sale['product']]['name'];?></td>
			<td style="text-align:center"><b><?php echo $sale['qty'] - $sale['return_bag'];?></b></td>
			<td><?php echo $finalRate * ($sale['qty'] - $sale['return_bag']) - $sale['order_no'] .'/-';?></td>																													<?php
			if(getVerificationStatus($sale['sales_id'],$con) !== null)
			{		
				$userId = getVerificationStatus($sale['sales_id'],$con);																																?>
				<td><font style="font-weight:bold;font-style:italic;"><?php echo $userMap[$userId];?></font></td>																	<?php
			}
			else
			{																																										?>
				<td><button class="btn" value="<?php echo $sale['sales_id'];?>" style="background-color:#E6717C;color:white;" onclick="callAjax(this.value)">Verify</button></td>																											<?php			
			}																																										?>
		</tr>																																										<?php	
	}																																												?>	
</table>
<br><br><br><br><br><br>
</div>
<script>
	function callAjax(saleId){
		$.ajax({
			type: "POST",
			url: "ajax/updateSaleTally.php",
			data:'saleId='+saleId,
			success: function(response){
				if(response != false){
					$('#'+response).find('td').eq(6).text('VERIFIED!');
					$('#'+response).find('td').eq(6).addClass("green")
				}
				else{
					alert('Some error occured. Try again');
					location.reload();
				}
			}
		});	  
	}
</script>
</body>			
<?php
}
else
	header("Location:../index.php");
?>