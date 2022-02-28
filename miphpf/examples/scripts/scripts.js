var oldColor;
function toNewColor (currentRow, newColor) {
	oldColor = currentRow.style.backgroundColor;
	currentRow.style.backgroundColor = newColor;
}
function toOldColor (currentRow) {
	currentRow.style.backgroundColor = oldColor;
}

function sortForm(formName, sortBy, sortDir)
{
	form = document.forms[formName];
	
	var sortByElem = document.createElement('input');
	sortByElem.name = 'sortBy';
	sortByElem.value = sortBy;
	sortByElem.type = 'hidden';
	form.appendChild(sortByElem);
	
	var sortDirElem = document.createElement('input');
	sortDirElem.name = 'sortDir';
	sortDirElem.value = sortDir;
	sortDirElem.type = 'hidden';
	form.appendChild(sortDirElem);
	
	return form.submit();
}
