<?php
session_start();
if(isset($_SESSION["user_name"]))
{					?>
<html>
	<head>
		<style type="text/css">
		a{
		  text-decoration:none;
		}
		</style>	
		<title>Engineers</title>
		<link rel="stylesheet" type="text/css" href="../css/index.css" />
	</head>
	
	<body>
		<div class="background">
		</div>
		<div class="container">
			<div class="row">
				<a href="../index.php"><img alt='Add' title='Add New' src='../images/homeSilver.png' width='80px' height='80px'/></a>
			</div>
		</div>

		<div class="row">
			<h1>ENGINEERS</h1>
			<br><br> 
			<a href="list.php" class="btn lg ghost" >ENGINEER LIST</a>
			<br><br><br>	

			<a href="points.php?" class="btn lg ghost">POINTS</a>
			<br><br><br>
			
			<a href="../redemption" class="btn lg ghost">REDEMPTION</a>
			<br><br><br>			
			
			<a href="ledger.php?" class="btn lg ghost">LEDGER</a>
			<br><br><br>						
		</div>
	</body>
</html>
<?php
}
else
	header("Location:../index.php");
?>