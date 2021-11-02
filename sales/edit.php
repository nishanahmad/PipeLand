<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if(isset($_SESSION["user_name"]) && $_SESSION["role"] != 'marketing')
{
	require '../connect.php';
	require 'getHistory.php';
	require 'sheetModal.php';
	require 'rateBreakDownModal.php';
	require 'holdingModal.php';
	require 'historyModal.php';
	require 'newTruckModal.php';
	require 'deleteModal.php';
	require '../navbar.php';
	
	$urlsql = $_GET['sql'];
	$urlrange = $_GET['range'];
	
	$engMap = null;
	$products = mysqli_query($con,"SELECT id,name FROM products WHERE status = 1 ORDER BY id ASC") or die(mysqli_error($con));	
	$arObjects = mysqli_query($con,"SELECT id,name,type,shop_name FROM ar_details ORDER BY name") or die(mysqli_error($con));	
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']] = $ar['name']; 
		
		$shopName = strip_tags($ar['shop_name']); 
		$shopNameMap[$ar['id']] = $shopName;

		$shopNameArray = json_encode($shopNameMap);
		$shopNameArray = str_replace('\n',' ',$shopNameArray);
		$shopNameArray = str_replace('\r',' ',$shopNameArray);		
	}
	$result = mysqli_query($con,"SELECT * FROM nas_sale WHERE sales_id='" . $_GET["sales_id"] . "'") or die(mysqli_error($con));
	$row= mysqli_fetch_array($result,MYSQLI_ASSOC);
	$historyList = (getHistory($row['sales_id']));	

	$trucks = mysqli_query($con,"SELECT * FROM truck_details ORDER BY number");
	$godowns = mysqli_query($con,"SELECT * FROM godowns ORDER BY name");

	if($_POST)
	{
		$URL='list.php?sql='.$_POST['sql1'].'&range='.$_POST['range1'];
		echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
		echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';		
	}																																							

	$blockDateQuery = mysqli_query($con,"SELECT date FROM block_old_sale") or die(mysqli_error($con));
	$blockDate = mysqli_fetch_array($blockDateQuery,MYSQLI_ASSOC)['date'];
	$blockDate = date('Y-m-d',strtotime($blockDate));																																											
	
	$holdings = mysqli_query($con,"SELECT * FROM holdings WHERE returned_sale =".$row['sales_id']." OR cleared_sale =".$row['sales_id']) or die(mysqli_error($con));
	
	$unlocked = true;
	$lockedQuery = mysqli_query($con,"SELECT * FROM lock_sale WHERE sale =".$row['sales_id']) or die(mysqli_error($con));
	if(mysqli_num_rows($lockedQuery) > 0)
		$unlocked = false;																																								?>
	
	
	<html>
	<head>
		<title>Edit Sale <?php echo $row['sales_id']; ?></title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
		<link href="../css/styles.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>		
		<script src='edit.js' type='text/javascript'></script>
		<script>
			var shopNameList = '<?php echo $shopNameArray;?>';
			var shopName_array = JSON.parse(shopNameList);
			var shopNameArray = shopName_array;
		</script>
	</head>
	<body>
		<nav class="navbar navbar-light bg-light sticky-top bottom-nav">
			<div style="float:left;margin-left:20px;">
				<form method="post" action="">
					<input hidden name="sql1" value="<?php echo $urlsql;?>">
					<input hidden name="range1" value="<?php echo $urlrange;?>">				
					<button type="submit" class="btn" style="background-color:#54698D;color:white;">
						<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Go Back
					</button>				
				</form>
			</div>
			<span class="navbar-brand" style="font-size:25px;margin-left:10%;"><i class="fa fa-bolt"></i> Sale</span>
				<div style="float:right;margin-right:20px;"><?php
					if($row['deleted'] == null)
					{
						if(isset($sheet))
						{																																						?>
							<button type="button" class="btn" id="sheetMdlBtn" style="background-color:#F2CF5B;color:white;" data-toggle="modal" data-target="#sheetModal">
								<i class="far fa-edit"></i>&nbsp;&nbsp;Sheet
							</button>&nbsp;&nbsp;																																			<?php
						}
						else
						{																																						?>
							<button type="button" class="btn" id="sheetMdlBtn" style="background-color:#7dc37d;color:white;" data-toggle="modal" data-target="#sheetModal">
								<i class="fas fa-plus"></i>&nbsp;&nbsp;Sheet
							</button>																																			<?php
						}																																												
					}																																					?>
					&nbsp;
					<div style="float:right" id="content-desktop">
						<button type="button" class="btn" style="background-color:#2A739E;color:white;" data-toggle="modal" data-target="#historyModal">
							<i class="fa fa-history"></i>&nbsp;&nbsp;History
						</button>&nbsp;&nbsp;															<?php
						if($row['deleted'] == null)
						{																				?>
							<button type="button" class="btn" style="background-color:#708090;color:white;" data-toggle="modal" data-target="#holdingModal">
								<i class="fas fa-box"></i>&nbsp;&nbsp;Holding
							</button>																	<?php
						}																				?>
					</div>	
				</div>
		</nav>
		<br/><br/>
		<div id="snackbar"><i class="fa fa-check"></i>&nbsp;&nbsp;Updated successfull !!!</div>
		<form name="editForm" id="editForm" method="post" action="update.php">
			<input hidden name="id" id="id" value="<?php echo $row['sales_id'];?>">
			<input hidden name="sql" id="sql" value="<?php echo $urlsql;?>">
			<input hidden name="range" id="range" value="<?php echo $urlrange;?>">
			<div style="width:100%;">
				<div align="center" style="padding-bottom:5px;">
					<div class="card" style="width:65%;">
						<div class="card-header" style="background-color:<?php if($row['deleted']) echo '#DC143C';else echo '#f2cf5b';?>;font-size:20px;font-weight:bold;color:white">Sale <?php echo $row['sales_id']; ?></div>
						<div class="card-body">
							<div class="card" id="holding-card" style="width:30%;margin-bottom:50px;"></div>
							<p id="insertError" style="color:red;"></p>							
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-5"><i class="far fa-calendar-alt"></i>&nbsp;Date</span>
										<input type="text" required id="date" name="entryDate" class="form-control" autocomplete="off" value="<?php
												$originalDate1 = $row['entry_date'];
												$newDate1 = date("d-m-Y", strtotime($originalDate1));
												echo $newDate1; ?>">
									</div>
								</div>
								<div class="col col-md-4 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text" style="width:34%"><i class="fas fa-warehouse"></i></i>&nbsp;Godown</span>
										<select name="godown" id="godown" class="form-control" style="width:60%">
											<option value = "">---Select---</option>																						<?php
											foreach($godowns as $godown) 
											{																							?>
												<option value="<?php echo $godown['id'];?>" <?php if($godown['id'] == $row['godown']) echo 'selected';?>><?php echo $godown['name'];?></option>			<?php	
											}																							?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col col-md-5 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-4"><i class="fa fa-address-card-o"></i>&nbsp;AR</span>
										<select name="ar" id="ar" required class="form-control" style="width:250px;" onChange="arRefresh(shopNameArray);">																	<?php
											foreach($arMap as $arId => $arName)
											{																							?>
												<option value="<?php echo $arId;?>" <?php if($row['ar_id'] == $arId) echo 'selected';?>><?php echo $arName;?></option>														<?php	
											}																							?>
										</select>
									</div>
								</div>
								<div class="col col-md-4 offset-1">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-4"><i class="far fa-file-alt"></i>&nbsp;Bill No</span>
										<input type="text" name="bill" id="bill" class="form-control" value="<?php echo $row['bill_no']; ?>">										
									</div>
								</div>
							</div>							
							<div class="row">
								<div class="col col-md-5 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-4"><i class="fa fa-hard-hat"></i>&nbsp;Engineer</span>
										<select name="engineer" id="engineer" class="form-control" style="width:250px;">
											<option value="">-- NULL --</option>																																<?php
											foreach($engMap as $engId => $engName)
											{																																				?>
												<option value="<?php echo $engId;?>" <?php if($row['eng_id'] == $engId) echo 'selected';?>><?php echo $engName;?></option><?php																																			?>																																						<?php		
											}																																				?>
										</select>
									</div>
								</div>
								<div class="col col-md-5 offset-1">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-3"><i class="fas fa-truck-moving"></i>&nbsp;Truck</span>
										<select name="truck" id="truck" class="form-control" style="line-height:20px;width:46%;">	
											<option value = "">-- NULL --</option>																																		<?php
											foreach($trucks as $truck) 
											{																																												?>
												<option value="<?php echo $truck['id'];?>" <?php if($truck['id'] == $row['truck']) echo 'selected';?>><?php echo $truck['number'];?></option>								<?php	
											}																																											?>
										</select>
										&nbsp;&nbsp;<a data-toggle="modal" data-target="#newTruckModal" style="color:limegreen;cursor:pointer">New</a>										
									</div>
								</div>
							</div>														
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-5"><i class="fa fa-shield"></i>&nbsp;Product</span>
										   <div class="input-group" style="width:150px;">
												<select name="product" id="product" required class="form-control">																								<?php
													foreach($products as $product) 
													{																																							?>
														<option <?php if($row['product'] == $product['id']) echo 'selected';?> value="<?php echo $product['id'];?>"><?php echo $product['name'];?></option>		<?php	
													}																							?>
												</select>
											</div>
									</div>
								</div>
								<div class="col col-md-4 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-4" style="width:120px;"><i class="fa fa-money"></i>&nbsp;Order No</span>
										<input type="text" name="order_no" id="order_no" class="form-control" value="<?php echo $row['order_no']; ?>">									
									</div>
								</div>
							</div>																					
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-5"><i class="fab fa-buffer"></i>&nbsp;Quantity</span>
										<input type="text" name="qty" required id="qty" class="form-control" pattern="[0-9]+" value="<?php echo $row['qty'];?>" title="Input a valid number" autocomplete="off">
									</div>
								</div>
								<div class="col col-md-4 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-4" style="width:80px;"><i class="far fa-user"></i>&nbsp;Cust</span>
										<input type="text" name="customerName" id="customer" class="form-control" value="<?php echo $row['customer_name']; ?>">
									</div>
								</div>
								&nbsp;&nbsp;
								<input class="form-check-input" type="checkbox" name="ar_direct" id="autoDiscount" <?php echo ($row['ar_direct']==1 ? 'checked' : '');?>>&nbsp;Shop
							</div>
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-5"><i class="fa fa-tags"></i>&nbsp;Bill Disc.</span>
										<input type="text" name="bd" id="bd" class="form-control" value="<?php echo $row['discount'];?>">
									</div>
								</div>
								<div class="col col-md-4 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-4"><i class="fas fa-mobile-alt"></i>&nbsp;Phone</span>
										<input type="text" name="customerPhone" id="phone" class="form-control" value="<?php echo $row['customer_phone']; ?>">
									</div>
								</div>								
							</div>							
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-5"><i class="fa fa-rupee-sign"></i>&nbsp;Final Rate</span>
										<input readonly id="final" class="form-control" style="cursor:pointer" data-toggle="modal" data-target="#rateBreakDownModal">
									</div>								
								</div>
								<div class="col col-md-5 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-3" style="width:100px;"><i class="fas fa-map-marker-alt"></i>&nbsp;Address</span>
										<textarea name="address1" id="address" class="form-control"><?php echo $row['address1']; ?></textarea>
									</div>
								</div>
							</div>														
							<div class="row">
								<div class="col col-md-4 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-4"><i class="fas fa-money-check-alt"></i>&nbsp;Total</span>
										<input readonly class="form-control" name="total" id="total">
									</div>								
								</div>
								<div class="col col-md-5 offset-2">
									<div class="input-group mb-3">
										<span class="input-group-text col-md-3"><i class="fa fa-address-card-o"></i>&nbsp;Shop</span>
										<input type="text" readonly name="shopName" id="shopName" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col col-md-6 offset-1">
									<div class="input-group">
										<span class="input-group-text col-md-3" style="width:125px;"><i class="far fa-comment-dots"></i>&nbsp;Remarks</span>
										<textarea name="remarks" id="remarks" class="form-control" rows="4"><?php echo $row['remarks']; ?></textarea>
									</div>
								</div>
								<div class="col col-md-4 offset-1">

								</div>								
							</div>							
							<p id="displayError" style="color:red;"></p>
							<br/><?php
							$entryDate = date('Y-m-d',strtotime($row['entry_date']));
							if($entryDate > $blockDate && $unlocked && $row['deleted'] == null)
							{																																						?>
								<button id="updatebtn" class="btn" style="width:100px;font-size:18px;background-color:#f2cf5b;color:white;"><i class="fa fa-save"></i> Save</button><?php
							}																																						?>
							
						</div>
						<div class="card-footer" style="background-color:#f2cf5b;padding:1px;"></div>
					</div>
					<br/><br/>																																							<?php
					if($entryDate > $blockDate && mysqli_num_rows($holdings) <= 0 && $unlocked && $row['deleted'] == null)
					{																																						?>
						<button type="button" class="btn" style="float:right;margin-right:150px;background-color:#E6717C;color:#FFFFFF" data-toggle="modal" data-target="#deleteModal">
						<i class="far fa-trash-alt"></i>&nbsp;&nbsp;Delete</button>																										<?php
					}																																						?>							
				</div>
			</div>
			<br/><br/><br/><br/>		
		</form>
		<br/><br/><br/><br/>
	</body>
	</html>																																								<?php
	mysqli_close($con);
}
else
	header("Location:../index/home.php");
