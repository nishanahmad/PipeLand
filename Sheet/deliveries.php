<?php
	
session_start();	

if(isset($_SESSION["user_name"]))
{
	require '../connect.php';	
	require 'navbar.php';	
	
	if(isset($_GET['panelId']))
		$panelId = $_GET['panelId'];
	else
		$panelId = 'mainView';   // Don't scroll
		
	$designation = $_SESSION['role'];	
	
	$driversQuery = mysqli_query($con,"SELECT user_id,user_name FROM users WHERE role ='driver' ORDER BY user_name") or die(mysqli_error($con));
	
	if(isset($_GET['delivered_by']))
		$delivered_by = $_GET['delivered_by'];
	else
		$delivered_by = 'All';
	
	$users = mysqli_query($con,"SELECT * FROM users WHERE role ='driver' ORDER BY user_name ASC" ) or die(mysqli_error($con));
	foreach($users as $user)
	{
		$userMap[$user['user_id']] = $user['user_name']; 
	}
	
	if($delivered_by == 'All')	
		$sheets = mysqli_query($con,"SELECT * FROM sheets WHERE status ='delivered' ORDER BY delivered_on ASC" ) or die(mysqli_error($con));		 	 
	else
		$sheets = mysqli_query($con,"SELECT * FROM sheets WHERE status ='delivered' AND delivered_by = '$delivered_by' ORDER BY delivered_on ASC" ) or die(mysqli_error($con));		 	 
	
	if($delivered_by == 'All')
	{
		$agr = mysqli_query($con,"SELECT SUM(qty) FROM sheets WHERE status ='delivered'" ) or die(mysqli_error($con));
		$onSite = (int)mysqli_fetch_Array($agr,MYSQLI_ASSOC)['SUM(qty)'];			
	}		
	else
	{
		$agr = mysqli_query($con,"SELECT SUM(qty) FROM sheets WHERE status ='delivered' AND delivered_by = '$delivered_by' " ) or die(mysqli_error($con));
		$onSite = (int)mysqli_fetch_Array($agr,MYSQLI_ASSOC)['SUM(qty)'];					
	}
?>	
<html>
	<head>
		<title>Delivered Sheets</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<script>
			$(function() {
				var elmnt = document.getElementById("<?php echo $panelId;?>");
				elmnt.scrollIntoView();					
			});
		
			function closeRequest(id,divId){
				var designation = "<?php echo $designation;?>";
				if(designation != 'driver')
				{
					var arr1 = [];
					<?php
					foreach($driversQuery as $driver)
					{?>
						arr1.push({text:"<?php echo $driver['user_name'];?>", value:"<?php echo $driver['user_id'];?>"});<?php
					}?>						
					bootbox.prompt({
						title: "Select the driver",
						inputType: 'select',
						inputOptions: arr1,
						callback: function (result) {
							if(result)
							{
								driver = result;
								hrf = 'close.php?';
								window.location.href = hrf +"id="+ id + "&driver=" + driver;		
							}
						}
					});				
				}
				else
				{
					driver = "<?php echo $_SESSION["user_id"];?>";
					bootbox.confirm({
						title: "Confirm?",
						message: "Sheets will be added to stock.",
						buttons: {
							confirm: {
								label: '<i class="fa fa-check"></i> Confirm'
							},							
							cancel: {
								label: '<i class="fa fa-times"></i> Cancel'
							}
						},
						callback: function (result) {
							if(result)
							{
								hrf = 'close.php?';
								divId = divId - 1;
								window.location.href = hrf +"id="+ id + "&driver=" + driver + "&panelId=" + divId;		
							}
						}
					});		
				}
			}
		</script>		
	</head>
	<body>
		<br/><br/>
		<div align="center">
			<h2><i class="fa fa-truck"></i> Delivered</h2><br/>
			<select name="delivered_by" id="delivered_by" onchange="document.location.href = 'deliveries.php?delivered_by=' + this.value" class="form-control col-md-2">
				<option value = "All" <?php if($delivered_by == 'All') echo 'selected';?> >ALL</option>													    	<?php
				foreach($users as $user)
				{																																			?>
					<option value="<?php echo $user['user_id'];?>" <?php if($delivered_by == $user['user_id']) echo 'selected';?>><?php echo $user['user_name'];?></option> 						<?php
				}																																			?>
			</select>			
		
			<br/><br/>
			<h2><?php echo $onSite;?> Sheets to collect</h2>
			<br/><br/>
		</div>	 			
		<div class="container"><?php 
			$divId = 1;																				
			foreach($sheets as $sheet)
			{																							?>
					<div class="card" id="panel<?php echo $divId;?>">
						<div class="card-header" style="background-color:#2a739e;color:#ffffff;font-family:Bookman;text-transform:uppercase;"><i class="fa fa-map-marker"></i> <?php echo $sheet['area']; ?></div>
						<div class="card-body"><?php
							if($sheet['customer_name'])
							{?>
								<p><i class="fas fa-user-tie"></i> Cust : <?php echo $sheet['customer_name'];?>
								, <i class="fa fa-mobile"></i> <a href="tel:<?php echo $sheet['customer_phone'];?>"><?php echo $sheet['customer_phone'];?></a></p><?php
							}
							if($sheet['mason_name'])
							{?>
								<p><i class="fa fa-user"></i> Mason : <?php echo $sheet['mason_name'];?>
								, <i class="fa fa-mobile"></i> <a href="tel:<?php echo $sheet['mason_phone'];?>"><?php echo $sheet['mason_phone'];?></a></p><?php
							}?>									
							<p><i class="fa fa-file"></i> <?php echo $sheet['qty'].' sheets for '.$sheet['bags']. ' bags';?></p>
							<p><i class="fa fa-calendar"></i> <?php echo date("d-m-Y",strtotime($sheet['delivered_on']));?></p>
							<p><i class="fas fa-store"></i> <?php echo $sheet['shop'];?></p>
							<p><i class="fa fa-align-left"></i> <?php echo $sheet['remarks'];?></p>																	<?php
							if($designation != 'driver')
							{?>
								<p><i class="fas fa-desktop"></i> Req by <?php echo $sheet['requested_by'];?></p>														<?php
							}?>									
							<p><i class="fa fa-truck"></i> Deliv by																									<?php
														if($userMap[$sheet['delivered_by']] == 'GODOWN')
														{
															echo $sheet['driver'];																					?>
															<a href="tel:<?php echo $sheet['phone'];?>"><?php echo $sheet['phone'];?></a>							<?php
														}
														else
														{
															echo $userMap[$sheet['delivered_by']];
														}																											?>
							</p>
							<br/>
							<div align="center">
								<a href="edit.php?id=<?php echo $sheet['id'];?>" class="btn" style="color:#ffffff;background-color:e1be5c;width:100px;"><i class="fa fa-pencil"></i> Edit</a>&nbsp;&nbsp;								
								<button class="btn" style="color:#ffffff;background-color:7dc37d;width:100px;" onclick="closeRequest(<?php echo $sheet['id'];?>,<?php echo $divId;?>)"> <i class="fas fa-check"></i> Close</button>				
							</div>
						</div>
					</div>
					<br/><br/>																			<?php	
				$divId ++;
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
			});

		</script>				
	</body>
</html>																								<?php
}
else
	header("Location:../index.php");
