<div class="card">
	<div class="card-header text-center">
		<h2>Weather at a glance<?php echo (isset($_GET["fromSaved"]) && $_GET["fromSaved"] == "true") ? " (From Saved ICAO List)" : ""; ?></h2>
	</div>				
	<div class="card-body">
		<div class="table-responsive">
			<table class="table text-center">
				<thead>
					<tr>
						<th scope="col">Legend</th>
						<th scope="col" colspan="2" class="table-success">Can File to Field</th>
						<th scope="col" colspan="2" class="table-warning">&lt;2000ft or 3SM&nbsp;&nbsp;&nbsp;Alternate Required</th>
						<th scope="col" colspan="2" class="table-danger">&lt;1000ft or 2SM&nbsp;&nbsp;&nbsp;Invalid Alternate</th>
					<tr>
						<th scope="col">ICAO<br><small>Click for more info</small></th>
						<th scope="col">Field Status</th>
						<th scope="col"><?php echo date("d M H", strtotime("Now"))."00Z"; ?></th>
						<th scope="col"><?php echo date("d M H", strtotime("Now + 1 hour"))."00Z"; ?></th>
						<th scope="col"><?php echo date("d M H", strtotime("Now + 2 hours"))."00Z"; ?></th>
						<th scope="col"><?php echo date("d M H", strtotime("Now + 3 hours"))."00Z"; ?></th>
						<th scope="col"><?php echo date("d M H", strtotime("Now + 4 hours"))."00Z"; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($icao_array as $icao) {
																					
							//-------------------------------------------------------------------------------------------------------------------------
							// Go through the METAR / TAF data and determine if there is existing weather data
							//-------------------------------------------------------------------------------------------------------------------------
							$has_metar = false;
							$has_taf = false;
							
							// Initialize these variables so that we can use the METAR / TAF later on in the table
							$m;
							$taf;
							$raw_metar;
							
							// Somewhat inefficient, but go through each METAR / TAF to determine if it matches the ICAO we're looking at
							// If it does, then that ICAO has weather, if not, we will end up displaying "No Weather Data"
							// This is usually only an issue for OCONUS weather stations not supported by the ADDS service
							foreach ($metars->METAR as $m) {
								if($m->station_id == $icao) {
									$has_metar = true;
									$raw_metar = $m->raw_text;
									break;
								}
							}
						
							foreach ($tafs->TAF as $taf) {
								if($taf->station_id == $icao) {
									$has_taf = true;
									$taf = $taf->raw_text;
									break;
								}
							}
							
							//-------------------------------------------------------------------------------------------------------------------------
							// Get the Korean METARs if RKSO or RKJK (2 Korean bases) are in the request
							//-------------------------------------------------------------------------------------------------------------------------
							if (strpos($icao_list, "RKSO") === false && strpos($icao_list, "RKJK") === false) {
								// Do Nothing, RKSO and RKJK are not in the request
							} else {
								// Haven't found any METARS
								if ($has_metar == false) {
									// RKSO (Osan AB, ROK - ADDS)
									// RKJK (Kunsan AB, ROK - ADDS)
									// RKSW (Suwon AB, ROK - AMO)
									// RKTP (Seosan AB, ROK - AMO)
									// RKNN (Gangneung, ROK - AMO)
									// RKSM (Seoul AB, ROK - ADDS)
									// RKNW (Wonju, ROK - AMO)
									// RKTI (Jungwon, ROK - AMO)
									// RKJJ (Gwangju, ROK - AMO)
									// RKTU (Cheongju, ROK - ADDS)
									// RKTN (Daegu, ROK - ADDS)
									foreach ($korea_metar_list as $korean_m) {
										$metar = $korean_m->textContent;
										$raw_metar = $metar;
																				
										if(substr($metar, 6, 4) == $icao) {
											$has_metar = true;
											
											
											// Fun time, take the METARs, parse through them and then make an xml object so that the rest of the script can parse through it.
											// Visibility
											// 1NM = 1852m
											// 1SM = 1609.34m
											$metar = explode("KT ", $metar);
											$metar = trim($metar[1]);									
											$vis = trim(substr($metar, 0, 4));		
											
											
											// Weird Korea-ism where when reporting CAVOK, no visibility is reported
											if (is_numeric($vis)) {														
												$vis = round(intval($vis) / 1609.34, 1);														
											} else {
												// 9999m = 6.21 SM
												$vis = 6.2;
											}
											
											// Clouds
											// Get string positions of any ceiling level	
											$bkn = strpos($metar, "BKN");
											$ovc = strpos($metar, "OVC");
											
											// There is some clouds, check for a ceiling
											// Set the initial "no ceiling" level to an impossibly high number
											$ceiling = 99999;
											
											// Check for BKN 
											if($bkn === false) {
												$bkn_layer = 99999;
											} else {
												$bkn_layer = intval(substr($metar, $bkn + 3, 3)) * 100;
											}
											
																						
											// Check for OVC
											if($ovc === false) {
												$ovc_layer = 99999;
											} else {
												$ovc_layer = intval(substr($metar, $ovc + 3, 3)) * 100;
											}
											
											// Ceiling is the lowest of BKN or OVC
											$ceiling = min($ceiling, $bkn_layer, $ovc_layer);

											if ($vis < 1 || $ceiling < 500) {
												$flight_category = "LIFR";
											} else if ($vis < 3 || $ceiling < 1000) {
												$flight_category = "IFR";
											} else if ($vis < 5 && $ceiling < 3000) {
												$flight_category = "MVFR";
											} else {
												$flight_category = "VFR";
											}
											
											/*if ($ceiling < 500) {
												$flight_category = "LIFR";
											} else if ($ceiling < 1000) {
												$flight_category = "IFR";
											} else if ($ceiling < 3000) {
												$flight_category = "MVFR";
											} else {
												if ($vis >= 5) {
													$flight_category = "VFR";
												}
											}*/
											
									
											$xml = "<response><METAR><raw_text>$raw_metar</raw_text><station_id>$icao</station_id><flight_category>$flight_category</flight_category></METAR></response>";		
											$xml = new SimpleXMLElement($xml);										
											$m = $xml->METAR;
											break;
										}
									
										// If not found
										if($has_metar == false) {
											$raw_metar = "METAR Not Found for $icao";												
											$xml = "<response><METAR><raw_text>$raw_metar</raw_text><station_id>$icao</station_id><flight_category>UNKNOWN</flight_category></METAR></response>";
											$xml = new SimpleXMLElement($xml);												
											$m = $xml->METAR;
										}
									}
								}
																		
								// Haven't found any TAFs
								if ($has_taf == false) {

									foreach ($korea_taf_list as $korean_t) {
										$taf = $korean_t->textContent;
										
										// Get the Station ID
										$station = trim(substr($taf, 4, 4));
										
										// Make sure the ICAO is correct
										// If TAF is amended or corrected, it would be in front of the station ID
										if(strlen($station) < 4) {
											$station = trim(substr($taf, 8, 4));
										}
										
										if($station == $icao) {										
											$has_taf = true;	
											break;
										}									
									}
								}
							}
							
							//-------------------------------------------------------------------------------------------------------------------------
							// Parse through the TAF for the ICAO
							// This is inefficient but since we have the logic to do it (Thanks KOREA) then we can quickly use the same logic, that we know works for ADDS too
							//-------------------------------------------------------------------------------------------------------------------------
							// For now we're just doing 5 hours in advance, so make those variables
							$now = date("mdyH", strtotime("Now"));
							$now_plus1 = date("mdyH", strtotime("Now + 1 hour"));
							$now_plus2 = date("mdyH", strtotime("Now + 2 hours"));
							$now_plus3 = date("mdyH", strtotime("Now + 3 hours"));
							$now_plus4 = date("mdyH", strtotime("Now + 4 hours"));
														
							// Make some variables to hold the first 5 valid TAFs
							$taf1 = null;
							$taf2 = null;
							$taf3 = null;
							$taf4 = null;
							$taf5 = null;
							
							$taf1_type = "";
							$taf2_type = "";
							$taf3_type = "";
							$taf4_type = "";
							$taf5_type = "";
							
							$taf1_vis = "";
							$taf2_vis = "";
							$taf3_vis = "";
							$taf4_vis = "";
							$taf5_vis = "";
							
							$taf1_ceiling = "";
							$taf2_ceiling = "";
							$taf3_ceiling = "";
							$taf4_ceiling = "";
							$taf5_ceiling = "";
							
							$taf1_coverage = "";
							$taf2_coverage = "";
							$taf3_coverage = "";
							$taf4_coverage = "";
							$taf5_coverage = "";
							
							$taf1_level = "secondary";
							$taf2_level = "secondary";
							$taf3_level = "secondary";
							$taf4_level = "secondary";
							$taf5_level = "secondary";
							
							// Add some delimiters
							$taf = str_replace(" BECMG", "~BECMG", $taf);
							$taf = str_replace(" FM", "~FM", $taf);
							$taf = str_replace(" TEMPO", "~TEMPO", $taf);
							
							// Trim off the beginning of the TAF
							// AMD = Amended // COR = Corrected // RTD = Report Delayed
							if(strpos($taf, "AMD") === false && strpos($taf, "COR") === false && strpos($taf, "RTD") === false) {
								$taf = substr($taf, 17);
							} else {
								$taf = substr($taf, 21);
							}
							
							// Add "Prevailing" to the front, this helps with the automation 									
							$taf = "PREVAILING ".$taf;
							
							// Break out the TAF by BECMG / TEMPO / FM
							$taf_array = explode("~", $taf);
							
							// Initialize "previous" variables
							// Highest Vis possible (9999m = 6.21SM)
							$previous_vis = 6.2;
							// Impossibly high ceiling
							$previous_ceiling = 99999;
							$previous_coverage = "No Ceiling";
														
							// Go through each TAF
							foreach($taf_array as $t) {
								
								// Get rid of some garbage
								$t = explode(" TN", $t);
								$t = $t[0];
								
								// Get the type, time, and potential visibility
								$t_exp = explode(" ", $t);
								$type = $t_exp[0];
								$time = $t_exp[1];
								$wind = $t_exp[2];
								$vis = $t_exp[3];
								
								if(substr($type, 0, 2) == "FM") {
									$vis = $wind;
									$wind = $time;					
									$time = substr($type, 2, 4);
								}
								
								// Split the time into start and end
								$time_array = explode("/", $time);
								
								// Get the start day and hour
								$start = $time_array[0];
								$start_day = intval(substr($start, 0, 2));
								$start_hour = intval(substr($start, 2));
																							
								// Create a start "date" with the day and hour using current month and year
								$start = date("mdyH", mktime($start_hour, 0, 0, date("n"), $start_day, date("y")));
								
								// If the start day is less than now, then add a month
								if ($start_day < date("d")) {
									$start = date("mdyH", strtotime("+1 month", mktime($start_hour, 0, 0, date("n"), $start_day, date("Y"))));
								}								
															
								// Get the end day and hour
								// Only a factor for TEMPO groups
								$end = $time_array[1];
								$end_day = intval(substr($end, 0, 2));
								$end_hour = intval(substr($end, 2));
																
								// Create a end "date" with the day and hour using current month and year
								$end = date("mdyH", mktime($end_hour, 0, 0, date("n"), $end_day, date("Y")));
								
								// If the end day is less than now, add a month to the date
								if ($end_day < date("d")) {
									$end = date("mdyH", strtotime("+1 month", mktime($end_hour, 0, 0, date("n"), $end_day, date("Y"))));
								}
																																
								// Check TAF Visibility								
								$sm = strpos($t, "SM");
								
								if($sm === false) {
									// Sometimes there are no winds, so check to see if that's the case
									if(strlen($wind) <= 4) {
										$vis = $wind;
									}
									
									// If there is no vis, use the previous visibility, this works because the PREVAILING TAF always has visibility
									if(is_numeric($vis)) {
										$vis = intval($vis);
										$vis = round($vis / 1609.34, 1);
									} else {
										$vis = $previous_vis;
									}
								} else {
									$vis = substr($t, $sm - 6, 6);
									
									$space_count = substr_count($vis, " ");
									
									if($space_count > 1) {
										// We're good
										// Example
										// _1_1/2SM
									} else {
										// Example
										// 1/2SM
										$vis = trim(substr($vis, 3));
																			
										// If the TAF reports greater than 6SM Visibility, report something that the code will recognize and display
										// "Unrestricted Vis"
										if($vis == "P6") {
											$vis = 6.2;
										} else {
											$vis = intval(substr($vis, 0, 1)) / intval(substr($vis, 2));
										}
									}
								}
								
								// Now set the previous visibility to this one only if the group is not TEMPO
								if($type != "TEMPO") {
									$previous_vis = $vis;
								}
								
								// Take everything off the front of the taf through the time
								//$t = substr($t, strlen($type) + strlen($time) + 2);
								
								// Get string positions of the various clouds
								$cavok = strpos($t, "CAVOK");
								$nsc = strpos($t, "NSC");
								$clr = strpos($t, "CLR");
								$skc = strpos($t, "SKC");
								$few = strpos($t, "FEW");
								$sct = strpos($t, "SCT");			
								$bkn = strpos($t, "BKN");
								$ovc = strpos($t, "OVC");
								
								// If there are no change to cloud coverage reported, then use the previous ceiling
								if($cavok === false && $clr === false && $skc === false && $few === false && $sct === false && $bkn === false && $ovc === false && $nsc === false){
									// There is no change to cloud coverage
									$ceiling = $previous_ceiling;
									$coverage = $previous_coverage;
								} else {
									// There is some clouds, check for a ceiling
									// Set the initial "no ceiling" level to an impossibly high number
									$ceiling = 99999;
									
									// Check for BKN 
									if($bkn === false) {
										$bkn_layer = 99999;
									} else {
										$bkn_raw = substr($t, $bkn + 3, 3);
										$bkn_layer = intval($bkn_raw) * 100;
									}
									
									// Check for OVC
									if($ovc === false) {
										$ovc_layer = 99999;
									} else {
										$ovc_raw = substr($t, $ovc + 3, 3);
										$ovc_layer = intval($ovc_raw) * 100;
									}
									
									// Ceiling is the lowest of BKN or OVC
									$ceiling = min($ceiling, $bkn_layer, $ovc_layer);
									
									// Get the coverage
									if($bkn_layer == 99999 && $ovc_layer == 99999) {
										$coverage = "No Ceiling";
									}else if($bkn_layer < $ovc_layer) {
										$coverage = "BKN".$bkn_raw;
									} else {
										$coverage = "OVC".$ovc_raw;
									}
									
									// Set the previous ceilng to current ceiling (only if non TEMPO)
									if($type != "TEMPO") {
										$previous_ceiling = $ceiling;
										$previous_coverage = $coverage;
									}
								}								
								
								// Check TAF validity, as each TAF goes through it can overwrite if it is also valid, this makes sense as the PREVAILING weather can potentially be valid for the next 5 hours but there are other TAF groups that are also valid for that time period
								// TAF is valid now
								if($type != "TEMPO" && $start <= $now) {
									
									$taf1 = $t;
									$taf1_vis = $vis." SM";
									$taf1_ceiling = $ceiling;
									$taf1_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf1_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf1_level = "warning";
									} else {
										$taf1_level = "success";
									}
								} else if($type == "TEMPO" && $start <= $now && $end > $now) {
									$taf1 = $t;
									$taf1_vis = $vis." SM";
									$taf1_ceiling = $ceiling;
									$taf1_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										
										$taf1_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf1_level = "warning";
									} else {
										$taf1_level = "success";
									}
								}
								
								// TAF is valid in 1 hour
								if ($type != "TEMPO" && $start <= $now_plus1) {
									$taf2 = $t;
									$taf2_vis = $vis." SM";
									$taf2_ceiling = $ceiling;
									$taf2_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf2_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf2_level = "warning";
									} else {
										$taf2_level = "success";
									}
								} if($type == "TEMPO" && $start <= $now_plus1 && $end > $now_plus1) {
									$taf2 = $t;
									$taf2_vis = $vis." SM";
									$taf2_ceiling = $ceiling;
									$taf2_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf2_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf2_level = "warning";
									} else {
										$taf2_level = "success";
									}
								}
								
								// TAF is valid in 2 hours
								if ($type != "TEMPO" && $start <= $now_plus2) {
									$taf3 = $t;
									$taf3_vis = $vis." SM";
									$taf3_ceiling = $ceiling;
									$taf3_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf3_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf3_level = "warning";
									} else {
										$taf3_level = "success";
									}
								} else if($type == "TEMPO" && $start <= $now_plus2 && $end > $now_plus2) {
									$taf3 = $t;
									$taf3_vis = $vis." SM";
									$taf3_ceiling = $ceiling;
									$taf3_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf3_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf3_level = "warning";
									} else {
										$taf3_level = "success";
									}
								} 
								
								// TAF is valid in 3 hours
								if ($type != "TEMPO" && $start <= $now_plus3) {
									$taf4 = $t;
									$taf4_vis = $vis." SM";
									$taf4_ceiling = $ceiling;
									$taf4_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf4_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf4_level = "warning";
									} else {
										$taf4_level = "success";
									}
								} else if($type == "TEMPO" && $start <= $now_plus3 && $end > $now_plus3) {
									$taf4 = $t;
									$taf4_vis = $vis." SM";
									$taf4_ceiling = $ceiling;
									$taf4_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf4_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf4_level = "warning";
									} else {
										$taf4_level = "success";
									}
								}
								
								// TAF is valid in 4 hours
								if ($type != "TEMPO" && $start <= $now_plus4) {
									$taf5 = $t;
									$taf5_vis = $vis." SM";
									$taf5_ceiling = $ceiling;
									$taf5_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf5_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf5_level = "warning";
									} else {
										$taf5_level = "success";
									}
								} else if($type == "TEMPO" && $start <= $now_plus4 && $end > $now_plus4) {
									$taf5 = $t;
									$taf5_vis = $vis." SM";
									$taf5_ceiling = $ceiling;
									$taf5_coverage = $coverage;
									
									if($ceiling < $ALTERNATE_CEILING_REQUIREMENT || $vis < $ALTERNATE_VISIBILITY_REQUIREMENT) {
										$taf5_level = "danger";
									} else if($ceiling < $CEILING_REQUIRES_ALTERNATE || $vis < $VISIBILITY_REQUIRES_ALTERNATE) {
										$taf5_level = "warning";
									} else {
										$taf5_level = "success";
									}
								}

								// Change visibility of 9999 to "Unrestricted Vis"
								if($taf1_vis == "6.2 SM") {
									$taf1_vis = "Unrestricted Vis";
								}
								
								if($taf2_vis == "6.2 SM") {
									$taf2_vis = "Unrestricted Vis";
								}
								
								if($taf3_vis == "6.2 SM") {
									$taf3_vis = "Unrestricted Vis";
								}
								
								if($taf4_vis == "6.2 SM") {
									$taf4_vis = "Unrestricted Vis";
								}
								
								if($taf5_vis == "6.2 SM") {
									$taf5_vis = "Unrestricted Vis";
								}
									
								
							}					
							
							// If there are no valid TAFs for the time, then put in some nice text
							if($taf1 == null) {
								$taf1 = "No TAF for this time";
								$taf1_coverage = "No TAF data";
							}
							
							if($taf2 == null) {
								$taf2 = "No TAF for this time";
								$taf2_coverage = "No TAF data";
							}
							
							if($taf3 == null) {
								$taf3 = "No TAF for this time";
								$taf3_coverage = "No TAF data";
							}
							
							if($taf4 == null) {
								$taf4 = "No TAF for this time";
								$taf4_coverage = "No TAF data";
							}
							
							if($taf5 == null) {
								$taf5 = "No TAF for this time";
								$taf5_coverage = "No TAF data";
							}
							
							// Determine the color of the field status cell
							switch($m->flight_category) {
								case "VFR":
								case "MVFR":
									$flight_cat_level = "success";
									break;
								case "IFR":
									$flight_cat_level = "warning";
									break;
								case "LIFR":
								default:
									$flight_cat_level = "danger";
							}	
					?>	
					<tr>
						<th scope="row"><a href="/details?icao=<?php echo $icao; ?>" target="_blank"><?php echo $icao; ?></a></th>
						<td class="table-<?php echo $flight_cat_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $m->raw_text; ?>"><?php echo $m->flight_category; ?></td>
						<td class="table-<?php echo $taf1_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $taf1; ?>"><?php echo $taf1_coverage; ?><br><?php echo $taf1_vis; ?></td>
						<td class="table-<?php echo $taf2_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $taf2; ?>"><?php echo $taf2_coverage; ?><br><?php echo $taf2_vis; ?></td>
						<td class="table-<?php echo $taf3_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $taf3; ?>"><?php echo $taf3_coverage; ?><br><?php echo $taf3_vis; ?></td>
						<td class="table-<?php echo $taf4_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $taf4; ?>"><?php echo $taf4_coverage; ?><br><?php echo $taf4_vis; ?></td>
						<td class="table-<?php echo $taf5_level; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $taf5; ?>"><?php echo $taf5_coverage; ?><br><?php echo $taf5_vis; ?></td>
					</tr>					
					<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer text-center">
		<button type="button" class="btn btn-block btn-primary" id="save-icao-list-btn">Save ICAO List</button>
		<button type="button" class="btn btn-block btn-danger" id="delete-icao-list-btn">Delete Saved ICAO List</button>
		<small><?php require("../req/cookie-text.php"); ?></small>
	</div>
</div>