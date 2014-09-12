<?php
include_once('config.php'); //Any configs that might be necessary

foreach($watch_files as $from => &$to) { //Go through all files to watch defined in config.php
	if (file_exists($from)) { //Check if file exists
		$to = ['to' => $to, 'time' => filemtime($from)]; //Sets the latest edit time of file
	} else {
		unset($watch_files[$from]); //File don't exist. Remove the file from array
	}
}

//This is for showing the wait message each loop
$show_wait_message = true;

//Start message
echo 'start listening to changes...'. "\n";
while(true) { //Continuous loop (this is our program)
	foreach($watch_files as $from => &$to) { //Go through all files to watch defined in config.php and modified above
		clearstatcache($from); //Remove any cache about the file (file edit time is saved in cache)
		if($to['time'] < filemtime($from)) { //Check if the file has been changed last saved
			//If file has been updated a new css is compiled with lessc, an output message is sent and the new change time is saved to the file info in array
			shell_exec('lessc '. $from. ' '. $to['to']. ' --clean-css -x');
			echo $from. ' compiled'. "\n";
			
			$to['time'] = &filemtime($from);
			
			//After a compilation is done we want to show the wait message
			$show_wait_message = true;
		}
	}
	
	if($show_wait_message) { //should we show the wait message or not?
		echo 'Waiting for changes...'. "\n";
	}
	
	//Halt program for 2 seconds and set to not show wait message. This makes the wait message only show if any compilations has been made. and not every time the loop is run (every two seconds)
	sleep(2);
	$show_wait_message = false;
}