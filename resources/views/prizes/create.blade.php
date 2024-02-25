@extends('default')

@section('content')
    @include('prob-notice')

    @if ($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
			<p>{{ $error }}</p>
			@endforeach
		</div>
    @endif
	
	{!! Form::open(['route' => 'prizes.store']) !!}
    <div class="mb-3">
        {{ Form::label('title', __('message.labels.title'), ['class' => 'form-label']) }}
        {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Enter title']) }}
    </div>
    <div class="mb-3">
        {{ Form::label('probability',__('message.labels.probability'), ['class' => 'form-label']) }}
        {{ Form::number('probability', null, ['class' => 'form-control', 'min' => '0', 'max' => '100', 'placeholder' => '0 - 100', 'step' => '0.01']) }}
    </div>

    {{ Form::submit(__('message.buttons.save'), ['class' => 'btn btn-primary']) }}
    <a class="btn btn-dark float-end" href={{ url('prizes') }}>
        <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
        {{ __('message.buttons.back') }}
    </a>

    {{ Form::close() }}
@endsection
