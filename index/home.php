<!DOCTYPE html>
<html>
<?php
session_start();

if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
}
else
	header("Location:../sessions/loginPage.php");																													?>