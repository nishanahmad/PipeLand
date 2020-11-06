$(function(){
	
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
	}	
	
			
	$(".table").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	});
});	