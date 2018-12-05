<!DOCTYPE html>
<html lang="en">
	<head><?php require("../req/head.php"); ?></head>
	<body>
		<?php // Navigation / Header ?>
		<?php require("../req/navbar.php"); ?>
				
		<?php // Body Container ?>
		<div id="body-container" class="container d-none" style="margin-top: 2rem; margin-bottom: 2rem;">
		
			<?php // Spinning Loader ?>
			<?php require("../req/loader.php"); ?>
				
			<?php // Alert Container ?>
			<div id="alert" class="alert" role="alert"></div>
			
			<div class="card">
				<div class="card-header">
					<h4>About SOF WX</h4>
				</div>
				<div class="card-body">
					<h5>Last Updated: 5 December 2018</h5>
					<h4>SOF WX Aggregates weather information from various sources in order to provide a quick reference of the field status for whatever fields you choose</h4>
					<p>The main source is Aviation Weather ADDS information. It pulls the most recent METAR and TAF for each ICAO and determines the field status for if you can file to the field, if you need an alternate, or if the field cannot be a valid alternate. For Korean ICAOs that are not supported by the ADDS system, then Korea's Global AMO site is used.</p>
					<p>US Air Force AFI 11-217v1 describes the weather requirements for filing to a field. These are the numbers that the Service uses when determining if a field is valid, needs an alternate, or cannot be used as an alternate.</p>
					<p>The Service only takes Weather into consideration for determining field status. It is up to the individual user to check NOTAMs by visiting the detailed information page by clicking on the ICAO. Furthermore, the calculations only take into consideration the normal 2000/3 500/1 rules not taking into consideration the lowest compatible approaches for the calculations.</p>
					<h4>SOF WX and the Service it provides is not an official weather source, nor can it be used as a substitute for complete weather planning. The Service provided is for reference and planning purposes only.</h4>
				</div>
			</div>
		</div>
		<?php // Footer ?>
		<?php require("../req/footer.php"); ?>
	</body>
</html>