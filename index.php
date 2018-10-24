<?php
session_start();
if(isset($_SESSION["user_name"]) && $_SESSION["role"] != 'driver')
{																						?>
<html>
<style type="text/css">
a{
  text-decoration:none;
}
</style>
<head>
<title>HOME</title>
<link rel="stylesheet" type="text/css" href="css/index.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="background">
</div>
<div class="container">
  <div class="row">
    <h1><img alt='Add' title='Add New' src='images/logo.png' width='300px' height='50px'/></h1>
    <h4></h4>
  </div>
  <hr/>
</div>
   
<br><br> 

	<div class="row">																	
	
	<a href="sales/todayList.php?ar=all" class="btn lg ghost">TODAY SALES</a>
    <br><br><br>

	<a href="sales/list.php" class="btn lg ghost">ALL SALES</a>
    <br><br><br><br>
		
   	<a href="ar/" class="btn lg ghost">AR DETAILS & POINTS</a>
    <br><br><br>	
	
   	<a href="engineers/" class="btn lg ghost">ENGINEERS</a>
    <br><br><br>		
	
	<a href="reports/" class="btn lg ghost">REPORTS</a>
    <br><br><br>

	<a href="extraBags/" class="btn lg ghost">EXTRA BAGS</a>
	<br><br><br>
		
	<a href="Sheet/" class="btn lg ghost">SHEET DELIVERY</a>
    <br><br><br>		

	</div>
</body>
</html>
<?php
}
else if(isset($_SESSION["user_name"]) && $_SESSION["role"] == 'driver')
	header("Location:Sheet/index.php");
else
	header("Location:loginPage.php");
?>