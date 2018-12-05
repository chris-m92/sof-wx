<?php	
	require("../req/wx-users.php");

	if(isset($_GET["icao"]) && $_GET["icao"] != "") {
		// ICAO List is set (Only get first ICAO)
		$icao = substr($_GET["icao"], 0, 4);

		//$icao_list = str_replace(" ", "+", $icao_list);
		//$icao_array = explode("+", $icao_list);
					
		//-------------------------------------------------------------------------------------------------------------------------
		// Get ADDS METARs
		//-------------------------------------------------------------------------------------------------------------------------
		$metar = file_get_contents("https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=$icao&hoursBeforeNow=2&mostRecentForEachStation=constraint");
				
		// Load the METARs into an XML parser
		$metar = simplexml_load_string($metar);

		// Step into <data><METAR><raw_text>
		$metar = $metar->data->METAR->raw_text;
		
		if($metar == "") {
			$has_metar = false;
		} else {
			$has_metar = true;
		}
					
		//-------------------------------------------------------------------------------------------------------------------------			
		// Get ADDS TAFs
		//-------------------------------------------------------------------------------------------------------------------------
		$taf = file_get_contents("https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=tafs&requestType=retrieve&format=xml&stationString=$icao&hoursBeforeNow=2&mostRecentForEachStation=constraint");
		
		// Load the TAFs into an XML parser
		$taf = simplexml_load_string($taf);	
		
		// Step into <data><TAF><raw_text>
		$taf = $taf->data->TAF->raw_text;
		
		if($taf == "") {
			$has_taf = false;
		} else {
			$has_taf = true;
		}
		
		//-------------------------------------------------------------------------------------------------------------------------
		// Get the Korean METARs if no METAR / TAF is found by ADDS
		//-------------------------------------------------------------------------------------------------------------------------
		if($has_metar == false) {		
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
			
			// Get the METAR in question
			foreach($korea_metar_list as $m) {
				$m = $m->textContent;
				
				if(substr($m, 6, 4) == $icao) {
					$has_metar = true;
					$metar = $m;
					break;
				}
			}
		}
		
		if($has_taf == false) {
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

			foreach($korea_taf_list as $t) {
				$t = $t->textContent;
				
				$station = trim(substr($t, 4, 4));
				
				if(strlen($station) < 4) {
					$station = trim(substr($t, 8, 4));
				}
				
				if($station == $icao) {
					$has_taf = true;
					$taf = $t;
					break;
				}
			}
		}
		
		if($has_metar == false) {
			$metar = "No METAR Data";
		}
		
		if($has_taf == false) {
			$taf = "No TAF Data";
		} else {
			// Add some delimiters
			$taf = str_replace(" BECMG", "<br/>BECMG", $taf);
			$taf = str_replace(" FM", "<br/>FM", $taf);
			$taf = str_replace(" TEMPO", "<br/>TEMPO", $taf);
			
			// Trim off the beginning of the TAF
			// AMD = Amended // COR = Corrected // RTD = Report Delayed
			if(strpos($taf, "AMD") === false && strpos($taf, "COR") === false && strpos($taf, "RTD") === false) {
				$begin = substr($taf, 0, 17);
				$taf = substr($taf, 17);
			} else {
				$begin = substr($taf, 0, 21);
				$taf = substr($taf, 21);
			}
											
			$taf = "<br/>".$taf;
			
			$taf = $begin.$taf;
			
		}
		
		// Get the NOTAMs
		$dins_link = "https://www.notams.faa.gov/dinsQueryWeb/queryRetrievalMapAction.do?retrieveLocId=$icao&actionType=notamRetrievalByICAOs&submit=NOTAMs";
		$notams = file_get_contents($dins_link);
		$dins_page = strip_tags($notams, "<PRE><table>");
		$dins_arr = explode("<table>", $dins_page);
		$dins_arr = $dins_arr[0];
		$dins_arr = explode("<PRE>", $dins_arr);
		
		unset($dins_arr[0]);
		
		$has_notams = true;
		if(count($dins_arr) == 0) {
			$has_notams = false;
		}
		
	} else {
		header("Location: ./");
		closelogs();
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

			<h1 class="display-1"><?php echo $icao; ?></h1>
			<a href="http://airnav.com/airport/<?php echo $icao; ?>" target="_blank">Airnav</a> /
			<?php if($has_notams) { ?>
			<a href="<?php echo $dins_link; ?>" target="_blank">DINS NOTAMS</a> / 
			<?php }
				$end = false;
				$i = 13;
				$cycle = date("y").$i;
				while(!$end) {					
					$check_site = "https://www.faa.gov/air_traffic/flight_info/aeronav/digital_products/dtpp/search/results/?cycle=$cycle&ident=$icao";

					$headers = get_headers($check_site, 1);

					if($headers[0] != "HTTP/1.0 503 Service Unavailable") {
						$end = true;
					} else {
						$i--;
						$cycle = date("y").$i;
					}
				}
			?>		
			<a href="https://www.faa.gov/air_traffic/flight_info/aeronav/digital_products/dtpp/search/results/?cycle=<?php echo $cycle; ?>&ident=<?php echo $icao; ?>" target="_blank">FAA Terminal Procedures [<?php echo $cycle; ?>]</a> 
	
			<?php // Metar Card ?>
			<div class="card" id="metar-card">		
				<div class="card-header">
					<h4>METAR</h4>
				</div>
				<div class="card-body">
					<?php echo $metar; ?>
				</div>
			</div>
			<?php // TAF Card ?>
			<div class="card my-2">
				<div class="card-header">
					<h4>TAF</h4>
				</div>
				<div class="card-body">
					<?php echo $taf; ?>
				</div>
			</div>
			<?php // Notams ?>
			<div class="card">
			<?php if($has_notams) { ?>
				<div class="card-header">
					<h4>NOTAMs</h4>
				</div>
				<ul class="list-group list-group-flush">
				<?php
					$i = 0;
					foreach($dins_arr as $entry) {
						$entry = substr($entry, 11);
						$closed = strpos($entry, "CLOSED");
						
						if($closed !== false) {
							$notam_level = "list-group-item-danger";
						} else {
							$notam_level = "";
						}
				?>
					<li class="list-group-item <?php echo $notam_level; ?>"><?php echo $entry; ?></li>
				<?php
					$i++;
					}					
				?>
				</ul>
			<?php } else { ?>
			<h4>No Notam Data</h4>
			<?php } ?>
			</div>
		</div>
		<?php // Footer ?>
		<?php require("../req/footer.php"); ?>
	</body>
</html>