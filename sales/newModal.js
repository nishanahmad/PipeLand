$("#saleModal").on("hidden.bs.modal", function(){
	$("#insertError").text('');
	$("#bill").val('');
	$("#ar").val(''); 
	$("#truck").val('');
	$("#engineer").val('');
	$("#order_no").val('');
	$("#product").val('');
	$("#godown").val('');
	$("#qty").val('');
	$("#customer").val('');
	$("#bd").val('');
	$("#phone").val('');
	$("#remarks").val('');
	$("#address1").val('');
	document.getElementById('holding-card').innerHTML = "";
});

$('#saleModal').on('shown.bs.modal', function (e) {
	$("#phone").keyup(function(){
		console.log('Key Up');
		$.ajax({
			type: "POST",
			url: "ajax/readPhone.php",
			data:'keyword='+$(this).val(),
			beforeSend: function(){
				$("#phone").css("background","#FFF no-repeat 165px");
			},
			success: function(data){
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);
				$("#phone").css("background","#FFF");
			}	
		});
	});			
})

$("#newSaleForm").submit(function(){
	var bill = $("#bill").val().toUpperCase();
	var godown = $("#godown").val();
	if(bill.includes('BB') || bill.includes('BC') || bill.includes('GB') || bill.includes('GC') || bill.includes('PB') || bill.includes('PC'))
	{
		if(!godown)
		{
			$("#insertError").text('Please select the godown');
			return false;	
		}
	}
});

function selectPhone(val) {
	$("#phone").val(val);
	$("#suggesstion-box").hide();
	
	var phone = $("#phone").val();
	console.log(phone);		
	$.ajax({
		type: "POST",
		url: "ajax/customerAJAX.php",
		dataType: 'json',
		data:'phone='+phone,
		success: function(response){
			console.log(response);
			if(response.status == 'success'){
				$('#customer').val(response.customer_name);
				$('#address1').val(response.address);
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
			console.log(msg);
			return false;
		}				
	});	
}
