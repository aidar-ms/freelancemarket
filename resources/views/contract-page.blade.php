@extends('layouts.app')

@section('content')

<contract-info :contract-id = "'{{app('request')->input('id')}}'" >

</contract-info>
    

@endsection