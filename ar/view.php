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
	<body>
		<style>
		.tbl {
		}
		.tbl td {
		   padding: 5px;
		   width:150px;
		}

		</style>	
		<section class="wrapper">
			<h2><i style="margin-left:150px;" class="fa fa-user"></i>&nbsp;<label id="name"><?php echo $ar['name'];?></label>&nbsp;&nbsp;&nbsp;</h2>
			<div class="col-md-10 col-md-offset-1">	
				<div class="row content-panel">
				  <div class="col-md-6">
					<div class="right-divider" style="height:250px;">
					  <h4 style="margin-left:50px;">
					  <table class="tbl">
						  <tr>
							<td>Name</td>
							<td style="width:300px">: <b><?php echo $ar['name'];?></b></td>
						  </tr>
						  <tr>
							<td>Mobile</td>
							<td>: <b><?php echo $ar['mobile'];?></b></td>
						  </tr>							  
						  <tr>
							<td>Shop Name</td>
							<td>: <b><?php echo $ar['shop_name'];?></b></td>
						  </tr>
						  <tr>
							<td>SAP Code</td>
							<td>: <b> <?php echo $ar['sap_code'];?></b></td>
						  </tr>					  
						  <tr>
							<td>Area</td>
							<td>: <b><?php echo $ar['area'];?></b></td>
						  </tr>					  
						  <tr>
							<td>Type</td>
							<td>: <b> <?php echo $ar['type'];?></b></td>
						  </tr>					  					  
						  <tr>
							<td>Status</td>																								<?php
							if($ar['isActive'])
							{																											?>
								<td>: <b> Active</b></td>																				<?php
							}
							else
							{																											?>
								<td>: <b> Inactive</b></td>																				<?php
							}																											?>
						  </tr>					  					  
						</table>  
					</div>
				  </div>
				  <div class="col-md-4">
					<br/><br/>
					<a  href="/orders/$order->id/edit" class="btn btn-theme" style="width:120px"><i class="fa fa-pencil"></i> Edit AR</a>
					<br/><br/>
					<a type="submit" class="btn btn-danger" id="delete" style="width:120px"><i class="fa fa-times"></i> Deactivate</a>	
				  </div>
				</div>
			</div>

			<div class="col-md-10 col-md-offset-1">	
				<div class="row mt">
					<div class="content-panel">
						<h4><i class="fa fa-gift"></i>&nbsp;&nbsp;Gifts</h4>
						<section id="unseen">
							<table class="table table-bordered table-striped table-condensed">
								<thead>
									<tr>
										<th width="2%;"></th>
										<th>&nbsp;&nbsp;Date</th>
										<th>Category</th>
										<th>Item</th>
										<th>Qty</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><a href="/invoices/ $invoice -> id "><i class="fa fa-pencil"></i></a></td>
										<td> date('d-m-Y',strtotime($invoice -> date))</td>
										<td> $invoice -> number</td>
										<td> $invoice -> qty</td>
										<td> $invoice -> user -> name</td>
										<td> $invoice -> user -> name</td>
									</tr>
								</tbody>
							</table>
							<div align="center">
								<a  href="/gifts/new.php?id=<?php echo $ar['id'];?>" class="btn btn-theme"><i class="fa fa-gift"></i> New Gift</a><br/><br/>
							</div>	
						</section>
					</div>
				</div>
			</div>
		</section>
	</body>
</html>
<?php
}
else
	header("Location:../index.php");
?>