<?php
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
    
// Populate maps for SAP CODE and SHOP NAME
	$arObjects = mysqli_query($con,"SELECT id,name,sap_code,shop_name FROM ar_details ORDER BY name");
	foreach($arObjects as $arObject)
	{
		$arId = $arObject['id'];
		$clientMap[$arId] = $arObject['name'];  
		$shopName = strip_tags($arObject['shop_name']);
		$shopNameMap[$arId] = $shopName;
	}
	
	$shopNameArray = json_encode($shopNameMap);
	$shopNameArray = str_replace('\n',' ',$shopNameArray);
	$shopNameArray = str_replace('\r',' ',$shopNameArray);																																?>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<div class="modal fade" id="newModal">
	  <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#54698D;color:white">
				<h4 class="modal-title"><i class="fas fa-hand-holding-usd"></i>&nbsp;&nbsp;New Redemption</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form name="newRedemptionForm" id="newRedemptionForm" method="post" action="insert.php">
				<div class="modal-body">
					<br/>
					<p id="insertError" style="color:red;"></p>
					<div class="col col-md-4 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="far fa-calendar-alt"></i>&nbsp;Start Date</span>
							<input type="text" required name="date" id="date" class="form-control datepicker" autocomplete="off">
						</div>
					</div>
					<div class="col col-md-6 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="fa fa-address-card-o"></i>&nbsp;Client</span>
							<select required name="client" id="client" class="form-select" style="line-height:20px;">
								<option value = "">---Select---</option>																			<?php
								foreach($clientMap as $id => $name) 
								{																													?>
									<option value="<?php echo $id;?>"><?php echo $name;?></option>										<?php	
								}																													?>
							</select>
						</div>
					</div>
					<div class="col col-md-4 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="fas fa-hand-holding-usd"></i>&nbsp;Redeemed</span>
							<input type="text" required name="points" id="points" class="form-control" autocomplete="off">
						</div>
					</div>
					<div class="col col-md-6 offset-1">
						<div class="input-group mb-3">
							<span class="input-group-text" style="width:120px;"><i class="far fa-comment"></i>&nbsp;Remarks</span>
							<textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
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
			</form>												
		</div>
		<div class="modal-footer"></div>
	  </div>
	</div>																																																<?php
}
else
	header( "Location: ../index/home.php" );	