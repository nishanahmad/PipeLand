<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../sales/listHelper.php';

	$clientNamesMap = getClientNames($con);
	$targetBags = mysqli_query($con,"SELECT * FROM targetbags ORDER BY date DESC") or die(mysqli_error($con));		 
?>
<html>
<head>
<link href="../css/styles.css" rel="stylesheet" type="text/css">	
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.2.1/jquery.floatThead.min.js" integrity="sha512-q0XkdCnK0e3QLJgYrtENEEmAv+urSGCQs/xCXF4xs+NoLfNWD+j7iMqNYXtFOQfnYDsfE4Z7phZqaHgYJrGB/g==" crossorigin="anonymous"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$(".maintable").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	});
} );
</script>

<title>Target Bags</title>
<style>

</style>
</head>
<body>
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">AR List</a></li>
					<li class="active"><a href="#">Target</a></li>
					<li><a href="../SpecialTarget/list.php?">Special Target</a></li>
					<li><a href="../redemption/list.php?">Redemption</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Target Bags
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">
							<li><a href="../list.php?" class="dropdown-item">Monthly Points</a></li>
							<li><a href="../points_full/mainPage.php?" class="dropdown-item">Accumulated Points</a></li>
							<li><a href="edit.php?" class="dropdown-item">Update Target</a></li>
						</ul>
					</div>
				</div>					
				<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-chart-line"></i> Target Bags</span>
				<a href="#" class="btn btn-sm" role="button" style="background-color:#54698D;color:white;float:right;margin-right:7%;" data-toggle="modal" data-target="#saleModal"><i class="fa fa-bolt"></i> New</a>			
			</nav>
			<br/><br/>			
			<table class="maintable table table-hover table-bordered" style="width:75%;margin-left:15%;">
				<thead>
					<tr class="table-success">
						<th>Date</th>
						<th>AR</th>
						<th style="width:50px;">Qty</th>
						<th>Remarks</th>
						<th>Entered By</th>
						<th>Entered On</th>
					</tr>
				</thead>
				<tbody>																																	<?php
					foreach($targetBags as $targetBag)
					{																																	?>
						<tr>
							<td><?php echo date('d-m-Y',strtotime($targetBag['date']));?></td>
							<td><?php echo $clientNamesMap[$targetBag['ar_id']];?></td>
							<td style="width:50px;"><?php echo $targetBag['qty'];?></td>
							<td><?php echo $targetBag['remarks'];?></td>
							<td><?php echo $targetBag['entered_by'];?></td>
							<td><?php echo date('d-m-Y',strtotime($targetBag['entered_on']));?></td>
						</tr>																															<?php
					}																																	?>
				</tbody>	
			</table>
		</div>
		<br/><br/><br/><br/>
	</div>
</body>
</html>
<?php
}
else
	header("../Location:index.php");
?>