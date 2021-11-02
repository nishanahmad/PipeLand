<?php
$flag = null;
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, 'home') !== false)
	$flag = 'home';
if (strpos($url, 'sales') !== false)
	$flag = 'sales';
if (strpos($url, 'trucks') !== false)
	$flag = 'loading';
if (strpos($url, 'ar') !== false || strpos($url, 'points') !== false || strpos($url, 'Target') !== false || strpos($url, 'redemption') !== false)
	$flag = 'ar';
if (strpos($url, 'engineers') !== false || strpos($url, 'Engineer') !== false)
	$flag = 'engineers';
if (strpos($url, 'rate/') !== false)
	$flag = 'rate';
if (strpos($url, 'discounts') !== false)
	$flag = 'discounts';
if (strpos($url, 'reports') !== false)
	$flag = 'reports';

$today = date('Y-m-d');
$sql = "SELECT * FROM nas_sale WHERE deleted IS NULL AND entry_date = '$today'";

$url = "javascript:location.href='../sales/list.php?range=Today&sql=".$sql."'";
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous"/>
<link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet"/>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/742221945b.js" crossorigin="anonymous"></script>
<style>
body{
	font-family: 'Ubuntu', sans-serif;
	overflow-x: hidden;
}
.main-brand{
	font-family:'GlacialIndifferenceRegular';
	font-weight:normal;
	font-style:normal;
	margin-left:20px;
}
.btn:hover {
  background-color: #343A40 !important;
}

.navbar-icon-top .navbar-nav .nav-link > .fa {
  position: relative;
  width: 36px;
  font-size: 20px;
}

.navbar-icon-top .navbar-nav .nav-link > .fa > .badge {
  font-size: 0.75rem;
  position: absolute;
  right: 0;
  font-family: sans-serif;
}

.navbar-icon-top .navbar-nav .nav-link > .fa {
  top: 3px;
  line-height: 12px;
}

.navbar-icon-top .navbar-nav .nav-link > .fa > .badge {
  top: -10px;
}

@media (min-width: 576px) {
  .navbar-icon-top.navbar-expand-sm .navbar-nav .nav-link {
	text-align: center;
	display: table-cell;
	height: 70px;
	vertical-align: middle;
	padding-top: 0;
	padding-bottom: 0;
  }

  .navbar-icon-top.navbar-expand-sm .navbar-nav .nav-link > .fa {
	display: block;
	width: 48px;
	margin: 2px auto 4px auto;
	top: 0;
	line-height: 24px;
  }

  .navbar-icon-top.navbar-expand-sm .navbar-nav .nav-link > .fa > .badge {
	top: -7px;
  }
}

@media (min-width: 768px) {
   #content-mobile{
	   display:none;
   }
  .navbar-icon-top.navbar-expand-md .navbar-nav .nav-link {
	text-align: center;
	display: table-cell;
	height: 70px;
	vertical-align: middle;
	padding-top: 0;
	padding-bottom: 0;
  }

  .navbar-icon-top.navbar-expand-md .navbar-nav .nav-link > .fa {
	display: block;
	width: 48px;
	margin: 2px auto 4px auto;
	top: 0;
	line-height: 24px;
  }

  .navbar-icon-top.navbar-expand-md .navbar-nav .nav-link > .fa > .badge {
	top: -7px;
  }
}

@media (max-width: 768px) {
   #content-desktop{
	   display:none;
   }
}

@media (min-width: 992px) {
  .navbar-icon-top.navbar-expand-lg .navbar-nav .nav-link {
	text-align: center;
	display: table-cell;
	height: 70px;
	vertical-align: middle;
	padding-top: 0;
	padding-bottom: 0;
  }

  .navbar-icon-top.navbar-expand-lg .navbar-nav .nav-link > .fa {
	display: block;
	width: 48px;
	margin: 2px auto 4px auto;
	top: 0;
	line-height: 24px;
  }

  .navbar-icon-top.navbar-expand-lg .navbar-nav .nav-link > .fa > .badge {
	top: -7px;
  }
}

@media (min-width: 1200px) {
  .navbar-icon-top.navbar-expand-xl .navbar-nav .nav-link {
	text-align: center;
	display: table-cell;
	height: 70px;
	vertical-align: middle;
	padding-top: 0;
	padding-bottom: 0;
  }

  .navbar-icon-top.navbar-expand-xl .navbar-nav .nav-link > .fa {
	display: block;
	width: 48px;
	margin: 2px auto 4px auto;
	top: 0;
	line-height: 24px;
  }

  .navbar-icon-top.navbar-expand-xl .navbar-nav .nav-link > .fa > .badge {
	top: -7px;
  }
}

.glow {
  color: #fff;
  text-align: center;
  -webkit-animation: glow 2s ease-in-out infinite alternate;
  -moz-animation: glow 2s ease-in-out infinite alternate;
  animation: glow 2s ease-in-out infinite alternate;
}

