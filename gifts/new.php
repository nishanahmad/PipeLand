<html>
	<head>
		<title><?php echo $ar['name'];?></title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dashio.css" rel="stylesheet">
		<link href="../css/dashio-responsive.css" rel="stylesheet">	
		<link href="../css/font-awesome.min.css" rel="stylesheet">		
		<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	</head>
	<script>
	$(document).ready(function() {
			var pickerOpts = { dateFormat:"dd-mm-yy"}; 					
			$( "#datepicker" ).datepicker(pickerOpts);
		});	
	</script>   
	<section class="wrapper">
		<h2><i class="fa fa-inr" style="margin-right:.5em;margin-left:.5em;"></i>New Invoice</h3>
		<div class="row mt">
			<div class="col-lg-8">
				<div class="form-panel">
					<h4 class="mb"><i class="fa fa-angle-right" style="margin-right:.5em;"></i>Enter details</h4>
					<form class="form-horizontal style-form"  action="/invoices" method="post">
						<input type="hidden" name="_token" value="{!! csrf_token() !!}">
						<input type="hidden" name="order" value="{{$order->id}}">
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Date</label>
							<div class="col-sm-6">
								@if(old('date') != null)
									<input type="text" name="date" value="{{old('date')}}" class="form-control"  id="datepicker" tabindex="1" required>
								@else
									<input type="text" name="date" value="{{date('d-m-Y',strtotime($today))}}" class="form-control" id="datepicker" tabindex="1" required>
								@endif
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Client</label>
							<div class="col-sm-6">
								<input type="text" readonly name="client" value="{{$order->client->name}}" class="form-control">
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Item</label>
							<div class="col-sm-6">
								<input type="text" readonly name="item" value="{{$order->item->name}}" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Quantity</label>
							<div class="col-sm-6">
								<input type="text" name="qty" class="form-control" tabindex="2" required value="{{old('qty')}}">
							</div>
						</div>					
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Invoice No.</label>
							<div class="col-sm-6">
								<input type="text" name="number" class="form-control" tabindex="3" required value="{{old('number')}}">
							</div>
						</div>										
						<button type="submit" class="btn btn-primary" style="margin-left:200px;" tabindex="4">Create Invoice</button> 
						<a href="/orders/{{$order->id}}" class="btn btn-default" style="margin-left:10px;" tabindex="5">Cancel</a>
						<br/><br/>
					</form>
				</div>
			</div>
		</div>
	</section>
</html>	