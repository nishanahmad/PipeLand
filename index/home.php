<!DOCTYPE html>
<?php
session_start();

if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../sales/listHelper.php';   // imported to get clientNamesMap and productDetailsMap
	
	$clientNamesMap = getClientNames($con);
	$productDetailsMap = getProductDetails($con);	
	$unlockedList = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE status = 'UNLOCKED' ORDER BY id") or die(mysqli_error($con));
	
	$users = mysqli_query($con,"SELECT * FROM users") or die(mysqli_error($con));	
	foreach($users as $user)
		$userMap[$user['user_id']] = $user['user_name'];
?>
<html>
	<head>
		<link rel="stylesheet" href="home.css"/>
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<link href="../css/navbarMobile.css" media="screen and (max-device-width: 768px)" rel="stylesheet" type="text/css">
		<title>Home</title>
		<style>

		</style>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<span class="navbar-brand offset-5" style="font-size:25px;"><i class="fa fa-home"></i> Home</span>
		</nav>	
		<br/><br/>
		<div class="container">																															<?php
			foreach($unlockedList as $unlocked)
			{
				$id = $unlocked['sale'];
				$query = mysqli_query($con, "SELECT * FROM nas_sale WHERE sales_id = $id") or die(mysqli_error($con));
				$sale = mysqli_fetch_array($query, MYSQLI_ASSOC);																							?>
				<a class="card" href="../reports/tallyverification.php?date=<?php echo $sale['entry_date'];?>">
					<h3><?php echo $sale['bill_no'];?></h3>
					<p class="small"><?php 
						echo $clientNamesMap[$sale['ar_id']].'<br/>'.date('d-m-Y',strtotime($sale['entry_date'])).'<br/>'.$productDetailsMap[$sale['product']]['name'].' '.$sale['qty'].' bags';?>
					</p>
					Unlocked By : <?php echo $userMap[$unlocked['unlocked_by']];?>
					<div class="dimmer"></div>
					<div class="go-corner" href="#">
						<div class="go-arrow"><i class="fa fa-unlock fa-xs"></i></div>
					</div>
				</a>																																	<?php
			}																																			?>
		</div>	
		<br/><br/><br/><br/>
	</body>
</html>																																					<?php
}
else
	header("Location:../sessions/loginPage.php");																													?>