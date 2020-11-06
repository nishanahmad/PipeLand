<?php
	
session_start();

if(isset($_SESSION["user_name"]))
{	
	require '../connect.php';
	require 'navbar.php';
	
	$designation = $_SESSION['role'];
	
	if(isset($_GET['error']))
		$error = $_GET['error'];
	else
		$error = 'false';	
	
	$userId = $_SESSION['user_id'];

	$driversQuery = mysqli_query($con,"SELECT user_id,user_name FROM users WHERE role ='driver' ORDER BY user_name") or die(mysqli_error($con));
	foreach($driversQuery as $driver)
		$drivers[$driver['user_id']] = $driver['user_name'];
	
	if($designation != 'driver')
	{
		if(isset($_GET['assigned_to']))
			$assigned_to = $_GET['assigned_to'];
		else
			$assigned_to = 'All';

		$users = mysqli_query($con,"SELECT DISTINCT(assigned_to) FROM sheets WHERE status ='requested' AND assigned_to > 0 ORDER BY assigned_to ASC" ) or die(mysqli_error($con));
		
		if($assigned_to == 'All')
			$sheets = mysqli_query($con,"SELECT * FROM sheets WHERE status ='requested' ORDER BY date ASC" ) or die(mysqli_error($con));
		else
			$sheets = mysqli_query($con,"SELECT * FROM sheets WHERE status ='requested' AND assigned_to = '$assigned_to' ORDER BY date ASC" ) or die(mysqli_error($con));		
	}
	else
		$sheets = mysqli_query($con,"SELECT * FROM sheets WHERE status ='requested' AND assigned_to = '$userId' ORDER BY date ASC" ) or die(mysqli_error($con));		
	
	$inHandQuery = mysqli_query($con,"SELECT SUM(qty) FROM sheets_in_hand" ) or die(mysqli_error($con));
	$stockInHand = (int)mysqli_fetch_array($inHandQuery,MYSQLI_ASSOC)['SUM(qty)'];
	
	$agr = mysqli_query($con,"SELECT SUM(qty) FROM sheets WHERE status ='delivered' " ) or die(mysqli_error($con));
	$onSite = (int)mysqli_fetch_Array($agr,MYSQLI_ASSOC)['SUM(qty)'];	
	
	$yesterday = date('Y-m-d',strtotime("-1 days"));
	$lateAgr = mysqli_query($con,"SELECT SUM(qty),delivered_by FROM sheets WHERE status ='delivered' AND delivered_on < '$yesterday' GROUP BY delivered_by" ) or die(mysqli_error($con));
	$lateTotal = 0;
	foreach($lateAgr as $row)
	{
		$feQtyMap[$row['delivered_by']] = $row['SUM(qty)'];
		$lateTotal = $lateTotal + $row['SUM(qty)'];
	}
		
?>	
<html>
	<style>
	.stockTable{
		border: 1px solid black;
		width:300px;
	}	
	.stockTable th,td {
		padding: 5px;	
		border: 1px solid black;
	}
	</style>
	<head>
		<title>Pending Requests</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script>
		function deliver(id){
			var qty;
			var driver;
			var designation = "<?php echo $designation;?>";			
			
			var arr1 = [];
			<?php
			foreach($driversQuery as $driver)
			{?>
				arr1.push({text:"<?php echo $driver['user_name'];?>", value:"<?php echo $driver['user_id'];?>"});<?php
			}?>	
			
			bootbox.prompt({
				title: "Enter number of sheets delivered to this site",
				inputType: 'number',
				callback: function (result1) {
					if(result1)
					{
						qty = result1;
						if(designation != 'driver')
						{
							bootbox.prompt({
								title: "Select the driver",
								inputType: 'select',
								inputOptions: arr1,
								callback: function (result2) {
									if(result2)
									{
										driver = result2;
										hrf = 'deliver.php?';
										window.location.href = hrf +"id="+ id + "&qty=" + qty + "&driver=" + driver;																		
									}
								}
							});				
						}
						else
						{
							driver = "<?php echo $_SESSION["user_id"];?>";
							hrf = 'deliver.php?';
							window.location.href = hrf +"id="+ id + "&qty=" + qty + "&driver=" + driver;						
						}
					}						
				}
			});							
		}
		function cancel(id){
			bootbox.prompt({
				title: "Enter reason for cancellation",
				inputType: 'text',
				callback: function (result) {
					if(result)
					{
						console.log(result);
						hrf = 'cancel.php?';
						window.location.href = hrf +"id="+ id + "&reason=" + result;		
					}						
				}
			});										
		}		
		</script>
	</head>
	<body>
		<div align="center">
			<h2>Pending Requests</h2><br/>																																	<?php
			if($designation != 'driver')
			{																																							?>
				<select name="assigned_to" id="assigned_to" onchange="document.location.href = 'requests.php?assigned_to=' + this.value" class="form-control col-md-2">
						<option value = "All" <?php if($assigned_to == 'All') echo 'selected';?> >ALL</option>													    	<?php
						foreach($users as $user)
						{																																				?>
							<option value="<?php echo $user['assigned_to'];?>" <?php if($assigned_to == $user['assigned_to']) echo 'selected';?>><?php echo $drivers[$user['assigned_to']];?></option> 						<?php
						}																																			?>
					</select>			
					<br/><br/>
					<table class="stockTable">
						<tr>
							<th style="width:40%;"></th>
							<th style="width:30%;text-align:center">In hand</th>
							<th style="width:30%;text-align:center">Late to collect</th>
						</tr><?php
						$stockQuery = mysqli_query($con,"SELECT * FROM sheets_in_hand ORDER BY user") or die(mysqli_error($con));
						foreach($stockQuery as $stock)
						{?>
							<tr>
								<td><?php echo $drivers[$stock['user']];?></td>
								<td style="text-align:center"><?php echo $stock['qty'];?></td>
								<td style="text-align:center"><?php if(isset($feQtyMap[$stock['user']])) echo $feQtyMap[$stock['user']]; else echo '0';?></td>
							</tr>																											<?php					
						}?>
						<tr>
							<td>On site</td>
							<td style="text-align:center"><?php echo $onSite;?></td>
						</tr>						
						<tr>
							<th>Total</th>
							<th style="text-align:center"><?php echo $stockInHand + $onSite;?></th>
							<th style="text-align:center"><?php echo $lateTotal;?></th>
						</tr>																										
					</table>
					<br/><br/>																												<?php	 				
			}
			else
			{
				$stockQuery = mysqli_query($con,"SELECT * FROM sheets_in_hand WHERE user = '$userId'") or die(mysqli_error($con));
				$stock = mysqli_fetch_array($stockQuery,MYSQLI_ASSOC);
				echo '<b>'.$stock['qty'].' sheets in hand</b><br/><br/>';	
			}				?>
		</div>
		<div class="container" >																											<?php 
				foreach($sheets as $sheet)
				{																															?>
					<div class="card">
						<div class="card-header" style="background-color:#2a739e;color:#ffffff;font-family:Bookman;text-transform:uppercase;"><i class="fa fa-map-marker"></i> <?php echo $sheet['area']; ?></div>
						<div class="card-body"><?php
							if(!empty($sheet['customer_name']))
							{?>
								<p><i class="fa fa-user"></i> Cust :  <?php echo $sheet['customer_name'];?>
								, <i class="fa fa-mobile"></i> <a href="tel:<?php echo $sheet['customer_phone'];?>"><?php echo $sheet['customer_phone'];?></a></p><?php
							}
							if(!empty($sheet['mason_name']))
							{?>
								<p><i class="fa fa-user"></i> Mason :  <?php echo $sheet['mason_name'];?>
								, <i class="fa fa-mobile"></i> <a href="tel:<?php echo $sheet['mason_phone'];?>"><?php echo $sheet['mason_phone'];?></a></p><?php
							}?>
							<p><i class="fa fa-calendar"></i> <?php echo date("d-m-Y",strtotime($sheet['date']));?>, <i class="fa fa-shopping-bag"></i> <?php echo $sheet['bags'].' bags';?></p>
							<p><i class="fas fa-store"></i> <?php echo $sheet['shop'];?></p>
							<p><i class="fas fa-desktop"></i> Req by <b><?php echo $sheet['requested_by']; 
							if($sheet['created_on'] != null && $designation != 'driver')
							{																																?>
								</b> on <?php echo date('d M, h:i A', strtotime($sheet['created_on']));?></p>			<?php
							}
							if($designation != 'driver' && $sheet['assigned_to'] != 0)
							{																																?>
								<p><i class="fa fa-share"></i> Assigned to <b><?php echo $drivers[$sheet['assigned_to']];?></b></p>								<?php										
							}																																?>
							<p><i class="fa fa-align-left"></i> <?php echo $sheet['remarks'];?></p>	
							<br/>
							<div align="center">
								<a href="edit.php?id=<?php echo $sheet['id'];?>" class="btn" style="color:#ffffff;background-color:e1be5c;width:80px;"><i class="fa fa-pencil"></i> Edit</a>&nbsp;&nbsp;								
								<button class="btn" style="color:#ffffff;background-color:7dc37d;width:95px;" onclick="deliver(<?php echo $sheet['id'];?>)"><i class="fas fa-check"></i> Deliver</button>&nbsp;&nbsp;<?php
								if($designation != 'driver')
								{																														?>																																								
									<button class="btn" onclick="cancel(<?php echo $sheet['id'];?>)" style="background-color:#E6717C;color:#FFFFFF;width:80px;"><i class="far fa-trash-alt"></i> Dlt</button>							<?php
								}																														?>
							</div>							
						</div>
					</div>
					<br/><br/><br/>																		<?php				
				}																							?>
		</div>
		<script>

			$(function(){

				var menu = $('.menu-navigation-dark');

				menu.slicknav();

				// Mark the clicked item as selected

				menu.on('click', 'a', function(){
					var a = $(this);

					a.siblings().removeClass('selected');
					a.addClass('selected');
				});
				

				// SHOW ERROR IF RETURNED URL CONTAINS ERROR
				var error = "<?php echo $error;?>";
				if(error == 'true')
				{
					bootbox.alert("Not enough sheets in hand to deliver!!!");					
				}

			});

		</script>		
	</body>
</html>																				<?php
}
else
	header("Location:../index.php");
