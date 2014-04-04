<?php
include_once('../dbconnect.php');
  
/* change character set to utf8 */
if (!mysqli_set_charset($con, "utf8")) {
    echo "Error loading character set utf8: " . mysqli_error($con);
}

/* check what we want to see */
$selection = $_POST["selection"];

if ($selection === "past"){
	$result = mysqli_query($con,"SELECT * FROM past_projects");
	echo "<h1>Past associated projects</h1>";		
} elseif ($selection === "current") {
	$result = mysqli_query($con,"SELECT * FROM current_projects");
	echo "<h1>Current associated projects</h1>";	
} else {
	$result = mysqli_query($con,"SELECT * FROM all_projects");
	echo "<h1>All associated projects</h1>";	
}

echo "<table border='1'>";
  echo "<tr>"."<td colspan='15'> Name </td>" . "</tr>";
  echo 
  "<tr>".
  "<th> kbär </th>".
  "<th> Duration </th>" .
  "<th> Duration in months </th>" .
  "<th> External funds HIS </th>" .
  "<th> Matching funds HIS </th>" .
  "<th> Total funds HIS </th>" .
  "<th> Matching funds industry </th>" .
  "<th> Other partners </th>" .
  "<th> Total project budget </th>" .
  "<th> Source of ext funds </th>" .
  "<th> Partners </th>" .
  "<th> Coordinators HIS </th>" .
  "<th> Granted </th>" .
  "<th> Start date </th>" .
  "<th> End date </th>";
  "</tr>";
while($row = mysqli_fetch_array($result))
  {
  echo "<tr>"."<td colspan='15'>" . $row['name'] . "</td>" . "</tr>";
  echo "<tr><td>" . $row['kbar'] . "</td>".
  "<td>" . $row['duration'] . "</td>" .
  "<td>" . $row['duration_in_months'] . "</td>" .
  "<td>" . $row['external_funds_his']."</td>" .
  "<td>" . $row['matching_funds_his'] . "</td>" .
  "<td>" . $row['total_funds_his'] . "</td>" .
  "<td>" . $row['matching_funds_industry'] . "</td>" .
  "<td>" . $row['other_partners'] . "</td>" .
  "<td>" . $row['total_budget'] . "</td>" .
  "<td>" . $row['source_of_ext_funds'] . "</td>".
  "<td>" . $row['partners'] . "</td>" .
  "<td>" . $row['coordinators_his'] . "</td>" .
  "<td>" . $row['granted'] . "</td>" .
  "<td>" . $row['start_date'] . "</td>" .
  "<td>" . $row['end_date'] . "</td>";
  echo "</tr>";
  }
echo "</table>";
mysqli_close($con);
?> 
