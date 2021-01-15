<?php

function getJson() {
	$jsonFile = 'list.json';
	$jsonData = file_get_contents($jsonFile);
	$arrData = json_decode($jsonData, true);
	return $arrData;
}

function setJson($arrData) {
	$jsonFile = 'list.json';
	$jsonData = json_encode($arrData, JSON_PRETTY_PRINT);
	if (file_put_contents($jsonFile, $jsonData)) {
		return $arrData;
	} else {
		return false;
	}
}

function addClassToJson($formData) {
	$arrData = getJson();
	$data = array(
		'name' => $formData
	);
	$arrData[] = $data;
	$success = setJson($arrData);
	if ($success) {
		return $success;
	} else {
		return false;
	}
}

function addGradeToClass($class, $grade, $date) {
	$arrData = getJson();
	$data = array(
		'grade' => $grade,
		'date' => $date
	);
	$arrData[$class]['classes'][] = $data;
	$success = setJson($arrData);
	if ($success) {
		return $success;
	} else {
		return false;
	}
}

function createHTML($data, $class, $grade, $date) {
	$html = '<!DOCTYPE html><html><head><title>gradeTopia</title><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><link rel="stylesheet" href="css/main.css"></head><body><header class="w3-white"><nav class="w3-row"><a href="index.html#form" class="w3-bar-item w3-button w3-col s12">Zurück</a></nav></header><section class="w3-container"><h1 id="mainTitle">gradeTopia</h1></section><main><section class="w3-contianer"><h2>Resultate</h2>';
	if (isset($_COOKIE['gradeSuccess'])) {
		$html .= '<h4>Du hast die Note <b>' . $grade . '</b> am <b>' . $date . '</b> im Fach <b>' . $data[$class]['name'] . '</b> erreicht.</h4>';
	} else {
		$html .= '<h4>Versuche es erneut, etwas ist schiefgelaufen!</h4>';
	}
	$html .= '</section></main></body></html>';
	return $html;
}

// add class part
$json = file_get_contents('php://input');

if (!$json) {
	echo json_encode(array("success" => false));
} else {
	try {
		$data = json_decode($json);
		if ($data) {
			$name = htmlspecialchars($data->addClass);
			$success = addClassToJson($name);
			if ($success) {
				echo json_encode($success);
			}
		}
		
	} catch (Exception $e) {
		echo json_encode($e);
	}
}

// add grade to class part
if (isset($_POST['class']) && is_numeric($_POST['class']) && isset($_POST['grade']) && !empty($_POST['grade']) && isset($_POST['date']) && !empty($_POST['date'])) {
	// class is index of the array in this case
	$class = htmlspecialchars($_POST['class']);
	$grade = htmlspecialchars($_POST['grade']);
	$date = htmlspecialchars($_POST['date']);
	$success = addGradeToClass($class, $grade, $date);
	if ($success) {
		setcookie('gradeSuccess', 'true', time() + 60, '/');
		$html = createHTML($success, $class, $grade, $date);
		echo $html;
		// echo $success;
	}
} 
// else {
// 	$class = htmlspecialchars($_POST['class']);
// 	echo is_numeric($class);

// }

?>