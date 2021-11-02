<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{			
	$clients = mysqli_query($con,"SELECT id,name FROM ar_details ORDER BY name ASC");
	$products= mysqli_query($con,"SELECT id,name FROM products ORDER BY id ASC");																?>
	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<div class="modal fade" id="filterModal">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#696E71;color:white">
				<h4 class="modal-title"><i class="fas fa-filter"></i>&nbsp;&nbsp;Custom Filter</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br/>
				<p id="displayError" style="color:red;"></p>
				<div class="col col-md-5 offset-1">
					<div class="input-group">
						<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;Start Date</span>
						<input type="text" name="start-date" id="start-date" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
				<br/>
				<div class="col col-md-5 offset-1">
					<div class="input-group">
						<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;End Date</span>
						<input type="text" name="end-date" id="end-date" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
				<br/>
				<div class="col col-md-5 offset-1">
					<div class="input-group">
						<span class="input-group-text col-md-5"><i class="fa fa-shield"></i>&nbsp;Product</span>
						<select name="product-filter" id="product-filter" class="form-control" style="line-height:20px;">
							<option value = "">ALL</option>																			<?php
							foreach($products as $product) 
							{																													?>
								<option value="<?php echo $product['id'];?>"><?php echo $product['name'];?></option>										<?php	
							}																													?>
						</select>
					</div>
				</div>
				<br/>
				<div class="col col-md-7 offset-1">
					<div class="input-group">
						<span class="input-group-text" style="width:135px;"><i class="fa fa-address-card-o"></i>&nbsp;AR</span>
						<select name="client-filter" id="client-filter" class="form-control" style="width:250px;">
							<option value = "">ALL</option>																			<?php
							foreach($clients as $client) 
							{																													?>
								<option value="<?php echo $client['id'];?>"><?php echo $client['name'];?></option>										<?php	
							}																													?>
						</select>					
					</div>
				</div>
				<br/>
				<div class="col col-md-7 offset-1">
					<div class="input-group">
						<span class="input-group-text" style="width:135px;"><i class="fa fa-suitcase"></i>&nbsp;Engineer</span>
						<select name="eng-filter" id="eng-filter" class="form-control" style="width:250px;">
							<option value = "">ALL</option>																			<?php
							foreach($engineers as $engineer) 
							{																													?>
								<option value="<?php echo $engineer['id'];?>"><?php echo $engineer['name'];?></option>										<?php	
							}																													?>
						</select>					
					</div>
				</div>	
				<br/>
				<div class="col col-md-5 offset-1">
					<div class="input-group">
						<span class="input-group-text col-md-5"><i class="fas fa-mobile-alt"></i>&nbsp;Cust. Phone</span>
						<input type="text" name="phone-filter" id="phone-filter" class="form-control" autocomplete="off">
					</div>
				</div>				
				<br/><br/>
				<div class="col col-md-5 offset-5">
					<div class="input-group">
						<button class="btn" id="filterBtn" style="width:120px;font-size:18px;background-color:#696E71;color:white;"><i class="fa fa-search"></i> Search</button>				 
					</div>
				</div>
				<br/>
			</div>
			<div class="modal-footer">
			</div>
			<script>
				$(function(){
					$("#filterBtn").on("click",function(){
						var startDate = $("#start-date").val();
						var endDate = $("#end-date").val();
						var product = $("#product-filter").val();
						var client = $("#client-filter").val();
						var eng = $("#eng-filter").val();
						var phone = $("#phone-filter").val();
						var data = '';
						if(startDate)
							data = data + 'startDate='+ startDate + '&';
						if(endDate)
							data = data + 'endDate='+ endDate + '&';
						if(product)
							data = data + 'product='+ product + '&';
						if(client)
							data = data + 'client='+ client + '&';
						if(eng)
							data = data + 'eng='+ eng + '&';		
						if(phone)
							data = data + 'phone='+ phone;												
						
						$.ajax({
							type: "POST",
							url: "ajax/filterSales.php",
							data:data,
							success: function(response){
								if(response)
								{
									window.location.href = 'list.php?sql='+response+'&range=Custom Filter';
									console.log(response);
								}
								else
									$("#error").text('Search returned too many sales. Please apply more filters');
							},
							error: function (jqXHR, exception) {
								var msg = '';
								if (jqXHR.status === 0) {
									msg = 'Not connect.\n Verify Network.';
								} else if (jqXHR.status == 404) {
									msg = 'Requested page not found. [404]';
								} else if (jqXHR.status == 500) {
									msg = 'Internal Server Error [500].';
								} else if (exception === 'parsererror') {
									msg = 'Requested JSON parse failed.';
								} else if (exception === 'timeout') {
									msg = 'Time out error.';
								} else if (exception === 'abort') {
									msg = 'Ajax request aborted.';
								} else {
									msg = 'Uncaught Error.\n' + jqXHR.responseText;
								}
								$("#displayError").text(msg);
								return false;
							}							
						});					
					});
				});
			</script>
		</div>
	  </div>
	</div><?php
}
else
	header( "Location: ../index/home.php" );	