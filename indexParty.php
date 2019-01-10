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
<title>Party</title>
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
	
	<a href="ar/list.php" class="btn lg ghost">AR</a>
    <br><br><br>

	<a href="engineers/list.php" class="btn lg ghost">ENGINEERS</a>
    <br><br><br><br>
	
	<a href="redemption/" class="btn lg ghost">POINT REDEMPTION</a>
    <br><br><br><br>	

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