<?php
	require '../connect.php';	
	session_start();	
	
	$total = 100;
	
	$agr = mysqli_query($con,"SELECT SUM(qty) FROM sheets WHERE status ='delivered'" ) or die(mysqli_error($con));
	$onSite = (int)mysqli_fetch_Array($agr,MYSQLI_ASSOC)['SUM(qty)'];
	
	$inHand = $total - $onSite;
	
	echo 'In hand - '.$inHand.'<br/>'; 
	echo 'On site - '.$onSite.'<br/>'; 
	echo 'Total - '.$total.'<br/>'; 
	