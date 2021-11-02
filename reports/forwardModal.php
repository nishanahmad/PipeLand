<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{																																							?>
	<div class="modal fade" id="forwardModal">
	  <div class="modal-dialog modal-md" style="width:70%">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#E6717C;color:white">
				<h4 class="modal-title"><i class="fas fa-arrow-right"></i>&nbsp;&nbsp;Forward</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="forwardIdModal">	
				<div class="col col-md-10 offset-1">
					<input type="text" required name="remarks" id="remarks" class="form-control" autocomplete="off">
				</div>
				<br/>
				<div class="col col-md-10 offset-4">
					<button id="forwardbtn" class="btn" style="width:100px;font-size:18px;background-color:#E6717C;color:white;"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>
		<div class="modal-footer">
		</div>
		</div>
	  </div>
	  <?php
}
else
	header( "Location: ../index.php" );