<div class="card">

    @if ($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
			<p>{{ $error }}</p>
			@endforeach
		</div>
    @endif

    <div class="card-header">
        <h3>{{ __('message.buttons.simulate') }}</h3>
    </div>
    <div class="card-body">
        {!! Form::open(['method' => 'POST', 'route' => ['simulate'], 'id' => 'simulationForm']) !!}
        <div class="form-group">
            {!! Form::label('number_of_prizes', __('message.labels.numberOfPrizes')) !!}
            {!! Form::number('number_of_prizes', 50, ['class' => 'form-control','id' => 'number_of_prizes']) !!}
        </div>
        {!! Form::button(__('message.buttons.simulate'), ['type' => 'submit', 'class' => 'mt-2 btn btn-primary cursor-pointer', 'id' => 'simulationButton']) !!}

        {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
        {!! Form::submit('Reset', ['class' => 'mt-2 btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>
