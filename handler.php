<?php

// if (isset($_POST['addClass']))
// 	echo htmlspecialchars($_POST['addClass']);
function addClassToJson($formData) {
	$jsonFile = 'list.json';
	$jsonData = file_get_contents($jsonFile);
	$arrData = json_decode($jsonData, true);
	$data = array(
		'name' => $formData
	);
	array_push($arrData, $data);
	$jsonData = json_encode($arrData, JSON_PRETTY_PRINT);
	if (file_put_contents($jsonFile, $jsonData)) {
		return $arrData;
	} else {
		return false;
	}
}

$json = file_get_contents('php://input');

if (!$json) {
	echo json_encode(array("success" => false));
} else {
	try {
		$data = json_decode($json);
		$name = htmlspecialchars($data->addClass);
		$success = addClassToJson($name);
		if ($success) {
			echo json_encode($success);
		}
		
	} catch (Exception $e) {
		echo json_encode($e);
	}
}


?>