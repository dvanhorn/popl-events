<?php

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}


$UploadDirectory = "/home/lambdacalcul/webdata/events/" ;

$Domain = "popl.lambda-caclul.us" ;

date_default_timezone_set("America/Denver") ;

$ShortName = $_POST['short_name'] ;

# $Name = $_POST['name'] ;
# if ($Name) {
#  setcookie("name", "$Name", time()+60*60*24*120, "/", ".$Domain") ;
# }


?>

<!DOCTYPE html>

<html>

 <head>
   <title>POPL event submission</title>

   <style>
@import url('./paper.css') ;
   </style>

 </head>

 <body>

  <div class="module">


   <h1>Proposal submission</h1>


<?php

$Proposal = json_encode($_POST) ;

$EmergencyName = "proposal-" . date("Y-m-d-H-i") . ".txt" ;

file_put_contents("/tmp/$EmergencyName", "$Proposal") ;

function ErrorNotice() {
  global $Proposal ;

  echo "<p>Please forward this error code to <a href=\"http://ccs.neu.edu/home/dvanhorn/\">David Van Horn</a> to manually complete the submission process:</p>" ;

  echo "<textarea style=\"width: 500px; height: 100px;\">$Proposal</textarea>" ;
}

$EventName = "POPL2014" ;

$ProposalsDir = $UploadDirectory . "/$EventName/proposals" ;
$ArchiveDir = $UploadDirectory . "/$EventName/archive" ;
$ProposalDir = $ProposalsDir . "/$ShortName" ;

echo "Validating short name..." ;
if (!preg_match('/^[-A-Za-z0-9_]+$/',"$ShortName")) {
  echo "Invalid Short Name: $ShortName" ;
  ErrorNotice() ;
  exit ;
}
echo "OK.<br />" ;

$EventDir = $UploadDirectory . "/$EventName" ;

echo "Initializing database..." ;
if (!file_exists($EventDir)) {
 if (!mkdir($EventDir)) {
   echo "Error: Could not create upload directory." ;
   ErrorNotice() ;
   exit ;
 }

 if (!mkdir($ProposalsDir)) {
   echo "Error: Could not create assignments directory." ;
   ErrorNotice() ;
   exit ;
 }

 if (!mkdir($ArchiveDir)) {
   echo "Error: Could not create archive directory." ;
   ErrorNotice() ;
   exit ;
 }
}

if (!file_exists($ProposalDir)) {
  if (!mkdir($ProposalDir)) {
    echo "Error: Could not create $ShortName directory." ;
    ErrorNotice() ;
    exit ;
  }
}
echo "OK.<br />" ;


$Dest = $ProposalDir . "/data.json" ;

# echo "Debug: Saving to $Dest" ;


echo "Saving proposal to database..." ;
if (!file_put_contents($Dest,"$Proposal")) {
  echo "Error: Could not save proposal." ;
  ErrorNotice() ;
  exit ;
}


$ArchiveName = date("Y-m-d-H-i") . "-" . "$ShortName.json" ;

if (!copy($Dest,"$ArchiveDir/$ArchiveName")) {
  echo "Error: Could not archive submission." ;
  ErrorNotice() ;
  exit ;
}
echo "OK.<br />" ;


?>



</p>


  <p>
  Submission successful!
  </p>

  <p>
  If you need to resubmit or make a change, please contact 
  <a href="http://ccs.neu.edu/home/dvanhorn/">David Van Horn</a>.
  </p>

  </div>


<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-3661244-1");
pageTracker._trackPageview();
</script>




 </body>

</html>

