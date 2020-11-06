<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{			
	$currentRateMap = getCurrentRates($con);
	$productDetailsMap = getProductDetails($con);		
	$discountMap = getDiscounts($con);	?>	
	
	<style>
		.ratetable td{
			padding:5px;
		}	
	</style>
	<div class="modal fade" id="rateModal">
	  <div class="modal-dialog modal-xl" style="width:40%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>		
		  <div class="modal-body">
				<table class="ratetable table table-hover table-bordered" style="width:90%;margin:5%;">
					<thead>
						<tr class="table-info">
							<th><i class="fa fa-shield"></i> Product</th>
							<th style="width:90px;"><i class="fa fa-rupee-sign"></i> Rate</th>
							<th style="width:110px;"><i class="fa fa-tags"></i> Discount</th>
						</tr>
					</thead>
					<tbody><?php				
						foreach($currentRateMap as $product=>$rate)
						{?>
							<tr>
								<td><?php echo $productDetailsMap[$product]['name'];?></td>
								<td><?php echo $rate.'/-';?></td>
								<td><?php if(isset($discountMap[$product])) echo $discountMap[$product].'/-';?></td>
							</tr><?php
						}?>
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