@extends('main')

@section('title', '| Login')

@section('content')
{{-- need to have csrf login protection will fail if don't do this!--}}
	{!!csrf_field()!!}
	<div class ="row">
		<div class = "col-md-6 col-md-offset-3">
			{!! Form::open() !!}
				{{Form::label('email','Email:')}}
				{{Form::email('email',null,['class'=>'form-control'])}}

				{{Form::label('password','Password:')}}
				{{Form::password('password',['class'=> 'form-control'])}}
				<br>
				{{Form::checkbox('remember')}}{{Form::label('remember',' Remember Me')}}
				<br>
				{{Form::submit('Login',['class'=> 'btn btn-primary btn-block'])}}

				<a href="{{route('password.request')}}">Forgot My Password</a>

			{!! Form::close() !!}
		</div>
	</div>

@endsection