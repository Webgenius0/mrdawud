@extends('backend.app')
@section('title','General Setting')
@section('content')
    <div class="app-content content ">
        @if (session()->has('message'))
            <div class="alert alert-success" id="successAlert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger" id="errorAlert">
                {{ session('error') }}
            </div>
        @endif

        show User

    </div>
@endsection
