// AJAX to populate new modal	
$(function(){
	$('#saveNew').click(function(){
		if(document.getElementById('number').value && document.getElementById('driver').value && document.getElementById('phone').value)
		{
			var number = document.getElementById('number').value;
			var driver = document.getElementById('driver').value;
			var phone = document.getElementById('phone').value;

			$.ajax({
				url: 'insertAjax.php',
				type: 'post',
				data: {number: number, driver: driver, phone: phone},
				success: function(response){
					if(response.status == 'success'){
						window.location.href = 'list.php?success';
					}else if(response.status == 'error'){
						$("#insertError").text(response.value);
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
					$("#insertError").text(msg);
					return false;
				}						
			});			
		}
		else
		{
			$("#insertError").text('Please enter values for all fields');
			return false;
		}
	});
	$("#newModal").on("hidden.bs.modal", function(){
		$("#insertError").text('');
		$("#number").val('');
		$("#driver").val('');
		$("#phone").val('');
	});	
});		