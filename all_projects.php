<?php
include_once('../dbconnect.php');  
/* change character set to utf8 */
if (!mysqli_set_charset($con, "utf8")) {
    echo "Error loading character set utf8: " . mysqli_error($con);
}

echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta charset="utf-8" /> 
	<title>Associated projects UMIF</title>

<style>
.granted {
	background-color:#C4DF9B;
}

.rejected {
	background-color:#F7977A;
}

.data-table, .data-table td, .data-table th {
    border-color: black;
    border-style: solid;
}

.data-table {
    border-width: 0 0 0px 0px;  
    border-spacing: 0;
    border-collapse: collapse; 
    margin: 0;
}

.data-table td, .data-table th {
    margin: 0px;
    padding: 6px;
    border-width: 1px;
    vertical-align: top;
}

.data-table th {
    background-color: #D7D7D7;
	text-align:left; 
}

.missing {
    background-color:#FFF868;
}
</style>

	<script type="text/javascript" src="jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="jquery.freezeheader.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
    	$("table").freezeHeader({ top: true, left: false });
	});
</script> 
</head>
<body>';

/* check what we want to see a specific coordinators projects*/
if( isset($_REQUEST['coordinator']) ){
	$coordinator = $_REQUEST['coordinator']; /* The coordinator in question */
	$query = "SELECT * FROM all_projects where coordinators_his LIKE '%$coordinator%' order by submitted_date DESC;";
} else {
	$query = "SELECT * FROM all_projects order by submitted_date DESC;";
}

$result = mysqli_query($con,$query);

echo "<h1>All associated projects</h1>";
echo "<table class='data-table'>";
echo "<thead>";
echo "<tr>";
echo    "<th colspan='18'>Name</th>";
echo "</tr>";
echo "<tr>";
echo    "<th> kbär </th>";
echo    "<th> Duration </th>";
echo    "<th> Duration in months </th>";
echo    "<th> External funds HIS </th>";
echo    "<th> Matching funds HIS </th>";
echo    "<th> Total funds HIS </th>";
echo    "<th> Matching funds industry </th>";
echo    "<th> Other partners </th>";
echo    "<th> Total project budget </th>";
echo    "<th> Source of ext funds </th>";
echo    "<th> Call name </th>";
echo    "<th> Partners </th>";
echo    "<th> Coordinators HIS </th>";
echo    "<th> Submitted date </th>";
echo    "<th> Granted date </th>";
echo    "<th> Rejected date </th>";
echo    "<th> Start date </th>";
echo    "<th> End date </th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
  
while($row = mysqli_fetch_array($result))
  {
      $granted = false;
      $rejected = false;
  echo "<tr>"."<td colspan='18'>" . $row['name'] . "</td>" . "</tr>";
  if ($row['granted_date'] !== null){
    echo "<tr class='granted'>";
    $granted = true;
  } elseif ($row['rejected_date'] !== null) {
    echo "<tr class='rejected'>";
    $rejected = true;
  }else {
    echo "<tr>";
  }
  if ($granted && $row['kbar'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }
  echo $row['kbar'] . "</td>";
  
  if ($row['duration'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }
  echo $row['duration'] . "</td>";

  if ($row['duration_in_months'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }
  echo $row['duration_in_months'] . "</td>";

  if ($row['external_funds_his'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }
  echo $row['external_funds_his']."</td>";
  
  if ($row['matching_funds_his'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }
  echo $row['matching_funds_his'] . "</td>";

  if ($row['total_funds_his'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['total_funds_his'] . "</td>";
  
  if ($row['matching_funds_industry'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['matching_funds_industry'] . "</td>";
  
  if ($row['other_partners'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['other_partners'] . "</td>";
  
  if ($row['total_budget'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['total_budget'] . "</td>";
  
  if ($row['source_of_ext_funds'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['source_of_ext_funds'] . "</td>";

  if ($row['call_name'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['call_name'] . "</td>";
  
  if ($row['partners'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['partners'] . "</td>" ;

  if ($row['coordinators_his'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['coordinators_his'] . "</td>";

  if ($row['submitted_date'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['submitted_date'] . "</td>";
  
  echo "<td>" . $row['granted_date'] . "</td>";
  echo "<td>" . $row['rejected_date'] . "</td>";
  
  if ($row['start_date'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['start_date'] . "</td>";
  
  if ($row['end_date'] == null){
      echo "<td class='missing'>";
  } else {
      echo "<td>";
  }  
  echo $row['end_date'] . "</td>";
  echo "</tr>";
  }
echo "</tbody></table></body></html>";
mysqli_close($con);
?> 
