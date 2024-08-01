<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	End of job run - fetch and process history
//
//  Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            STL Test job
        </title>
        <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body>
        <div class="banner">
        <h1>Recording job</h1>
        </div>
        <div class="menubar">
        <div class="menu" onclick="window.location='index.php'">HOME</div>
        </div>
<?php

/* -------   Data for job ------------- */
$columnMap = array();   // For flags
$valueMap = array();
$jobStartString;        // Start time as string
$jobStartInt;
$phase = "Prestart";
$currentOp;
$opStart;               // Relative to job in seconds
$currentTemperature;
$currentPressure;
$result = array();      // Values to be recorded

session_start();
    require_once 'common.php';

    $config = setConfig();
    $dbConnection = dbConnect($config);
    $mysqli = dbConnect($config);
/*print_r($_SESSION);
    $booth = $_SESSION['booth'];
    $job = $_SESSION['job']; */
    unset($_SESSION['job']);    // May need to move
    $booth = 3;              // Kludges for developping
    $job = "Jon1";
    showButton();
    exit();

    // $hist = fetchHistory();
    $hist = file_get_contents("h2b.txt") . "'";

    $hist = str_replace("\r", "", $hist);   // Remove carriage return & quotes
    $hist = str_replace('"', '', $hist); 
    $lines = explode("\n", $hist);          // Break input into array of lines

    headerRecord($lines[0]);     // Fetch the header record and make column map
    setStart($lines[1]);                // Set start times

    for ($i = 1; $i < count($lines) - 1; $i++) {
        processLine($lines[$i]);        // Now process each line
    }

    endOfData();

function showButton() {
    global $booth, $job;
    
    $str = '<button onClick="window.location='
        . "'showjob.php?booth=3&job=Jon1'"
        . '">Report </button>';
    
    echo '<div style="margin-left: 40px; margin-top: 20px;"><br>';
    echo "$str</div";
//    <button onClick="window.location='showjob.php
}


/* --------- Fetch history from the booth --------
 * 
 */
function fetchHistory() {
    global $mysqli, $booth, $job;
    
                                // Make dates for the EBD call
                                // It's easist to get start from the DB
    $sql = "SELECT * FROM jobs WHERE customer=$booth AND jobno='$job'";
    $result = mysqli_query($mysqli, $sql)
        or die ("Error reading jobs" . mysqli_error($mysqli));
    $record = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);

    $jobStartString = $record['date'];          // Fetch hour & minutes
    $startH = substr($jobStartString, 11, 2);
    $startM = substr($jobStartString, 14, 2);

    $start = date("dmY") . "$startH$startM";
    $end = date("dmyhi");
    $params = '$dtHT$ftT$st' . $start . '$et' . $end;
    echo $params;
                    // Now need to add address & credentials and make the call
}

/* ------------  End of run --------------
 * Make the header record, build the JSOM
 * and write to the DB
 */
function endOfData() {
    global $result;
    
    $jobNo = $_SESSION['job'];
    $arHead = makeJobHeader($jobNo);
                                // end of process. Finalise the JSON result
    $final = array();
    $polling = array("Values", $result);
    $header = array("Header", $arHead);
    array_push($final, $header);
    array_push($final, $polling);
    $JSON1 = json_encode($final);
    $JSON = str_replace(",[", ",\n[", $JSON1);  // Break into lines
    file_put_contents("json.txt", $JSON);       // Only for debugging
    
    updateDB($JSON, $jobNo);
}

function updateDB($JSON, $jobNo) {
    global $mysqli;
    
    $booth = $_SESSION['booth'];
    $sql = "UPDATE jobs SET result = '$JSON' "
            . "WHERE customer=$booth AND jobno='$jobNo'";
    mysqli_query($mysqli, $sql)
        or die ("Error writing to jobs" . mysqli_error($mysqli));
}

function makeJobHeader($jobno) {
    global $jobStartString;
    
    $ar = array();
    $el1 = array("Date", $jobStartString);
    array_push($ar, $el1);
    $el2 = array("Jobno", $jobno);
    array_push($ar, $el2);
   
    return $ar;
}

/*  --------- Process each line of input ---------
 *  Parameter - input line as text
 * 
 *  This is driven by a phase: pre start, 
 *  the booth, operation flag on, operation finished
 */
function processLine($line) {
    global $columnMap;
    global $phase, $currentOp, $opStart, $jobStartInt;
    
    $columns = explode(";", $line);     // Break into array of columns
    checkTemp($columns);
    checkPressure($columns);
    // Do T & P and bottoms
    switch ($phase) {                   // First scan until BOOTH_RUN goes on
        case "Prestart":
            $value = checkColumn($columns, "Running");
            if ($value != '0') {        // Set the 1st operation
                $currentOp = "Prepare booth";
                $opStart = 0;
                $jobStartInt = $columns[0];
                $phase = "Preparing";
            }
            break;
        case "Preparing":               // Now in 1st phase
                                        // It only ends when (Spray) starts
            $value = seekAnyFlagOn($columns);
            if (!is_null($value)) {
                $tm = getTime($columns);
//           echo "\nFound $value $tm"; 
                endOp($tm);
                $currentOp = $value;
                $opStart = $tm - $jobStartInt;
                $phase = "Running";     // and off we go
            }
            break;
        case "none":                // After the end of an op. Look for the next
            $value = seekAnyFlagOn($columns);
            if (!is_null($value)) {
                $tm = getTime($columns);
//            echo "\nFound $value $tm";      // Not happening
                $currentOp = $value;
                $opStart = $tm - $jobStartInt;
                $phase = "Running";
            }
            break;
        default:                    // Look for end of current operation
            $value = seekFlagOff($columns);
            if ($value === '0') {
                $tm = getTime($columns);
                endOp($tm);
                $phase = "none";
            }
            break;
    }
}

