<?php
ini_set('max_execution_time', '0'); // for infinite time of execution 
ini_set('memory_limit', '-1');

function getSales($con,$sql)
{	
	$mainMap = array();
	
	if(!strpos($sql,'ORDER BY bill_no') !== false)
	{
		$sql = $sql.' ORDER BY entry_date DESC';
	}
	else
	{
		$today = date('Y-m-d');
		$day10backwards = date( 'Y-m-d', strtotime('-10 days') );		
		$unbilledOldSalesQuery = mysqli_query($con,"SELECT * FROM nas_sale WHERE deleted IS NULL AND entry_date < '$today' AND entry_date > '$day10backwards'") or die(mysqli_error($con));
		foreach($unbilledOldSalesQuery as $sale) 
		{
			if( !(fnmatch("B*",$sale['bill_no']) || fnmatch("C*",$sale['bill_no']) || fnmatch("GB*",$sale['bill_no']) || fnmatch("GC*",$sale['bill_no']) || fnmatch("PB*",$sale['bill_no']) || fnmatch("PC*",$sale['bill_no'])))
			{
				$saleArray = array();
				$saleArray['id'] = $sale['sales_id'];
				$saleArray['date'] = $sale['entry_date'];
				$saleArray['client'] = $sale['ar_id'];
				$saleArray['engineer'] = $sale['eng_id'];
				$saleArray['product'] = $sale['product'];
				$saleArray['qty'] = $sale['qty'];
				$saleArray['discount'] = $sale['discount'];
				$saleArray['bill'] = $sale['bill_no'];
				$saleArray['truck'] = $sale['truck'];
				$saleArray['name'] = $sale['customer_name'];
				$saleArray['phone'] = $sale['customer_phone'];
				$saleArray['remarks'] = $sale['remarks'];
				$saleArray['address'] = $sale['address1'];
				$saleArray['direct_order'] = $sale['direct_order'];
				
				$mainMap[] = $saleArray;			
			}			
		}
	}
		
	$salesQuery = mysqli_query($con,$sql) or die(mysqli_error($con));	
	foreach($salesQuery as $sale) 
	{ 
		$saleArray = array();
		$saleArray['id'] = $sale['sales_id'];
		$saleArray['date'] = $sale['entry_date'];
		$saleArray['client'] = $sale['ar_id'];
		$saleArray['engineer'] = $sale['eng_id'];
		$saleArray['product'] = $sale['product'];
		$saleArray['qty'] = $sale['qty'];
		$saleArray['discount'] = $sale['discount'];
		$saleArray['bill'] = $sale['bill_no'];
		$saleArray['truck'] = $sale['truck'];
		$saleArray['name'] = $sale['customer_name'];
		$saleArray['phone'] = $sale['customer_phone'];
		$saleArray['remarks'] = $sale['remarks'];
		$saleArray['address'] = $sale['address1'];
		$saleArray['direct_order'] = $sale['direct_order'];
		
		$mainMap[] = $saleArray;
	}
	
	return $mainMap;
}

function getProductSum($con,$sql)
{	
	$productSumMap = array();
	if(strpos($sql,'ORDER BY bill_no') !== false)
		$sql = str_replace('ORDER BY bill_no','',$sql);
	
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


function getGodownNames($con)
{
	$godownMap = array();
	$godowns = mysqli_query($con,"SELECT * FROM godowns ORDER BY name");
	foreach($godowns as $godown)
		$godownMap[$godown['id']] = $godown['name'];	
		
	return $godownMap;	
}


function getClientType($con)
{
	$clientMap = array();
	$clients = mysqli_query($con,"SELECT id,type FROM ar_details ORDER BY name ASC");	
	foreach($clients as $client)
		$clientMap[$client['id']] = $client['type'];
		
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