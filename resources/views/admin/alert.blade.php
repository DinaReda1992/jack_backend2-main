@if(Session::has('success'))
<div class="alert alert-success" role="alert" style="text-align: center;">
    {{ Session::get('success') }} 
</div>
@endif
@if(Session::has('error'))
<div class="alert alert-danger" role="alert" style="text-align: center;">
	 {{ Session::get('error') }} 
</div>
@endif

@if($errors->has('error'))
<div class="alert alert-danger" role="alert" style="text-align: center;" >
	<p>
		Some errors happend :
	</p>

	<ul style="text-decoration: none;list-style: none;">
		@foreach($errors->all() as $error)

		<li>
			{!! $error !!}
		</li>

		@endforeach
	</ul>
</div>
<!-- end form errors -->
@endif