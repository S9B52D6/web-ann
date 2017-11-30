<?php
	if ($_FILES['file']['error'] != 0) {
		echo '{"success": "false", "error":"' . $_FILES['file']['error'] . '"}';
	}
	else {
		$id = uniqid();
		move_uploaded_file($_FILES['file']['tmp_name'], './uploads/' . $id . '.csv' );
		echo '{"success":"true", "id":"'. $id . '"}';
	}

?>
