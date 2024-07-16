<?php
// ----------------------------------------------
//	Read the database access parameters from
//	the config file
//
// ----------------------------------------------
function setConfig() {
    global $mode;
    
    $hfile = fopen('config.txt', 'r');
    if (!$hfile) {
            echo "Could not open config file";
    }
    $config = array();
    $config['mode'] = $mode;
    while (!feof($hfile)) {
            $str = fgets($hfile);
            sscanf($str, '%s %s', $ky, $val);
            $config[$ky] = $val;
    }
    fclose ($hfile);
    $_SESSION['config'] = $config;
    return $config;
}

// ----------------------------------------------
//  Connect to the database
//
//  Parameter Configuration onject
//  (db connection values)
//
//  Returns	  MYSQL connection
// ----------------------------------------------
function dbConnect($config)
{
    $dbConnection = mysqli_connect 
        ($config['dbhost'], $config['dbuser'], $config['dbpw'], $config['dbname'])
            or die ("Connection fail " . mysqli_error($dbConnection));
    mysqli_select_db($dbConnection, $config['dbname']) 
        or die (
            "Could not select database : " . mysqli_error($dbConnection));

    return $dbConnection;
}
?>

