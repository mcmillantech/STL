<!DOCTYPE html>
<html>
    <head>
        <title>
            STL New Job
        </title>
        <script src="dashboard.js"></script>
        <link type="text/css" rel="stylesheet" href="stl.css">
        <style>
            .ivName {
                width:  200px;
                float: left;
                margin-left: 40px;
            }
        </style>
    </head>
    <body onload="startPage()">

<?php
    require 'header.php';
    showHeader(" STL spray job");

?>
    <div style="margin-left: 40px;">
        <br><br>
        <form method='post' action='index.php?action=posjob'>
            <span class='prompt'>Job number</span>
            <span class='input'>
                <input name="jobno">
            </span>
            <br><br><button type='submit'>Start</button>
        </form>
    </div>
    </body>
</html>


