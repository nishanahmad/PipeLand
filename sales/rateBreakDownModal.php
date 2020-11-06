<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{																																												?>		
	<style>
		.ratetable td{
			padding:5px;
		}	
	</style>
	<div class="modal fade" id="rateBreakDownModal">
	  <div class="modal-dialog modal-xl" style="width:40%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>		
		  <div class="modal-body">
			<table border="0" cellpadding="5" cellspacing="0" align="left">
				<tr>
					<td><label>General Rate</label></td>
					<td><input readonly id="rate"/></td>
				</tr>
				<tr>
					<td><label>Wagon Discount</label></td>
					<td><input readonly id="wd"/></td>
				</tr>			
				<tr>
					<td><label>Cash Discount</label></td>
					<td><input readonly id="cd"/></td>
				</tr>	
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