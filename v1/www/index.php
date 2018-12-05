<?php		
	require("../req/log.php");
	require("../req/wx-users.php");
	
	// Weather Required Assumes Fixed Wing
	$CEILING_REQUIRES_ALTERNATE = 2000;
	$VISIBILITY_REQUIRES_ALTERNATE = 3;
	
	$ALTERNATE_CEILING_REQUIREMENT = 1000;
	$ALTERNATE_VISIBILITY_REQUIREMENT = 2;	

	if(isset($_GET["icao"]) && $_GET["icao"] != "") {
		// ICAO List is set
		$icao_list = urlencode(strtoupper($_GET["icao"]));

		//$icao_list = str_replace(" ", "+", $icao_list);
		$icao_array = explode("+", $icao_list);
					
		//-------------------------------------------------------------------------------------------------------------------------
		// Get ADDS METARs
		//-------------------------------------------------------------------------------------------------------------------------
		$metars = file_get_contents("https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=$icao_list&hoursBeforeNow=2&mostRecentForEachStation=constraint");
				
		// Load the METARs into an XML parser
		$metars = simplexml_load_string($metars);

		// Step into <data>
		$metars = $metars->data;
					
		//-------------------------------------------------------------------------------------------------------------------------			
		// Get ADDS TAFs
		//-------------------------------------------------------------------------------------------------------------------------
		$tafs = file_get_contents("https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=tafs&requestType=retrieve&format=xml&stationString=$icao_list&hoursBeforeNow=2&mostRecentForEachStation=constraint");
		
		// Load the TAFs into an XML parser
		$tafs = simplexml_load_string($tafs);	
		
		// Step into <data>
		$tafs = $tafs->data;
		
		//-------------------------------------------------------------------------------------------------------------------------
		// Get the Korean METARs if RKSO or RKJK are in the request
		//-------------------------------------------------------------------------------------------------------------------------
		if(strpos($icao_list, "RKSO") === false && strpos($icao_list, "RKJK") === false) {
			// Do nothing, RKSO and RKJK are not in the request
		} else {
			
			//-------------------------------------------------------------------------------------------------------------------------
			// Log into the global.amo.go.kr site and get the Korean METARs
			//-------------------------------------------------------------------------------------------------------------------------
		
			// Initialze cURL
			$curl = curl_init();
			
			// Set POST values
			$values = array("memId"=>$korea_user, "memPwd"=>$korea_pass);
			
			// Set cURL URL
			curl_setopt($curl, CURLOPT_URL, "http://global.amo.go.kr/mobile/modules/met/gmap_v2/login_ok.php");
			
			// Excludes header in output
			curl_setopt($curl, CURLOPT_HEADER, false);
			
			// Includes body in output
			curl_setopt($curl, CURLOPT_NOBODY, false);
			
			// Do not check names in SSL peer certificate
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			
			// Stop cURL from verifying peer certificate
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			// Return the transfer as a string
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
			// Set custom request method
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			
			// Do a regular HTTP POST
			curl_setopt($curl, CURLOPT_POST, true);
			
			// Do not follow any "Location: " headers
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			
			// Set post fields
			curl_setopt($curl, CURLOPT_POSTFIELDS, $values);
			
			// Handle any login cookies
			curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
			
			// Execute the cURL
			$response = curl_exec($curl);
			
			// At this point we should be logged in
			curl_setopt($curl, CURLOPT_URL, "http://global.amo.go.kr/mobile/modules/met/gmap_v2/mmtt.php");
			
			// Execute the cURL request
			$korea_metars = curl_exec($curl);
			
			// Close the cURL
			curl_close($curl);
			
			//-------------------------------------------------------------------------------------------------------------------------
			// Parse through the results and get the needed METARs only available via the website
			//-------------------------------------------------------------------------------------------------------------------------
			// Create the Parser
			$dom = new DOMDocument;
			
			// Load the String into the parser
			$dom->loadHTML($korea_metars);
			
			// Create a DOM Path query machine (magic)
			$xpath = new DOMXPath($dom);
			
			// Query the Dom for the specific HTML element
			// This is helpful because the site is a table of information with each METAR in a  with the class of td_left
			$korea_metar_list = $xpath->query("//td[@class='td_left']");	

			//-------------------------------------------------------------------------------------------------------------------------
			// Get the Korean TAFs
			//-------------------------------------------------------------------------------------------------------------------------
			$korea_tafs = file_get_contents("http://global.amo.go.kr/mobile/modules/met/gmap_v2/mtmt.php");
			
			// Parse through the results and get the needed METARs only available via the website
			$taf_dom = new DOMDocument;
			
			// Suppress any errors (There's an unexpected <br> somewhere in the website)
			libxml_use_internal_errors(true);
			
			// Create a DOM Parser
			$taf_dom->loadHTML($korea_tafs);
			
			// Create the Query machine (magic)
			$taf_xpath = new DOMXPath($taf_dom);
			
			// Query the DOM
			$korea_taf_list = $taf_xpath->query("//td[@class='td_left']");			
		}
	}
?>
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
			
			<?php 
			// No ICAO Selected
			if(!isset($_GET["icao"])) { 
				// Form Card 
				require("../req/form-card.php");
			} else {
				// Weather Card
				require("../req/weather-card.php");
			}
			?>
		</div>
		<?php // Footer ?>
		<?php require("../req/footer.php"); ?>
	</body>
</html>
<?php closelogs(); ?>