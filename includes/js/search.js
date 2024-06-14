<script type="text/javascript">
function changeValuesLevel(form) {
	if (form.value=='0') {
		document.form.level.value='0';
		document.form.level2.value='999999';	
	} else if (form.value=='1') {
		document.form.level.value='1';
		document.form.level2.value='2';
	} else if (form.value=='2') {
		document.form.level.value='2';
		document.form.level2.value='5';
	} else if (form.value=='3') {
		document.form.level.value='5';
		document.form.level2.value='10';
	} else if (form.value=='4') {
		document.form.level.value='10';
		document.form.level2.value='20';
	} else if (form.value=='5') {
		document.form.level.value='20';
		document.form.level2.value='40';
	} else if (form.value=='6') {
		document.form.level.value='40';
		document.form.level2.value='75';
	} else if (form.value=='7') {
		document.form.level.value='75';
		document.form.level2.value='125';
	} else if (form.value=='8') {
		document.form.level.value='125';
		document.form.level2.value='999999';					
	}
}

function changeValuesMoney(form) {
if (form.value=='0') {
		document.form.money.value='0';
	} else if (form.value=='1') {
		document.form.money.value='500';
	} else if (form.value=='2') {
		document.form.money.value='2500';
	} else if (form.value=='3') {
		document.form.money.value='10000';
	} else if (form.value=='4') {
		document.form.money.value='50000';
	} else if (form.value=='5') {
		document.form.money.value='250000';
	} else if (form.value=='6') {
		document.form.money.value='500000';
	} else if (form.value=='7') {
		document.form.money.value='1000000';
	} 	
}
</script>