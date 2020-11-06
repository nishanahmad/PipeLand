<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{	
	$clients = mysqli_query($con,"SELECT id,name FROM ar_details WHERE type = 'AR/SR' ORDER BY name ASC");	
	$products= mysqli_query($con,"SELECT id,name FROM products WHERE status = 1 ORDER BY id");													?>
	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<div class="modal fade" id="newModal" style="margin-top:100px;">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#54698D;color:white">
				<h4 class="modal-title"><i class="fa fa-tag"></i>&nbsp;&nbsp;New Discount</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br/>
				<p id="insertError" style="color:red;"></p>
				<div class="col col-md-4 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="fa fa-list-alt"></i>&nbsp;Type</span>
						<select required name="type" id="type" class="form-control" style="line-height:20px;">
							<option value="">-- SELECT --</option>
							<option value="Wagon Discount">Wagon Discount</option>
							<option value="Cash Discount">Cash Discount</option>
							<option value="Special Discount">Special Discount</option>
						</select>
					</div>
				</div>				
				<div class="col col-md-4 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="far fa-calendar-alt"></i>&nbsp; Date</span>
						<input type="text" required name="date" id="date" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
				<div class="col col-md-5 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="fa fa-shield"></i>&nbsp;Product</span>
						<select required name="product" id="product" class="form-control" style="line-height:20px;">
							<option value = "">---Select---</option>																			<?php
							foreach($products as $product) 
							{																													?>
								<option value="<?php echo $product['id'];?>"><?php echo $product['name'];?></option>										<?php	
							}																													?>
						</select>
					</div>
				</div>
				<div class="col col-md-6 offset-1" id="clientLabel">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="fa fa-address-card-o"></i>&nbsp;Client</span>
						<select required name="client" id="client" class="form-control" style="line-height:20px;">
							<option value = "">---Select---</option>																			<?php
							foreach($clients as $client) 
							{																													?>
								<option value="<?php echo $client['id'];?>"><?php echo $client['name'];?></option>										<?php	
							}																													?>
						</select>
					</div>
				</div>				
				<div class="col col-md-4 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="fa fa-tag"></i>&nbsp;Discount</span>
						<input type="text" required name="discount" id="discount" class="form-control" autocomplete="off">
					</div>
				</div>
				<div class="col col-md-6 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text" style="width:100px;"><i class="far fa-comment-dots"></i>&nbsp;Remarks</span>
						<textarea name="remarks" id="remarks" class="form-control"></textarea>
					</div>
				</div>											
				<br/><br/>
				<div class="row">
					<div class="col col-md-5 offset-5">
						<div class="input-group mb-3">
							<button type="submit" class="btn" id="saveNew" style="width:100px;font-size:18px;background-color:#54698D;color:white;"><i class="fa fa-save"></i> Save</button>				 
						</div>
					</div>							
				</div>																			
			</div>
			<div class="modal-footer">
			</div>
		</div>
	  </div>
	</div>
	<script src="newModal.js"></script>																					<?php
}
else
	header( "Location: ../index.php" );	