@extends('layouts.app')

@section('content')

    <div class="container">
        
            <h2> <?= $contract->title ?> </h2>
        
            <p class="small"> Contract ID: <span style="vertical-align: top"> <?= $contract->id ?> </span> </p>
            <p class="small"> Due at: <?= $contract->deadline_at ?> </p>
            <p class="small"> Status: @if($contract->status === 'active')
                                            <?= 'Taken by ' . $contract->freelancer?>
                                      @elseif($contract->status === 'closed')
                                            <?= 'Closed' ?>
                                      @else 
                                          <?= 'Open' ?>
                                      @endif
            </p>

            <br>

            <p> <?= $contract->description?> </p>

            <br>

            @if($contract->freelancer && $contract->status === 'active')
                  <form method="post" action="{{url('/make-payment')}}">
                        {{ csrf_field() }}
                        <input name="contract_id" type="hidden" value="<?= encrypt($contract->id) ?>">
                        <input name="hirer_email" type="hidden" value="<?= encrypt($contract->hirer_email) ?>">
                        <input name="freelancer_email" type="hidden" value="<?= encrypt($contract->freelancer_email) ?>">
                        <input class="btn btn-default" type="submit" onsubmit="alert('Payment sent'); window.reload()" value="Pay">
                  </form>
            @endif
        
        </div>

@endsection