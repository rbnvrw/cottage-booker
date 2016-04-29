@extends('layouts.app')

@section('content')

{{ Form::open(['url' => 'booking']) }}

<div class="form-group">
    {{ Form::label('start', 'Begindatum') }}
    {{ Form::text('start', Input::old('start'), ['class' => 'form-control']) }}
</div>

<div class="form-group">
    {{ Form::label('end', 'Einddatum') }}
    {{ Form::text('end', Input::old('end'), ['class' => 'form-control']) }}
</div>

<div class="form-group">
    {{ Form::label('costs', 'Kosten') }}
    {{ Input::old('costs') }}
</div>

<div class="form-group">
    {{ Form::label('persons', 'Personen') }}
    {{ Form::select('persons', [1, 2, 3], Input::old('persons'), ['class' => 'form-control']) }}
</div>

<div class="form-group">
    {{ Form::label('description', 'Opmerkingen') }}
    {{ Form::textarea('description', Input::old('description'), ['class' => 'form-control']) }}
</div>

{{ Form::submit('Boeking opslaan', ['class' => 'btn btn-primary']) }}

{{ Form::close() }}

@endsection
