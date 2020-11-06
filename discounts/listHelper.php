<?php

function getWagonDiscounts($con)
{
	$wagonDiscountsMap = array();
	$discounts = mysqli_query($con,"SELECT * FROM discounts WHERE type = 'Wagon Discount' ORDER BY date DESC") or die(mysqli_error($con));
	foreach($discounts as $discount)
	{
		$value = $discount['date'].'--'.$discount['discount'];
		$wagonDiscountsMap[$discount['product']][] = $value;
	}
		
	return $wagonDiscountsMap;
}


function getClientDiscounts($con)
{	
	$clientDiscountsMap = array();
		$mainMap = array();
	$discounts = mysqli_query($con,"SELECT * FROM discounts WHERE type != 'Wagon Discount' ORDER BY date ASC") or die(mysqli_error($con));
	foreach($discounts as $discount)
	{
		$key = $discount['product'].'--'.$discount['client'].'--'.$discount['type'];
		$clientDiscountsMap[$key][] = array($discount['date'] => $discount['discount']);
	}
	
	
	foreach($clientDiscountsMap as $key => $subMap)
	{
		for($i=0; $i<sizeof($subMap); $i++)
		{
			foreach($subMap[$i] as $date => $discount)
			{
				$currentDisc = $discount;
				$startDate = $date;
			}
				
			if(isset($subMap[$i+1]))
			{			
				foreach($subMap[$i+1] as $date => $discount)
					$endDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));
			}
			else
			{
				$endDate = 'CURRENT';
			}	
			$mainMap[$key][$startDate.' TO '.$endDate] = $currentDisc;
		}
	}
	
	foreach($mainMap as $product => $subMap)
		$mainMap[$product] = array_reverse($subMap);

	return $mainMap;
}


function getProductNames($con)
{
	$productNameMap = array();
	$products = mysqli_query($con,"SELECT id,name FROM products ORDER BY name ASC");
	foreach($products as $product)
		$productNameMap[$product['id']] = $product['name'];
		
	return $productNameMap;
}


function getClientNames($con)
{
	$clientMap = array();
	$clients = mysqli_query($con,"SELECT id,name FROM ar_details ORDER BY name ASC");	
	foreach($clients as $client)
		$clientMap[$client['id']] = $client['name'];	
		
	return $clientMap;	
}