function checkTemp($columns) {
    global $currentTemperature, $valueMap, $jobStartInt, $result;

    $value = $columns[$valueMap["Temperature"]];
    if ($currentTemperature !== $value) {
        $tm = getTime($columns) - $jobStartInt;
        $ar = array("Temp", $tm, $value);
        array_push($result, $ar);
        $currentTemperature = $value;
    }
}

function checkPressure($columns) {
    global $currentPressure, $valueMap, $jobStartInt, $result;

    $value = $columns[$valueMap["Pressure"]];
    if ($currentPressure !== $value) {
        $tm = getTime($columns) - $jobStartInt;
//        echo $columns[0] . " Pr $tm $value \n";
        $ar = array("Press", $tm, $value);
        array_push($result, $ar);
        $currentPressure = $value;
    }
}

/* -------- Process end of an operation ----------
 * Parameter end time, integer
 * 
 * Compute length of operation and add an object
 * to the result
 */
function endOp($endTime){
    global $currentOp, $opStart, $jobStartInt, $currentPressure, $currentTemperature, $result;
    
    $endTime -= $jobStartInt;
    $length = $endTime - $opStart;
//    $name = "op" . $opCount++;
    $job = array($currentOp, $opStart, $length, $currentTemperature, $currentPressure);
    array_push($result, $job);
}

/* ------- Find the value of a column ----------
 * Parameters: line as array of columns, 
 *              column name, text
 * Returns value of column, untyped
 */
function checkColumn($columns, $col) {
    global $columnMap;

    return $columns[$columnMap[$col]];
}

/* ---------- Fetch the time of an input line ------
 * Parameter: line as array of columns
 * Returns time as UNIX seconds
 * 
 * First element of the line holds the time
 */
function getTime($columns) {
    return $columns[0];
}

/* ---------  An operation is running. Check for ite end -----
 * Check the value of the flag, ignore if still 1
* Parameter: line as array of columns
  * Set data for the start of the op
 */
 function checkStateOff($columns) {    
    global $columnMap, $phase, $phaseStart, $jobStartInt;

    $value = $columns[$columnMap[$phase]];
    if ($value === '0') {                   // It's off
        $end = $columns[0] - $jobStartInt;
        $length = $end - $phaseStart;
//    echo "Check off " . $columns[0] . " $phase $jobStartInt $phaseStart\n";
        $phase = "NoPhase";
    }
}

/* --------- Look for end of the current op -------
 * Parameter: line as array of columns
 * Returns flag for the op:
 *      1 - still running
 *      2 - stopped
 */
function seekFlagOff($columns) {
    global $currentOp;
    
    return checkColumn($columns, $currentOp);
}

/* -------- Look for any of the run flags goin to on ----
* Parameter: line as array of columns
  * 
 */
function seekAnyFlagOn($columns) {
    global $columnMap;

    foreach ($columnMap as $col => $index) {    // Scan each column value
        if ($col == "Running") {                // In this case, we know it's on
            continue;
        }
        if (checkColumn($columns, $col) == '1')
            return $col;
    }
    return null;
}

/* --------- New phase -----------
 * Parameter: line as array of columns
 * 
 * Check - one of these may not be used
 */
function checkForNewPhase($columns) {
    global $columnMap, $phase, $phaseStart, $jobStartInt;
    
    if ($phase == "Prepare booth") {
        return;
    }
    foreach ($columnMap as $thisPhase => $index) {
        if ($columns[$index] == 1) {
                $phaseStart = $columns[0] - $jobStartInt;
        echo $columns[0] . " New phase $index $thisPhase $phaseStart\n";
                $phase = $thisPhase;
        }
    }
}

/* -------------  Header record ---------
 * Create maps of column indeces
 * Parameter: line as array of columns
 * 
 */
function headerRecord($line) {
    global $columnMap, $valueMap; // Only for trace
    
    $columns = explode(";", $line);
    
    for($index = 0; $index < count($columns); $index++) {
        $tag = $columns[$index];
         setOpColumn($tag, $index);
    }
 
//    setOpColumn("Prestart", count($columns));
}

/* ---------- Set one operation map record --------
 * Parameters Tag name from history
 *            Index to set
 * This is where the tag names turn into 
 */
function setOpColumn($tag, $index) {
    global $columnMap, $valueMap;
    
    switch ($tag) {
        case "Booth_Run_Flag":
            $columnMap["Running"] = $index;
            break;
        case "Booth_Spray_Flag":
            $columnMap["Spray"] = $index;
            break;
        case "Booth_Bake_Flag":
            $columnMap["Bake"] = $index;
            break;
        case "Booth_FlashOff_Flag":
            $columnMap["Flash-off"] = $index;
            break;
        case "Booth_Cool_Flag":
            $columnMap["Cooling"] = $index;
            break;
        case "Pressure":
            $valueMap["Pressure"] = $index;
            break;
        case "Temperature":
            $valueMap["Temperature"] = $index;
            break;
    }
}
    
/* ---------- Set job data from the 1st value record --------
 * 
 */
function setStart($line) {
    global $jobStartString, $jobStartInt;
    
    $columns = explode(";", $line);
    $jobStartInt = $columns[0];
    $jobStartString = $columns[1];
    $jobStartString = str_replace('\/', '/', $jobStartString);
// echo "Set start $jobStartString, $jobStartInt, $pressure, $temperature\n";
}

?>
    </body>
</html>

