<?php
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
require_once('FirePHPCore/fb.php');
ob_start();

$con=mysqli_connect("localhost","ifadm","Profile09","associated_projects");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
/* change character set to utf8 */
if (!mysqli_set_charset($con, "utf8")) {
    echo "Error loading character set utf8: " . mysqli_error($con);
}

/* check what we want to see */
if( isset($_POST['start']) ){
	$start_date = $_POST['start']; /* The start of graph */
} else {
	$start_date = "'2005-01-01'"; /* Default */
}

if( isset($_POST['end']) ){
	$end_date = $_POST['end']; /* The end of the graph */
} else {
	$end_date = "'2018-12-31'"; /* Default */
}

if( isset($_POST['end']) ){
	$view_point_date = $_POST['view_point']; /* The view point date. Defaults to now() */
} else {
	$view_point_date = "'". date('Y-m-d') . "'"; /* The view point date. Defaults to now */
}

$query = "select date_format(months.months, '%Y-%m') as months from months where months between '$start_date' and '$end_date';";
$firephp->log('Range Query: ' . $query);
$result = mysqli_query($con,$query);


	/* Insert all months first */
	$result_set = array();

	while($row = mysqli_fetch_array($result))
	{
		$result_set[$row[0]] = array(); /* create an empty array for every month we found */
	}
	/* fill with UMIF  and Infofusion*/

	$query = "select date_format(months.months, '%Y-%m') as month, sum(all_projects.external_funds_his/all_projects.duration_in_months) as 'UMIF' 
		from all_projects,months 
		where ( (months.months between '$start_date' and '$end_date') and
				(months.months between all_projects.start_date and all_projects.end_date) and 
				((name = 'UMIF: Uncertainty Management for Information Fusion') 
					or(name = 'Information Fusion from Databases, Sensors and Simulations'))) 
		group by months.months;";
	$firephp->log('IF/UMIF: ' . $query);
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result))
	{
		array_push($result_set[$row[0]], (int)$row[1]); /* Add to appropriate month */
	}	

	/* fill with '0' for 'empty months' */
	
	foreach (array_keys($result_set) as $i)
	{
	
		if(count($result_set[$i]) < 1)  
		{
			// add '0' if now data exists
			array_push($result_set[$i], 0); 
		}
		
	}

	/* fill with past projects */

	$query = "select date_format(months.months, '%Y-%m') as month, sum(past_projects.external_funds_his/past_projects.duration_in_months) as 'past' 
			from past_projects,months 
			where ( (months.months between '$start_date' and '$end_date') and
					(months.months < end_date and months.months >= start_date) and 
					not((name = 'UMIF: Uncertainty Management for Information Fusion') 
						or(name = 'Information Fusion from Databases, Sensors and Simulations'))) 
			group by months.months;";
	$result = mysqli_query($con,$query);
	$firephp->log('Past projects query: ' . $query);
	
	Fb::log($result, "dumping an array");
	while($row = mysqli_fetch_array($result))
	{
		array_push($result_set[$row[0]], (int)$row[1]); /* Add to appropriate month */
	}	


	/* fill with '0' for 'empty months' */
	
	foreach (array_keys($result_set) as $i)
	{	
		if(count($result_set[$i]) < 2)  
		{
			// add '0' if now data exists
			array_push($result_set[$i], 0); 
		}		
	}	

	/* fill with current projects */
	$query = "select date_format(months.months, '%Y-%m') as month, sum(all_projects.external_funds_his/all_projects.duration_in_months) as 'current'
			from all_projects,months 
			where ( ('$view_point_date' between all_projects.start_date and all_projects.end_date) and  
					(months.months < end_date and months.months >= start_date) and
					not((name = 'UMIF: Uncertainty Management for Information Fusion') 
						or(name = 'Information Fusion from Databases, Sensors and Simulations'))) 
			group by months.months;";
	$firephp->log('Current projects query: ' . $query);
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result))
	{
		array_push($result_set[$row[0]], (int)$row[1]); /* Add to appropriate month */
	}
	
	foreach (array_keys($result_set) as $i)
	{
		// add '0' if now data exists
		if(count($result_set[$i]) < 3) 
		{
			array_push($result_set[$i], 0); 
		}
	}
	
	
	$data = array();
	foreach (array_keys($result_set) as $i){
		array_push( $data, $result_set[$i]);
	}
	
	  	/* fill with applied projects */
	$query = "select date_format(months.months, '%Y-%m') as month, sum(all_projects.external_funds_his/(all_projects.duration_in_months)) as sum
	          from all_projects,months where ( (months.months between '$start_date' and '$end_date') and (all_projects.start_date is not NULL) and ('$view_point_date' < all_projects.start_date) and  
			                                    (months.months < end_date and months.months >= start_date) and
			                                    not((name = 'UMIF: Uncertainty Management for Information Fusion') 
												     or(name = 'Information Fusion from Databases, Sensors and Simulations'))) group by months.months;";
	$firephp->log('Applied projects query: ' . $query);
	$result = mysqli_query($con,$query) or die(mysqli_error($con));
	while($row = mysqli_fetch_array($result))
	{
		array_push($result_set[$row[0]], (int)$row[1]); /* Add to appropriate month */
	}
	
	foreach (array_keys($result_set) as $i)
	{
		// add '0' if now data exists
		if(count($result_set[$i]) < 4) 
		{
			array_push($result_set[$i], 0); 
		}
	}
	
	
	$data = array();
	foreach (array_keys($result_set) as $i){
		array_push( $data, $result_set[$i]);
	}

  
  $google_charts_data = array();
  array_push($google_charts_data, array("Month","Infofusion/UMIF", "Past Projects", "Running projects", "Applied projects"));
	foreach (array_keys($result_set) as $i){
		array_push($google_charts_data, array_merge(array($i),$result_set[$i])); /* Add the x-axis column (months) and y-axis data columns */
	}
  
  echo '{"graph":[ { "data":' . json_encode($data) . '},{ "labels":' . json_encode(array_keys($result_set)) . '}, { "google_charts_data":' . json_encode($google_charts_data) . '} ]}';

mysqli_close($con);
?> 
