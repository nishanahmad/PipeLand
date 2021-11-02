<?php
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
    
// Populate maps for SAP CODE and SHOP NAME
	$products = mysqli_query($con,"SELECT id,name FROM products WHERE status = 1 ORDER BY id ASC");

	$arObjects = mysqli_query($con,"SELECT id,name,sap_code,shop_name,type FROM ar_details WHERE type <> 'Engineer Only' OR type IS NULL ORDER BY name ASC");
	foreach($arObjects as $arObject)
	{
		$arId = $arObject['id'];
		
		$shopName = strip_tags($arObject['shop_name']);
		$shopNameMap[$arId] = $shopName;
	}
	
	$shopNameArray = json_encode($shopNameMap);
	$shopNameArray = str_replace('\n',' ',$shopNameArray);
	$shopNameArray = str_replace('\r',' ',$shopNameArray);
	
	$engineerObjects = mysqli_query($con,"SELECT id,name,sap_code,shop_name FROM ar_details WHERE type = 'Engineer only' ORDER BY name ASC");
	$trucks = mysqli_query($con,"SELECT * FROM truck_details ORDER BY number");
	$godowns = mysqli_query($con,"SELECT * FROM godowns ORDER BY name");																											?>

	<style>
	#country-list{list-style:none;margin-top:-3px;margin-left:120px;padding:0;width:190px;}
	#country-list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
	#country-list li:hover{background:#ece3d2;cursor: pointer;}
	#phone{padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;}	
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<div class="modal fade" id="saleModal">
	  <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
		<div class="modal-content">
			<div class="modal-header" style="background-color:#54698D;color:white">
				<h4 class="modal-title"><i class="fa fa-bolt"></i>&nbsp;&nbsp;New Sale</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br/>
				<p id="insertError" style="color:red;"></p>
				<form name="newSaleForm" id="newSaleForm" method="post" action="insert.php">
					<div class="card" id="holding-card" style="width:50%;margin-left:30%;margin-bottom:50px;">
					</div>				
					<div class="row">					
						<div class="form-group row">
							<div class="col-sm-6 col-md-4 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5 col-xs-3"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
									<input type="text" id="date" class="form-control datepicker" name="date" required value="<?php echo date('d-m-Y'); ?>" autocomplete="off"/>
								</div>
							</div>
							<div class="col-sm-6 col-md-5 offset-md-1" id="content-desktop">
								<div class="input-group mb-3">
									<span class="input-group-text" style="width:40%"><i class="fas fa-warehouse"></i></i>&nbsp;Godown</span>
									<select name="godown" id="godown" class="form-control" style="width:60%">
										<option value = "">---Select---</option>																						<?php
										foreach($godowns as $godown) 
										{																							?>
											<option value="<?php echo $godown['id'];?>"><?php echo $godown['name'];?></option>			<?php	
										}																							?>
									</select>									
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-5 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-3"><i class="fa fa-address-card-o"></i>&nbsp;AR</span>
									<select name="ar" id="ar" required class="form-control" style="width:74%">
										<option value = "">---Select---</option>																						<?php
										foreach($arObjects as $ar) 
										{																							?>
											<option value="<?php echo $ar['id'];?>"><?php echo $ar['name'];?></option>			<?php	
										}																							?>
									</select>
								</div>
							</div>
							<div class="col-sm-6 col-md-5">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-4 col-xs-3"><i class="far fa-file-alt"></i>&nbsp;Bill No</span>
									<input type="text" name="bill" id="bill" class="form-control">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-5 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-3"><i class="fa fa-hard-hat"></i>&nbsp;Eng</span>
									<select name="engineer" id="engineer"  class="form-control" style="width:72%">
										<option value = "">---Select---</option>
																																	<?php
										foreach($engineerObjects as $eng) 
										{																							?>
											<option value="<?php echo $eng['id'];?>"><?php echo $eng['name'];?></option>			<?php	
										}																							?>
									</select>
								</div>
							</div>
							<div class="col-sm-6 col-md-5" id="content-desktop">
								<div class="input-group mb-3">	
									<span class="input-group-text col-md-4 col-xs-3"><i class="fa fa-truck-moving"></i>&nbsp;Truck</span>
									<select name="truck" id="truck" class="form-control" style="width:67%">
										<option value = "">---Select---</option>																						<?php
										foreach($trucks as $truck) 
										{																							?>
											<option value="<?php echo $truck['id'];?>"><?php echo $truck['number'];?></option>			<?php	
										}																							?>
									</select>									
								</div>
							</div>
						</div>
					</div>					
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-4 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fa fa-shield"></i>&nbsp;Product</span>
									<select name="product" id="product" required class="form-control">									<?php
										foreach($products as $product) 
										{																							?>
											<option value="<?php echo $product['id'];?>"><?php echo $product['name'];?></option>		<?php	
										}																							?>
									</select>
								</div>
							</div>
							<div class="col-sm-6 col-md-5 offset-md-1" id="content-desktop">
								<div class="input-group mb-3">
									<span class="input-group-text" style="width:40%"><i class="fa fa-money"></i></i>&nbsp;Order No</span>
									<input type="text" name="order_no" id="order_no" class="form-control">
								</div>
							</div>
						</div>
					</div>										
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-4 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fab fa-buffer"></i>&nbsp;Qty</span>
									<input type="text" name="qty" id="qty" required class="form-control" pattern="[0-9]+" title="Input a valid number">
								</div>
							</div>
							<div class="col-sm-6 col-md-5 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text" style="width:40%"><i class="fas fa-mobile-alt"></i>&nbsp;Phone</span>
									<input type="text" name="customerPhone" id="phone" class="form-control" autocomplete="off">
									<div id="suggesstion-box"></div> 
								</div>
							</div>
						</div>
					</div>															
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-4 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-5"><i class="fa fa-tags"></i>&nbsp;Disc</span>
									<input type="text" name="bd" id="bd" class="form-control">
								</div>
							</div>
							<div class="col-sm-6 col-md-6 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text" style="width:33%"><i class="far fa-user"></i></i>&nbsp;Customer</span>
									<input type="text" name="customerName" id="customer" class="form-control">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-5 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-4"></i>&nbsp;Remarks</span>
									<textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-4"><i class="fas fa-map-marker-alt"></i>&nbsp;Address</span>
									<textarea name="address1" id="address1" class="form-control" rows="3"></textarea>
								</div>
							</div>
						</div>
					</div>																																			
					<div class="row">
						<div class="form-group row">
							<div class="col-sm-6 col-md-5 offset-md-1">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-4"><i class="fa fa-rupee-sign"></i>&nbsp;Final</span>
									<input readonly id="final" class="form-control">
								</div>
							</div>
							<div class="col-sm-6 col-md-6" id="content-desktop">
								<div class="input-group mb-3">
									<span class="input-group-text col-md-4"><i class="fa fa-address-card-o"></i>&nbsp;Shop</span>
									<input type="text" readonly name="shopName" id="shopName" class="form-control">
								</div>
							</div>
						</div>
					</div>																									
					<br/>
					<div class="row">
						<div class="col col-md-5 offset-5">
							<div class="input-group mb-3">
								<button id="saveNew" class="btn" style="width:100px;font-size:18px;background-color:#54698D;color:white;"><i class="fa fa-save"></i> Save</button>
							</div>
						</div>							
					</div>			
					<input hidden name="sql" value="<?php echo $_GET['sql'];?>"/>
					<input hidden name="range" value="<?php echo $_GET['range'];?>"/>
				</form>
				<br/>
				<div class="col col-md-4 offset-1" id="content-desktop">
					<div class="input-group">
						<span class="input-group-text col-md-6">General Rate</span>
						<input readonly id="rate" class="form-control">
					</div>
				</div>				
				<div class="col col-md-4 offset-1" id="content-desktop">
					<div class="input-group">
						<span class="input-group-text col-md-6">Wagon Disc</span>
						<input readonly id="wd" class="form-control">
					</div>
				</div>								
				<div class="col col-md-4 offset-1" id="content-desktop">
					<div class="input-group">
						<span class="input-group-text col-md-6">Cash Disc</span>
						<input readonly id="cd" class="form-control">
					</div>
				</div>												
			</div>				
		</div>
		<div class="modal-footer"></div>
	  </div>
	</div>	
																																				<?php
}
else
	header( "Location: ../index/home.php" );	