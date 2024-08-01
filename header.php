<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	Meader & menus
//
//  Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
session_start();
function showHeader($title, $jobRun = NULL) {
    echo '<div class="banner">';
    echo "<h1 style='margin-left:40px'> $title</h1>";
    ?>
        </div>
        <div class="menubar">
        <div class="menu" onclick="doInstantValues()">INSTANT VALUES</div>
        <div class="menu" onclick="doTagHeadings()">TAG HEADINGS</div>
        <div class="menu" onclick="window.location='history.php'">HISTORY</div>
        <div class="menu" onclick="window.location='jobform.php'">RUN JOB</div>
        <div class="menu" onclick="settings()">SETTINGS</div>
        <div class="menu" onclick="logOff(0)">LOG OFF</div>
        <div style = "clear: both"> </div>
<?php
    if (array_key_exists('job', $_SESSION)) {
        showJobRunning();
    }
    echo "</div>";
}

function showJobRunning() {
    $jobNo = $_SESSION['job'];
    echo "<div style = 'margin-top:10px; margin-left:40px;'>Running job $jobNo";
    ?>
        <span style ="margin-left:40px;">
            <button onClick="window.location='jobend.php'">
                Job finished </button><br><br>
        </span>
<?php
    echo "</div";
}

        
