let rateMap = new Map();
let discountMap = new Map();

$(function(){
	
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	var totalAmount = urlParams.get('total');
	if(totalAmount)
	{
		$("#totalAmount").text(totalAmount);
		var totalModal = new bootstrap.Modal(document.getElementById('totalModal'), {keyboard: false});
		totalModal.show();
		setTimeout(function() {totalModal.hide()}, 5000);		
	}

	$('#ar,#engineer,#truck,#client-filter,#eng-filter').select2();
	
	var pickeropts = { dateFormat:"dd-mm-yy"}; 
	$( ".datepicker" ).datepicker(pickeropts);	
	
	$(".maintable").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
		widgets: ['filter'],
		filter_columnAnyMatch: true
	}); 
	$(".ratetable").tablesorter({
		dateFormat : "ddmmyyyy",
		theme : 'bootstrap',
	}); 
	
	
	if(window.location.href.includes('success')){
		var x = document.getElementById("snackbar");
		x.className = "show";
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);	
	}





	// Populate the tables based on SQL and Range from URL
	
	$("#todayFilter").on("click",function(){
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0');
		var yyyy = today.getFullYear();
		today = dd + '-' + mm + '-' + yyyy;
		
		callAjax(today,'Today');
	});				
	
	$("#10DaysFilter").on("click",function(){
		var days10 = new Date(Date.now() - (10 * 24 * 60 * 60 * 1000));
		var dd = String(days10.getDate()).padStart(2, '0');
		var mm = String(days10.getMonth() + 1).padStart(2, '0');
		var yyyy = days10.getFullYear();
		days10 = dd + '-' + mm + '-' + yyyy;
		
		callAjax(days10,'10 Days');
	});				
	
	function callAjax(date,range){
		if(range !== 'Today'){
			$.ajax({
				type: "POST",
				url: "ajax/filterSales.php",
				data:'startDate='+ date,
				success: function(response){
					if(response)
						window.location.href = 'list.php?sql='+response+'&range='+range;
					else
						alert('Some error occured. Please contact developer !!!');
				}
			});			
		}
		else{
			$.ajax({
				type: "POST",
				url: "ajax/filterSales.php",
				data:'startDate='+ date + '&endDate=' + date,
				success: function(response){
					if(response)
						window.location.href = 'list.php?sql='+response+'&range='+range;
					else
						alert('Some error occured. Please contact developer !!!');
				}
			});						
		}
	}
	
	$("#customFilter").on("click",function(){
		var filterModal = new bootstrap.Modal(document.getElementById('filterModal'), {})
		filterModal.show();				
	});
				
			
	colCount = document.getElementById('ratetable').rows[1].cells.length;
	if(colCount > 2){
		$('.ratetable').find('tbody tr:visible').each(function(){
			var productName = $(this).find('td:eq(0)').text();
			var rate = $(this).find('td:eq(2)').text();
			var discount = $(this).find('td:eq(3)').text();
			
			rateMap.set(productName,rate);
			discountMap.set(productName,discount);
		});
	}
	
	
			
	// AJAX to navigate to edit page	
	$('.saleId').click(function(){
		var saleId = $(this).data('id');
		var params = $(this).data('params');
		params = params.replace('success&','');
		window.location.href = 'edit.php?sales_id='+saleId + '&' + params;
	});

				

		
		
	//  SET FINAL RATE ON PAGE LOAD
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
		url: "ajax/getWD.php",
		data:'product='+product+'&date='+date,
		success: function(data){
			$("#wd").val(data);
			refreshRate();
		}
	});
	
	//  REFRESH FINAL RATE ON FIELD CHANGES				
	$("#date").change(function()
	{
		date = $(this).val();
		product = $("#product").val();
		client = $("#ar").val();
		
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
						str += '<input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">';
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

		var arId = $('#ar').val();
		var shopName = shopNameArray[arId];
		$('#shopName').val(shopName);	
		
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
						str += '<input class="form-check-input clearHolding" type="checkbox" id="'+holding.id+'" name="'+holding.id+'">';
						str += '<label class="form-check-label" for="clearholding">'+holding.qty+' bags holding</label></div></li>';
					}
					str += '</ul>';
					document.getElementById("holding-card").innerHTML = str;
				}
			}
		});	
	});	
	

	$("#bd").change(function(){
		refreshRate();
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
});

function refreshRate()
{
	var rate=document.getElementById("rate").value;
	var cd=document.getElementById("cd").value;
	var wd=document.getElementById("wd").value;
	var bd=document.getElementById("bd").value;
	
	$('#final').val(rate-cd-wd-bd);
}			

	
let qtyMap = new Map();				
$('.maintable').on('initialized filterEnd', function(){
	qtyMap.clear();
	var qty;
	$(this).find('tbody tr:visible').each(function(){
		var productName = $(this).find('td:eq(2)').text();
		if(qtyMap.has(productName))
			qty = parseFloat( qtyMap.get(productName) ) + parseFloat( $(this).find('td:eq(3)').text() );
		else
			qty = parseFloat( $(this).find('td:eq(3)').text() );
		
		qtyMap.set(productName,qty);
	});
	$("#ratebody").empty();
	for (let [key, value] of qtyMap){
		var table = document.getElementById("ratetable");
		var tableRef = document.getElementById('ratetable').getElementsByTagName('tbody')[0];
		var row   = tableRef.insertRow();					
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		cell1.innerHTML = `${key}`;
		cell2.innerHTML = `${value}`;
		if(colCount >2)
		{
			var cell3 = row.insertCell(2);
			var cell4 = row.insertCell(3);
			cell3.innerHTML = rateMap.get(`${key}`);
			cell4.innerHTML = discountMap.get(`${key}`);
		}
	}
})	

$("#newSaleForm").submit( function(eventObj) {
var holdings = document.getElementsByClassName('clearHolding');
for(var i=0;i<holdings.length;i++)
{	  
  var holdingId = holdings[i].id;
  var checkboxValue = $('#'+holdingId).prop('checked');	
  $("<input>").attr("type", "hidden")
	  .attr("name", "clearHolding["+holdingId+"]")
	  .attr("value", checkboxValue)
	  .appendTo("#newSaleForm");
}	  
  return true;
});
