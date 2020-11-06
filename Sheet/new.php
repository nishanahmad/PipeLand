<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require 'navbar.php'																														?>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="../css/styles.css" rel="stylesheet" type="text/css">
	<script>
		$(function() {
			var pickerOpts = { dateFormat:"dd-mm-yy"}; 
					
			$( ".datepicker" ).datepicker(pickerOpts);
		
			if(window.location.href.includes('success')){
				var x = document.getElementById("snackbar");
				x.className = "show";
				setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
			}		
		});
	</script>	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Sheet Request New</title>
	</head>
	<body>
		<div id="snackbar"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Request sent successfully !!!</div>
		<form class="form" id="form1" method="post" action="insert.php" autocomplete="off">
			<div style="width:100%;">
				<div align="center" style="padding-bottom:5px;">
					<br/><br/>
					<div class="card" style="width:40%;">
						<div class="card-header" style="background-color:#3498db;font-size:20px;font-weight:bold;color:white">New Sheet Request</div>
						<div class="card-body">
							<br/>
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
									<input name="date" required type="text" class="form-control datepicker"/>
								</div>
							</div>
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="far fa-user"></i>&nbsp;Customer Name</span>
									<input name="customer_name" type="text" class="form-control"/>
								</div>
							</div>
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fas fa-mobile-alt"></i>&nbsp;Customer Phone</span>
									<input name="customer_phone" type="text" class="form-control"/>
								</div>
							</div>
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="far fa-user"></i>&nbsp;Mason Name</span>
									<input name="mason_name" type="text" class="form-control"/>
								</div>
							</div>
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fas fa-mobile-alt"></i>&nbsp;Mason Phone</span>
									<input name="mason_phone" type="text" class="form-control"/>
								</div>
							</div>							
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fab fa-buffer"></i>&nbsp;No.of Bags</span>
									<input name="bags" required type="text" class="form-control"/>
								</div>
							</div>														
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fa fa-address-card-o"></i>&nbsp;Shop</span>
									<input name="shop" type="text" class="form-control"/>
								</div>
							</div>																					
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fas fa-map-marker-alt"></i>&nbsp;Address</span>
									<textarea required name="area" class="form-control"></textarea>
								</div>
							</div>																												
							<div class="col col-md-10 offset-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="far fa-comment-dots"></i>&nbsp;Remarks</span>
									<textarea name="remarks" class="form-control"></textarea>
								</div>
							</div>																																			
							<br/>
							<button type="submit" class="btn" style="width:150px;font-size:18px;background-color:#3498db;color:white;"><i class="fa fa-paper-plane"></i> REQUEST</button>
						</div>
						<div class="card-footer" style="background-color:#3498db;padding:1px;"></div>
					</div>
				</div>
			</div>
			<br/><br/><br/><br/>		
		</form>		
	</body>	
</html>
																										<?php
}	
else
	header("Location:../index.php");