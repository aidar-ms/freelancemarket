@extends('layouts.app')

@section('content')

    <div class="row">
        <h1 class="col-md-10">Your contracts</h1>  
        

        <div class="col-md-2">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createContractModal">
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

                        <form method="post" id="createContractForm" action=" {{ url('/contracts') }}">
                            {{ csrf_field() }}
                            <label for="title" class="col-md-6">Title</label>
                            <input type="text" class="col-md-6" id="title" name="title">

                            <label for="description" class="col-md-6" style="">Description</label>
                            <textarea rows="10" cols="80" form="createContractForm" id="description" name="description"> 
                            </textarea> 

                            <label for="price" class="col-md-6">Price</label>
                            <input class="col-md-6" type="number" id="price" name="price">

                            <label for="deadline" class="col-md-6">Deadline</label>
                            <input class="col-md-6" type="datetime-local" id="deadline" name="deadline">
                        </form>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="createContractForm">Submit</button>
                    </div>
                    
                </div>
            
        </div>
    </div>

    <contract-list>

    </contract-list>
    
@endsection