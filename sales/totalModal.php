<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{																																									?>	
	<div class="modal fade" id="totalModal">
	  <div class="modal-dialog modal-xl" style="width:40%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>		
		  <div class="modal-body">
				<table class="ratetable table table-hover table-bordered" style="width:50%;margin-left:30%;">
					<thead>
						<tr class="table-success">
							<th><i class="fa fa-rupee-sign"></i> Total</th>
							<th style="font-size:25px;;"><p id="totalAmount"/></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>	
		  </div>
		  <div class="modal-footer">
		  </div>
		</div>
	  </div>
	</div>																																								<?php
}
else
	header( "Location: ../index/home.php" );	