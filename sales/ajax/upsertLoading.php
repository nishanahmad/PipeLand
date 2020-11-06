<?php
header('Content-Type: application/json');

require '../../connect.php';
require '../listHelper.php';

$productNamesMap = getProductNames($con);
$truckNumbersMap = getTruckNumbers($con);
	
if(isset($_POST['id']))
	$id = $_POST['id'];
if(isset($_POST['product']))
	$product = $_POST['product'];
if(isset($_POST['qty']))
	$qty = $_POST['qty'];
if(isset($_POST['truck']))
	$truck = $_POST['truck'];

$date = date('Y-m-d');
$time = date('H:i');

if(!isset($id) || !empty($id)) 		// if sale id not present, that means this is an insert operation
{
	if(!empty($truck))
	{
		$loadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = $truck AND unbilled_qty >= $qty");
		
		if(mysqli_num_rows($loadQuery) > 0 )
		{
			$load = mysqli_fetch_array($loadQuery, MYSQLI_ASSOC);
			$loadId = $load['id'];
			$update = "UPDATE loading SET unbilled_qty = unbilled_qty - $qty WHERE id = $loadId";
			$result = mysqli_query($con,$update);

			if($result)
			{
				$response_array['status'] = 'success';
				$response_array['value'] = 'Success!!!';	
			}
			else
			{
				$response_array['status'] = 'error';
				$response_array['value'] = mysqli_error($con);
			}		
		}
		else
		{
			$response_array['status'] = 'error';
			$response_array['value'] = 'Truck number '.$truckNumbersMap[$truck].' does not contain '.$qty.' bags of '.$productNamesMap[$product];
		}		
	}
	else
	{
		$response_array['status'] = 'success';
		$response_array['value'] = 'Success!!!';			
	}

}
else
{
	$oldSaleQuery = mysqli_query($con,"SELECT * FROM nas_sale WHERE sales_id = $id") or die(mysqli_error($con));
	$oldSale = mysqli_fetch_array($oldSaleQuery, MYSQLI_ASSOC);
	$oldProduct = $oldSale['product'];
	$oldQty = $oldSale['qty'];
	$oldTruck = $oldSale['truck'];
	
	if(empty($oldTruck) && empty($truck))
	{
		$response_array['status'] = 'success';
		$response_array['value'] = 'No truck inserted or updated. Proceed with submit';
	}
	else
	{
		if($oldProduct != $product)
		{
			$response_array['status'] = 'error';
			$response_array['value'] = 'You cannot update product after truck is entered.Please remove truck first and then update product';
		}
		else
		{
			if($oldQty != $qty && $oldTruck != $truck)
			{
				$oldloadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$oldTruck' ORDER BY id DESC LIMIT 1");
				if(mysqli_num_rows($oldloadQuery) > 0 )
				{
					$oldload = mysqli_fetch_array($oldloadQuery, MYSQLI_ASSOC);
					$oldloadId = $oldload['id'];					
				}
				
				$loadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$truck' AND unbilled_qty >= $qty ORDER BY id DESC LIMIT 1");
				if(mysqli_num_rows($loadQuery) > 0 )
				{
					$load = mysqli_fetch_array($loadQuery, MYSQLI_ASSOC);				
					$loadId = $load['id'];					
				}
				
				if(isset($load))
				{			
					mysqli_autocommit($con, false);

					if(isset($oldload))
						$upsert = "UPDATE loading SET unbilled_qty = unbilled_qty + $oldQty WHERE id = $oldloadId";
					else
						$upsert = "UPDATE loading SET qty = qty WHERE id = $loadId";   // Do nothing
					
					$update = "UPDATE loading SET unbilled_qty = unbilled_qty - $qty WHERE id = $loadId";

					$result1 = mysqli_query($con, $upsert);
					if(!$result1)
						$errormsg = mysqli_error($con);
						
					$result2 = mysqli_query($con, $update);
					if(!$result2)
						$errormsg = mysqli_error($con);

					if ($result1 && $result2)
					{
						mysqli_commit($con);
						$response_array['status'] = 'success';
						$response_array['value'] = 'Success!!';
					}
					else
					{
						mysqli_rollback($con);
						$response_array['status'] = 'error';
						$response_array['value'] = $errormsg;
					}

					mysqli_close($con);
				}
				else
				{
					$update = "UPDATE loading SET unbilled_qty = unbilled_qty + $oldQty WHERE id = $oldloadId";
					$result = mysqli_query($con, $update);
					if($result)
					{
						$response_array['status'] = 'success';
						$response_array['value'] = 'Success !!!';
					}
					else
					{
						$response_array['status'] = 'error';
						$response_array['value'] = mysqli_error($con);
					}

				}
			}
			else if($oldQty != $qty)
			{
				if($oldQty < $qty)
				{
					$difference = $qty - $oldQty;
					$loadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$truck' AND unbilled_qty >= $difference ORDER BY id DESC LIMIT 1");
					if(mysqli_num_rows($loadQuery) > 0 )
					{
						$load = mysqli_fetch_array($loadQuery, MYSQLI_ASSOC);				
						$loadId = $load['id'];
						$update = "UPDATE loading SET unbilled_qty = unbilled_qty - $difference WHERE id = $loadId";
						$result = mysqli_query($con,$update);
						if($result)
						{
							$response_array['status'] = 'success';
							$response_array['value'] = 'Success!!!';			
						}
						else
						{
							$response_array['status'] = 'error';
							$response_array['value'] = mysqli_error($con);
						}
					}
					else
					{
						$response_array['status'] = 'error';
						$response_array['value'] = 'Truck '.$truckNumbersMap[$truck].' doesnt have enough qty to perform this operation';						
					}
				}
				else
				{
					$difference = $oldQty - $qty;
					$loadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$truck' ORDER BY id DESC LIMIT 1");
					if(mysqli_num_rows($loadQuery) > 0 )
					{
						$load = mysqli_fetch_array($loadQuery, MYSQLI_ASSOC);				
						$loadId = $load['id'];
						$update = "UPDATE loading SET unbilled_qty = unbilled_qty + $difference WHERE id = $loadId";
						$result = mysqli_query($con,$update);
						if($result)
						{
							$response_array['status'] = 'success';
							$response_array['value'] = 'Success!!!';			
						}
						else
						{
							$response_array['status'] = 'error';
							$response_array['value'] = mysqli_error($con);
						}
					}
					else
					{
						$insert = "INSERT INTO loading (date, time, truck, product, qty, unbilled_qty) VALUES ('$date', '$time', $truck, $product, $difference, $difference)";
						$result = mysqli_query($con,$insert);
						if($result)
						{
							$response_array['status'] = 'success';
							$response_array['value'] = 'Success!!!';			
						}
						else
						{
							$response_array['status'] = 'error';
							$response_array['value'] = mysqli_error($con);
						}
					}					
				}					
			}
			else if($oldTruck != $truck)	
			{
				$loadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$truck' AND unbilled_qty >= $qty ORDER BY id DESC LIMIT 1");
				if(mysqli_num_rows($loadQuery) > 0 )
				{
					mysqli_autocommit($con,FALSE);
					
					$load = mysqli_fetch_array($loadQuery, MYSQLI_ASSOC);				
					$loadId = $load['id'];
					$update = "UPDATE loading SET unbilled_qty = unbilled_qty - $qty WHERE id = $loadId";
					mysqli_query($con,$update);
					
					$oldLoadQuery = mysqli_query($con,"SELECT * FROM loading WHERE product = $product AND truck = '$oldTruck' ORDER BY id DESC LIMIT 1");
					if(mysqli_num_rows($oldLoadQuery) > 0)
					{
						$oldLoad = mysqli_fetch_array($oldLoadQuery, MYSQLI_ASSOC);				
						$oldLoadId = $oldLoad['id'];						
						$updateOld = "UPDATE loading SET unbilled_qty = unbilled_qty + $qty WHERE id = $oldLoadId";
						mysqli_query($con,$updateOld);
					}
					else
					{
						$insert = "INSERT INTO loading (date, time, truck, product, qty, unbilled_qty) VALUES ('$date', '$time', $oldTruck, $product, $qty, $qty)";
						mysqli_query($con,$insert);
					}
					
					if(mysqli_commit($con))
					{
						$response_array['status'] = 'success';
						$response_array['value'] = 'Success!!!';					
					}
					else
					{
						mysqli_rollback($con);	
						$response_array['status'] = 'error';
						$response_array['value'] = mysqli_error($con);
					}	
				}
				else
				{
					$response_array['status'] = 'error';
					$response_array['value'] = 'Truck doesnt have enough qty to perform this operation';					
				}									
			}
			else
			{
				$response_array['status'] = 'success';
				$response_array['value'] = 'No changes. You may proceed with the submit';												
			}
		}
	}
}

echo json_encode($response_array);

exit;