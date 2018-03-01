@extends('layouts.app')

@section('content')

    <div class="container">
        
            <h2> <?= $contract->title ?> </h2>
        
            <p class="small"> Contract ID: <span style="vertical-align: top"> <?= $contract->id ?> </span> </p>
            <p class="small"> Due at: <?= $contract->deadline_at ?> </p>
            <p class="small"> Price: <?= $contract->price ?> </p>
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
                  <payment-component :contract-id = "'{{encrypt($contract->id)}}'" :hirer-email = "'{{encrypt($contract->hirer_email)}}'" :freelancer-email = "'{{encrypt($contract->freelancer_email)}}'">
                        {{-- PAYMENT COMPONENT --}}
                  </payment-component>
            @endif
        
        </div>

@endsection