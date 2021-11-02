<!DOCTYPE html>
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../sales/listHelper.php';
  	
	$truckNumbersMap = getTruckNumbers($con);
	if(isset($_GET['from']))
		$fromDate = date("Y-m-d", strtotime($_GET['from']));		
	else
		$fromDate = date("Y-m-d");		

	if(isset($_GET['to']))		
		$toDate = date("Y-m-d", strtotime($_GET['to']));		
	else
		$toDate = date("Y-m-d");		
	

	$salesList = mysqli_query($con, "SELECT * FROM nas_sale WHERE deleted IS NULL AND entry_date >= '$fromDate' AND entry_date <= '$toDate' ORDER BY entry_date") or die(mysqli_error($con));
			
	if($_POST)
	{
		$URL='truckReport.php?from='.$_POST['fromDate'].'&to='.$_POST['toDate'];
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';				
	}	
?>
	<html>
		<head>
			<title>Truck Reports</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="../css/styles.css" rel="stylesheet" type="text/css">
			<link href="../css/navbarMobile.css" media="screen and (max-device-width: 768px)" rel="stylesheet" type="text/css">
			<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
			<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
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
				<div class="btn-group" role="group" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Truck Report
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
							<li><a href="salesSummary.php" class="dropdown-item">Summary Report</a></li>							
						</ul>
					</div>
				</div>								
				<span class="navbar-brand" style="font-size:25px;margin-right:45%"><i class="fas fa-truck-moving"></i> Truck Report</span>
			</nav>
			<div style="width:100%;" class="mainbody">	
				<br/><br/>
				<div align="center">
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
									<input type="submit" class="btn" style="background-color:#54698D;color:white;" value="Search">		
								</div>								
							</div>							
						</div>
						<br/>
					</form>																													
					<br/>
					<div class="col-md-7 table-responsive-sm">
					<h4>Total : <span class="total"></span></h4>
					<br/>
					<table class="maintable table table-hover table-bordered table-responsive">
						<thead>
							<tr style="background-color:#F2CF5B">
								<th class="header" scope="col"><i class="fa fa-calendar"></i> Date</th>
								<th class="header" scope="col"><i class="far fa-file-alt"></i> Bill</th>
								<th style="max-width:500px;" class="header" scope="col"><i class="fas fa-map-marker-alt"></i> Address</th>
								<th class="header" scope="col"><i class="fas fa-truck-moving"></i> Truck</th>	
								<th class="header" scope="col"><i class="fa fa-money"></i> Order No</th>	
							</tr>
						</thead>																								
						<tbody class="tablesorter-no-sort">																																<?php
						$total = 0;
						foreach($salesList as $sale)
						{
							if( fnmatch("B*",$sale['bill_no']) || fnmatch("C*",$sale['bill_no']) || fnmatch("GB*",$sale['bill_no']) || fnmatch("GC*",$sale['bill_no']) || fnmatch("PB*",$sale['bill_no']) || fnmatch("PC*",$sale['bill_no']))
							{																																							?>
								<tr>
									<td style="text-align:left;"><?php echo date('d-m-Y',strtotime($sale['entry_date']));?></td>
									<td style="text-align:left;"><?php echo $sale['bill_no'];?></td>
									<td style="width:30%;text-align:left;"><?php echo $sale['address1'];?></td>
									<td style="text-align:left;"><?php if(isset($truckNumbersMap[$sale['truck']])) echo $truckNumbersMap[$sale['truck']];?></td>
									<td style="text-align:left;"><?php if($sale['order_no']) echo $sale['order_no']; else echo '0';?></td>
								</tr>																																				<?php									
							}							
						}																																						?>	
						</tbody>
					</table>
					</div>
				</div>
				<br/><br/><br/>
			</div>
			<script>
				$(function(){					

					var pickerOpts = { dateFormat:"dd-mm-yy"}; 
					$( "#fromDate" ).datepicker(pickerOpts);
					
					var pickerOpts2 = { dateFormat:"dd-mm-yy"}; 
					$( "#toDate" ).datepicker(pickerOpts2);		
										
					var total = 0;
						
					$('.maintable').find('tbody tr:visible').each(function(){
						total += parseFloat( $(this).find('td:eq(4)').text() );
					});
					$('.total').text(total);
						
					$('.maintable').on('initialized filterEnd', function(){
						var total = 0;
						$(this).find('tbody tr:visible').each(function(){
							total += parseFloat( $(this).find('td:eq(4)').text());
						});
						$('.total').text(total);
					})      
					
					$(".maintable").tablesorter({
						dateFormat : "ddmmyyyy",
						theme : 'bootstrap',
						widgets: ['filter'],
						filter_columnAnyMatch: true
					}); 
				}); 					
			</script>
		</body>	
	</html>																																											<?php
}
else
	header("Location:../index.php");	
?>