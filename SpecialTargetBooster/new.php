<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../SpecialTarget/dropDownGenerator.php';
	require '../functions/monthMap.php';
    
	if(isset($_GET['year']))
	{
		$year = (int)$_GET['year'];		

		$monthList = getMonths($year);
		if(isset($_GET['month']))
		{
			$month = (int)$_GET['month'];
			if(isset($_GET['dateString']))
				$dateString = $_GET['dateString'];
			else	
			{
				$stringList = getStrings($year,$month);					
				$dateString = end($stringList);					
			}				
		}
		else
		{
			$month = end($monthList);
			$stringList = getStrings($year,$month);					
			$dateString = end($stringList);					
		}
	}
	else
	{
		$sql = mysqli_query($con,"SELECT YEAR(from_date) FROM special_target_date ORDER BY from_date DESC LIMIT 1") or die(mysqli_error($con));	
		$row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
		$year = (int)$row['YEAR(from_date)'];

		$monthList = getMonths($year);		
		$month = end($monthList);

		$stringList = getStrings($year,$month);					
		$dateString = end($stringList);							
	}	
?>
<html>
	<head>
		<title>Discount</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dashio.css" rel="stylesheet">
		<link href="../css/dashio-responsive.css" rel="stylesheet">	
		<link href="../css/font-awesome.min.css" rel="stylesheet">		
		<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script>
			function refreshYear()
			{
				var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
				
				var hrf = window.location.href;
				hrf = hrf.slice(0,hrf.indexOf("?"));
				
				window.location.href = hrf +"?year="+ year;
			}	
			
			function refreshMonth()
			{
				var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
				var month=document.getElementById("jsMonth").value;
				
				var hrf = window.location.href;
				hrf = hrf.slice(0,hrf.indexOf("?"));
				
				window.location.href = hrf +"?year="+ year + "&month=" + month;
			}
			
			function refreshString()
			{
				var year = document.getElementById("jsYear").options[document.getElementById("jsYear").selectedIndex].value;
				var month=document.getElementById("jsMonth").value;
				var dateString = document.getElementById("jsDateString").options[document.getElementById("jsDateString").selectedIndex].value;
				
				var hrf = window.location.href;
				hrf = hrf.slice(0,hrf.indexOf("?"));
				
				window.location.href = hrf +"?year="+ year + "&month=" + month + "&dateString=" + dateString;
			}		
		</script>
	</head>
	<section class="wrapper">
		<div align="center" style="padding-bottom:5px;">
			<a href="../index.php" class="link"><img alt='home' title='home' src='../images/home.png' width='60px' height='60px'/> </a> &nbsp;&nbsp;&nbsp;
		</div>	
		<h2><i class="fa fa-bolt" style="margin-right:.5em;margin-left:.5em;"></i>New ST Boost</h3>
		<div class="row mt">
			<div class="col-lg-8">
				<div class="form-panel">
					<h4 class="mb"><i class="fa fa-angle-right" style="margin-right:.5em;"></i>New Booster</h4>
					<form class="form-horizontal style-form"  action="insert.php" method="post">
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Year</label>
							<div class="col-sm-6">
								<select id="jsYear" name="jsYear" class="form-control" onchange="return refreshYear();">																<?php	
								$yearList = getYears();	
								foreach($yearList as $yr)
								{																																				?>
									<option value="<?php echo $yr;?>" <?php if($year == $yr) echo 'selected';?>><?php echo $yr;?></option>									<?php										
								} 																										?>		
								</select>
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Month</label>
							<div class="col-sm-6">
								<select id="jsMonth" name="jsMonth" class="form-control" onchange="return refreshMonth();">																<?php	
									if(!isset($monthList))
										$monthList = getMonths($year);	
									foreach($monthList as $mnth) 
									{																																				?>			
										<option value="<?php echo $mnth;?>" <?php if($month == $mnth) echo 'selected';?>><?php echo getMonth($mnth);?></option>																<?php						
									}
							?>	</select>					
							</div>
						</div>											
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Range</label>
							<div class="col-sm-6">
								<select id="jsDateString" name="jsDateString" class="form-control" onchange="return refreshString();">																	<?php	
									if(!isset($stringList))
										$stringList = getStrings($year,$month);
									foreach($stringList as $string) 
									{																																															?>
										<option value="<?php echo $string;?>" <?php if($dateString == $string) echo 'selected';?>><?php echo $string;?></option>																			<?php						
									}																																					?>																										
								</select>
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Min Achieved %</label>
							<div class="col-sm-6">
								<input type="text" required name="achieved" pattern="[0-9]+" title="Input a valid number" class="form-control">
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Boost %</label>
							<div class="col-sm-6">
								<input type="text" required name="boost" pattern="[0-9]+" title="Input a valid number" class="form-control">
							</div>
						</div>					
						<button type="submit" class="btn btn-primary" style="margin-left:200px;" tabindex="4">Insert</button> 
						<a href="list.php" class="btn btn-default" style="margin-left:10px;">Cancel</a>
						<br/><br/>
					</form>
				</div>
			</div>
		</div>
	</section>
</html>	
<?php
}
else
	header("Location:../index.php");
?>