<!DOCTYPE html>
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require 'newModal.php';
	

	$trucks = mysqli_query($con,"SELECT * FROM truck_details ORDER BY driver") or die(mysqli_error($con));																												?>	
<html>
	<head>
    	<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">	
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.min.js" integrity="sha512-mWSVYmb/NacNAK7kGkdlVNE4OZbJsSUw8LiJSgGOxkb4chglRnVfqrukfVd9Q2EOWxFp4NfbqE3nDQMxszCCvw==" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.9.1/jquery.tablesorter.widgets.min.js" integrity="sha512-6I1SQyeeo+eLGJ9aSsU43lGT+w5HYY375ev/uIghqqVgmSPSDzl9cqiQC4HD6g8Ltqz/ms1kcf0takjBfOlnig==" crossorigin="anonymous"></script>		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<title>Trucks</title>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Trucks</button>
					<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
						<li id="trucks"><a class="dropdown-item">Trucks</a></li>
						<li id="loading"><a class="dropdown-item" href="../loading/list.php">Loading</a></li>			
					</ul>
				</div>
			</div>		
			<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-truck"></i> Trucks</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:40px;" data-toggle="modal" data-target="#newModal"><i class="fa fa-truck"></i> New Truck</a>
		</nav>
		<div style="width:100%;" class="mainbody">	
   			<div id="snackbar"><i class="fas fa-dolly"></i>&nbsp;&nbsp;New truck details inserted successfully !!!</div>		
			<div align="center">
				<br/><br/>
				<table class="table table-hover table-bordered" style="width:30%">
					<thead>
						<tr style="background-color:#F2CF5B">
							<th><i class="fa fa-truck"></i> Number</th>
							<th><i class="fa fa-user"></i> Driver</th>
							<th><i class="fa fa-mobile"></i> Phone</th>
						</tr>
					</thead>
					<tbody><?php				
						foreach($trucks as $truck)
						{																														?>
							<tr>
								<td><?php echo $truck['number'];?></td>
								<td><?php echo $truck['driver'];?></td>
								<td><?php echo $truck['phone'];?></td>
							</tr>																												<?php
						}																														?>
					</tbody>																														
				</table>
			</div>
			<br/><br/><br/>	  
		</div>
		<script src="list.js"></script>
	</body>
</html>																																						<?php
}
else
	header("Location:../index.php");																														?>