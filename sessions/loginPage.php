<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>	
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>	
		<style>
			.login,
			.image {
			  min-height: 100vh;
			}

			.bg-image {
			  background-image: url('https://res.cloudinary.com/mhmd/image/upload/v1555917661/art-colorful-contemporary-2047905_dxtao7.jpg');
			  background-size: cover;
			  background-position: center center;
			}
		</style>
		<title>Login</title>
	</head>	
	<body>
		<div class="container-fluid">
			<div class="row no-gutter">
				<div class="col-md-6 d-none d-md-flex bg-image"></div>
				<div class="col-md-6 bg-light">
					<div class="login d-flex align-items-center py-5">
						<div class="container">
							<div class="row">
								<div class="col-lg-10 col-xl-7 mx-auto">
									<img src="../images/logo.png"/><?php
									if(isset($_GET['message']))
									{																													?>
										<br/><font style="color:red;text-shadow:none;font-size:16px;margin-left:5px;"><?php echo $_GET['message'];?></font><br/><br/><?php
									}
									else
									{																													?>															
										<p class="text-muted mb-4">Please enter your login credentials</p><?php
									}																													?>	
									<form method="post" action="login.php">
										<div class="form-group mb-3">
											<input id="inputUser" type="text" name="user_name" placeholder="Username" required="" autofocus="" class="form-control rounded-pill border-0 shadow-sm px-4">
										</div>
										<div class="form-group mb-3">
											<input id="inputPassword" type="password" name="password" placeholder="Password" required="" class="form-control rounded-pill border-0 shadow-sm px-4 text-primary">
										</div>
										<button type="submit" class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">Sign in</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
