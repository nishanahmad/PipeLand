<?php

function getSales($con,$sql)
{	
	if(!strpos($sql,'ORDER BY bill_no') !== false)
		$sql = $sql.' ORDER BY entry_date DESC';
				
	$mainMap = array();
	$salesQuery = mysqli_query($con,$sql) or die(mysqli_error($con));
	
	foreach($salesQuery as $sale) 
	{ 
		$saleArray = array();
		$saleArray['id'] = $sale['sales_id'];
		$saleArray['date'] = $sale['entry_date'];
		$saleArray['client'] = $sale['ar_id'];
		$saleArray['product'] = $sale['product'];
		$saleArray['qty'] = $sale['qty'];
		$saleArray['discount'] = $sale['discount'];
		$saleArray['bill'] = $sale['bill_no'];
		$saleArray['truck_no'] = $sale['truck_no'];
		$saleArray['name'] = $sale['customer_name'];
		$saleArray['phone'] = $sale['customer_phone'];
		$saleArray['remarks'] = $sale['remarks'];
		$saleArray['address'] = $sale['address1'];
		
		$mainMap[] = $saleArray;
	}
	
	return $mainMap;
}

function getProductSum($con,$sql)
{	
	$productSumMap = array();
	if(strpos($sql,'ORDER BY bill_no') !== false)
		$sql = str_replace('ORDER BY bill_no ASC','',$sql);
	
	$sql = str_replace('*','SUM(qty),product',$sql);
	$sql = $sql.' GROUP BY product';
	$sumQuery = mysqli_query($con,$sql) or die(mysqli_error($con));				 	 	
	foreach($sumQuery as $row) 
		$productSumMap[$row['product']] = $row['SUM(qty)'];
	
	return $productSumMap;
}

function getCurrentRates($con)
{	
	$rateMap = array();
	$rates = mysqli_query($con,"SELECT * FROM rate INNER JOIN products ON rate.product = products.id ORDER BY date DESC,products.name ASC") or die(mysqli_error($con));
	foreach($rates as $rate)
	{
		if(!isset($rateMap[$rate['product']]))
			$rateMap[$rate['product']] = $rate['rate'];
	}
	
	return $rateMap;
}


function getClientNames($con)
{
	$clientMap = array();
	$clients = mysqli_query($con,"SELECT id,name FROM ar_details ORDER BY name ASC");	
	foreach($clients as $client)
		$clientMap[$client['id']] = $client['name'];	
		
	return $clientMap;	
}


function getProductDetails($con)
{
	$productMap = array();
	$products = mysqli_query($con,"SELECT id,name,colorcode FROM products ORDER BY name ASC");
	foreach($products as $product)
	{
		$productMap[$product['id']]['name'] = $product['name'];	
		$productMap[$product['id']]['colorcode'] = $product['colorcode'];	
	}
		
		
	return $productMap;	
}


function getTruckNumbers($con)
{
	$truckNumbersMap = array();
	$trucks = mysqli_query($con,"SELECT id,number FROM truck_details");
	foreach($trucks as $truck)
		$truckNumbersMap[$truck['id']] = $truck['number'];	
		
	return $truckNumbersMap;	
}


function getDiscounts($con)
{
	$discountMap = array();
	$discounts = mysqli_query($con,"SELECT * FROM discounts WHERE type = 'Wagon Discount' AND date = CURDATE()") or die(mysqli_error($con));
	foreach($discounts as $discount)
	{
		$discountMap[$discount['product']] = $discount['discount'];
	}	
		
	return $discountMap;	
}

function getRateMap()
{
	require '../connect.php';	
	
	$rateMap = array();

	$rates = mysqli_query($con, "SELECT * FROM rate ORDER BY date") or die(mysqli_error($con));				 	 
	foreach($rates as $rate)
	{
		$rateMap[$rate['product']][$rate['date']] = $rate['rate'];
	}
	
	return $rateMap;
}

function closestDate($arr, $target)  
{
	$ans = -1;
	
	if(!in_array($target, $arr))
	{
		$start = 0; $end = sizeof($arr)-1;  


		while ($start <= $end)  
		{  
			$mid =(int)(($start + $end) / 2);  

			if ($arr[$mid] >= $target)  
			{
				$end = $mid - 1;  
			}
				
			else 
			{
				$ans = $mid;  
				$start = $mid + 1;
			}  
		}		
	}
	else
		$ans = array_search($target,$arr);
		
	return $ans;  
}

function getWDMap()
{
	require '../connect.php';	
	
	$wdMap = array();

	$discounts = mysqli_query($con, "SELECT * FROM discounts WHERE type = 'Wagon Discount' ORDER BY date") or die(mysqli_error($con));				 	 
	foreach($discounts as $discount)
	{
		$wdMap[$discount['product']][$discount['date']] = $discount['discount'];
	}
	
	return $wdMap;	
}


function getCDMap()
{
	require '../connect.php';	
	
	$cdMap = array();

	$discounts = mysqli_query($con, "SELECT * FROM discounts WHERE type = 'Cash Discount' ORDER BY date") or die(mysqli_error($con));				 	 
	foreach($discounts as $discount)
	{
		$cdMap[$discount['product']][$discount['client']][$discount['date']] = $discount['discount'];
	}

	foreach($cdMap as $product => $array1)
	{
		foreach($array1 as $client => $array2)
		{
			$current = date('Y-m-d', strtotime(get_array_key_first($array2)));
			$today = date('Y-m-d');

			while($current < $today)
			{
				$next = date('Y-m-d', strtotime($current. ' + 1 days'));
				if(!array_key_exists($next,$array2))
				{
					$cdMap[$product][$client][$next] = $cdMap[$product][$client][$current];
				}
				$current = date('Y-m-d', strtotime($current. ' + 1 days'));
			}
		}
	}
	
	return $cdMap;	
}

function get_array_key_first(array $arr) 
{
	foreach($arr as $key => $unused) 
	{
		return $key;
	}
	return NULL;
}

function get_array_key_last($array) 
{
	if (!is_array($array) || empty($array)) 
	{
		return NULL;
	}
	
	return array_keys($array)[count($array)-1];
}