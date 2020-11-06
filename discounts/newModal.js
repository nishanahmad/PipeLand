// AJAX to populate new modal	
$(function(){
	$('#saveNew').click(function(){
		var date = document.getElementById('date').value;
		var type = document.getElementById('type').value;
		var product = document.getElementById('product').value;
		var client = document.getElementById('client').value;
		var discount = document.getElementById('discount').value;
		var remarks = document.getElementById('remarks').value;
		
		$.ajax({
			url: 'insertAjax.php',
			type: 'post',
			data: {date: date, type: type, product: product, client: client, discount: discount, remarks: remarks},
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
	
	$('#type').on('change',function(){
		if( $(this).val()=="Wagon Discount"){
			$("#client").removeAttr( "required");
			$("#clientLabel").hide();
		}
		else{
			$("#client").attr( "required");
			$("#clientLabel").show();
		}
	});		
});		