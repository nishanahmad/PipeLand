<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
  	
	if(isset($_GET['from']))
		$fromDate = date("Y-m-d", strtotime($_GET['from']));		
	else
		$fromDate = date("Y-m-d");		

	if(isset($_GET['to']))		
		$toDate = date("Y-m-d", strtotime($_GET['to']));		
	else
		$toDate = date("Y-m-d");		
	
	if(isset($_GET['product']))		
		$product = (float)$_GET['product'];
	else
		$product = 'all';
	
	
	if($product == 'all')
		$salesList = mysqli_query($con, "SELECT ar_id,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$fromDate' AND entry_date <= '$toDate' GROUP BY ar_id" ) or die(mysqli_error($con));
	else
		$salesList = mysqli_query($con, "SELECT ar_id,product,SUM(qty),SUM(return_bag) FROM nas_sale WHERE entry_date >= '$fromDate' AND entry_date <= '$toDate' AND product = $product GROUP BY ar_id,product" ) or die(mysqli_error($con));
		

	$products = mysqli_query($con, "SELECT * FROM products" ) or die(mysqli_error($con));	
	foreach($products as $pro)
	{
		$productMap[$pro['id']] = $pro['name'];
	}
	
	$arObjects = mysqli_query($con, "SELECT * FROM ar_details order by name ASC" ) or die(mysqli_error($con));	
	foreach($arObjects as $ar)
	{
		$arNameMap[$ar['id']] = $ar['name'];
		$arCodeMap[$ar['id']] = $ar['sap_code'];
		$arShopMap[$ar['id']] = $ar['shop_name'];
		$arPhoneMap[$ar['id']] = $ar['mobile'];
	}
	
	$tallyFlag = false;
	if($fromDate == $toDate && $product == 'All')
	{
		$tallyFlag = true;
		$tallyObjects = mysqli_query($con, "SELECT * FROM tally_day_check WHERE date = '$toDate'" ) or die(mysqli_error($con));
		foreach($tallyObjects as $tally)
			$tallyMap[$tally['ar']] = $tally['checked_by'];
	}
	
	$userObjects = mysqli_query($con, "SELECT * FROM users" ) or die(mysqli_error($con));
	foreach($userObjects as $user)
		$userMap[$user['user_id']] = $user['user_name'];	
		
	if($_POST)
	{
		$URL='salesSummary.php?from='.$_POST['fromDate'].'&to='.$_POST['toDate'].'&product='.$_POST['product'];
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';				
	}	
?>
<html>
<head>
	<title>Sales Summary AR</title>
	<link href="../css/styles.css" rel="stylesheet" type="text/css">
	<link href="../css/navbarMobile.css" media="screen and (max-device-width: 768px)" rel="stylesheet" type="text/css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
	<script>
	$(function() {
		var pickerOpts = { dateFormat:"dd-mm-yy"}; 
		$( "#fromDate" ).datepicker(pickerOpts);
		
		var pickerOpts2 = { dateFormat:"dd-mm-yy"}; 
		$( "#toDate" ).datepicker(pickerOpts2);		

		$(".maintable").tablesorter(); 
		var $table = $('.maintable');
	});
	</script>	
    <style> 
		.green{
			font-weight:bold;
			font-style:italic;
			color:LimeGreen			
		}
	</style> 
	
</head>
<body>
<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
	<span class="navbar-brand" style="font-size:25px;margin-left:40%"><i class="fa fa-line-chart"></i> Summary Report</span>
</nav>
<div align="center">
<br/><br/>
<form method="post" action="" autocomplete="off">
	<div class="row" style="margin-left:30%">
		<div style="width:220px;">
			<div class="input-group">
				<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;From</span>
				<input type="text" required name="fromDate" id="fromDate" class="form-control" autocomplete="off" value="<?php echo date('d-m-Y',strtotime($fromDate)); ?>">
			</div>
		</div>
		<div style="width:220px;">
			<div class="input-group">
				<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;To</span>
				<input type="text" required name="toDate" id="toDate" class="form-control" value="<?php echo date('d-m-Y',strtotime($toDate)); ?>">
			</div>
		</div>
		<div style="width:220px;">
			<div class="input-group">
				<span class="input-group-text col-md-6"><i class="fa fa-shield"></i>&nbsp;Product</span>
					<select name="product" id="product" required class="form-control">
						<option value="all">ALL</option>																<?php
						foreach($products as $pro) 
						{																								?>
							<option <?php if($product == $pro['id']) echo 'selected';?> value="<?php echo $pro['id'];?>"><?php echo $pro['name'];?></option>		<?php	
						}																								?>
					</select>					
			</div>
		</div>
	</div>
	<br/>
	<div class="col col-md-2 offset-1">
		<div class="input-group">		
			<input type="submit" class="btn" style="background-color:#54698D;color:white;" value="Search">		
		</div>
	</div>			
	<br/><br/>
