<!DOCTYPE html>
<html>
    <head>
        <title>
            STL Dashboard 2
        </title>
        <script src="dashboard.js"></script>
       <script src = "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin = "anonymous" referrerpolicy = "no-referrer"> </script>
        <link type="text/css" rel="stylesheet" href="stl.css">
        <style>
            .ivName {
                width:  200px;
                float: left;
                margin-left: 40px;
            }
            .ivValue {
                width:  100px;
                float: left;
            }
            .ivUnit {
                width: 180px;
                float: left;
            }
            .ivAl {
                width:  80px;
                float: left;
            }
            .ivAlOn {
                width:  80px;
                color:  red;
                float: left;
            }
            .alarm {
                color: red;
            }
        </style>
    </head>
    <body onload="startPage()">
<?php
//    session_start();
    require 'header.php';
    showHeader(" STL Dashboard");
/*
    Tests
        case 1:
            console.log(sessionStorage);
        case 2:
            boothApi('iv', instantValues);
        case 3:
            doInstantValues(1);
        4: startApp();
 *      5 startupMakeBoothUrls
 */
    if (array_key_exists("action", $_GET)) {
        postNewJob();
    }
    
    function postNewJob() {
        require_once 'common.php';

        $config = setConfig();
        $dbConnection = dbConnect($config);
        $mysqli = dbConnect($config);
 
        $jobNo = addslashes($_POST['jobno']);
        $cusId = 3;                 // KLUDGE!!!
        $sdate = date("Y/m/d h:i");
        $sql = "INSERT into jobs (jobno, customer, date) "
                ."VALUES('$jobNo', $cusId, '$sdate')";
//        $mysqli->query($sql)
//            or die ("Error updating item " . mysqli_error($mysqli));
        $_SESSION['booth'] = 3;
        $_SESSION['job'] = $jobNo;
    }
?>

        <div id="content" style="margin-left: 40px;">
            
            </div>
    </body>
</html>

