<?php
function billUpdatedCheck($oldSale,$newSale,$con)
{
	$oldBill = $oldSale['bill_no'];
	$newBill = $newSale['bill_no'];
	
	if($oldBill != $newBill)
	{
		if( fnmatch("B*",$newBill) || fnmatch("C*",$newBill) || fnmatch("GB*",$newBill) || fnmatch("GC*",$newBill) || fnmatch("PB*",$newBill) || fnmatch("PC*",$newBill))
			return true;
	}
	else
	{
		return false;
	}
}

function clearPendingTruck($oldSale,$newSale,$con)
{
	if($oldSale != null)
		$oldTruck = $oldSale['truck'];
	else
		$oldTruck = 'Old Truck';	
	
	$newTruck = $newSale['truck'];
	$bill = $newSale['bill_no'];
	$product = $newSale['product'];
	$saleQty = $newSale['qty'];
	$saleId = $newSale['sales_id'];
	$godown = $newSale['godown'];

	if($oldTruck != $newTruck)
	{
		$loadSql = mysqli_query($con,"SELECT * FROM loading WHERE product=$product AND truck = $newTruck AND status = 'pending'");
		if(mysqli_num_rows($loadSql) > 0)
		{
			$load = mysqli_fetch_array($loadSql, MYSQLI_ASSOC);
			$loadId = $load['id'];
			$date = $load['date'];
			$time = $load['time'];
			if( fnmatch("B*",$bill) || fnmatch("C*",$bill) || fnmatch("GB*",$bill) || fnmatch("GC*",$bill) || fnmatch("PB*",$bill) || fnmatch("PC*",$bill))
			{
				if($load['qty'] <= $saleQty)
				{
					$clear = mysqli_query($con,"UPDATE loading SET status = 'cleared', cleared_sale = $saleId WHERE id= $loadId") or die(mysqli_error($con));
				}
				else	
				{
					$difference = $load['qty'] - $saleQty;
					$clear = mysqli_query($con,"UPDATE loading SET qty = qty - $difference, status = 'cleared', cleared_sale = $saleId WHERE id= $loadId") or die(mysqli_error($con));
					$new = mysqli_query($con,"INSERT INTO loading (date,time,truck,product,qty,cleared_sale) VALUES ('$date','$time',$newTruck,$product,$difference,$saleId)") or die(mysqli_error($con));
				}
				$lockSale = mysqli_query($con,"UPDATE nas_sale SET locked = 1 WHERE sales_id = $saleId") or die(mysqli_error($con));
			}
		}
	}
}

function updateUserDetails($oldSale,$newSale)
{
	require '../connect.php';

	$products = mysqli_query($con,"SELECT id,name FROM products WHERE status = 1 ORDER BY id ASC");
	foreach($products as $product)
	{
		$productMap[$product['id']] = $product['name'];
	}
	
	$arObjects = mysqli_query($con,"SELECT id,name FROM ar_details ORDER BY name");
	foreach($arObjects as $arObject)
	{
		$arId = $arObject['id'];
		$arMap[$arId] = $arObject['name'];
	}
		
	$id = $newSale['sales_id'];
	$user = $_SESSION["user_name"];
	$userId = $_SESSION["user_id"];
	$dateTime = date('Y-m-d H:i:s');	
	
	$unlocked = false;
	
	if($oldSale['entry_date'] != $newSale['entry_date'])
	{
		$oldValue = date('d-m-Y',strtotime($oldSale['entry_date']));
		$newValue = date('d-m-Y',strtotime($newSale['entry_date']));
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Date', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}
	if($oldSale['ar_id'] != $newSale['ar_id'])
	{
		$oldValue = $arMap[$oldSale['ar_id']];
		$newValue = $arMap[$newSale['ar_id']];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'AR', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));	
		$unlocked = true;		
	}	
	if($oldSale['eng_id'] != $newSale['eng_id'])
	{
		$oldValue = $arMap[$oldSale['eng_id']];
		$newValue = $arMap[$newSale['eng_id']];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Engineer', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['truck_no'] != $newSale['truck_no'])
	{
		$oldValue = $oldSale['truck_no'];
		$newValue = $newSale['truck_no'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Truck', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;				
	}	
	if($oldSale['product'] != $newSale['product'])
	{
		$oldValue = $productMap[$oldSale['product']];
		$newValue = $productMap[$newSale['product']];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Product', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['qty'] != $newSale['qty'])
	{
		$oldValue = $oldSale['qty'];
		$newValue = $newSale['qty'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Qty', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}		
	if($oldSale['return_bag'] != $newSale['return_bag'])
	{
		$oldValue = $oldSale['return_bag'];
		$newValue = $newSale['return_bag'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Return', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['discount'] != $newSale['discount'])
	{
		$oldValue = $oldSale['discount'];
		$newValue = $newSale['discount'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Discount', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}		
	if($oldSale['remarks'] != $newSale['remarks'])
	{
		$oldValue = $oldSale['remarks'];
		$newValue = $newSale['remarks'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Remarks', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['bill_no'] != $newSale['bill_no'])
	{
		$oldValue = $oldSale['bill_no'];
		$newValue = $newSale['bill_no'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Bill', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['truck_no'] != $newSale['truck_no'])
	{
		$oldValue = $oldSale['truck_no'];
		$newValue = $newSale['truck_no'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Truck', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}		
	if($oldSale['order_no'] != $newSale['order_no'])
	{
		$oldValue = $oldSale['order_no'];
		$newValue = $newSale['order_no'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Order No', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}			
	if($oldSale['customer_name'] != $newSale['customer_name'])
	{
		$oldValue = $oldSale['customer_name'];
		$newValue = $newSale['customer_name'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Cust Name', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['customer_phone'] != $newSale['customer_phone'])
	{
		$oldValue = $oldSale['customer_phone'];
		$newValue = $newSale['customer_phone'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Cust Phone', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;		
	}	
	if($oldSale['address1'] != $newSale['address1'])
	{
		$oldValue = $oldSale['address1'];
		$newValue = $newSale['address1'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Address1', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));	
		$unlocked = true;				
	}	
	if($oldSale['address2'] != $newSale['address2'])
	{
		$oldValue = $oldSale['address2'];
		$newValue = $newSale['address2'];
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'Address2', '$oldValue', '$newValue')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		$unlocked = true;				
	}		
	
	$query = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = '$id'");
	if(mysqli_num_rows($query) > 0 && $unlocked)
	{
		$update ="UPDATE tally_sale_check SET status = 'UNLOCKED', unlocked_by = $userId WHERE sale = '$id'";		
		$result = mysqli_query($con, $update);				 			 			
	}				
}