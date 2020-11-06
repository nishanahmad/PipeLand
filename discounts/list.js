$(function(){
	
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
	}	
	
	$('#client').select2({
		theme: "classic",
		width:"65%"
	});
	
	var pickeropts = { dateFormat:"dd-mm-yy"}; 
	$( ".datepicker" ).datepicker(pickeropts);		
	
	$(".discounttable thead th:eq(0)").data("sorter", false);
	$(".discounttable thead th:eq(1)").data("sorter", false);
	$(".discounttable thead th:eq(2)").data("sorter", false);
	$(".discounttable thead th:eq(3)").data("sorter", false);
	$(".discounttable thead th:eq(4)").data("sorter", false);
	$(".discounttable thead th:eq(5)").data("sorter", false);
		
	$(".discounttable").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	}); 
	
	$('#discounttable td').each(function() {
		if($(this).text() == '0/-'){
			 $(this).parent().hide();
		}
	 });	
});	