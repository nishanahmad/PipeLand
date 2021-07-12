<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../css/styles.css" rel="stylesheet" type="text/css">	
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
<title>AR List</title>
</head>
<body>
<div id="main" class="main">
	<aside class="sidebar">
		<nav class="nav">
			<ul>
				<li class="active"><a href="#">AR List</a></li>
				<li><a href="../Target/list.php?">Target</a></li>
				<li><a href="../SpecialTarget/list.php?">Special Target</a></li>
				<li><a href="../redemption/list.php?">Redemption</a></li>
			</ul>
		</nav>
	</aside>
    <div class="container">
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:13%;width:100%">
			<span class="navbar-brand" style="font-size:25px;margin-left:40%;"><i class="fa fa-address-card-o"></i> AR List</span>
		</nav>	
		<div align="center">
		<br/><br/>
		<table class="maintable table table-hover table-bordered" style="width:70%;margin-left:10%;">
		<?php
			$sql = "SELECT * FROM ar_details WHERE type LIKE '%AR%' ORDER BY name ASC";
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));																					?>
			<thead>
				<tr class="table-success">
					<th style="width:20%">Name</th>
					<th style="width:20%">Shop</th>
					<th style="text-align:center;width:8%">SAP</th>
					<th style="text-align:center;width:8%">Old SAP</th>
					<th>Mobile</th>
					<th>Whatsapp</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>																																			<?php
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) 
			{
				$arId = $row['id'];
				$arname = $row['name'];
				$shopName = $row['shop_name'];
				$sapCode = $row['sap_code'];
				$oldSap = $row['old_sap'];
				$dealing = $row['dealing'];
				$mobile = $row['mobile'];
				$whatsapp = $row['whatsapp'];
			?>	
			<tr>
				<td><?php echo $arname; ?></td>	
				<td><?php echo $shopName; ?></td>	
				<td style="text-align:center;width:8%"><label align="center"><?php echo $sapCode; ?></td>	
				<td style="text-align:center;width:8%"><label align="center"><?php echo $oldSap; ?></td>	
				<td style="text-align:center;width:10%"><?php echo $mobile;?></td>		
				<td style="text-align:center;width:10%"><?php echo $whatsapp;?></td>		
				<td style="width:12%"><?php echo $dealing;?></td>	
			</tr>																																			<?php
			}																																																										?>
			</tbody>	
		</table>
		</div>
	</div>
	<br/><br/>
</div>
</body>
<script>
$(document).ready(function() {		
	$("table").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	}); 
} );
</script>
</html>																														<?php

}
else
	header("Location:../index/home.php");
