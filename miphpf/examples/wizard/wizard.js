function nextPage(input)
{
	input.form.wizardAction.value = "next";
	input.form.submit();
}

function previousPage(input)
{
	input.form.wizardAction.value = "previous";
	input.form.submit();
}

function cancelWizard(input)
{
	if (window.confirm("You will lose all changes. Are you sure?")) {
		input.form.wizardAction.value = "cancel";
		input.form.submit();
	}
}