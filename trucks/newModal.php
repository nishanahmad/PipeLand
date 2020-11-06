<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{																																															?>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<div class="modal fade" id="newModal" style="margin-top:100px;">
	  <div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#54698D;color:white">
				<h4 class="modal-title"><i class="fa fa-truck"></i>&nbsp;&nbsp;New Truck</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br/>
				<p id="insertError" style="color:red;"></p>
				<div class="col col-md-8 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text col-md-4"><i class="fa fa-truck"></i>&nbsp;Number</span>
						<input type="text" name="number" id="number" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
				<div class="col col-md-8 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text col-md-4"><i class="fa fa-user"></i>&nbsp;Driver</span>
							<input type="text" name="driver" id="driver" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
				<div class="col col-md-8 offset-1">
					<div class="input-group mb-3">
						<span class="input-group-text col-md-4"><i class="fa fa-mobile"></i>&nbsp;Phone</span>
						<input type="text" name="phone" id="phone" class="form-control" autocomplete="off">
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
	<script src="newTruckModal.js"></script><?php
}
else
	header( "Location: ../index.php" );