<?php

$con=mysqli_connect("localhost","__NAME__","__PASSWORD__","associated_projects");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>