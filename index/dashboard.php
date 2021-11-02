<!DOCTYPE html>
<html lang="en">
<?php
session_start();

if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../navbar.php';
	require '../sales/listHelper.php';   // imported to get clientNamesMap and productDetailsMap;

	/**************************************				 Find weekly sales fof last 4 weeks for Bar chart	 		**************************************/
	
	function getWeekMonSat($weekOffset)
	{
		$dt = new DateTime();
		$dt->setIsoDate($dt->format('o'), $dt->format('W') + $weekOffset);
		return array(
			'Mon' => $dt->format('Y-m-d'),
			'Sat' => $dt->modify('+5 day')->format('Y-m-d'),
		);
	}
	
	$week1 = getWeekMonSat(-4);
	$week2 = getWeekMonSat(-3);
	$week3 = getWeekMonSat(-2);
	$week4 = getWeekMonSat(-1);
	$week5 = getWeekMonSat(0);
	
	$query1 = mysqli_query($con,"SELECT SUM(qty) FROM nas_sale WHERE deleted IS NULL AND entry_date >='".$week1['Mon']."' AND entry_date <='".$week1['Sat']."'") or die(mysqli_error($con));		
	$sum1 = (int)mysqli_fetch_array($query1, MYSQLI_ASSOC);

	$query2 = mysqli_query($con,"SELECT SUM(qty) FROM nas_sale WHERE deleted IS NULL AND entry_date >='".$week2['Mon']."' AND entry_date <='".$week2['Sat']."'") or die(mysqli_error($con));		
	$sum2 = (int)mysqli_fetch_array($query2, MYSQLI_ASSOC);

	$query3 = mysqli_query($con,"SELECT SUM(qty) FROM nas_sale WHERE deleted IS NULL AND entry_date >='".$week3['Mon']."' AND entry_date <='".$week3['Sat']."'") or die(mysqli_error($con));		
	$sum3 = (int)mysqli_fetch_array($query3, MYSQLI_ASSOC);

	$query4 = mysqli_query($con,"SELECT SUM(qty) FROM nas_sale WHERE deleted IS NULL AND entry_date >='".$week4['Mon']."' AND entry_date <='".$week4['Sat']."'") or die(mysqli_error($con));		
	$sum4 = (int)mysqli_fetch_array($query4, MYSQLI_ASSOC);

	$query5 = mysqli_query($con,"SELECT SUM(qty) FROM nas_sale WHERE deleted IS NULL AND entry_date >='".$week5['Mon']."' AND entry_date <='".$week5['Sat']."'") or die(mysqli_error($con));		
	$sum5 = (int)mysqli_fetch_array($query5, MYSQLI_ASSOC);																																						
	
    $arr=[$sum1, $sum2, $sum3, $sum4, $sum5];
	
	$today = date('Y-m-d');	
	$sql = "SELECT * FROM nas_sale WHERE deleted IS NULL AND entry_date = '$today' ORDER BY bill_no ASC";																									?>
	
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Home</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
					<br/><br/>
                    <div class="row">
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Weekly Sales Overview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myBarChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Current Bill Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> Direct
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> Social
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-info"></i> Referral
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>					
                    <div class="row">
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Earnings (Monthly)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Earnings (Annual)</div>
											<table class="card-table table">
												<thead>
													<tr>
														<th scope="col">First</th>
														<th scope="col">Last</th>
														<th scope="col">Handle</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Mark</td>
														<td>Otto</td>
														<td>@mdo</td>
													</tr>
													<tr>
														<td>Jacob</td>
														<td>Thornton</td>
														<td>@fat</td>
													</tr>
													<tr>
														<td colspan="2">Larry the Bird</td>
														<td>@twitter</td>
													</tr>
												</tbody>
											</table>											
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="../js/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<script src="../js/chart-pie-demo.js"></script>
	<script src="../js/chart-bar-demo.js"></script>
</body>

</html>																																					<?php
}
else
	header("Location:../sessions/loginPage.php");																													?>