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
            STL Job
        </title>
        <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body>
        <h1>STL Job</h1>
<?php

    $values = array();
    $jsonValues;
    $arHeader;
    $jobNo;
    $jobDate;

    $json = file_get_contents("json.txt");
    $obj = json_decode($json);
    foreach ($obj as $x => $obj2) {
        $type = $obj2[0];
        switch ($type) {
            case "Values":
                $jsonValues = $obj2[1];
                break;
            case "Header":
                $arHeader = $obj2[1];
                break;
        }
    }

    showJobHeading($arHeader);
    showHeadings();
    showValues();
    
    function showJobHeading($arHeader) {
    //    $ar = json_decode($jsonHeader);
    $jobNo;
    $jobDate;
//    print_r($arHeader);
        foreach ($arHeader as $inx => $item) {
            switch ($item[0]) {
                case "Jobno":
                    $jobNo = $item[1];
                    break;
                case "Date":
                    $jobDate = $item[1];
                    break;
            }
        }
        echo "<span class='jobName'>";
        echo "Job number $jobNo  Run on $jobDate";
        echo "</span><br><br>";
    }
    
    function showValues() {
        global $jsonValues, $values;

        $val = $jsonValues;
        foreach ($val as $x => $arVal) {
            remap($arVal);
        }
        
        sort($values);
        foreach ($values as $x => $line) {
            showLine($line);
        }
    }

    function showLine($line) {
        echo "<span class='jobName'>" . $line[1] . "</span>";
        echo "<span class='jobStart'>" . $line[0] . "</span>";
        echo "<span class='jobTime'>" . $line[2] . "</span>";
        echo "<span class='jobTemp'>" . $line[3] . "</span>";
        echo "<span class='jobPress'>" . $line[4] . "</span>";
        echo "<br>";
    }
    
    function remap($line) {
        global $values;
        
        $nl = array('','','','','','','');
        $nl[0] = $line[1];
        
        $op = $line[0];
        switch ($op) {
            case "Temp":
                $nl[3] = $line[2];
                break;
            case "Press":
                $nl[4] = $line[2];
                break;
            default:
                $nl[1] = $line[0];
                $nl[2] = $line[2];
                $nl[3] = $line[3];
                break;
        }
        array_push($values, $nl);
    }
    
function showHeadings() {
    echo "<b><span class='jobName'>Operation</span>";
    echo "<span class='jobStart'>Start</span>";
    echo "<span class='jobTime'>Length</span>";
    echo "<span class='jobTemp'>Temp'tre</span>";
    echo "<span class='jobPress'>Pressure</span>";
    echo "<div style = 'clear: both'> </div>";
    echo "</b><br>";
}
/*
function showOp($line) {
    echo "<span class='jobName'>" . $line[0] . "</span>";
    echo "<span class='jobStart'>" . $line[1] . "</span>";
    echo "<span class='jobTime'>" . $line[2] . "</span>";
    echo "<span class='jobTemp'>" . $line[3] . "</span>";
    echo "<span class='jobPress'>" . $line[4] . "</span>";
    echo "<div style = 'clear: both'> </div>\n";
}

function showTemp($line){
//    echo "<span class='jobName'>" . "&nbsp; " . "</span>";
    echo "<span class='jobStart'>" . $line[1] . "</span>";
//    echo "<span class='jobTime'>" . "&nbsp; " . "</span>";
    echo "<span class='jobTemp'>" . $line[2] . "</span>";
    echo "<div style = 'clear: both'> </div>\n";
}

function showPress($line){
    echo "<span class='jobName'>" . "&nbsp; " . "</span>";
    echo "<span class='jobStart'>" . $line[1] . "</span>";
    echo "<span class='jobTime'>" . "&nbsp; " . "</span>";
    echo "<span class='jobTemp'>" . "&nbsp;" . "</span>";
    echo "<span class='jobPress'>" . $line[2] . "</span>";
    echo "<div style = 'clear: both'> </div>\n";
}
*/

/* function showValue($fld, $wid) {
    $w = $wid . "px" ;
    echo "<span style='width: $w; margin-right:10px;'>";
    echo "$fld";
    echo "</span>";
} */
//    '{"Load":["Load booth / Prep", 0, 1200, 18,"OFF","25Hz","ON"],'
//	. '"Spray":["Spray basecoat",1200,1200, 23,"OFF", "100%", "ON"]} ';
//    echo $json;
?>
    </body>
</html>