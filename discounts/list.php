<!DOCTYPE html><?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require 'listHelper.php';
	require '../navbar.php';
	require 'newModal.php';

	if(isset($_GET['type']))
		$type = $_GET['type'];
	else
		$type = 'Cash Discounts';
	
	if($type == 'Wagon Discounts')
		$wagonDiscountsMap = getWagonDiscounts($con);
	else
		$clientDiscountsMap = getClientDiscounts($con);
	
	
	$productNamesMap = getProductNames($con);
	$clientNamesMap = getClientNames($con);																													?>
	
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
		<title>Discounts</title>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:20px;">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php echo $type;?>
					</button>
					<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">											<?php
						if($type != 'Cash Discounts')
						{																								?>
							<li><a class="dropdown-item" href="list.php">Cash Discounts</a></li>						<?php
						}
						if($type != 'Wagon Discounts')
						{																								?>
							<li><a class="dropdown-item" href="list.php?type=Wagon Discounts">Wagon Discounts</a></li><?php
						}																								?>						
					</ul>
				</div>
			</div>		
			<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-tags"></i> Discounts</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:40px;" data-toggle="modal" data-target="#newModal"><i class="fa fa-tag"></i> New Discount</a>			
		</nav>
		<div style="width:100%;" class="mainbody">	
			<div id="snackbar"><i class="fa fa-tags"></i>&nbsp;&nbsp;New Discount updated successfully !!!</div>
			<div align="center">
				<br/><br/>
				<table class="discounttable table table-hover table-bordered" id="discounttable" style="<?php if($type=='Cash Discounts') echo 'width:55%'; else echo 'width:35%;';?>">
					<thead>
						<tr class="table-info">																						<?php 
							if($type == 'Cash Discounts')
							{																										?>
								<th style="width:200px;"><i class="fa fa-address-card-o"></i> Client</th>							<?php
							}																										?>
							<th style="width:100px;"><i class="fa fa-shield"></i> Product</th>
							<th><i class="fa fa-list-alt"></i> Type</th>
							<th><i class="fa fa-calendar"></i> <?php if($type=='Cash Discounts') echo 'Start Date'; else echo 'Date';?></th>																						<?php 
							if($type == 'Cash Discounts')
							{																										?>
								<th><i class="fa fa-calendar"></i> End Date</th>													<?php
							}																										?>
							<th style="width:75px;"><i class="fa fa-tag"></i> Disc.</th>
						</tr>
					</thead>
					<tbody><?php	
						if(isset($wagonDiscountsMap))
						{
							foreach($wagonDiscountsMap as $product=>$subMap)
							{
								foreach($subMap as $key=>$value)
								{
									$date = explode('--',$value)[0];
									$discount = explode('--',$value)[1];																?>
									<tr>
										<td><?php echo $productNamesMap[$product];?></td>
										<td>Wagon Discount</td>
										<td><?php echo date('d-m-Y',strtotime($date));?></td>
										<td><?php echo $discount.'/-';?></td>
									</tr>																								<?php
								}							
							}																																					
						}																												?>
																																		<?php	
						if(isset($clientDiscountsMap))
						{
							foreach($clientDiscountsMap as $key=>$subMap)
							{
								$product = explode('--',$key)[0];
								$client = explode('--',$key)[1];
								$discType = explode('--',$key)[2];
								foreach($subMap as $period=>$discount)
								{						
									$startDate = explode(' TO ',$period)[0];
									$endDate = explode(' TO ',$period)[1];																											?>
									<tr>
										<td><?php echo $clientNamesMap[$client];?></td>
										<td><?php echo $productNamesMap[$product];?></td>
										<td><?php echo $discType;?></td>
										<td><?php echo date('d-m-Y',strtotime($startDate));?></td>													<?php 
										if($endDate != 'CURRENT')
										{																											?>
											<td><?php echo date('d-m-Y',strtotime($endDate));?></td>												<?php
										}
										else
										{																											?>
											<td><font style="font-weight:bold;font-style:italic;color:LimeGreen">CURRENT</font></td>																						<?php
										}																											?>
										<td><?php echo $discount.'/-';?></td>
									</tr>																								<?php
								}																
							}																																					
						}																												?>						
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