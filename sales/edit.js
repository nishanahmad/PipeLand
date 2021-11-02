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
	
	$("#ar,#engineer,#truck,#driver_area").select2();
	
	var pickerOpts = { dateFormat:"dd-mm-yy"}; 
	$( "#date" ).datepicker(pickerOpts);	
	$( "#sheetDate" ).datepicker(pickerOpts);	
	
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

		document.getElementById('holding-card').innerHTML = "";
		$.ajax({
			type: "POST",
			url: "ajax/fetchHolding.php",
			data:'ar='+client+'&product='+product,
			success: function(response){
				if(response.status == 'success'){
					var str = '<ul class="list-group list-group-flush">';
					for(var i = 0; i < response.holdings.length; i++){
						var holding = response.holdings[i];
						str += '<li class="list-group-item"><div class="form-check form-switch">';
						str += '<input class="form-check-input" type="checkbox" id="'+holding.id+'" name="'+holding.id+'" onchange="ClearHolding(this);">';
						str += '<label class="form-check-label" for="flexSwitchCheckDefault">'+holding.qty+' bags holding</label></div></li>';
					}
					str += '</ul>';
					document.getElementById("holding-card").innerHTML = str;
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

		document.getElementById('holding-card').innerHTML = "";
		$.ajax({
			type: "POST",
			url: "ajax/fetchHolding.php",
			data:'ar='+client+'&product='+product,
			success: function(response){
				if(response.status == 'success'){
					var str = '<ul class="list-group list-group-flush">';
					for(var i = 0; i < response.holdings.length; i++){
						var holding = response.holdings[i];
						str += '<li class="list-group-item"><div class="form-check form-switch">';
						str += '<input class="form-check-input" type="checkbox" id="'+holding.id+'" name="'+holding.id+'" onchange="ClearHolding(this);">';
						str += '<label class="form-check-label" for="flexSwitchCheckDefault">'+holding.qty+' bags holding</label></div></li>';
					}
					str += '</ul>';
					document.getElementById("holding-card").innerHTML = str;
				}
			}
		});								
	});	
	$("#bd,#order_no,#qty").change(function(){
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

	$("#autoDiscount").click(function(){
		var product = $("#product").val();
		var discount = $("#bd").val();
		if(!discount)
			discount = 0;
		if($(this).is(":checked")) 
		{
			if(product == 1 || product == 3) 
			{
				$("#bd").val(parseInt(discount) + 5);
				var client = $("#ar").val();
				$.ajax({
					type: "POST",
					url: "ajax/getARName.php",
					data:'id='+client,
					success: function(result){
						if(result != null)
						{
							$("#customer").val(result);
							refreshRate();
						}
					}
				});								
			}
		}
		else
		{
			if(product == 1 || product == 3) 
			{
				$("#bd").val(discount - 5);
				$("#customer").val('');	
				refreshRate();				
			}
		}	
	});


	$("#deleteModal").on("hidden.bs.modal", function(){
		$("#deleteError").text('');
		$("#confirmId").text('Are you sure you want to delete this sale?');
	});	

	
	// POPULATE HOLDING DATA IF ANY
	document.getElementById('holding-card').innerHTML = "";
	$.ajax({
		type: "POST",
		url: "ajax/fetchHolding.php",
		data:'ar='+client+'&product='+product,
		success: function(response){
			if(response.status == 'success'){
				var str = '<ul class="list-group list-group-flush">';
				for(var i = 0; i < response.holdings.length; i++){
					var holding = response.holdings[i];
					str += '<li class="list-group-item"><div class="form-check form-switch">';
					str += '<input class="form-check-input" type="checkbox" id="'+holding.id+'" name="'+holding.id+'" onchange="ClearHolding(this);">';
					str += '<label class="form-check-label" for="flexSwitchCheckDefault">'+holding.qty+' bags holding</label></div></li>';
				}
				str += '</ul>';
				document.getElementById("holding-card").innerHTML = str;
			}
		}	
	});
	
	$("#editForm").submit(function(){
		var bill = $("#bill").val().toUpperCase();
		var godown = $("#godown").val();
		if(bill.includes('B') || bill.includes('C') || bill.includes('GB') || bill.includes('GC') || bill.includes('PB') || bill.includes('PC'))
		{
			if(!godown)
			{
				$("#insertError").text('Please select the godown');
				return false;	
			}
		}
	});	
});

function refreshRate()
{
	var rate=document.getElementById("rate").value;
	var cd=document.getElementById("cd").value;
	var wd=document.getElementById("wd").value;
	var bd=document.getElementById("bd").value;
	var qty=document.getElementById("qty").value;
	var order_no=document.getElementById("order_no").value;
	
	var finalRate = rate-cd-wd-bd;
	var totalAmount = (finalRate * qty) - order_no;
	
	$('#final').val(finalRate);
	$('#total').val(totalAmount);	
}

function ClearHolding(checkbox) 
{
	var saleId = $("#id").val();
    if(checkbox.checked == true){
		$.ajax({
			url: 'ajax/clearHoldingFromEdit.php',
			type: 'post',
			data: {id:checkbox.id, saleId:saleId, checked:'true'},
			success: function(response){
				if(response.status == 'success'){
					console.log('cleared');
				}else if(response.status == 'error'){
					console.log('not cleared');
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
				console.log(msg);
				return false;
			}	
		});
    }
    else{
		$.ajax({
			url: 'ajax/clearHoldingFromEdit.php',
			type: 'post',
			data: {id:checkbox.id, saleId:saleId, checked:'false'},
			success: function(response){
				if(response.status == 'success'){
					console.log('cleared');
				}else if(response.status == 'error'){
					console.log('not cleared');
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
				console.log(msg);
				return false;
			}	
		});
    }	
}

