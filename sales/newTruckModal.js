// AJAX to populate new modal	
$(function(){
	$('#saveNewTruck').click(function(){
		if(document.getElementById('number').value && document.getElementById('driver').value)
		{
			var number = document.getElementById('number').value;
			var driver = document.getElementById('driver').value;
			
			if(document.getElementById('phone').value)
				var phone = document.getElementById('driverphone').value;
			else
				var phone = '';

			$.ajax({
				url: 'ajax/insertTruckAjax.php',
				type: 'post',
				data: {number: number, driver: driver, phone: phone},
				success: function(response){
					if(response.status == 'success'){
						$('#truck').append($('<option/>', { 
							value: response.newid,
							text : response.newnumber 
						}));
						$("#truck").val(response.newid);
						$("#newTruckModal").modal('hide');
						$('body').removeClass('modal-open');
						$('.modal-backdrop').remove();						
					}else if(response.status == 'error'){
						$("#truckInsertError").text(response.value);
					}
					else{
						$("#truckInsertError").text('Unknown error. Please contact admin');
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
					$("#truckInsertError").text(msg);
					return false;
				}						
			});			
		}
		else
		{
			$("#truckInsertError").text('Please enter values for number and driver names');
			return false;
		}
	});
	$("#newTruckModal").on("hidden.bs.modal", function(){
		$("#truckInsertError").text('');
		$("#number").val('');
		$("#driver").val('');
		$("#phone").val('');
	});	
});		