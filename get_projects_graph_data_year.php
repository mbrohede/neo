<?php

require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
require_once('FirePHPCore/fb.php');
ob_start();

include_once('../dbconnect.php');
 
/* change character set to utf8 */
if (!mysqli_set_charset($con, "utf8")) {
    echo "Error loading character set utf8: " . mysqli_error($con);
}

/* check what we want to see */
if( isset($_POST['start']) ){
	$start_date = $_POST['start']; /* The start of graph */
} else {
	$start_date = '2005-01-01'; /* Default */
}

if( isset($_POST['end']) ){
	$end_date = $_POST['end']; /* The end of the graph */
} else {
	$end_date = '2018-12-31'; /* Default */
}

if( isset($_POST['end']) ){
	$view_point_date = $_POST['view_point']; /* The view point date. Defaults to now() */
} else {
	$view_point_date = date('Y-m-d'); /* The view point date. Defaults to now */
}

//$query = "select date_format(months.months, '%Y') as years from months group by years;";
$query = "select date_format(years.years, '%Y')from years where years between '$start_date' and '$end_date';";
$firephp->log('Range Query: ' . $query);
$result = mysqli_query($con,$query) or die(mysqli_error($con));



	/* Insert all months first */
	$result_set = array();

	while($row = mysqli_fetch_array($result))
	{
		$result_set[$row[0]] = array(); /* create an empty array for every month we found */
	}
	/* fill with UMIF and Infofusion*/

	$query = "select date_format(months.months, '%Y') as year, sum(all_projects.external_funds_his/(all_projects.duration_in_months)) as 'UMIF' 
	          from all_projects,months 
			  where ( (months.months between '$start_date' and '$end_date') and
					  (months.months between all_projects.start_date and all_projects.end_date) and 
			          ((name = 'UMIF: Uncertainty Management for Information Fusion') or
					  (name = 'Information Fusion from Databases, Sensors and Simulations'))) 
			  group by year;";
	$firephp->log('Range Query: ' . $query);
	$result = mysqli_query($con,$query) or die(mysqli_error($con));
	
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

	$query = "select date_format(months.months, '%Y') as year, sum(all_projects.external_funds_his/all_projects.duration_in_months) as 'past' 
				from all_projects,months 
				where (	('$view_point_date' > all_projects.end_date ) and 
						granted_date is not null and 
						(months.months between '$start_date' and '$end_date') and
						(months.months < end_date and months.months >= start_date) and
						not((name = 'UMIF: Uncertainty Management for Information Fusion') 
							or(name = 'Information Fusion from Databases, Sensors and Simulations'))) 
				group by year;";
	$firephp->log('Range Query: ' . $query);			 
	$result = mysqli_query($con,$query) or die(mysqli_error($con));
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
	$query = "select date_format(months.months, '%Y') as year, sum(external_funds_his/duration_in_months) from all_projects,months
		where (months.months between start_date and end_date) and name in (
		select name from all_projects where granted_date is not null and 
							(
								(date_format(all_projects.granted_date, '%Y-%m-%d') <= '$view_point_date') and 
								('$view_point_date' <= date_format(all_projects.end_date, '%Y-%m-%d'))
							) and not
							(	(name = 'UMIF: Uncertainty Management for Information Fusion') or 
								(name = 'Information Fusion from Databases, Sensors and Simulations')))
					
			group by year  ;";
	$result = mysqli_query($con,$query) or die(mysqli_error($con));
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
  
  	/* fill with submitted projects */
	$query = "select date_format(months.months, '%Y') as year, sum(external_funds_his/duration_in_months) from all_projects,months
		where (months.months between start_date and end_date) and name in (
		select name from all_projects where granted_date is null and 
							(
								('$view_point_date' <= date_format(all_projects.start_date, '%Y-%m-%d')) and (date_format(all_projects.submitted_date, '%Y-%m-%d') <= '$view_point_date' )
							) and not
							(	(name = 'UMIF: Uncertainty Management for Information Fusion') or 
								(name = 'Information Fusion from Databases, Sensors and Simulations')))
					
			group by year  ;";
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
  array_push($google_charts_data, array("Year","Infofusion/UMIF","Past Projects", "Running projects", "Submitted projects"));
	foreach (array_keys($result_set) as $i){
		array_push( $google_charts_data, array_merge(array($i),$result_set[$i])); /* Add the x-axis column (months) and y-axis data columns */
	}
  
  echo '{"graph":[ { "data":' . json_encode($data) . '},{ "labels":' . json_encode(array_keys($result_set)) . '}, { "google_charts_data":' . json_encode($google_charts_data) . '} ]}';

mysqli_close($con);
?> 
