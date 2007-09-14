<?php

	$end_date = date("Y/m/d", time()); 
	$start_date = date("Y/m/d",strtotime("-10 year" ,strtotime($end_date)));
	$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($end_date)));
	for($i=0; $i<20; $i++) {
		$start_date = date("Y/m/d",strtotime("-10 year",strtotime($start_date)));
		echo $start_date."<br/>\n";
	}

?>