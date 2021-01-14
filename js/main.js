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
	// JSON.parse(response).forEach((el, i) => {
	// 	console.log(el.name)
	// })
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
		if (data.length > 0) 
			updateFormSelect(data)
	})
}

document.addEventListener("DOMContentLoaded", (event) => {
	// dom is ready at this point

	getClassList()
	console.log(document.getElementById("class").innerHTML)
	if (document.getElementById("class").hasChildNodes()) {
		console.log('sinep')
		document.getElementById("class").innerHTML = '<option value="" disabled selected>F&uuml;ge mindestens ein Fach hinzu</option>'
	}

	document.getElementById('gradesForm').addEventListener('submit', (e) => {
		// formhandler of grades
		e.preventDefault()
		console.log(e)
	})

	document.getElementById('classForm').addEventListener('submit', (e) => {
		// formhandler of grades
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

