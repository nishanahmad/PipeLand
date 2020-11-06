function arRefresh(shopNameArray)
{
	var arId = $('#ar').val();
	var shopName = shopNameArray[arId];
	$('#shopName').val(shopName);
}								
	
$(document).ready(function()
{
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);					
	}
	
	$("#ar,#engineer,#truck").select2();
	
	var pickerOpts = { dateFormat:"dd-mm-yy"}; 
	$( "#date" ).datepicker(pickerOpts);	
	
	var arId = $('#ar').val();
	var shopName = shopNameArray[arId];
	$('#shopName').val(shopName);	
		

	var date = $("#date").val();
	var product = $("#product").val();
	var client = $("#ar").val();
	
	$.ajax({
		type: "POST",
		url: "ajax/getRate.php",
		data:'date='+date+'&product='+product,
		success: function(data){
			var rate = data;
			$("#rate").val(rate);
			refreshRate();
		}
	});
	$.ajax({
		type: "POST",
		url: "ajax/checkEngineer.php",
		data:'client='+client,
		success: function(data){
			if(data.includes("Engineer"))
			{
				$("#wd").val(0);
				refreshRate();
			}
			else
			{
				$.ajax({
					type: "POST",
					url: "ajax/getWD.php",
					data:'product='+product+'&date='+date,
					success: function(data){
						$("#wd").val(data);
						refreshRate();
					}
				});										
			}
		}
	});
	$.ajax({
		type: "POST",
		url: "ajax/getCD.php",
		data:'date='+date+'&product='+product+'&client='+client,
		success: function(data){
			$("#cd").val(data);
			refreshRate();
		}
	});		


	$("#date").change(function()
	{
		var date = $(this).val();
		var product = $("#product").val();
		var client = $("#ar").val();
		
		$.ajax({
			type: "POST",
			url: "ajax/getRate.php",
			data:'date='+date+'&product='+product,
			success: function(data){
				var rate = data;
				$("#rate").val(rate);
				refreshRate();
			}
		});
		$.ajax({
			type: "POST",
			url: "ajax/checkEngineer.php",
			data:'client='+client,
			success: function(data){
				if(data.includes("Engineer"))
				{
					$("#wd").val(0);
					refreshRate();
				}
				else
				{
					$.ajax({
						type: "POST",
						url: "ajax/getWD.php",
						data:'product='+product+'&date='+date,
						success: function(data){
							$("#wd").val(data);
							refreshRate();
						}
					});										
				}
			}
		});												
		$.ajax({
			type: "POST",
			url: "ajax/getCD.php",
			data:'date='+date+'&product='+product+'&client='+client,
			success: function(data){
				$("#cd").val(data);
				refreshRate();
			}
		});		
	});
	
	
	
	$("#product").change(function()
	{
		date = $("#date").val();
		product = $(this).val();
		client = $("#ar").val();
		
		$.ajax({
			type: "POST",
			url: "ajax/getRate.php",
			data:'product='+product+'&date='+date,
			success: function(data){
				var rate = data;
				$("#rate").val(rate);
				refreshRate();
			}
		});			
		$.ajax({
			type: "POST",
			url: "ajax/getWD.php",
			data:'product='+product+'&date='+date,
			success: function(data){
				$("#wd").val(data);
				refreshRate();
			}
		});										
		$.ajax({
			type: "POST",
			url: "ajax/getCD.php",
			data:'product='+product+'&date='+date+'&client='+client,
			success: function(data){
				$("#cd").val(data);
				refreshRate();
			}
		});				
		$.ajax({
			type: "POST",
			url: "ajax/checkEngineer.php",
			data:'client='+client,
			success: function(data){
				if(data.includes("Engineer"))
				{
					$("#wd").val(0);
					refreshRate();
				}
				else
				{
					$.ajax({
						type: "POST",
						url: "ajax/getWD.php",
						data:'product='+product+'&date='+date,
						success: function(data){
							$("#wd").val(data);
							refreshRate();
						}
					});										
				}
			}
		});															
	});
	
	$("#ar").change(function()
	{
		var date = $("#date").val();
		var product = $("#product").val();
		var client = $(this).val();
		$.ajax({
			type: "POST",
			url: "ajax/getCD.php",
			data:'client='+client+'&date='+date+'&product='+product,
			success: function(data){
				$("#cd").val(data);
				refreshRate();
			}
		});			
		$.ajax({
			type: "POST",
			url: "ajax/checkEngineer.php",
			data:'client='+client,
			success: function(data){
				if(data.includes("Engineer"))
				{
					$("#wd").val(0);
					refreshRate();
				}
				else
				{
					$.ajax({
						type: "POST",
						url: "ajax/getWD.php",
						data:'product='+product+'&date='+date,
						success: function(data){
							$("#wd").val(data);
							refreshRate();
						}
					});										
				}
			}
		});			
	});	
	$("#bd").change(function(){
		refreshRate();
	});	

	$("#sheetMdlBtn").click(function(){
		var truck = $("#sheet_truck").val();
		truck = truck.replace("-", "").toUpperCase();
		$.ajax({
			type: "POST",
			url: "ajax/getDriverName.php",
			data:'truck='+truck,
			success: function(response){
				$("#driver_name").val(response);
			}
		});
		$.ajax({
			type: "POST",
			url: "ajax/getDriverPhone.php",
			data:'truck='+truck,
			success: function(response){
				$("#driver_phone").val(response);
			}
		});				
	});				
	

	$("#deletebutton").click(function(){
		var id = $("#id").val();
		var sql = $("#sql").val();
		var range = $("#range").val();
		$.ajax({
			url: 'ajax/delete.php',
			type: 'post',
			dataType: 'JSON',
			data: {id:id},
			success: function(response){
				if(response.status == 'success'){
					window.location.href = 'list.php?success&sql=' + sql + '&range=' + range;
				}else if(response.status == 'error'){
					$("#confirmId").text('');
					$("#deleteError").text(response.value);
					return false;
				}
				else{
					$("#deleteError").text('Unknown error. Please contact admin');
					return false;					
				}
			},
			error: function (jqXHR, exception) {
				var msg = '';
				if (jqXHR.status === 0) {
					msg = 'Not connect.\n Verify Network.';
				} else if (jqXHR.status == 404) {
					msg = 'Requested page not found. [404]';
				} else if (jqXHR.status == 500) {
					msg = 'Internal Server Error [500].';
				} else if (exception === 'parsererror') {
					msg = 'Requested JSON parse failed.';
				} else if (exception === 'timeout') {
					msg = 'Time out error.';
				} else if (exception === 'abort') {
					msg = 'Ajax request aborted.';
				} else {
					msg = 'Uncaught Error.\n' + jqXHR.responseText;
				}
				$("#displayError").text(msg);
				return false;
			}		
		});
	});		

