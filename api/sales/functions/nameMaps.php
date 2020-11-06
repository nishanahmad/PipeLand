<?php
function getArNamesMap($con)
{	
	$arNameMap = array();

	$arList = mysqli_query($con, "SELECT id,name,shop_name FROM ar_details ORDER BY name") or die(mysqli_error($con));				 	 
	foreach($arList as $ar)
	{
		$arNameMap[$ar['id']] = ['name' => $ar['name'],'shop' => $ar['shop_name']];
	}
	
	return $arNameMap;	
}

function getProductNamesMap($con)
{	
	$productNameMap = array();

	$products = mysqli_query($con, "SELECT id,name FROM products") or die(mysqli_error($con));				 	 
	foreach($products as $product)
	{
		$productNameMap[$product['id']] = $product['name'];
	}
	
	return $productNameMap;	
}
?>