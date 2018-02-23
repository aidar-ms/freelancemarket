@extends('layouts.app')

@section('content')

    <div class="container">
        
            <h2> <?= $contract->title ?> </h2>
        
            <p class="small"> Contract ID: <span style="vertical-align: top"> <?= $contract->id ?> </span> </p>
            <p class="small"> Due at: <?= $contract->deadline_at ?> </p>
            <p class="small"> Status: @if($contract->freelancer)
                                            <?= 'Taken by ' . $contract->freelancer?>
                                      @else
                                            <?= 'Open' ?>
                                      @endif
            </p>

            <br>

            <p> <?= $contract->description?> </p>
        
        </div>

@endsection
