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
?>

        <div id="content" style="margin-left: 40px;">
            
            </div>
    </body>
</html>