.glow-mobile {
  color: #B0E0E6;
  text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px white;
}

@-webkit-keyframes glow {
  from {
	text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #e60073, 0 0 40px #e60073, 0 0 50px #e60073, 0 0 60px #e60073;
  }
  to {
	text-shadow: 0 0 20px #fff, 0 0 30px #ff4da6, 0 0 40px #ff4da6, 0 0 50px #ff4da6, 0 0 60px #ff4da6, 0 0 70px #ff4da6;
  }
}
a {
  color: inherit; /* blue colors for links too */
  text-decoration: inherit; /* no underline */
}
</style>
<nav class="navbar navbar-icon-top navbar-expand-lg navbar-dark bg-dark sticky-top top-nav" id="content-desktop">
  <a class="navbar-brand main-brand" href="#"><img src="../images/logo.png"></img></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	<ul class="navbar-nav mr-auto">
	  <li class="nav-item">
		<a class="nav-link" <?php if($flag == 'home') echo 'href="#"'; else echo 'href="../index/home.php"';?>>
		  <i class="fa fa-home <?php if($flag == 'home') echo 'glow';?> aria-hidden="true"></i>
		  Home
		</a>
	  </li>	  
	  <li class="nav-item">
		<a class="nav-link" 
		  <?php if($flag == 'sales') echo 'href="#"'; else echo 'href="../sales/list.php?sql='.$sql.'&range=Today"';?>>
		  <i class="fa fa-bolt <?php if($flag == 'sales') echo 'glow';?> aria-hidden="true"></i>
		  Sales
		</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" <?php if($flag == 'ar') echo 'href="#"'; else echo 'href="../ar/list.php"';?>>
		  <i class="fa fa-address-card-o <?php if($flag == 'ar') echo 'glow';?> aria-hidden="true"></i>
		  AR
		</a>
	  </li>	  
	  <?php	
	  if($_SESSION['role'] != 'marketing')
	  {																																				?>
		  <li class="nav-item">
			<a class="nav-link" <?php if($flag == 'rate') echo 'href="#"'; else echo 'href="../rate/list.php"';?>>
			  <i class="fa fa-rupee-sign <?php if($flag == 'rate') echo 'glow';?> aria-hidden="true"></i>
			  Rate
			</a>
		  </li>	  
		  <li class="nav-item">
			<a class="nav-link" <?php if($flag == 'discounts') echo 'href="#"'; else echo 'href="../discounts/list.php"';?>>
			  <i class="fa fa-tags <?php if($flag == 'discounts') echo 'glow';?> aria-hidden="true"></i>
			  Discounts
			</a>
		  </li>																																		<?php
	  }																																				?>
	  <li class="nav-item">
		<a class="nav-link" <?php if($flag == 'reports') echo 'href="#"'; else echo 'href="../reports/salesSummary.php"';?>>
		  <i class="fa fa-chart-area <?php if($flag == 'reports') echo 'glow';?> aria-hidden="true"></i>
		  Reports
		</a>
	  </li>	  	  		
	  </li>
	</ul>
	<div class="float-right" style="margin-right:50px;">	
		<ul class="navbar-nav mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-user-circle"></i>
					<?php echo $_SESSION['user_name'];?>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="#">Logout</a>
					<a class="dropdown-item" href="#">Another action</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="../sessions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
				</div>
			</li>
		</ul>			
	</div>
  </div>
</nav>


<nav class="mobile-bottom-nav" id="content-mobile">
	<div class="mobile-bottom-nav__item <?php if($flag == 'home') echo 'glow-mobile';?>">
		<div class="mobile-bottom-nav__item-content"  onclick="javascript:location.href='../index/home.php'">
			<i class="fa fa-home fa-lg" aria-hidden="true"></i>
			Home
		</div>		
	</div>	
	<div class="mobile-bottom-nav__item <?php if($flag == 'sales') echo 'glow-mobile';?>">
		<a class="mobile-bottom-nav__item-content" href="../sales/list.php?sql=<?php echo $sql;?>&range=Today">
			<i class="fa fa-bolt fa-lg" aria-hidden="true"></i>
			Sales
		</a>		
	</div>
	<div class="mobile-bottom-nav__item <?php if($flag == 'reports') echo 'glow-mobile';?>">
		<div class="mobile-bottom-nav__item-content" onclick="javascript:location.href='../reports/salesSummary.php'">
			<i class="fa fa-chart-area fa-lg" aria-hidden="true"></i>
			Reports
		</div>		
	</div>
	<div class="mobile-bottom-nav__item">
		<div class="mobile-bottom-nav__item-content" onclick="javascript:location.href='https://menu.kannurzone.com/home.php'">
			<i class="fas fa-th-large"></i>
			NAS
		</div>		
	</div>	
</nav>