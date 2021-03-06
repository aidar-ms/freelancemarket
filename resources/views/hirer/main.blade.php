@extends('layouts.app')

@section('content')

    <div class="row">
        <h1 class="col-md-10" style="padding-left: 30px">Your requests</h1>  

       
        
    </div>
    <request-list>
        {{-- REQUEST LIST --}}
    </request-list>

    <div class="row">
        <div class="separator">

        </div>
    </div>

    
{{----------------------------------------------------------------------------------------------------------------}}


    <div class="row">
        <h1 class="col-md-10" style="padding-left: 30px">Your contracts</h1>  
        
        <div class="col-md-2">
            <!-- Button trigger modal -->
            <button dusk="create-contract" type="button" class="btn btn-primary" data-toggle="modal" data-target="#createContractModal">
                Create Contract
            </button>
        </div>
    </div>
      
      <!-- Modal -->
    <div class="modal fade" id="createContractModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create new contract</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <post-contract>

                            {{-- POST CONTRACT FORM --}}

                        </post-contract>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="createContractForm" name="submit" id="submit">Submit</button>
                    </div>
                    
                </div>
            
        </div>
    </div>

    <contract-list>

        {{-- CONTRACT LIST --}}

    </contract-list>
    
@endsection