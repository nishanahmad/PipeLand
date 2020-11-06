<?php
header('Content-Type: application/json');

require '../connect.php';

$id = $_POST['saleId'];

$saleQuery = mysqli_query($con, "SELECT * FROM sales WHERE id = $id") or die(mysqli_error($con));
$sale = mysqli_fetch_array($saleQuery, MYSQLI_ASSOC);

echo json_encode(array('id' => $id, 'date' => date('d-m-Y',strtotime($sale['date'])), 'client' => $sale['client'], 'truck' => $sale['truck'],
					   'product' => $sale['product'],'qty' => $sale['qty'], 'discount' => $sale['discount'], 'remarks' => $sale['remarks'],
					   'customer' => $sale['customer'], 'phone' => $sale['phone'], 'address' => $sale['address'], 'bill' => $sale['bill']));
exit;