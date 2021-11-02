<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require '../functions/ledger.php';
    
	$urlId = $_GET['id'];
	$sql = mysqli_query($con,"SELECT * FROM ar_details WHERE id='$urlId'") or die(mysqli_error($con));
	$ar = mysqli_fetch_array($sql,MYSQLI_ASSOC);	
	$giftQuery = mysqli_query($con,"SELECT * FROM gifts WHERE ar_id='$urlId' ORDER BY date DESC") or die(mysqli_error($con));
		
	if(isset($_GET['year']))
		$urlYear = $_GET['year'];
	else
		$urlYear = date("Y");
	
	$arName = $ar['name'];
	
	$targetMap = getTargets($urlYear,$urlId);
	$specialTargetMap = getSpecialTargets($urlYear,$urlId);
	$redemptionMap = getRedemptions($urlYear,$urlId);
	$saleMap = getSales($urlYear,$urlId);
	$pointsMap = getPoints($urlYear,$saleMap,$targetMap);
	$openingPoints = getOpeningPoints($urlYear,$urlId);	
?>
<html>
	<head>
		<title><?php echo $ar['name'];?></title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dashio.css" rel="stylesheet">
		<link href="../css/dashio-responsive.css" rel="stylesheet">	
		<link href="../css/font-awesome.min.css" rel="stylesheet">		
		<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script type="text/javascript">
		function rerender()
		{
			var ar = <?php echo $urlId;?>;
			var year = document.getElementById("year").options[document.getElementById("year").selectedIndex].value;

			var hrf = window.location.href;
			hrf = hrf.slice(0,hrf.indexOf("?"));

			window.location.href = hrf +"?id="+ ar + "&year=" + year;
		}
		</script>		
	</head>
	<body>
		<style>
		.tbl {
		}
		.tbl td {
		   padding: 5px;
		   width:150px;
		}

		</style>	
		<section class="wrapper">
		<div align="center" style="padding-bottom:5px;">
			<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='50px' height='50px'/> </a>
		</div>
			<h2 style="margin-left:150px;" <label id="name"><?php echo $ar['name'];?></label></h2>
			<div class="col-md-10 col-md-offset-1">	
				<div class="row content-panel">
				  <div class="col-md-6">
					<div class="right-divider" style="height:250px;">
					  <h4 style="margin-left:50px;">
					  <table class="tbl">
						  <tr>
							<td><i class="fa fa-user"></i> Name</td>
							<td style="width:300px">: <?php echo $ar['name'];?></td>
						  </tr>
						  <tr>
							<td><i class="fa fa-phone"></i> Mobile</td>
							<td>: <?php echo $ar['mobile'];?></td>
						  </tr>							  
						  <tr>
							<td><i class="fa fa-home"></i> Shop Name</td>
							<td>: <?php echo $ar['shop_name'];?></td>
						  </tr>
						  <tr>
							<td>SAP Code</td>
							<td>: <?php echo $ar['sap_code'];?></td>
						  </tr>					  
						  <tr>
							<td>Area</td>
							<td>: <?php echo $ar['area'];?></td>
						  </tr>					  
						  <tr>
							<td>Type</td>
							<td>: <?php echo $ar['type'];?></td>
						  </tr>					  					  
						</table>  
					</div>
				  </div>
				  <div class="col-md-4">
					<br/><br/>
					<a  href="edit.php?id=<?php echo $ar['id'];?>" class="btn btn-theme" style="width:120px"><i class="fa fa-pencil"></i> Edit Engineer</a>
					<br/>
				  </div>
				</div>
			</div>

			
			<div class="col-md-10 col-md-offset-1">	
				<div class="row mt">
					<div class="content-panel">
						<h3 style="margin-left:100px;"><i class="fa fa-book"></i>&nbsp;&nbsp;Points Ledger</h3>
						<div class="form-group">
							<div class="col-sm-6 col-md-offset-4">
								<select id="year" name="year" onchange="return rerender();">																							<?php	
								$yearList = mysqli_query($con, "SELECT DISTINCT YEAR(entry_date) FROM nas_sale ORDER BY entry_date DESC" ) or die(mysqli_error($con));	
								foreach($yearList as $yearObj) 
								{							
									$year = $yearObj['YEAR(entry_date)'];																				?>			
									<option <?php if($urlYear == $year) echo 'selected';?> value="<?php echo $year;?>"><?php echo $year;?></option>															<?php	
								}																																									?>	
								</select>												
							</div>
						</div>																	
						<br/><br/>
						<section id="unseen">
							<table class="table table-bordered table-condensed col-md-offset-1" style="width:60%;">							<?php
							foreach($targetMap as $month => $target) 
							{																																	?>							
								<thead>
									<tr style="background-color:#4ECDC4;color:#fff">
										<th style="text-align:center;"><?php echo getMonth($month);?></th>
										<th style="width:10%;">Target</th>
										<th style="width:10%;">Sale</th>
										<th style="width:10%;">Points</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<tbody>																																		
									<tr>
										<td style="text-align:left;"><?php echo getMonth($month).' Opening';?></td>
										<td colspan="2" style="text-align:center;">OPENING</td>
										<td><?php echo $openingPoints;?></td>
										<td></td>
									</tr>																														<?php
									if(isset($specialTargetMap[$month]))
									{
										foreach($specialTargetMap[$month] as $dateString => $subArray)
										{																															?>
											<tr>
												<td style="text-align:left;"><?php echo getMonth($month).' '.$dateString;?></td>
												<td><?php echo $subArray['target'];?></td>
												<td><?php echo $subArray['sale'];?></td>
												<td><?php 
													if($subArray['sale'] + $subArray['extra'] >= $subArray['target']) 
													{
														$openingPoints = $openingPoints + $subArray['sale'];
														echo $subArray['sale'];
													}	
													else 
														echo '0';?>
												</td>
												<td></td>
											</tr>																												<?php			
										}	
									}																															?>
									<tr>
										<td style="text-align:left;"><?php echo getMonth($month).' Full';?></td>
										<td><?php echo $target['target'];?></td>
										<td><?php if(isset($saleMap[$month]))echo $saleMap[$month]; else echo '0';?></td>
										<td><?php 
											if(isset($pointsMap[$month]['payment_points']))
											{
												$openingPoints = $openingPoints + $pointsMap[$month]['payment_points'];
												echo $pointsMap[$month]['payment_points'];
											} 
											else 
												echo '0';?>
										</td>															
										<td></td>
									</tr>																													<?php
									if(isset($redemptionMap[$month]))
									{
										foreach($redemptionMap[$month] as $redemption)
										{
											$openingPoints = $openingPoints - $redemption['points'];													?>
											<tr>
												<td style="text-align:left;"><?php echo date('F d',strtotime($redemption['date'])).' Redemption';?></td>
												<td colspan="2" style="text-align:center;">REDEMPTION</td>
												<td><?php echo $redemption['points'];?></td>
												<td><?php echo $redemption['remarks'];?></td>
											</tr>																												<?php			
										}	
									}																															?>	
									<tr>
										<td style="text-align:left;"><?php echo getMonth($month).' Closing';?></td>
										<td colspan="2" style="text-align:center;">CLOSING</td>
										<td><?php echo $openingPoints;?></td>
										<td></td>
									</tr>																												<?php																		
								}																																?>									
								</tbody>
							</table>
							<br/><br/>
						</section>
					</div>
				</div>
			</div>

			
			<div class="col-md-10 col-md-offset-1">	
				<div class="row mt">
					<div class="content-panel">
						<h3 style="margin-left:100px;"><i class="fa fa-gift"></i>&nbsp;&nbsp;Gifts</h3>
						<br/>
						<section id="unseen">
							<table class="table table-bordered table-striped table-condensed col-md-offset-1" style="width:60%;">
								<thead>
									<tr>
										<th width="2%;"></th>
										<th>&nbsp;&nbsp;Date</th>
										<th>Category</th>
										<th>Item</th>
										<th>Points/Qty</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<tbody>																																		<?php 
									while($gift = mysqli_fetch_array($giftQuery,MYSQLI_ASSOC))	
									{																																		?>
										<tr>
											<td><a href=""><i class="fa fa-pencil"></i></a></td>
											<td><?php echo date('d-m-Y',strtotime($gift['date']));?></td>
											<td><?php echo $gift['category'];?></td>
											<td><?php echo $gift['item'];?></td>
											<td><?php echo $gift['qty'];?></td>
											<td><?php echo $gift['remarks'];?></td>
										</tr>																																<?php									
									}																																	?>																																	

								</tbody>
							</table>
							<br/><br/>
							<div class="col-md-offset-4">
								<a  href="../gifts/new.php?id=<?php echo $ar['id'];?>" class="btn btn-theme"><i class="fa fa-gift"></i> New Gift</a><br/><br/>
							</div>	
							<br/><br/>
						</section>
					</div>
				</div>
			</div>
		</section>
	</body>
</html>
<?php
}
else
	header("Location:../index.php");
?>