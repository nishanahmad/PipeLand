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
	<script>
	$(document).ready(function() {
			var pickerOpts = { dateFormat:"dd-mm-yy"}; 					
			$( "#datepicker" ).datepicker(pickerOpts);
			
			$('#item').hide();
			$("#pointLabel").html('Points');
			
			$('#category').on('change',function(){
				if( $(this).val()!="amazon"){
					$("#item").show()
					$("#pointLabel").html('Quantity');
				}
				else{
					$("#item").hide();
					$("#pointLabel").html('Points');
				}
			});			
		});	
		


	</script>   
	<section class="wrapper">
		<h2><i class="fa fa-gift" style="margin-right:.5em;margin-left:.5em;"></i>New Gift</h3>
		<div class="row mt">
			<div class="col-lg-8">
				<div class="form-panel">
					<h4 class="mb"><i class="fa fa-angle-right" style="margin-right:.5em;"></i>Enter details</h4>
					<form class="form-horizontal style-form"  action="insert.php" method="post">
						<input type="hidden" name="ar" value="<?php echo $ar['id'];?>">
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Date</label>
							<div class="col-sm-6">
									<input type="text" name="date" value="<?php echo date('d-m-Y');?>" class="form-control" id="datepicker" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">AR</label>
							<div class="col-sm-6">
								<input type="text" readonly value="<?php echo $ar['name'];?>" class="form-control">
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Category</label>
							<div class="col-sm-6">
								<select name="category" id="category" class="form-control" required>
									<option value="amazon">AMAZON Card</option>
									<option value="nas">NAS Gift</option>
									<option value="acc">ACC Gift</option>
								</select>
							</div>
						</div>											
						<div class="form-group" id="item">
							<label class="col-sm-2 col-sm-2 control-label">Item</label>
							<div class="col-sm-6">
								<input type="text" name="item" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label"><span id="pointLabel"></span></script></label>
							<div class="col-sm-6">
								<input type="text" name="qty" class="form-control" required>
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Remarks</label>
							<div class="col-sm-6">
								<input type="text" name="remarks" class="form-control">
							</div>
						</div>										
						<button type="submit" class="btn btn-primary" style="margin-left:200px;" tabindex="4">Insert Gift</button> 
						<a href="../view.php?id=<?php echo $ar['id'];?>" class="btn btn-default" style="margin-left:10px;" tabindex="5">Cancel</a>
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