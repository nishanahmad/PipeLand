<?php

session_start();
if($_SESSION['sheet_only'] == 1)
	header("Location:Sheet/requests.php");
else	
	header("Location:index/home.php");