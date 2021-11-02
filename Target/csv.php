<?php
require '../connect.php';
require 'sendMessage.php';
?>
<html>
	<head>
	
	</head>
	<body>
		<div align="center"><input type="image" id = "Home" src="Images/home.png" onClick="window.location.href='PharmacyReorder.html'">
		<div align="center"><h2><font face="Tahoma, Trebuchet MS">Upload CSV</font></h2></div>
		<br /><br/>
		<?php
			$files = glob("send.csv");
			foreach($files as $file)
			{
				$csvFile = file($file);
				foreach ($csvFile as $str)
				{
					$row = explode(",",$str);
					$message = "DEAR AR, YOUR BALANCE TO ACHIEVE YOUR MONTHLY TARGET OF OCT 2021 IS ".$row[1]." BAGS. ACHIEVE YOUR TARGET & EARN SPECIAL BENEFITS - AR HELP";
					$phone = '91'.$row[0];
					echo $phone.'<br/>';
					echo $message.'<br/>';
					//$status = sendMessage($message,$phone);
				}
			}
		?>
	</body>
</html>