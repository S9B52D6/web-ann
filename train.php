<?php

	$filepath = 'uploads/' . $_POST['userID'] . '.csv';
	$outputFile = 'uploads/' . $_POST['userID'] . '.fann';
	$numInput = (int) $_POST['inputs'];
	$numOutput = (int) $_POST['outputs'];

	try {
		CSVToFann($filepath, $numInput, $numOutput, $outputFile);
		$targetError = (float) $_POST['targetError'];
		$epochLimit = (int) $_POST['epochLimit'];

		$layerSizes = $_POST['neuronCounts'];
		for($i = 0; $i < count(layerSize); $i++) {
			$layerSizes[$i] = (int) $layerSizes[$i];
		}
		array_unshift($layerSizes, $numInput);
		$ann = fann_create_standard_array(count($layerSizes), $layerSizes);

		if($ann) {

			$activationMethods = $_POST['activationMethods'];
			for($i = 0; $i < count($activationMethods); $i++) {
				$method = getActivationFunction($activationMethods[$i]);
				fann_set_activation_function_layer($ann, $method, $i+1); 
			} 	

			if(fann_train_on_file($ann, $outputFile, $epochLimit, 5000, $targetError)) {
				fann_save($ann, 'uploads/' . $_POST['userID'] . '.net');
			} 

			echo '{"success":"true"}';
			fann_destroy($ann);
		}
	}
	catch(Exception $e) {
		echo '{"success":"false", "error":"' . $e->getMessage() . '"}';
	}

	function CSVToFANN($filepath, $numInput, $numOutput, $outputFile) {
		//Ensure files were opened correctly
		try {

			$numLines = count(file($filepath));

			//Open input and output files
			$infile = fopen($filepath, 'r');
			$outfile = fopen($outputFile, 'w');

			//Write top meta line to file
			fwrite($outfile, $numLines . ' ' . $numInput . ' ' . $numOutput . "\n");
		
			//Step through file
			while(($line = fgets($infile)) !== false) {
				//Split CSV line by comma
				$line = str_replace(' ', '', $line);
				$values = explode(',', $line);
			
				//Write input line
				$inputLine = "";
				for($i = 0; $i < $numInput; $i++) {
					$inputLine .= $values[$i] . ' ';
				}
				fwrite($outfile, $inputLine . "\n");
	
				//Write output line
				$outputLine = "";
				for($i = $numInput; $i < $numInput + $numOutput; $i++) {
					$outputLine .= $values[$i];
				}
				fwrite($outfile, $outputLine);
			}

			//Cleanup files
			fclose($infile);
			fclose($outfile);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	function getActivationFunction($function) {
		switch($function) {
			case "linear":
				return FANN_LINEAR;
			case "sigmoid":
				return FANN_SIGMOID;
			case "sigmoid_stepwise":
				return FANN_SIGMOID_STEPWISE;
			case "sigmoid_symmetric":
				return FANN_SIGMOID_SYMMETRIC;
			case "sigmoid_symmetric_stepwise":
				return FANN_SIGMOID_SYMMETRIC_STEPWISE;
			case "gaussian":
				return FANN_GAUSSIAN;
			case "gaussian_symmetric":
				return FANN_GAUSSIAN_SYMMETRIC;
			case "gaussian_symmetric_stepwise":
				return FANN_GAUSSIAN_SYMMETRIC_STEPWISE;
			case "elliot":
				return FANN_ELLIOT;
			case "elliot_symmetric":
				return FANN_ELLIOT_SYMMETRIC;
			case "linear_piece":
				return FANN_LINEAR_PIECE;
			case "linear_piece_symmetric":
				return FANN_LINEAR_PIECE_SYMMETRIC;
			case "sin_symmetric":
				return FANN_SIN_SYMMETRIC;
			case "cos_symmetric":
				return FANN_COS_SYMMETRIC;
			case "sin":
				return FANN_SIN;
			case "cos":
				return FANN_COS;
			default:
				return NULL;
		}
	}
?>