</form>																													<br><?php 
if($tallyFlag)
{																															?>
	<a href="tallyVerification.php?date=<?php echo $toDate;?>" class="btn" style="background-color:#E6717C;color:white;float:right;margin-right:30px;">Verify Individual Sale</a><?php
}																																												  ?>

<table class="maintable table table-hover table-bordered" style="width:50%;margin-left:40px;">
<thead style="position: sticky;top: 0">
	<tr class="table-success">
		<th style="text-align:left;" class="header" scope="col"><i class="fa fa-map-o"></i> AR</th>
		<th style="text-align:left;" class="header" scope="col"><i class="fas fa-store"></i> Shop Name</th>	
		<th style="width:12%;" class="header" scope="col"><i class="fa fa-address-card-o"></i> SAP</th>	
		<th style="width:15%;" class="header" scope="col"><i class="fa fa-mobile"></i> Phone</th>
		<th style="width:12%;text-align:center" class="header" scope="col"><i class="fab fa-buffer"></i> Qty</th>					<?php
		if($tallyFlag)
		{																								?>
			<th class="header" scope="col">VerifiedBy</th>				<?php
		}																								?>
	</tr>
</thead>
<?php
	$total = 0;
	foreach($salesList as $arSale)
	{
?>		<tr id="<?php echo $arNameMap[$arSale['ar_id']];?>">
			<td style="text-align:left;"><?php echo $arNameMap[$arSale['ar_id']];?></td>
			<td style="text-align:left;"><?php echo $arShopMap[$arSale['ar_id']];?></td>			
			<td><?php echo $arCodeMap[$arSale['ar_id']];?></td>			
			<td><?php echo $arPhoneMap[$arSale['ar_id']];?></td>						
			<td style="text-align:center"><b><?php echo $arSale['SUM(qty)'];?></b></td>																		<?php
			if($tallyFlag == true)
			{		
				if(isset($tallyMap[$arSale['ar_id']]))
				{		
					$userId = $tallyMap[$arSale['ar_id']];																									?>
					<td><font style="font-weight:bold;font-style:italic;"><?php echo $userMap[$userId];?></font></td>										<?php
				}
				else
				{																																			?>
					<td><button class="btn" value="<?php echo $arSale['ar_id'];?>" style="background-color:#E6717C;color:white;" onclick="callAjax(this.value)">Verify</button></td>																											<?php			
				}
			}																																				?>																																
		</tr>
<?php	
		$total = $total + $arSale['SUM(qty)'] - $arSale['SUM(return_bag)'];
	}
?>	
	<tbody class="tablesorter-no-sort">
		<tr style="line-height:50px;background-color:#BEBEBE !important;font-family: Arial Black;">
			<td colspan="4" style="text-align:right" >TOTAL</td>
			<td colspan="2"><?php echo $total;?></td>
		</tr>
	</tbody>
</table>
<br/><br/><br/>
</div>
<script>
	function callAjax(ar){
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		var date = urlParams.get('to');
		if(!date)
		{
			date = new Date();
			var dd = String(date.getDate()).padStart(2, '0');
			var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = date.getFullYear();

			date = dd + '-' + mm + '-' + yyyy;
		}
		$.ajax({
			type: "POST",
			url: "ajax/updateDayTally.php",
			data:'ar='+ar +'&date='+date,
			success: function(response){
				if(response != false){
					$('#'+response).find('td').eq(5).text('VERIFIED!');
					$('#'+response).find('td').eq(5).addClass("green")
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