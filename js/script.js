$(document).ready(function() {});
// button "reset form" event on click
$('input[name="reset-form"]').click(function() {
	$(':checkbox').removeAttr('checked');
	$('input[type="number"]').val('');
	$('.point, .figure').css('display', 'none');
});
// button "reset results" event on click
$('input[name="reset-results"]').click(function() {
	$('.result-fields').remove();
	$('input[name="condition-results"]').val('reset');
	$('input[type="number"]').val('0');
});
// checkbox event on click
// $(':checkbox').click(function() {
// 	if ($(this).is('checked'))
// 		$(this).parent().css('background-color', 'rgba(0, 0, 255, 0.7)');
// });

// Get checkbox's id from X.
const xId = x => x < 0 ? `xm${-x}` : `x${x}`;
// Function for validation Y.
const validY = y => y >= -3.0 && y <= 5.0;

// array of points
const ElemPoints = new Array(9);
for (let x = -3; x <= 5; ++x)
	ElemPoints[x + 3] = document.getElementById(`point${xId(x)}`);
console.log(`Array of Points: ${ElemPoints}`);
// array of figures
const ElemFigures = new Array(5);
for (let r = 1; r <= 3; r += 0.5)
ElemFigures[r * 2 - 2] = document.getElementById(`figure${r}`);
console.log(`Array of Figures on coordinate grid: ${ElemFigures}`);

// array of X
const ElemCheckBoxesX = new Array(9);
for (let x = -3; x <= 5; ++x)
ElemCheckBoxesX[x + 3] = document.getElementById(xId(x));
console.log(`Array of X CheckBoxes: ${ElemCheckBoxesX}`);
// Y
const elemY = document.getElementById("y");
console.log(`Element Y: ${elemY}`);
let isValidY = !isNaN(parseFloat(elemY.value)) && validY(parseFloat(elemY.value));

// Processing change value of the X checkbox.
const changeX = x => ElemPoints[x + 3].style.display = (ElemCheckBoxesX[x + 3].checked && isValidY) ? "inline" : "none";
// Input text from form of Y.
function changeY() {
	let doubleY = parseFloat(elemY.value);
	if (isNaN(doubleY) || !validY(doubleY)) {
		// processing changes Y from "valid" to "not valid".
		isValidY = false;
		// hiding points.
		for (let x = -3; x <= 5; ++x)
			ElemPoints[x + 3].style.display = "none";
	} else {
		// processing changes Y from "not valid" to "valid".  
		isValidY = true;
		// if (checkbuttonX is on) than: update Y value of points and show checked points.
		for (let x = -3; x <= 5; ++x) {
			ElemPoints[x + 3].setAttribute("cy", `${48 - 2 * doubleY * 4}%`);
			if (ElemCheckBoxesX[x + 3].checked)
				ElemPoints[x + 3].style.display = "inline";
		}
	}
}
// Change visiality of figure with the R.
const changeR = r =>  ElemFigures[r * 2 - 2].style.display = (ElemFigures[r * 2 - 2].style.display == "none") ? "block" : "none";