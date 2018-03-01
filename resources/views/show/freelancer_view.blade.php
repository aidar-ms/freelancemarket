@extends('layouts.app')

@section('content')

    <div class="container">
        
        <h2> <?= $contract->title ?> </h2>
    
        <p class="small"> Contract ID: <span style="vertical-align: top"> <?= $contract->id ?> </span> </p>
        <p class="small"> Due at: <?= $contract->deadline_at ?> </p>
        <p class="small"> Price: <?= $contract->price ?> </p>
        <p class="small"> Status: @if(!is_null($contract->freelancer))
                                        <?= 'taken by ' . $contract->freelancer?>
                                    @else
                                        <?= 'Open' ?>
                                    @endif
        </p>

        <br>

        <p> <?= $contract->description?> </p>

        <br>

        @if(is_null($request))
            <a class="btn btn-default" id="request" href="{{ route('request', ['id' => $contract->id])}}" onclick="location.reload();">Request contract</a>
        @elseif($request->status === 'sent')
            <em> You've already requested this contract </em>
        @endif
        
        
    </div>
@endsection