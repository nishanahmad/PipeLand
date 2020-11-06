<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{																																										?>	
	<div class="modal fade" id="deleteModal">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
		  <div class="modal-header" style="background-color:#E6717C;color:#FFFFFF">
			<h4 class="modal-title"><i class="far fa-trash-alt fa-lg"></i>&nbsp;&nbsp;Delete</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  </div>
		  <div class="modal-body">
		    <p id="deleteError" style="color:red;"></p>
			<p id="confirmId">Are you sure you want to delete this sale?</p>
		  </div>
		  <div class="modal-footer">
		  <button type="button" id="deletebutton" class="btn btn-danger" >Delete</button>
		  <button type="button" class="btn" data-dismiss="modal">Cancel</button>
		  </div>
		</div>
	  </div>
	</div>																																								<?php
}
else
	header( "Location: ../index/home.php" );	