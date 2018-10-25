<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	$query = "SELECT * FROM ar_details order by name asc ";
	$arObjects = mysqli_query($con, $query) or die(mysqli_error($con));
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../css/loader.css">
<link rel="stylesheet" type="text/css" href="../css/responstable.css">
<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
<title>AR List</title>
</head>
<body>
<div id="main" class="main">
<div align="center" style="padding-bottom:5px;">
<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='60px' height='60px'/> </a>
<br><br>
</div>
<table align="center" class="responstable" style="width:60%;">
	<tr>
		<th style="width:20%">Name</th>
		<th style="width:20%">Shop</th>
		<th>Mobile</th>
		<th>Status</th>
	</tr>																																						<?php
	foreach($arObjects as $ar) 
	{																																							?>	
		<tr>
		<td><?php echo $ar['name']; ?></td>	
		<td><?php echo $ar['shop_name']; ?></td>	
		<td style="text-align:center;width:10%"><?php echo $ar['mobile'];?></td>		
		<td style="text-align:center;width:8%"><?php if($ar['isActive'] == 1 ) echo 'Active'; else echo 'InActive';?></td>
		</tr>																													<?php
	}																																																										?>
</table>
<br><br>
<div align="center"><input type="submit" name="submit" value="Submit"></div>		
</div>
</body>
</html>																														<?php
}
else
	header("Location:../index.php");
