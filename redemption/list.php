<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	
	$type = $_GET['type'];
	
	$arObjects = mysqli_query($con,"SELECT * FROM ar_details WHERE ORDER BY name") or die(mysqli_error($con));
	
	foreach($arObjects as $ar)
		$arMap[$ar['id']] = $ar['name'];
	
	$redemptionList = mysqli_query($con,"SELECT * FROM redemption WHERE ORDER BY date DESC" ) or die(mysqli_error($con));
?>	
<!DOCTYPE html>
<html>
	<title>Redemption List</title>
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
		<script type="text/javascript" language="javascript">
			$(function(){
				$("table").tablesorter({
					dateFormat : "ddmmyyyy",
					theme : 'blue',
					widgets: ['filter'],
					filter_columnAnyMatch: true,
				}); 
			});			
		</script>

	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
				<div class="btn-group" role="group">
					<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						Redemption List
					</button>
					<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
						<li><a class="dropdown-item" href="../engineers/points.php?">Total Points</a></li>							
					</ul>
				</div>
			</div>			
			<span class="navbar-brand" style="font-size:25px;"><i class="fas fa-hand-holding-usd"></i> <?php echo $type;?> Redemption</span>
			<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:40px;" data-toggle="modal" data-target="#newModal"><i class="fas fa-hand-holding-usd"></i> New Redemption</a>			
		</nav>
		<div style="width:100%;" class="mainbody">	
			<div id="snackbar"><i class="fas fa-hand-holding-usd"></i>&nbsp;&nbsp;Redemption inserted successfully !!!</div>		
			<div align="center">
				<br/><br/>
				<table class="maintable table table-hover table-bordered" style="width:70%">
					<thead>
						<tr class="table-info">
							<th style="width:90px">Date</th>
							<th style="width:200px">AR</th>
							<th style="width:50px">Points</th>	
							<th>Remarks</th>			
							<th style="width:90px">Entered On</th>
						</tr>
					</thead>
					<tbody>																														<?php
					foreach($redemptionList as $redemption)
					{																															?>
						<tr>
							<td><?php echo date('d-m-Y',strtotime($redemption['date']));?></td>
							<td><?php echo $arMap[$redemption['ar_id']];?></td>
							<td><?php echo $redemption['points'];?></td>
							<td><?php echo $redemption['remarks'];?></td>
							<td><?php echo date('d-m-Y',strtotime($redemption['entered_on']));?></td>																	
						</tr>																													<?php
					}																															?>
					</tbody>	
			</table>
		</div>
	</body>
	<script src="list.js"></script>
</html>																																														<?php
}
else
	header("Location:../index/home.php");
