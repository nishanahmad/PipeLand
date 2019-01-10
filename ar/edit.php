<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
    
	$id = $_GET['id'];
	$sql = mysqli_query($con,"SELECT * FROM ar_details WHERE id='$id'") or die(mysqli_error($con));
	$ar = mysqli_fetch_array($sql,MYSQLI_ASSOC);	
?>
<html>
	<head>
		<title><?php echo $ar['name'];?></title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dashio.css" rel="stylesheet">
		<link href="../css/dashio-responsive.css" rel="stylesheet">	
		<link href="../css/font-awesome.min.css" rel="stylesheet">		
		<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	</head>
	<section class="wrapper">
		<h2><i class="fa fa-user" style="margin-right:.5em;margin-left:.5em;"></i><?php echo $ar['name'];?></h3>
		<div class="row mt">
			<div class="col-lg-8">
				<div class="form-panel">
					<h4 class="mb"><i class="fa fa-angle-right" style="margin-right:.5em;"></i>Edit</h4>
					<form class="form-horizontal style-form"  action="update.php" method="post">
						<input type="hidden" name="id" value="<?php echo $ar['id'];?>">
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Name</label>
							<div class="col-sm-6">
								<input type="text" name="name" value="<?php echo $ar['name'];?>" class="form-control">
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Mobile</label>
							<div class="col-sm-6">
								<input type="text" name="mobile" value="<?php echo $ar['mobile'];?>" class="form-control">
							</div>
						</div>															
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Shop</label>
							<div class="col-sm-6">
								<input type="text" name="shop" value="<?php echo $ar['shop_name'];?>" class="form-control">
							</div>
						</div>																					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">SAP</label>
							<div class="col-sm-6">
								<input type="text" name="sap" value="<?php echo $ar['sap_code'];?>" class="form-control">
							</div>
						</div>																					
						<button type="submit" class="btn btn-primary" style="margin-left:200px;" tabindex="4">Update</button> 
						<a href="view.php?id=<?php echo $ar['id'];?>" class="btn btn-default" style="margin-left:10px;">Cancel</a>
						<br/><br/>
					</form>
				</div>
			</div>
		</div>
	</section>
</html>	
<?php
}
else
	header("Location:../index.php");
?>