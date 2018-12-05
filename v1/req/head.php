<!-- Bootstrap Required Meta Tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Vendor CSS from CDN -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

<!-- Custom CSS -->
<link href="http://plan.red6.dynu.net/css/footer.css" rel="stylesheet">

<!-- Favicon -->
<!--<link rel="shortcut icon" type="image/png" href=""/>-->

<!-- Bootstrap required JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	

<!-- Cusom JavaScript -->
<script src="./js/sof_plan.js"></script>

<title>
	<?php 
		echo "SOF Planner";
		
		switch($_SERVER["PHP_SELF"]) {
			case "/index.php":		
				if(isset($_GET["icao"]) && $_GET["icao"] != "") {
					echo " | ".$_GET["icao"];
				}
				break;
			case "/details.php":
				echo " | ".$_GET["icao"]." Details";
				break;
			case "/cookies.php":
				echo " | Cookie Policy";
				break;
			case "/privacy.php":
				echo " | Privacy Policy";
				break;
			case "/termsofuse.php":
				echo " | Terms of Use";
				break;
			case "/about.php":
				echo " | About SOF WX";
				break;
		}
	?>
</title>