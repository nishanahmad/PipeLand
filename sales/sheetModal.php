<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
if(isset($_SESSION["user_name"]))
{		
	$urlsql = $_GET['sql'];
	$urlrange = $_GET['range'];
	
	$result = mysqli_query($con,"SELECT * FROM nas_sale WHERE deleted IS NULL AND sales_id='" . $_GET["sales_id"] . "'") or die(mysqli_error($con));	
	$row= mysqli_fetch_array($result,MYSQLI_ASSOC);
	
	$sheetQuery = mysqli_query($con,"SELECT * FROM sheets WHERE site='" . $_GET["sales_id"] . "'") or die(mysqli_error($con));	
	$sheet= mysqli_fetch_array($sheetQuery,MYSQLI_ASSOC);	

	$products = mysqli_query($con,"SELECT id,name FROM products WHERE status = 1 ORDER BY id ASC") or die(mysqli_error($con));	
	$arObjects = mysqli_query($con,"SELECT id,name,type,shop_name FROM ar_details ORDER BY name") or die(mysqli_error($con));	
	foreach($arObjects as $ar)
	{
		if($ar['type'] != 'Engineer Only')
			$arMap[$ar['id']] = $ar['name']; 
		if($ar['type'] == 'Engineer' || $ar['type'] == 'Contractor' || $ar['type'] == 'Engineer Only')
			$engMap[$ar['id']] = $ar['name'];
		
		$shopName = strip_tags($ar['shop_name']); 
		$shopNameMap[$ar['id']] = $shopName;
	}

	$areaList = mysqli_query($con,"SELECT id,name FROM sheet_area ORDER BY name ASC");																				?>

	<div class="modal fade" id="sheetModal">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
		  <?php	
			if(isset($sheet))
			{?>
				<div class="modal-header" style="background-color:#F2CF5B;color:white">					
					<h4 class="modal-title"><i class="far fa-edit"></i>&nbsp;&nbsp;Edit Sheet Request</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>																														<?php
			}
			else
			{?>
				<div class="modal-header" style="background-color:#7dc37d;color:white">
					<h4 class="modal-title"><i class="fas fa-plus"></i>&nbsp;&nbsp;New Sheet Request</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>																														<?php
				
			}?>
		  <form class="well form-horizontal" id="form1" method="post" action="upsertSheet.php" autocomplete="off">
			<input hidden name="sheetSql" value="<?php echo $urlsql;?>">
			<input hidden name="sheetRange" value="<?php echo $urlrange;?>">		  
			  <div class="modal-body">																					<?php
				  if(isset($sheet))
				  {																										?>
					<input type="text" hidden name="id" value="<?php echo $sheet['id'];?>"> 							<?php
				  }																										?>					  
				  <input type="text" hidden name="site" value="<?php echo $row['sales_id'];?>">				  
				  <input type="text" hidden name="sheet_shop" value="<?php echo $shopNameMap[$row['ar_id']];?>" >
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group">
								<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
								<input type="text" name="sheetDate" id="sheetDate" class="form-control" required="true" value="<?php if(isset($sheet)) echo date("d-m-Y", strtotime($sheet['date'])); else echo date("d-m-Y", strtotime($row['entry_date']. ' +1 day'));?>" autocomplete="off">
							</div>
						</div>
						<div class="col col-md-4 offset-2">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-4"><i class="fab fa-buffer"></i>&nbsp;Qty</span>
								<input type="text" required name="sheet_bags" id="sheet_bags" class="form-control" value="<?php if(isset($sheet)) echo $sheet['bags']; else echo $row['qty'];?>">
							</div>
						</div>													
					</div>				  
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group">
								<span class="input-group-text col-md-5"><i class="fas fa-user-tie"></i>&nbsp;Cust</span>
								<input type="text" name="sheet_customer_name" id="sheet_customer_name" class="form-control" value="<?php if(isset($sheet)) echo $sheet['customer_name']; else echo $row['customer_name']?>">
							</div>
						</div>
						<div class="col col-md-4 offset-2">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-4"><i class="fas fa-mobile-alt"></i>&nbsp;Phone</span>
								<input type="text" name="sheet_customer_phone" id="sheet_customer_phone" class="form-control" value="<?php if(isset($sheet)) echo $sheet['customer_phone']; else echo $row['customer_phone']?>">
							</div>
						</div>
					</div>				  					
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group">
								<span class="input-group-text col-md-5"><i class="far fa-user"></i>&nbsp;Mason</span>
								<input type="text" name="sheet_mason_name" id="sheet_mason_name" class="form-control" value="<?php if(isset($sheet)) echo $sheet['mason_name'];?>">
							</div>
						</div>
						<div class="col col-md-4 offset-2">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-4"><i class="fas fa-mobile-alt"></i>&nbsp;Phone</span>
								<input type="text" name="sheet_mason_phone" id="sheet_mason_phone" class="form-control" value="<?php if(isset($sheet)) echo $sheet['mason_phone'];?>">
							</div>
						</div>
					</div>				  										
					<div class="row">
						<div class="col col-md-6 offset-1">
							<div class="input-group mb-3">
								<span class="input-group-text" style="width:27%"><i class="fa fa-map-o"></i>&nbsp;Area</span>
								<select name="driver_area" required id="driver_area" class="form-control" style="width:250px;">
									<option value="">--- SELECT ---</option>								<?php
									foreach($areaList as $area) 
									{																										?>
										<option value="<?php echo $area['id'];?>" <?php if($sheet['driver_area'] == $area['id']) echo 'selected';?>><?php echo $area['name'];?></option>						<?php
									}																										?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col col-md-6 offset-1">
							<div class="input-group mb-3">
								<span class="input-group-text" style="width:27%"><i class="fas fa-map-marker-alt"></i>&nbsp;Address</span>
								<textarea name="sheet_area" id="sheet_area" class="form-control" required><?php if(isset($sheet)) echo $sheet['area']; else echo $row['address1']?></textarea>
							</div>
						</div>
					</div>				  															
					<div class="row">
						<div class="col col-md-6 offset-1">
							<div class="input-group mb-3">
								<span class="input-group-text" style="width:27%"><i class="far fa-clipboard"></i>&nbsp;Remarks</span>
								<textarea name="sheet_remarks" id="sheet_remarks" class="form-control"><?php if(isset($sheet)) echo $sheet['remarks'];?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-5">&nbsp;Delivery</span>
								<select name="delivery" id="delivery" class="form-control" required>
									<option value="">-- SELECT --</option>
									<option value="upn">UPN</option>
									<option value="lorry">LORRY</option>
								</select>
							</div>
						</div>
					</div>					
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-5"><i class="fas fa-truck-moving"></i>&nbsp;Truck</span>
								<input type="text" name="sheet_truck" id="sheet_truck" class="form-control" value="<?php if(isset($row['truck_no'])) echo $row['truck_no'];?>">
							</div>
						</div>
					</div>				  					
					<div class="row">
						<div class="col col-md-4 offset-1">
							<div class="input-group">
								<span class="input-group-text col-md-5"><i class="far fa-id-card"></i>&nbsp;Driver</span>
								<input type="text" name="driver_name" id="driver_name" class="form-control">
							</div>
						</div>
						<div class="col col-md-4 offset-2">
							<div class="input-group mb-3">
								<span class="input-group-text col-md-4"><i class="fas fa-mobile-alt"></i>&nbsp;Phone</span>
								<input type="text" required name="driver_phone" id="driver_phone" class="form-control">
							</div>
						</div>
					</div>				  										
			  </div>
			  <div class="modal-footer"><?php 
				if(isset($sheet))
				{																												?>
					<button class="btn" style="background-color:#F2CF5B;color:white;" type="submit"><i class="far fa-edit"></i>&nbsp;&nbsp;Update</button><?php
				}	
				else
				{																												?>	
					<button class="btn" style="background-color:#7dc37d;color:white;" type="submit"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add Request</button>  <?php
				}																												?>
			  </div>
		  </form>
		</div>
	  </div>
	</div>
	  <?php
}
else
	header( "Location: ../index/home.php" );