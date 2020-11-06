<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	
	$yearQuery = mysqli_query($con,"SELECT MAX(year) FROM target")  or die(mysqli_error($con));
	$yearObj = mysqli_fetch_array($yearQuery,MYSQLI_ASSOC);
	$year = $yearObj['MAX(year)'];	
	
	$monthQuery = mysqli_query($con,"SELECT MAX(month) FROM target WHERE year = $year ")  or die(mysqli_error($con));
	$monthObj = mysqli_fetch_array($monthQuery,MYSQLI_ASSOC);
	$month = $monthObj['MAX(month)'];?>

<html>
<head>
<style type="text/css">
a{
  text-decoration:none;
}
</style>
<title>TARGET & POINTS</title>
<link rel="stylesheet" type="text/css" href="../css/index.css" />
</head>
<body>


<div class="background">
</div>
<div class="container">
	<div class="row">
		<a href="../index.php"><img alt='Add' title='Add New' src='../images/homeSilver.png' width='80px' height='80px'/></a>
	</div>
	<hr />
</div>

<div class="row">
	<h1>TARGET & POINTS</h1>
	<br><br> 
	<a href="updatePage.php?year=<?php echo $year;?>&month=<?php echo $month;?>" class="btn lg ghost">VIEW / UPDATE TARGET & RATE</a>
	<br><br><br>	

	<a href="generateDateSelectPage.php" class="btn lg ghost">GENERATE TARGET & RATE</a>
	<br><br><br>

	<a href="monthlyPointsList.php?month=<?php echo $month;?>&year=<?php echo $year;?>" class="btn lg ghost">MONTHLY POINTS AR</a>
	<br><br><br>
	
	<a href="../points_full/mainPage.php?month=<?php echo $month;?>&year=<?php echo $year;?>&dateString=FULL" class="btn lg ghost">ACCUMULATED POINTS AR</a>
	<br><br><br>

	<a href="../targetBags/" class="btn lg ghost">TARGET BAGS</a>
	<br><br><br>	
	
	<a href="../engineers/points.php?" class="btn lg ghost">ENGINEER POINTS</a>
	<br><br><br>		
	
	<a href="../engineers/smsPoints.php?" class="btn lg ghost">ENGINEER SMS</a>
	<br><br><br>			
	
	<a href="companyTarget.php?month=<?php echo $month;?>&year=<?php echo $year;?>" class="btn lg ghost">COMPANY TARGET AR</a>
	<br><br><br>	

</div>

</div>
</body>
</html>
<?php
}
else
	header("Location:../index.php");
?>