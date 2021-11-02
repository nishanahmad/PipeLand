<?php

require_once "../../connect.php";

session_start();

if(!empty($_POST["keyword"])) 
{
	$query ="SELECT DISTINCT customer_phone FROM nas_sale WHERE customer_phone like '" . $_POST["keyword"] . "%' ORDER BY customer_phone LIMIT 0,6";
	$result = mysqli_query($con, $query);
	if(!empty($result)) 
	{																															?>
		<ul id="country-list">																									<?php
			foreach($result as $sale) 
			{																														?>
				<li onClick="selectPhone('<?php echo $sale["customer_phone"]; ?>');"><?php echo $sale["customer_phone"]; ?></li><?php 
			} 																													  ?>
		</ul>																													<?php 
	} 
} 																																?>
