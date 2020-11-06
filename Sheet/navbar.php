<?php
$flag = null;
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, 'new') !== false)
	$flag = 'new';
if (strpos($url, 'edit') !== false)
	$flag = 'new';
if (strpos($url, 'plan') !== false)
	$flag = 'plan';
if (strpos($url, 'requests') !== false)
	$flag = 'requests';
if (strpos($url, 'deliveries') !== false)
	$flag = 'deliveries';
if (strpos($url, 'transfer') !== false)
	$flag = 'transfer';
if (strpos($url, 'transfer_logs') !== false)
	$flag = 'transfer_logs';
if (strpos($url, 'closed') !== false)
	$flag = 'closed';

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet"/>
<script src="https://kit.fontawesome.com/742221945b.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SlickNav/1.0.10/jquery.slicknav.min.js" integrity="sha512-FmCXNJaXWw1fc3G8zO3WdwR2N23YTWDFDTM3uretxVIbZ7lvnjHkciW4zy6JGvnrgjkcNEk8UNtdGTLs2GExAw==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/SlickNav/1.0.10/slicknav.min.css" integrity="sha512-heyoieAHmpAL3BdaQMsbIOhVvGb4+pl4aGCZqWzX/f1BChRArrBy/XUZDHW9WVi5p6pf92pX4yjkfmdaIYa2QQ==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js" integrity="sha512-8vfyGnaOX2EeMypNMptU+MwwK206Jk1I/tMQV4NkhOz+W8glENoMhGyU6n/6VgQUhQcJH8NqQgHhMtZjJJBv3A==" crossorigin="anonymous"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<style>
.menu-navigation-dark {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 0;
    text-align: center;
}

.menu-navigation-dark a {
    display: inline-block;
    color: #ffffff;
    background-color: #232526;
    font-size: 35px;
    font-weight: bold;
    box-shadow: 1px 3px 5px 0 rgba(0, 0, 0, 0.26);
    text-transform: uppercase;
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid #161718;
    border-top: none;
    width: 136px;
    margin: 0 auto;
    padding: 20px 0;
    box-sizing: border-box;
}

.menu-navigation-dark a:hover {
    background-color: #27292a;
}

.menu-navigation-dark a:first-child{
    border-left: 1px solid #161718;
}

.menu-navigation-dark a:not(.selected) {
    box-shadow:	1px 3px 5px 0 rgba(0, 0, 0, 0.26),
    inset 1px 0 0 #323435,
    inset 0 1px 0 #282a2b,
    inset 0 -1px 0 #282a2b;
}

.menu-navigation-dark a:last-child {
    box-shadow:	1px 3px 5px 0 rgba(0, 0, 0, 0.26),
    inset 1px 0 0 #323435,
    inset -1px 0 0 #282a2b,
    inset 0 1px 0 #282a2b,
    inset 0 -1px 0 #282a2b;
}

.menu-navigation-dark a i {
    display: block;
    line-height: 1.3;
}

.menu-navigation-dark a span {
    display: block;
    font-size: 11px;
    font-weight: bold;
    line-height: 2;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

.menu-navigation-dark a.selected {
    background-color: #1d1e1f;
    border-bottom: 2px solid #4a9fda;
    pointer-events: none;
}

.menu-navigation-dark a.selected i {
    color: #4a9fda;
}

/* Make this page responsive */

.slicknav_menu {
    display:none;
}

@media (max-width: 800px) {
    .menu-navigation-dark{
        display:none;
    }

    .slicknav_nav a i {
        display: none;
    }

    .slicknav_menu {
        display:block;
    }
}

</style>
		<nav class="menu-navigation-dark">																		<?php 
			if($_SESSION['role'] != 'driver')
			{																									?>	
				<a href="../index.php"><i class="fa fa-home"></i><span>Home</span></a>
				<a href="new.php"  class="<?php if($flag == 'new') echo 'selected';?>"><i class="fa fa-plus"></i><span>New</span></a>
				<a href="plan.php"  class="<?php if($flag == 'plan') echo 'selected';?>"><i class="fa fa-list-alt"></i><span>Driver Assign</span></a>					<?php
			}																									?>	
			<a href="requests.php" class="<?php if($flag == 'requests') echo 'selected';?>"><i class="fa fa-spinner"></i><span>Pending ...</span></a>
			<a href="deliveries.php"  class="<?php if($flag == 'deliveries') echo 'selected';?>"><i class="fa fa-truck"></i><span>Delivered</span></a>
			<a href="transfer.php" class="<?php if($flag == 'transfer') echo 'selected';?>"><i class="fa fa-exchange"></i><span>Transfer</span></a>					<?php
			if($_SESSION['role'] != 'driver')
			{																									?>				
				<a href="transfer_logs.php" class="<?php if($flag == 'transfer_logs') echo 'selected';?>"><i class="fa fa-file-text"></i><span>Transfer Logs</span></a>
				<a href="closed.php" class="<?php if($flag == 'closed') echo 'selected';?>"><i class="fa fa-check-square"></i><span>Closed</span></a><?php
			}?>				
		</nav>		