<?php
/* USAGE:
*
*  1. change the two constants
*     $PHP_LOGS and $LOG_FILENAME in this script
*     to appropriate values for your system.
*     Also, ensure the directory where the logfile is
*     being written has world Read and Write access.
*
*  2. in the script you are testing, insert the following
*     at the top of the script:
*
*     require ("require-log.php");
*
*  3. At the end of the script you are testing, insert the following
*      to close the logs (and flush any buffers):
*
*      closelogs();
*
*  4. In the script you are testing, wherever you want
*     something written to the log, use the statement
*     format:
*
*     log_infomsg(<string>);
*	  log_errormsg(<string>);
*	  log_debugmsg(<string>);
*
*     For example:
*     log_debugmsg("Query = ".$query);
*     log_infomsg("Found ".$numrows." outdated requests.");
*     log_errormsg("Field passed in [".$key."] did not map to any expected DB field.");
*
*
*   5. To help debug your script, you can use Filezilla or another server 
*      access client to open and review the file log.txt where your PHP
*      scripts are.
*/

// START CODE

// STEP 1. set up paths for job logging -- this needs to be
// from root. You need to make the appropriate changes
// for your system.

//$PHP_LOGS = "/var/www/plan.red6.dynu.net/v1/logs/";


$LOG_FILENAME = /*"log_".*/date("Y-m-d").".txt";

// set up constants for log messages
$START_TIME = strftime("[%D - %R] ");
// For windows only
// $START_TIME = strftime("[%m/%d/%y - %H:%M]");
$INDENT = "   ";
$DELIMITER = "~~";
$NEWLINE = "\n";
$ERRORMSG =    	"[ERROR]		> ";
$STARTMSG =		"[START]			> ";
$INFOMSG =     	"[INFO]			> ";
$DEBUGMSG =    	"[DEBUG]			> ";
$MAILMSG =     	"[MAIL]			> ";
$APIMSG =	   	"[WEB-API]		> ";
$DOMSG = 		"[DO]			> ";
$APPMSG =	   	"[MOBILE-API]	> ";
$ENDMSG =		"[END]			> End of Log";

//---- Set these to true to enable information messages, error messages, and debug message or false to disable ----//

$LOG_ERROR_MESSAGES = true;
$LOG_INFORMATION_MESSAGES = true;
$LOG_DEBUG_MESSAGES = true;
$LOG_START_MESSAGES = true;		
$LOG_MAIL_MESSAGES = true;
$LOG_API_MESSAGES = true;
$LOG_DO_MESSAGES = true;
$LOG_APP_MESSAGES = true;
$LOG_END_MESSAGES = true;

//---- THESE STATE USER FNAME, LNAME, AND ID AT BEGINNING OF EACH LOG ENTRY ----//

// Checks for logs, keeps sizes under control, opens log files
$logfilename = $PHP_LOGS.$LOG_FILENAME;

if (file_exists($logfilename) && (filesize($logfilename) > 1500000)) {
	rename ($logfilename, $logfilename.".old");
}

// Mode: a - Write only, pointer at end of file
// Mode: b - Binary flag, no windows translation of \n
$logfile = fopen($logfilename, "ab");

if (!$logfile) {
	echo "Unable to open log file: [".$logfilename."]";
	exit;
} else {
	// fwrite($logfile,$NEWLINE.$START_TIME.$DELIMITER.$_SERVER["PHP_SELF"]);
	fwrite($logfile,$START_TIME.$_SERVER["PHP_SELF"]." (".$_SERVER["REMOTE_ADDR"].")".$NEWLINE);
}

// Used by php files that use log files
function closelogs() {
	global $logfile;
	global $ENDMSG;
	global $DELIMITER;
	global $NEWLINE;
	//fwrite($logfile, $ENDMSG.$NEWLINE);
	fclose($logfile);
	exit;
}

//---- FUNCTIONS TO LOG THE MESSAGE ----//
function log_errormsg($errormsg) {
	global $LOG_ERROR_MESSAGES;
	global $logfile;
	global $ERRORMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_ERROR_MESSAGES) {
		fwrite($logfile, $ERRORMSG.$errormsg.$NEWLINE);
	}
}

function log_infomsg($infomsg) {
	global $LOG_INFORMATION_MESSAGES;
	global $logfile;
	global $INFOMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_INFORMATION_MESSAGES) {
		fwrite($logfile, $INFOMSG.$infomsg.$NEWLINE);
	}
}

function log_debugmsg($debugmsg) {
	global $LOG_DEBUG_MESSAGES;
	global $logfile;
	global $DEBUGMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_DEBUG_MESSAGES) {
		fwrite($logfile, $DEBUGMSG.$debugmsg.$NEWLINE);
	}
}

function log_startmsg() {
	global $LOG_START_MESSAGES;
	global $logfile;
	global $STARTMSG;
	global $NEWLINE;
	global $DELIMITER;
	
	if($LOG_START_MESSAGES) {
		
		$text = "";
		
		/*if(isLoggedIn()) {
			
			// User has provided a name in profile
			if(isset($_SESSION["user-fname"])) {
				$text .= $_SESSION["user-fname"]." ".$_SESSION["user-lname"]." ";
			}
			
			// User has no name in profile
			$text .= "(".$_SESSION["user-id"].") [".$_SERVER["REMOTE_ADDR"]."]";
		} else {
			$text = "Guest (".$_SERVER["REMOTE_ADDR"].")";
		}*/
		
		$text = "Guest (".$_SERVER["REMOTE_ADDR"].")";
			
		fwrite($logfile, $STARTMSG.$text.$NEWLINE);
	}
}

function log_mailmsg($mailmsg) {
	global $LOG_MAIL_MESSAGES;
	global $logfile;
	global $MAILMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_MAIL_MESSAGES) {
		fwrite($logfile, $MAILMSG.$mailmsg.$NEWLINE);
	}
}

function log_apimsg($apimsg) {
	global $LOG_API_MESSAGES;
	global $logfile;
	global $APIMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_API_MESSAGES) {
		fwrite($logfile, $APIMSG.$apimsg.$NEWLINE);
	}
}

function log_domsg($domsg) {
	global $LOG_DO_MESSAGES;
	global $logfile;
	global $DOMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_DO_MESSAGES) {
		fwrite($logfile, $DOMSG.$domsg.$NEWLINE);
	}
}

function log_appmsg($appmsg) {
	global $LOG_APP_MESSAGES;
	global $logfile;
	global $APPMSG;
	global $NEWLINE;
	global $DELIMITER;
	if($LOG_APP_MESSAGES) {
		fwrite($logfile, $APPMSG.$appmsg.$NEWLINE);
	}
}
?>
