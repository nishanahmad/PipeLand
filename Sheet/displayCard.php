<?php
function displayCard($sheet)
{
	$card = $sheet['area'].'<br/>';
	$card = $card.$sheet['customer_name'].', '.$sheet['customer_phone'].'<br/><b>';
	$card = $card.$sheet['bags'].' bags<br/>';
	$card = $card.$sheet['requested_by'].'<br/>';
	$card = $card.$sheet['shop'].'</b><br/>';
	if(isset($sheet['remarks']) && $sheet['remarks'] != '')
		$card = $card.$sheet['remarks'].'<br/>';
	$card = $card.'<font color="limegreen">Created On: '.date('d-M h:i A',strtotime($sheet['created_on'])).'</font>';
	
	return $card;
}
?>
