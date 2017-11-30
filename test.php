<?php

	$inputs = $_POST['inputs'];
	for($i = 0; $i < count($inputs); $i++) {
		$inputs[$i] = (float) $inputs[$i];
	}
	$filepath = 'uploads/' . $_POST['userID'] . '.net';

	if(!is_file($filepath)) {
		echo '{"success":"false", "error":"File not found!"}';
	}
	else {
		$ann = fann_create_from_file($filepath);
		if(!ann) {
			echo '{"success":"false", "error":"Could not create ANN"}';
		}
		else {
			$output = fann_run($ann, $inputs);
			$jsonString = '{"success":"true", "output":[';
			for($i = 0; $i < count($output); $i++) {
				$jsonString .= $output[$i] . ', ';
			}
			$jsonString = substr($jsonString, 0, -2);
			$jsonString .= ']}';
			echo $jsonString;
		}
	}
?>
