<?php
function checkEngineerPoints($engId,$qty)
{
	require '../connect.php';
	
	$today = date('d-m-Y');
	$sales = mysqli_query($con,"SELECT SUM(qty),SUM(return_bag) FROM nas_sale WHERE (ar_id = '$engId' OR eng_id = '$engId') AND bill_no NOT LIKE 'a%' AND bill_no NOT LIKE 'A%' AND bill_no <> '' ") or die(mysqli_error($con));		 
	$sale = mysqli_fetch_array($sales,MYSQLI_ASSOC);
	
	$totalSale = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];
	
	$redemptions = mysqli_query($con,"SELECT SUM(points) FROM redemption WHERE ar_id = '$engId'") or die(mysqli_error($con));		 
	$redemption = mysqli_fetch_array($redemptions,MYSQLI_ASSOC);
	
	$totalRedemption = $redemption['SUM(points)'];
	
	$prevTotal = $totalSale - $totalRedemption - $qty;
	$newTotal = $totalSale - $totalRedemption;
	
	if($prevTotal > 0 && $newTotal > 0)
	{
		if(floor($prevTotal/600) < floor($newTotal/600))
		{
			$acheivedGold = floor($newTotal/600) - floor($prevTotal/600);
			$totalGold = floor($newTotal/600);
			$message = "Dear customer, Total balance as on ".$today." is ".$newTotal." points. You are eligible for ".$totalGold." Grams of gold.";
			
			$engQuery = mysqli_query($con,"SELECT name,mobile FROM ar_details WHERE id = '$engId' ") or die(mysqli_error($con));		 
			$eng = mysqli_fetch_array($engQuery,MYSQLI_ASSOC);
			sendSingleSms($eng['mobile'],$message);
		}		
	}
	else if($prevTotal < 0 && $newTotal > 0)
	{
		if(floor($newTotal/600) > 0)
		{
			$acheivedGold = floor($newTotal/600);
			$totalGold = floor($newTotal/600);
			
			$message = "Dear customer, Total balance as on ".$today." is ".$newTotal." points. You are eligible for ".$totalGold." Grams of gold.";
			
			$engQuery = mysqli_query($con,"SELECT name,mobile FROM ar_details WHERE id = '$engId' ") or die(mysqli_error($con));		 
			$eng = mysqli_fetch_array($engQuery,MYSQLI_ASSOC);
			sendSingleSms($eng['mobile'],$message);
		}				
	}
}

function checkEngineerRedemption($engId,$points)
{
	require '../connect.php';
	
	$today = date('d-m-Y');
	$sales = mysqli_query($con,"SELECT SUM(qty),SUM(return_bag) FROM nas_sale WHERE (ar_id = '$engId' OR eng_id = '$engId') AND bill_no NOT LIKE 'a%' AND bill_no NOT LIKE 'A%' AND bill_no <> '' ") or die(mysqli_error($con));		 
	$sale = mysqli_fetch_array($sales,MYSQLI_ASSOC);
	
	$totalSale = $sale['SUM(qty)'] - $sale['SUM(return_bag)'];
	
	$redemptions = mysqli_query($con,"SELECT SUM(points) FROM redemption WHERE ar_id = '$engId'") or die(mysqli_error($con));		 
	$redemption = mysqli_fetch_array($redemptions,MYSQLI_ASSOC);
	
	$totalRedemption = $redemption['SUM(points)'];
	$balancePoints = $totalSale - $totalRedemption;
	$gold =  floor($points/600);
	
	$message = "Dear customer, You have redeemed ".$points." points. We will send you ".$gold." grams of gold shortly. Your available balance is ".$balancePoints." points. ";
	
	$engQuery = mysqli_query($con,"SELECT name,mobile FROM ar_details WHERE id = '$engId' ") or die(mysqli_error($con));		 
	$eng = mysqli_fetch_array($engQuery,MYSQLI_ASSOC);
	sendSingleSms($eng['mobile'],$message);
}

function sendSingleSms($phone,$message)
{
/*	
	require '../connect.php';
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
			CURLOPT_URL => "https://control.msg91.com/api/postsms.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "<MESSAGE><AUTHKEY>212006AOQzrpS4jW5adee1be</AUTHKEY><ROUTE>AUTO</ROUTE><CAMPAIGN>ENGINEERS</CAMPAIGN><COUNTRY>91</COUNTRY><SENDER>ACCHLP</SENDER><SMS TEXT=\"".$message."\"><ADDRESS TO=\"".$phone."\"></ADDRESS></SMS></MESSAGE>",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER => array(
			"content-type: application/xml"
			),
		)
	);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err)
	{
		$status = "cURL Error #:" . $err;	 
		mysqli_query($con,"INSERT INTO sms_report (sent_to, message, status) VALUES ('$phone', '$message', '$status')") or die(mysqli_error($con));		 		
	}		
	else
	{
		mysqli_query($con,"INSERT INTO sms_report (sent_to, message, status) VALUES ('$phone', '$message', '$response')") or die(mysqli_error($con));		 						
	}		
*/	
}