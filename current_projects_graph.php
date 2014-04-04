<?php
include_once('../dbconnect.php');

/* change character set to utf8 */
if (!mysqli_set_charset($con, "utf8")) {
    echo "Error loading character set utf8: " . mysqli_error($con);
}

/*echo '<?xml version="1.0" encoding="UTF-8"?><xml>';*/

$result = mysqli_query($con,"select months.months, sum(all_projects.external_funds_his/all_projects.duration_in_months) from all_projects, months where months between start_date and end_date group by months;");

$xml = new SimpleXMLElement('<xml/>');

while($row = mysqli_fetch_array($result))
  {
  
    $track = $xml->addChild('data_point');
    $track->addChild('x', $row[0]);
    $track->addChild('y', $row[1]);

  }
Header('Content-type: text/xml');
print($xml->asXML());

  mysqli_close($con);
?> 
