<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	
	$sql = "SELECT * FROM ar_details WHERE type LIKE '%Engineer%' ORDER BY name ASC";
	$engineers = mysqli_query($con, $sql) or die(mysqli_error($con));	
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
<link href="../css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
<script src="../js/TableSorter.js"></script>
<script src="../js/TablesorterWidgets.js"></script>	
<link rel="stylesheet" href="../css/TableSorterBlueTheme.css">						
<title>Engineers List</title>
</head>
<body>
<div id="main" class="main">
<div align="center" style="padding-bottom:5px;">
	<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='60px' height='60px'/> </a>
	<br><br>
	<h1>ENGINEERS LIST</h1>
</div>
<section style="margin-top:20px;margin-bottom:100px;margin-left:470px;margin-right:100px;">
	<table class="tablesorter" style="align:center;width:60%;">
		<thead>
		<tr>
			<th>Name</th>
			<th>Shop</th>
			<th style="width:120px;">Mobile</th>
			<th style="width:20px;">Report</th>
		</tr>
		</thead>
		<tbody>																																<?php
			foreach($engineers as $engineer) 
			{
				$arId = $engineer['id'];
				$arname = $engineer['name'];
				$shopName = $engineer['shop_name'];
				$mobile = $engineer['mobile'];																								?>
				<tr>
					<td><?php echo $arname; ?></td>	
					<td><?php echo $shopName; ?></td>	
					<td><?php echo $mobile;?></td>		
					<td style="text-align:center;"><?php echo $engineer['bag_report'];?></td>		
				</tr>																																	<?php
			}																																			?>
		</tbody>
	</table>
</section>
</div>
</body>
	<script>
	$(document).ready(function() {		
		$("table").tablesorter({
			dateFormat : "ddmmyyyy",
			theme : 'blue',
			widgets: ['filter'],
			filter_columnAnyMatch: true,
		}); 
	} );
	</script>
</html>																														<?php

}
else
	header("Location:../index.php");
