<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/monthMap.php';
	require 'dropDownGenerator.php';
	require '../navbar.php';
	
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
	
	$dateArray = explode(" to ",$dateString);
	$from = $dateArray[0];
	$to = $dateArray[1];
	$toString = $to.'-'.$month.'-'.$year;		
	$toDate = date("Y-m-d",strtotime($toString));	
	
	$fromString = $from.'-'.$month.'-'.$year;		
	$fromDate = date("Y-m-d",strtotime($fromString));	

	$arObjects = mysqli_query($con, "SELECT id,name FROM ar_details WHERE Type != 'Engineer' ORDER BY name ASC") or die(mysqli_error($con));
	foreach($arObjects as $ar)
	{
		$arMap[$ar['id']] = $ar['name'];
	}	
	
	$array = implode("','",array_keys($arMap));
	$sql = "SELECT ar_id, special_target FROM special_target WHERE fromDate='$fromDate' AND toDate='$toDate' AND ar_id IN ('$array')";
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));		 
?>

<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="../css/styles.css" rel="stylesheet" type="text/css">	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
	<title>Special Target</title>
</head>
<body>
	<div id="main" class="main">
		<aside class="sidebar">
			<nav class="nav">
				<ul>
					<li><a href="../ar/list.php">AR List</a></li>
					<li><a href="../Target/list.php?">Target</a></li>
					<li class="active"><a href="#">Special Target</a></li>
					<li><a href="../redemption/list.php?">Redemption</a></li>
				</ul>
			</nav>
		</aside>
		<div class="container">		
			<nav class="navbar navbar-light bg-light sticky-top bottom-nav" style="margin-left:12.5%;width:100%">
				<div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="float:left;margin-left:2%;">
					<div class="btn-group" role="group">
						<button id="btnGroupDrop1" type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Update
						</button>
						<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="cursor:pointer">									
							<li id="update"><a href="list.php?" class="dropdown-item">View</a></li>							
						</ul>
					</div>
				</div>					
				<span class="navbar-brand" style="font-size:25px;"><i class="fa fa-chart-pie"></i> Special Target Update</span>
				<a href="special_target_date.php" class="btn btn-sm" style="background-color:#54698D;color:white;float:right;margin-right:5%;"><i class="fa fa-chart-pie"></i> Create New</a>			
			</nav>
			<div id="snackbar"><i class="fa fa-chart-pie"></i>&nbsp;&nbsp;Special target list edited succesfully !!!</div>
			<br><br>		
			<div class="row">
				<div style="width:120px;margin-left:42%;">
					<div class="input-group">
						<select id="jsYear" name="jsYear" class="form-select" onchange="return refreshYear();">																<?php	
							$yearList = getYears();	
							foreach($yearList as $yr)
							{																																				?>
								<option value="<?php echo $yr;?>" <?php if($year == $yr) echo 'selected';?>><?php echo $yr;?></option>									<?php										
							} 			?>		
						</select>
					</div>
				</div>
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsMonth" name="jsMonth" class="form-select" onchange="return refreshMonth();">																<?php	
							if(!isset($monthList))
								$monthList = getMonths($year);	
							foreach($monthList as $mnth) 
							{																																				?>			
								<option value="<?php echo $mnth;?>" <?php if($month == $mnth) echo 'selected';?>><?php echo getMonth($mnth);?></option>																<?php						
							}?>	
						</select>
					</div>
				</div>
				<div style="width:150px;">
					<div class="input-group">
						<select id="jsDateString" name="jsDateString" class="form-select" onchange="return refreshString();">																	<?php	
							if(!isset($stringList))
								$stringList = getStrings($year,$month);
							foreach($stringList as $string) 
							{																																															?>
								<option value="<?php echo $string;?>" <?php if($dateString == $string) echo 'selected';?>><?php echo $string;?></option>																			<?php						
							}																																					?>																										
						</select>
					</div>
				</div>
			</div>			
			<br><br>
			<form method="post" action="update.php">
				<table align="center" class="maintable table table-hover table-bordered table-sm" style="width:30%;margin-left:43%">
					<tr style="background-color:#F2CF5B;">
						<th style="width:25%">AR NAME</th>
						<th style="width:25%;text-align:center;">SPECIAL TARGET</th>
					</tr>																																		<?php
					while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) 
					{
						$arId = $row['ar_id'];
						$special_target = $row['special_target'];																											?>				
						<tr>
							<td><label align="center"><?php echo $arMap[$arId]; ?></td>	
							<td style="text-align:center;"><input type="text" style="text-align:center;width:70px;border:0px;background-color: transparent;" name="<?php echo $arId.'-special_target';?>" value="<?php echo $special_target; ?>"></td>	
						</tr>																																					<?php
					}																																							?>
					<input type="hidden" name="fromDate" value="<?php echo $fromDate;?>">
					<input type="hidden" name="toDate" value="<?php echo $toDate;?>">
				</table>
				<br/>
				<div style="margin-left:54%"><input type="submit" name="submit" value="Update"></div>
			</form>
			<br/><br/><br/>
		</div>	
	</div>
	<script>
		$(document).ready(function() {		
			$(".maintable tbody tr").each(function(){
				var extra = $(this).find("td:eq(7)").text();   
				if (extra != '0'){
				$(this).addClass('selected');
				}
			});

			$(".maintable").tablesorter({
				theme : 'bootstrap',
				widgets: ['filter'],
				filter_columnAnyMatch: true
			});

			var checkbox = getUrlParameter('removeToday');
			if(checkbox =='true')
				$('#removeToday').prop('checked', true);
			else
				$('#removeToday').prop('checked', false);	
			
					
			if(window.location.href.includes('success')){
				var x = document.getElementById("snackbar");
				x.className = "show";
				setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
			}			
		} );
		function refresh()
		{
			var removeToday = $('#removeToday').is(':checked');
			
			var hrf = window.location.href;
			hrf = hrf.slice(0,hrf.indexOf("?"));
			window.location.href = hrf + "?removeToday=" + removeToday;
		}

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

		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = decodeURIComponent(window.location.search.substring(1)),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;
			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : sParameterName[1];
				}
			}
		};	
	</script>
</body>
</html>
<?php
}
else
	header("../Location:index.php");

?>