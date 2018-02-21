@extends('layouts.app')

@section('content')
    <h1>Your contracts</h1>    
    @if(count($contracts) > 1) 
        @foreach($contracts as $contract)
            <div class="well">
                <h3><a href="/contracts/{{$contract->title}}"></a></h3>
            </div>
        @endforeach
    @endif
@endsection