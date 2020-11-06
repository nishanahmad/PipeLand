<!DOCTYPE html>
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../sales/listHelper.php';
	require '../navbar.php';
	require 'newModal.php';
	
	$productDetailsMap = getProductDetails($con);
	$truckNumbersMap = getTruckNumbers($con);

	$zeroBilled = mysqli_query($con,"SELECT * FROM loading WHERE qty = unbilled_qty AND qty > 0 AND qty < 4500 ORDER BY date ASC,time ASC") or die(mysqli_error($con));
	$partialBilled = mysqli_query($con,"SELECT * FROM loading WHERE qty > unbilled_qty AND unbilled_qty > 0 AND qty < 4500 ORDER BY date ASC,time ASC") or die(mysqli_error($con));
	$fullBilled = mysqli_query($con,"SELECT * FROM loading WHERE unbilled_qty = 0 AND DATE(last_updated) = CURDATE() AND qty > 0 AND qty < 4500 ORDER BY time ASC") or die(mysqli_error($con));	?>
	
<html>
	<head>
    	<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">	
		<meta http-equiv="Refresh" content="120"> 
		<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="../css/loading-cards.css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>		
		<title>Loading</title>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Loading</button>
					<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
						<li id="loading"><a class="dropdown-item">Loading</a></li>							
						<li id="trucks"><a class="dropdown-item" href="../trucks/list.php">Trucks</a></li>		
					</ul>
				</div>
			</div>		
			<span class="navbar-brand" style="font-size:25px;"><i class="fas fa-dolly"></i> Loading</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:40px;" data-toggle="modal" data-target="#newModal"><i class="fas fa-dolly"></i> New Loading</a>			
		</nav>
		<div style="width:100%;" class="mainbody">	
   			  <div id="snackbar"><i class="fas fa-dolly"></i>&nbsp;&nbsp;New Loading inserted successfully !!!</div>		
			  <div id="main" class="row">
				  <div class="col-sm">
					  <div class="header">
						<h2>Unassigned Trucks</h2>
					  </div>				  				  
					  <div class="card">
						<div class="card-body">																												<?php
						  foreach($zeroBilled as $load)
						  {																																	?>
							  <ul class="list-group">
								<li class="list-group-item list-group-item-danger" style="text-align:center;"><i class="fa fa-truck-moving"></i>   <font style="font-size:20px;"><?php echo $truckNumbersMap[$load['truck']];?></font></li>
								<li class="list-group-item list-group-item-danger"><i class="fa fa-shield"></i> <?php echo $productDetailsMap[$load['product']]['name'];?></li>
								<li class="list-group-item list-group-item-danger"><i class="fab fa-buffer"></i> Qty : <?php echo $load['qty'] - $load['unbilled_qty'].'/'.$load['qty'];?>
									<i class="far fa-arrow-alt-circle-down loadId" data-id="<?php echo $load['id'];?>" title="Unload" style="font-size:18px;float:right;cursor:pointer;"></i>
								</li>	
								<li class="list-group-item list-group-item-danger"><i class="fa fa-calendar"></i>   Loaded on : <?php echo date('d-M',strtotime($load['date']));?>&nbsp;&nbsp;&nbsp;<i class="far fa-clock"></i> <?php echo date('h:i A',strtotime($load['time']));?></li>
							  </ul>																															<?php
						  }																																	?>
						</div>
					  </div>
				  </div>
				  <div class="col-sm">
					  <div class="header">
						<h2>Partially Assigned</h2>
					  </div>				  
					  <div class="card">
						<div class="card-body">																												<?php
						  foreach($partialBilled as $load)
						  {																																	?>
							  <ul class="list-group">
								<li class="list-group-item list-group-item-warning" style="text-align:center;"><i class="fa fa-truck-moving"></i>   <font style="font-size:20px;"><?php echo $truckNumbersMap[$load['truck']];?></font></li>
								<li class="list-group-item list-group-item-warning"><i class="fa fa-shield"></i> <?php echo $productDetailsMap[$load['product']]['name'];?></li>
								<li class="list-group-item list-group-item-warning"><i class="fab fa-buffer"></i> Qty : <?php echo $load['qty'] - $load['unbilled_qty'].'/'.$load['qty'];?>
									<i class="far fa-arrow-alt-circle-down loadId" data-id="<?php echo $load['id'];?>" title="Unload" style="font-size:18px;float:right;cursor:pointer;"></i>
								</li>
								<li class="list-group-item list-group-item-warning"><i class="fa fa-calendar"></i>   Loaded on : <?php echo date('d-M',strtotime($load['date']));?>&nbsp;&nbsp;&nbsp;<i class="far fa-clock"></i> <?php echo date('h:i A',strtotime($load['time']));?></li>
							  </ul>																															<?php
						  }																																	?>
						</div>
					  </div>
				  </div>
				  <div class="col-sm">
					  <div class="header">
						<h2>Cleared Trucks</h2>
					  </div>				  				  
					  <div class="card">
						<div class="card-body">																												<?php
						  foreach($fullBilled as $load)
						  {																																	?>
							  <ul class="list-group">
								<li class="list-group-item list-group-item-success" style="text-align:center;"><i class="fa fa-truck-moving"></i>   <font style="font-size:20px;"><?php echo $truckNumbersMap[$load['truck']];?></font></li>
								<li class="list-group-item list-group-item-success"><i class="fa fa-shield"></i> <?php echo $productDetailsMap[$load['product']]['name'];?></li>
								<li class="list-group-item list-group-item-success"><i class="fab fa-buffer"></i> Qty : <?php echo $load['qty'] - $load['unbilled_qty'].'/'.$load['qty'];?></li>
								<li class="list-group-item list-group-item-success"><i class="fa fa-calendar"></i>   Cleared on : <?php echo date('d-M',strtotime($load['last_updated']));?>&nbsp;&nbsp;&nbsp;<i class="far fa-clock"></i> <?php echo date('h:i A',strtotime($load['last_updated']));?></li>
							  </ul>																															<?php
						  }																																	?>
						</div>
					  </div>
				  </div>
			</div>	  
		</div>
		<script src="list.js"></script>
	</body>
</html>																																						<?php
}
else
	header("Location:../index.php");																														?>