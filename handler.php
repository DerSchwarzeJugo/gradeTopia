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

function addGradeToClass($class, $grade, $emphasis, $date) {
	$arrData = getJson();
	$data = array(
		'grade' => $grade,
		'emphasis' => $emphasis,
		'date' => $date
	);
	$arrData[$class]['grades'][] = $data;
	$success = setJson($arrData);
	if ($success) {
		setcookie('gradeSuccess', '1', time() + 60, '/');
		return $success;
	} else {
		return false;
	}
}

function createHTML($data, $class, $grade, $emphasis, $date) {
	$html = '<!DOCTYPE html><html><head><title>gradeTopia</title><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><link rel="stylesheet" href="css/main.css"></head><body><header class="w3-white"><nav class="w3-row"><a href="index.html#form" class="w3-bar-item w3-button w3-col s12">Zurück</a></nav></header><section class="w3-container"><h1 id="mainTitle">gradeTopia</h1></section><main><section class="w3-contianer"><h2>Resultate</h2>';
	$html .= '<h4>Du hast die Note <b>' . $grade . '</b> mit Gewichtung von <b>' . $emphasis . '</b> im Fach <b>' . $data[$class]['name'] . '</b> am <b>' . $date . '</b> erreicht.</h4>';
	if (isset($_COOKIE['gradeSuccess'])) {
		$html .= 'Toll, du hast schon mindestens eine Note hinzugefügt. Besuche den Canvas um die Magie zu betrachten!';
	}
	$html .= '</section></main></body></html>';
	doTheMath($data, $class);
	return $html;
}

function doTheMath($data, $class) {
	$grades = $data[$class]['grades'];
	if (is_array($grades)) {
		switch (count($grades)) {
			case 1:
				foreach($grades as $key => $value) {
					if ($value['emphasis'] == 1) {
						$data[$class]['average'] = number_format($value['grade'], 2);
					} else {
						$lowest = 1;
						$highest = 6;
						$gradeTotal = $value['emphasis'] * $value['grade'];
						$factor = 1 - $value['emphasis'];
						$lowestAvg = $gradeTotal + $lowest * $factor;
						$highestAvg = $gradeTotal + $highest * $factor;
						$data[$class]['average'] = number_format($lowestAvg, 2) . ' - ' . number_format($highestAvg, 2);
					}
				}
				break;
			default:
				$totalPercentage = 0;
				foreach($grades as $key => $value) {
					$totalPercentage += $value['emphasis'];
				}
				if ($totalPercentage > 1) {
					$data[$class]['average'] = 'Die Summer der Noten ergibt mehr als 100%!';
				} else if ($totalPercentage == 1) {
					$result = 0;
					foreach($grades as $key => $value) {
						$result = $result + $value['emphasis'] * $value['grade'];
					}
					$data[$class]['average'] = number_format($result, 2);
				} else {
					$result = 0;
					foreach($grades as $key => $value) {
						$result = $result + $value['emphasis'] * $value['grade'];
					}
					$factor = 1 - $totalPercentage;
					$lowest = 1;
					$highest = 6;
					$lowestAvg = $result + $lowest * $factor;
					$highestAvg = $result + $highest * $factor;
					$data[$class]['average'] = $lowestAvg . ' - ' . $highestAvg;
				}
		}
		setJson($data);
	}
}

// add class part
$json = file_get_contents('php://input');

if (!$json) {
	echo json_encode(array("success" => false));
} else {
	try {
		$data = json_decode($json);
		if (property_exists($data, 'addClass')) {
			$name = htmlspecialchars($data->addClass);
			$success = addClassToJson($name);
			if ($success) {
				echo json_encode($success);
			}
		} else if (property_exists($data, 'getInfo')) {
			$id = htmlspecialchars(explode('ci-', $data->getInfo)[1]);
			$currentData = getJson();
			echo json_encode($currentData[$id]);
		}
		
	} catch (Exception $e) {
		echo json_encode($e);
	}
}

// add grade to class part
if (isset($_POST['class']) && is_numeric($_POST['class']) && isset($_POST['grade']) && !empty($_POST['grade']) && isset($_POST['emphasis']) && !empty($_POST['emphasis']) && isset($_POST['date']) && !empty($_POST['date'])) {
	// class is index of the array in this case
	$class = htmlspecialchars($_POST['class']);
	$grade = htmlspecialchars($_POST['grade']);
	$emphasis = htmlspecialchars($_POST['emphasis']);
	$date = htmlspecialchars($_POST['date']);
	$success = addGradeToClass($class, $grade, $emphasis, $date);
	if ($success) {
		$html = createHTML($success, $class, $grade, $emphasis, $date);
		echo $html;
		// echo $success;
	}
} 
// else {
// 	$class = htmlspecialchars($_POST['class']);
// 	echo is_numeric($class);

// }

?>