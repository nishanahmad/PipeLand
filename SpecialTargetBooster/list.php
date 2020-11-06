<?php
session_start();
if(isset($_SESSION['user_name']))
{
	require '../connect.php';
    
	$boosts = mysqli_query($con,"SELECT * FROM special_target_booster ORDER BY fromDate DESC") or die(mysqli_error($con));														?>
<html>
	<head>
		<title>Booster List</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dashio.css" rel="stylesheet">
		<link href="../css/dashio-responsive.css" rel="stylesheet">	
		<link href="../css/font-awesome.min.css" rel="stylesheet">		
		<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<style>
		.dataTables_length{
		  display:none;
		}
		.dataTables_paginate{
		  display:none;
		}
		</style>
	</head>
	<body>
		<div class="row content-panel">
			<div class="col-md-12" align="center">
				<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='50px' height='50px'/> </a>
			</div>
		</div>
		<div class="row mt">
			<div class="col-lg-12">
				<div class="content-panel">
					<h2 style="margin-left:37%;" ><i class="fa fa-bolt"></i> Boost Special Target Points</i></h2>
					<br/>
					<a href="new.php?" style="margin-left:45%;" class="btn btn-theme" >Add New</a>
					<section style="margin-top:40px;">
						<form id="searchbox">
							<table class="col-md-offset-3" style="width:40%">
								<tr>
									<td style="width:30%;padding:1px;"><input type="text" data-column="0"  class="form-control" placeholder="FromDate"></td>
									<td style="width:30%;padding:1px;"><input type="text" data-column="1"  class="form-control" placeholder="ToDate"></td>	
									<td style="width:20%;padding:1px;"><input type="text" data-column="2"  class="form-control" placeholder="Acheieved"></td>				
									<td style="width:20%;padding:1px;"><input type="text" data-column="3"  class="form-control" placeholder="Boost"></td>				
								</tr>	
							</table>	
						</form>																					
						<table class="table table-bordered table-striped col-md-offset-3" style="width:40%" id="discounts">
							<thead class="cf">
								<tr>
									<th style="width:30%;">From Date</th>
									<th style="width:30%;">To Date</th>
									<th style="width:20%;">Achieved%</th>
									<th style="width:20%;">Boost%</th>
								</tr>
							</thead>
							<tbody>

							<?php
							foreach($boosts as $boost)
							{																												?>
								<tr>
									<td><?php echo date('d-m-Y',strtotime($boost['fromDate']));?></td>
									<td><?php echo date('d-m-Y',strtotime($boost['toDate']));?></td>
									<td><?php echo $boost['ifAchieved'].'%';?></td>
									<td><?php echo $boost['boost'].'%';?></td>
								</tr>																								<?php
							}																										?>
							</tbody>
						</table>
					</section>
				</div>
			</div>
		</div>
		<script>
		$(document).ready(function() {
			
			var table = $('#discounts').DataTable({
				"iDisplayLength": 10000
			});
				
			$("#discounts_filter").css("display","none");  // hiding global search box
			$('.form-control').on( 'keyup click', function () {   // for text boxes
				var i =$(this).attr('data-column');  // getting column index
				var v =$(this).val();  // getting search input value
				table.columns(i).search(v).draw();
			} );
			$('.select').on( 'change', function () {   // for select box
				var i =$(this).attr('data-column');  
				var v =$(this).val();  
				table.columns(i).search(v).draw();
			} );	

		} );
		</script>
	</body>
</html>
<?php
}
else
	header("Location:../index.php");
?>