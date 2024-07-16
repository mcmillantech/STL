<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	history.php
//		History form
//
//  Author	John McMillan, McMillan Technology
//              mcmillantech@uwclub.net
// ------------------------------------------------------
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Dashboard history 2</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="dashboard.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin = "anonymous" referrerpolicy = "no-referrer"> </script>
       <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body onload="startPage()">
        <div class="banner">
        <h1>History</h1>
        </div>
        <div class="menubar">
        <div class="menu" onclick="window.location='index.php'">HOME</div>
        </div>
        <div id="content"> </div>
<?php
    $year = date("Y");
?>
        <div id="content" style="margin-left: 40px;">
        <div><h2>Download historic data</div>
            <div id="formd">
              
                <span class='prompt'>Start from</span>
                <span class="input">
                    dd <input type="text" size="2" name="stday" id="stday">
                    mm <input type="text" size="2" name="stmon" id="stmon">
                    yy <input type="text" size="4" name="styear" id="styear"
                        value = <?php echo "'$year'"; ?> >&nbsp;&nbsp;Time&nbsp;
                    hh <input type="text" size="2" name="sthh" id="sthh">
                    min <input type="text" size="2" name="stmins" id="stmins">
                </span><br><br>
                
                <span class='prompt'>End</span>
                <span class="input">
                    dd <input type="text" size="2" name="ndday" id="ndday">
                    mm <input type="text" size="2" name="ndmon" id="ndmon">
                    yy <input type="text" size="4" name="ndyear" id="ndyear"
                              value = <?php echo "'$year'"; ?>>&nbsp;&nbsp;Time&nbsp;
                    hh <input type="text" size="2" name="ndhh" id="ndhh">
                    min <input type="text" size="2" name="ndmins" id="ndmins">
                </span><br><br>
                
                <div id='hisnext'>
                    <button onClick='hisPost()'> Next </button>
                </div>
                <div id='hisfile' style='visibility:hidden'>
                    <span class='prompt'>Download file</span>
                    <span class="input">
                        <input type='text' id='hisfname' size ='32' value=''>
                    </span>
                        <br><br>
                    <button onClick='hisGo()'> Download  </button>
                </div>
            </div>
        <br><br>
        <div id="status"></div>
              
          </div>
    </body>
</html>


