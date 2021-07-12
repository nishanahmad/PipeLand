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
		<br /><br />
		<div align="center">
			<form method="post" enctype="multipart/form-data" >
				<table>
					<tr><td>File</td><td><input type="file" name="ip_file" /></td></tr>
					<tr><td colspan="2"><input type="submit" name="Submit" /></td></tr>
				</table>
			</form>
		</div>
		<?php
			if ((isset($_FILES["ip_file"])) && ($_FILES["ip_file"]["error"] <= 0))
			{
				$file_handle = fopen($_FILES["ip_file"]["tmp_name"], "r");

				while (!feof($file_handle))
				{
					$row = fgetcsv($file_handle);
					if($row)
					{
						//$message = "DEAR AR, CONGRATS!! U ARE CREDITED ".$row[1]." PLUS POINTS FOR MAY 2021. NOW U HAVE ".$row[2]." PLUS POINTS IN UR ACCOUNT.  AR HELP";
						//$message = "DEAR AR, CONGRATS!! FOR ACHIEVING UR MONTHLY TARGET OF MAY 2021.U ARE CREDITED ".$row[1]." PLUS POINTS. NOW U HAVE ".$row[2]." PLUS POINTS IN UR ACCOUNT.  AR HELP";						
						//$message = "Dear AR, Ur July Month Target is ".$row[1]." Bags. Achieve Ur Target & Earn Full Lakshya Benefits - AR HELP.";
						//$message = "DEAR AR,  U HAVE CREDITED 0 PLUS POINTS FOR MAY 2021. NOW U HAVE ".$row[1]." PLUS POINTS IN UR ACCOUNT.  AR HELP";
						//$message = "Dear AR, Ur Concrete+ Special target for the period of 07th to 18th June 2021 is ".$row[1]." Bags. Achieve Ur Trgt Earn Spcl Benefits & Full Lakshya Benefits -AR HELP";
						//$message = "Dear AR, Ur balance to achieve ur Concrete+ special target of 07th to 18th June is ".$row[1]." bags. Achieve Ur Trgt Earn Spcl Benefits & Full Lakshya Benefits -AR HELP";
						//$message = "DEAR AR, YOUR BALANCE TO ACHIEVE YOUR MONTHLY TARGET OF JUNE 2021 IS ".$row['1']." BAGS. ACHIEVE YOUR TARGET & EARN SPECIAL BENEFITS - AR HELP";
						$message = "DEAR AR , ".$row['1']." PLUS POINTS REDEEMED FROM YOUR ACCOUNT AND THE VALUE OF ".$row['1']." POINTS IS RS.".$row['2']." CREDITED IN YOUR ACCOUNT ON 31.03.2021. NOW YOU HAVE BALANCE ".$row['3']." PLUS POINTS IN YOUR ACCOUNT";
						
						$phone = '91'.$row[0];
						echo $phone.'<br/>';
						echo $message.'<br/>';
						$status = sendMessage($message,$phone);
					}
				}
				fclose($file_handle);
			}
		?>
	</body>
</html>