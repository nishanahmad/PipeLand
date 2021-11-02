<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{		
	$urlsql = $_GET['sql'];
	$urlrange = $_GET['range'];
		
	$holdingQuery = mysqli_query($con,"SELECT * FROM holdings WHERE returned_sale =".$_GET['sales_id']) or die(mysqli_error($con));
	if(mysqli_num_rows($holdingQuery) > 0 )
		$holding = mysqli_fetch_array($holdingQuery,MYSQLI_ASSOC);	
	?>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<div class="modal fade" id="holdingModal">
	  <div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#708090;color:white">					
				<h4 class="modal-title"><i class="fas fa-box"></i>&nbsp;&nbsp;Return bags to Hold</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<input hidden name="sheetSql" value="<?php echo $urlsql;?>">
			<input hidden name="sheetRange" value="<?php echo $urlrange;?>">	
			<input hidden id="hproduct" value="<?php echo $row['product'];?>">
			<input hidden id="har" value="<?php echo $row['ar_id'];?>">
			  <div class="modal-body">
				  <input type="text" hidden name="returnId" id="returnId" value="<?php echo $row['sales_id'];?>">
					<br/><br/>
					<p id="error" style="color:red;"></p>
					<div class="col col-md-8 offset-2">
						<div class="input-group">
							<span class="input-group-text col-md-4"><i class="fab fa-buffer"></i>&nbsp;Qty</span>
							<input type="text" required name="holdingQty" id="holdingQty" class="form-control" value="<?php if(isset($holding)) echo $holding['qty'];?>">
						</div>
					</div>
					<br/><br/>
			  </div>
			  <div class="modal-footer">
				<button class="btn" id="rtnbtn" style="background-color:#708090;color:white;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add to Holding</button>
			  </div>
		</div>
	  </div>
	</div>
	<script>
		$('#rtnbtn').click(function(){
			var ar = document.getElementById('har').value;
			var product = document.getElementById('hproduct').value;
			var qty = document.getElementById('holdingQty').value;
			var returnId = document.getElementById('returnId').value;
			if(isNaN(qty) || !qty)
				$("#error").text('Please enter a valid number');
			else{
				$.ajax({
					url: 'ajax/upsertHolding.php',
					type: 'post',
					data: {returnId: returnId, qty: qty, ar: ar, product: product},
					success: function(response){
						if(response.status == 'success'){
							var url = window.location.href + '&success';    
							window.location.href = url;
						}else if(response.status == 'error'){
							$("#error").text(response.value);
						}
					},
					error: function (request, status, error) {
						$("#error").text(request.responseText);
					}
				});				
			}
		});
		$("#holdingModal").on("hidden.bs.modal", function(){
			$("#error").text('');
			$("#holdingQty").val('');
		});		
	</script>
<?php
}
else
	header( "Location: ../index/home.php" );