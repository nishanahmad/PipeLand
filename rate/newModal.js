// AJAX to populate new modal	
$(function(){
	$('#saveNew').click(function(){
		var date = document.getElementById('date').value;
		var product = document.getElementById('product').value;
		var rate = document.getElementById('rate').value;
		
		$.ajax({
			url: 'insertAjax.php',
			type: 'post',
			data: {date: date, product: product, rate: rate},
			success: function(response){
				if(response.status == 'success'){
					window.location.href = 'list.php?success';
				}else if(response.status == 'error'){
					$("#insertError").text(response.value);
				}
			}
		});
	});
	$("#newModal").on("hidden.bs.modal", function(){
		$("#insertError").text('');
		$("#date").val('');
		$("#product").val('');
		$("#rate").val('');
	});	
});		