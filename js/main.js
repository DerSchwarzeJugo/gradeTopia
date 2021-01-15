function changeNav(e) {
	let el = e.target
	let slug = el.href.split('#')[1]
	document.getElementsByClassName('general active')[0].classList.remove('active')
	document.getElementById(slug).classList.add('active')
}

function parseClassData(response) {
	document.getElementById('classForm').classList.add('hidden')	
	document.getElementById('gradesForm').classList.remove('hidden')
	document.getElementById('addClassIcon').classList.remove('hidden')
	document.querySelector('nav a:last-child').click()
	updateFormSelect(JSON.parse(response))
	outputClasses(JSON.parse(response))
} 

function outputClasses(data) {
	let newData = '' 
	data.forEach((el, i) => {
		newData += '<div class="classItem w3-card-4" id="ci-' + i + '"><div class="w3-container w3-center"><p>' + el.name + '</p></div></div>'
	})
	document.getElementById('classList').innerHTML = newData
}

function updateFormSelect(data) {
	let newData = ''
	data.forEach((el, i) => {
		newData += '<option value="' + i + '">' + el.name + '</option>'
	})
	document.getElementById('class').innerHTML = newData
}

function sendClassForm(data) {
	let form = document.querySelector('#classForm')
	let xhttp = new XMLHttpRequest()
	xhttp.onreadystatechange = (e) => {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			parseClassData(xhttp.response)
		}
		
	}
	xhttp.open(form.getAttribute("method"), form.getAttribute("action"), true);
	xhttp.setRequestHeader("Content-type", "application/json");
	dataString = JSON.stringify({"addClass": data.addClass})
	xhttp.send(dataString)
}

function getClassList() {
	fetch("list.json")
	.then(response => {
	return response.json();
})
.then(data => {
	if (data.length > 0) {
		updateFormSelect(data)
		outputClasses(data)
	} 
})
}

function drawBaseCanvas() {
	let cv = document.getElementById('mainCanvas')
	if (cv) {
		let ctx = cv.getContext('2d')
		ctx.clearRect(0, 0, canvas.width, canvas.height)
		ctx.moveTo(40, 20)
		ctx.lineTo(40, cv.offsetHeight - 20)
		ctx.stroke()
		ctx.moveTo(20, cv.offsetHeight - 40)
		ctx.lineTo(cv.offsetWidth - 20, cv.offsetHeight - 40)
		ctx.stroke()
	}
}

function formValidation() {
	
	let gradeVal = document.getElementById('grade').value
	let classVal = document.getElementById('class').value
	let dateVal = document.getElementById('date').value
	let dateObj = new Date(Date.parse(dateVal))
	if (gradeVal >= 1 && gradeVal <= 6 && typeof classVal == "string" && classVal != "" && !isNaN(dateObj.getTime())) {
		return true
	} else {
		return false
	}
}

function openSpecificTab() {
	if (location.hash != "") {
		let hash = location.hash.split('#')[1]
		document.querySelector('#link-' + hash).click()
	}
}

document.addEventListener("DOMContentLoaded", (event) => {
	drawBaseCanvas()
	openSpecificTab()
	getClassList()
	
	if (document.getElementById("class").hasChildNodes()) {
		document.getElementById("class").innerHTML = '<option value="" disabled selected>F&uuml;ge mindestens ein Fach hinzu</option>'
	}

	document.getElementById('gradesForm').addEventListener('submit', (e) => {
		// formhandler of grades
		let success = formValidation()
		if (success) {
			return true
		} else {
			e.preventDefault()
			alert('Deine Formularangaben sind nicht korrekt')
			return false
		}
	})

	document.getElementById('classForm').addEventListener('submit', (e) => {
		// formhandler of classes
		e.preventDefault()
		let formData = []
		formData['addClass'] =  document.querySelector('#addClass').value
		sendClassForm(formData)
	})
	
	document.getElementById('addClassIcon').addEventListener('click', (e) => {
		document.getElementById('classForm').classList.remove('hidden')
		document.getElementById('addClassIcon').classList.add('hidden')
		document.getElementById('gradesForm').classList.add('hidden')
	})
})