$("#deleteModal").on("hidden.bs.modal", function(){
	$("#deleteError").text('');
	$("#confirmId").text('Are you sure you want to delete this sale?');
});	


	// TRUCK LOADING FUNCTIONS ON EDIT 
	/*
	$('#editForm').on('submit', function(event){
		event.preventDefault();
		var id = document.getElementById('id').value;
		var product = document.getElementById('product').value;
		var qty = document.getElementById('qty').value;
				
		if(document.getElementById('truck').value !== null)
			var truck = document.getElementById('truck').value;
		else
			var truck = "";		

		$.ajax({
			url: 'ajax/upsertLoading.php',
			type: 'post',
			data: {id:id, product:product, qty:qty, truck:truck},
			success: function(response){
				if(response.status == 'success'){
					$("#editForm")[0].submit();
				}else if(response.status == 'error'){
					$("#displayError").text(response.value);
					return false;
				}
			},
			error: function (jqXHR, exception) {
				var msg = '';
				if (jqXHR.status === 0) {
					msg = 'Not connect.\n Verify Network.';
				} else if (jqXHR.status == 404) {
					msg = 'Requested page not found. [404]';
				} else if (jqXHR.status == 500) {
					msg = 'Internal Server Error [500].';
				} else if (exception === 'parsererror') {
					msg = 'Requested JSON parse failed.';
				} else if (exception === 'timeout') {
					msg = 'Time out error.';
				} else if (exception === 'abort') {
					msg = 'Ajax request aborted.';
				} else {
					msg = 'Uncaught Error.\n' + jqXHR.responseText;
				}
				$("#displayError").text(msg);
				return false;
			}		
		});
	});	
	*/
});

function refreshRate()
{
	var rate=document.getElementById("rate").value;
	var cd=document.getElementById("cd").value;
	var wd=document.getElementById("wd").value;
	var bd=document.getElementById("bd").value;
	
	$('#final').val(rate-cd-wd-bd);
}