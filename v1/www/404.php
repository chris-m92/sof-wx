<?php require("../req/log.php"); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Bootstrap Required Meta Tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Vendor CSS from CDN -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

		<!-- Custom CSS -->
        <!--
		<link href="http://plan.red6.dynu.net/css/landing-page.css" rel="stylesheet">
		<link href="http://plan.red6.dynu.net/css/footer.css" rel="stylesheet">
        -->

		<!-- Favicon -->
		<!--<link rel="shortcut icon" type="image/png" href="http://mfl.red6.dynu.net/img/favicon.png"/>-->

		<!-- Bootstrap required JavaScript -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
		
		<title>Page Not Found!</title>
		
		<script>		
			// Stuff that happens as soon as the page loads
			$(document).ready(function() {
				$("#body-container").removeClass("d-none");		
			});
		</script>
	</head>
	<body>
		<?php // Navigation / Header ?>
		<nav class="navbar navbar-dark bg-dark">
			<a class="navbar-brand" href="/">SOF Planner</a>
		</nav>
				
		<div id="body-container" class="container d-none" style="margin-top: 2rem; margin-bottom: 2rem;">
		
			<?php // HTML Error Notice ?>
			<div class="card text-center mx-auto" style="max-width: 35rem;">
				<div class="card-body">
					<h5 class="card-title">Page Not Found (404)</h5>
					<h5 class="card-title"><i class="fas fa-unlink fa-5x"></i></h5>
					<p class="card-text">Sorry, but the page <b><?php echo $_SERVER["REQUEST_URI"]; ?></b> was not found on this server.</p>
				</div>
			</div>
		</div>
		<footer class="footer bg-dark">	
			<div class="container">
				<div class="row">
					<div class="col-lg-6 h-100 text-center text-lg-left my-auto">
						<ul class="list-inline mb-2">
							<li class="list-inline-item">
								<a href="about">About</a>
							</li>
							<li class="list-inline-item text-muted">&sdot;</li>
							<li class="list-inline-item">
								<a href="contact">Contact</a>
							</li>
							<li class="list-inline-item text-muted">&sdot;</li>
							<li class="list-inline-item">
								<a href="terms">Terms of Use</a>
							</li>
							<li class="list-inline-item text-muted">&sdot;</li>
							<li class="list-inline-item">
								<a href="privacy">Privacy Policy</a>
							</li>
							<li class="list-inline-item text-muted">&sdot;</li>
							<li class="list-inline-item">
								<a href="cookies">Cookie Policy</a>
							</li>
						</ul>
						<p class="text-muted small my-auto">&copy; SOF Planner 2018. All Rights Reserved.</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
</html>
<?php closelogs(); ?>
