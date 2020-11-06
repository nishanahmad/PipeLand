<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 21600)) 
{
	session_unset();
	session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require 'listHelper.php';
	require '../navbar.php';
	require 'newModal.php';

	$rateMap = getRates($con);
	$productNamesMap = getProductNames($con);																													?>
	
<html>
	<head>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" ></script>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>	
		<title>Rate</title>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<span class="navbar-brand" style="font-size:25px;margin-left:47%;"><i class="fa fa-rupee-sign"></i> Rate</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:40px;" data-toggle="modal" data-target="#newModal"><i class="fa fa-rupee-sign"></i> New Rate</a>			
		</nav>
		<div style="width:100%;" class="mainbody">	
			<div id="snackbar"><i class="fa fa-rupee-sign"></i>&nbsp;&nbsp;New Rate updated successfully !!!</div>		
			<div align="center">
				<br/><br/>
				<table class="ratetable table table-hover table-bordered" style="width:30%">
					<thead>
						<tr class="table-info">
							<th style="width:100px;"><i class="fa fa-shield"></i> Product</th>
							<th><i class="fa fa-calendar"></i> Start Date</th>
							<th><i class="fa fa-calendar"></i> End Date</th>
							<th style="width:90px;"><i class="fa fa-rupee-sign"></i> Rate</th>
						</tr>
					</thead>
					<tbody><?php				
						foreach($rateMap as $product=>$subMap)
						{
							foreach($subMap as $period=>$rate)
							{
								$startDate = explode(' TO ',$period)[0];
								$endDate = explode(' TO ',$period)[1];																			?>
								<tr>
									<td><?php echo $productNamesMap[$product];?></td>
									<td><?php echo date('d-m-Y',strtotime($startDate));?></td>													<?php 
									if($endDate != 'CURRENT')
									{																											?>
										<td><?php echo date('d-m-Y',strtotime($endDate));?></td>												<?php
									}
									else
									{																											?>
										<td><font style="font-weight:bold;font-style:italic;color:LimeGreen">CURRENT</font></td>																						<?php
									}																											?>
									<td><?php echo $rate.'/-';?></td>
								</tr>																											<?php
							}							
						}																														?>
					</tbody>																														
				</table>
			</div>
			<br/><br/><br/>
		</div>
		<script src="list.js"></script>
	</body>
</html>																																					<?php
}
else
	header("Location:../index.php");																													?>