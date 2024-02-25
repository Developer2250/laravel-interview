@extends('default')

@section('content')

@include('prob-notice')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($prize, array('route' => array('prizes.update', $prize->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('title',__('message.labels.title'), ['class'=>'form-label']) }}
			{{ Form::text('title', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('probability',__('message.labels.probability'), ['class'=>'form-label']) }}
			{{ Form::number('probability', null, array('class' => 'form-control','min' => '0','max' => '100', 'placeholder' => '0 - 100','step' => '0.01')) }}
		</div>

		{{ Form::submit(__('message.buttons.update'), ['class' => 'btn btn-primary']) }}
		<a class="btn btn-dark float-end" href={{ url('prizes') }}>
			<i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
			{{ __('message.buttons.back') }}
		</a>

	{{ Form::close() }}
@stop
