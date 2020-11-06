<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	
	if($_POST)
	{
		$fromDate = date("Y-m-d", strtotime($_POST["from"]));
		$toDate = date("Y-m-d", strtotime($_POST["to"]));
		
		$query = mysqli_query($con,"INSERT INTO special_target_date (from_date,to_date) VALUES ('$fromDate','$toDate') ") or die(mysqli_error($con));
		
		$URL='insertNewList.php?fromDate='.$fromDate.'&toDate='.$toDate;
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
	}
?>
<head>
	<link href="../css/styles.css" rel="stylesheet" type="text/css">	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
</head>
<body>
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">List</a></li>
					<li><a href="../Target/monthlyPoints.php">Monthly Points</a></li>
					<li><a href="#">Total Points</a></li>
					<li class="active"><a href="#">Special Target</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">		
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12%;width:100%">
				<span class="navbar-brand" style="font-size:25px;margin-left:40%;"><i class="fa fa-chart-pie"></i> Insert Date Range</span>
			</nav>			
			<br/><br/>
			<div align="center">
			<form name="frm" method="post" action="" style="margin-left:20%">
				<input type="text" id="datepicker" name="from" required  class="form-control" style="width:150px;" placeholder="From Date" autocomplete="off"/>
				<br/>
				<input type="text" id="datepicker2" name="to" required  class="form-control" style="width:150px;" placeholder="To date" autocomplete="off"/>
				<br/>
				<input type="submit" name="submit" class="btn btn-success" value="Insert" onclick="return confirm('Do you want to insert new special target range?')">
			</form>
			</div>
		</div>
	</div>
</body>
<script>
$(function() {

var pickerOpts = { dateFormat:"d-mm-yy"}; 
	    	
$( "#datepicker" ).datepicker(pickerOpts);
$( "#datepicker2" ).datepicker(pickerOpts);


});
</script>																							<?php
}
else
	header("Location:../index.php");

