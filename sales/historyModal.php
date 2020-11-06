<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{			
	$historyList = (getHistory($row['sales_id']));	?>	
	
	<style>
		.ratetable td{
			padding:5px;
		}	
	</style>
	<div class="modal fade" id="historyModal">
	  <div class="modal-dialog modal-xl" style="width:60%">
		<div class="modal-content">
		  <div class="modal-header" style="background-color:#2A739E;color:white">
			<h4 class="modal-title"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  </div>
		  <div class="modal-body">
			<i class="fa fa-user"></i>&nbsp;&nbsp;Created By : <?php echo $row['entered_by'];?><br/>
			<i class="fa fa-calendar"></i>&nbsp;&nbsp;Created On : <?php echo date('d-m-Y, h:i A', strtotime($row['entered_on']));?>
			<br/><br/>
			<section id="unseen">
				<table class="table table-bordered table-condensed" style="width:90%;">
					<tr>
						<th></th>
						<th><i class="fa fa-user"></i>&nbsp;Modified By</th>
						<th><i class="fa fa-history"></i>&nbsp;Old Value</th>
						<th><i class="fa fa-check"></i>&nbsp;New Value</th>
						<th><i class="fa fa-calendar"></i>&nbsp;Modified On</th>
					</tr>																																				<?php 
					if(isset($historyList))
					{
						foreach($historyList as $history)
						{																																					?>
							<tr>
								<td><?php echo $history['field'];?></td>
								<td><?php echo $history['edited_by'];?></td>
								<td><?php echo $history['old_value'];?></td>
								<td><?php echo $history['new_value'];?></td>
								<td><?php echo date('d-m-Y, h:i A', strtotime($history['edited_on']));?></td>
							</tr>																																		<?php	
						}																																													
					}																																					?>

				</table>	
			</section>				
		  </div>
		  <div class="modal-footer">
		  </div>
		</div>
	  </div>
	</div>																																								<?php
}
else
	header( "Location: ../index/home.php" );	