<!DOCTYPE html>
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../sales/listHelper.php';
	
	$productDetailsMap = getProductDetails($con);
	$namesMap = getClientNames($con);
	$salesList = mysqli_query($con, "select entry_date,ar_id,product,qty,customer_name,customer_phone,address1,remarks FROM nas_sale WHERE deleted IS NULL AND ar_id IN (SELECT id from ar_details where type = 'Engineer') AND entry_date > '2020-12-01'" ) or die(mysqli_error($con));	
  		
?>
	<html>
		<head>
			<title>Sales Summary AR</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="../css/styles.css" rel="stylesheet" type="text/css">
			<link href="../css/navbarMobile.css" media="screen and (max-device-width: 768px)" rel="stylesheet" type="text/css">
			<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
			<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
		</head>
		<body>
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
				<div class="btn-group" role="group" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Export Sales Detail
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
							<li><a href="salesSummary.php" class="dropdown-item">Summary Report</a></li>							
						</ul>						
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
							<li><a href="truckReport.php" class="dropdown-item">Truck Report</a></li>							
						</ul>
					</div>
				</div>								
				<span class="navbar-brand" style="font-size:25px;margin-right:45%"><i class="fa fa-line-chart"></i> Summary Report</span>
			</nav>
			<div style="width:100%;" class="mainbody">	
				<br/><br/>
				<div align="center">
					<br/><br/>
					<table class="maintable table table-hover table-bordered table-responsive">
						<thead>
							<tr class="table-success">
								<th style="text-align:left;" class="header" scope="col">Date</th>
								<th style="text-align:left;" class="header" scope="col">AR</th>
								<th style="text-align:center" class="header" scope="col">Product</th>
								<th style="text-align:left;" class="header" scope="col">Qty</th>	
								<th style="text-align:left;" class="header" scope="col"> Customer</th>	
								<th class="header" scope="col">Phone</th>
								<th class="header" scope="col">Address</th>
								<th class="header" scope="col">Remarks</th>								
							</tr>
						</thead>																								
						<tbody class="tablesorter-no-sort">																		<?php
						foreach($salesList as $sale)
						{																										?>
							<tr>
								<td style="text-align:left;width:120px"><?php echo date('d-m-y',strtotime($sale['entry_date']));?></td>
								<td style="text-align:left;"><?php echo $namesMap[$sale['ar_id']];?></td>
								<td style="text-align:left;"><?php echo $productDetailsMap[$sale['product']]['name'];?></td>
								<td style="text-align:left;"><?php echo $sale['qty'];?></td>
								<td style="text-align:left;"><?php echo $sale['customer_name'];?></td>
								<td style="text-align:left;"><?php echo $sale['customer_phone'];?></td>
								<td style="text-align:left;"><?php echo $sale['address1'];?></td>
								<td style="text-align:left;"><?php echo $sale['remarks'];?></td>
							</tr>																																				<?php	
						}																																						?>	
						</tbody>
					</table>
				</div>
				<br/><br/><br/>
			</div>
		</body>	
	</html>																																											<?php
}
else
	header("Location:../index.php");	
?>