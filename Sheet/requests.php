<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
<title>
Sheets
</title>
<style>
.button {
	background: rgb(28, 184, 65);
}
.button-error {
            background: rgb(202, 60, 60); /* this is a maroon */
        }
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
<script>
function deliver(id){
	var qty = window.prompt("Enter number of sheets delivered to this site");		
	if(isNaN(qty))
	{
		alert('Please enter a valid number');
		return false;
	}
	else
	{
		hrf = 'deliver.php?';
		window.location.href = hrf +"id="+ id + "&qty=" + qty;
	}
}
function cancel(id){
	var conf = confirm("This will cancel this request. Are you sure?");
	if(conf)
	{
		hrf = 'cancel.php?';
		window.location.href = hrf +"id="+ id;		
	}
}
</script>
																														<?php
	require '../connect.php';																															
	$sheets = mysqli_query($con,"SELECT * FROM sheet_requests WHERE status IS NULL ORDER BY date ASC" ) or die(mysqli_error($con));		 	 
	foreach($sheets as $sheet)
	{																													?>
		<div class="row">
		  <div class="column" style="background-color:#ddd;">
			<p><?php echo $sheet['area'] .', '.$sheet['location'].', '.$sheet['landmark'];?></p>		  
			<p><?php echo $sheet['masonName'] . ', ' .$sheet['masonPhone'];?>
			<p><?php echo $sheet['customerName'] . ', ' .$sheet['customerPhone'];?>
			<p><?php echo 'Qty:'.$sheet['qty'];?></p>
			<p><?php echo date("d-m-Y",strtotime($sheet['date']));?></p>
			<p><?php echo $sheet['fe'];?></p>
			<div align="center">
				<button class="button pure-button" onclick="deliver(<?php echo $sheet['id'];?>)">Deliver</button>&nbsp;&nbsp;&nbsp;&nbsp;				
				<button class="button-error pure-button" onclick="cancel(<?php echo $sheet['id'];?>)">Cancel</button>
			</div>
			<br/><br/>
		  </div>
		</div>																											<?php	
	}																													?>
</html>																														