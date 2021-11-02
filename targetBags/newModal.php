<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{			
	$arList =  mysqli_query($con,"SELECT id,name FROM ar_details WHERE type != 'Engineer' ORDER BY name");																?>
	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<div class="modal fade" id="newModal" style="margin-top:100px;">
	  <div class="modal-dialog modal-xl" style="width:50%">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#54698D;color:white">
				<h4 class="modal-title"><i class="fa fa-chart-line"></i>&nbsp;&nbsp;New Target Bag</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br/>
				<p id="insertError" style="color:red;"></p>
				<form name="newForm" id="newForm" method="post" action="insert.php">
					<div class="col col-md-4 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
							<input type="text" required name="date" id="date" class="form-control datepicker" autocomplete="off">
						</div>
					</div>
					<div class="col col-md-6 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="fa fa-shield"></i>&nbsp;AR</span>
							<select required name="ar" id="ar" class="form-control" style="line-height:20px;">
								<option value = "">---Select---</option>																			<?php
								foreach($arList as $ar) 
								{																													?>
									<option value="<?php echo $ar['id'];?>"><?php echo $ar['name'];?></option>										<?php	
								}																													?>
							</select>
						</div>
					</div>
					<div class="col col-md-4 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="fab fa-buffer"></i>&nbsp;Bags</span>
							<input type="text" required name="qty" id="qty" class="form-control" autocomplete="off">
						</div>
					</div>
					<div class="col col-md-6 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text col-md-4"></i>&nbsp;Remarks</span>
							<textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
						</div>
					</div>					
					<br/><br/>
					<div class="row">
						<div class="col col-md-5 offset-5">
							<div class="input-group mb-3">
								<button type="submit" class="btn" style="width:100px;font-size:18px;background-color:#54698D;color:white;"><i class="fa fa-save"></i> Save</button>				 
							</div>
						</div>							
					</div>
				</form>	
			</div>
			<div class="modal-footer">
			</div>
		</div>
	  </div>
	</div>
	<script src="newModal.js"></script><?php
}
else
	header( "Location: ../index.php" );	