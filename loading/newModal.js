// AJAX to populate new modal	
$(function(){
	$('#saveNew').click(function(){
		var date = document.getElementById('date').value;
		var time = document.getElementById('time').value;
		var truck = document.getElementById('truck').value;
		var product = document.getElementById('product').value;
		var qty = document.getElementById('qty').value;
		
		if(!date)
			$("#insertError").text('Please fill the date field');
		else if(!time)
			$("#insertError").text('Please fill the time field');
		else if(!truck)
			$("#insertError").text('Please fill the truck field');
		else if(!product)
			$("#insertError").text('Please fill the product field');
		else if(!qty || isNaN(qty))
			$("#insertError").text('Please fill the qty field with a valid number');
		else
		{
			$.ajax({
				url: 'insertAjax.php',
				type: 'post',
				data: {date: date, time: time, truck: truck, product: product, qty: qty},
				success: function(response){
					if(response.status == 'success'){
						window.location.href = 'list.php?success';
					}else if(response.status == 'error'){
						$("#insertError").text(response.value);
					}
				}
			});			
		}			
	});
	$("#newModal").on("hidden.bs.modal", function(){
		$("#insertError").text('');
		$("#date").val('');
		$("#time").val('');
		$("#truck").val('');
		$("#product").val('');
		$("#qty").val('');
	});	
});		