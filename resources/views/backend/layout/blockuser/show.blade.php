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

        <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <!-- User Sidebar -->
                <div class="col-xl-4 col-lg-5 order-1 order-md-0">
                    <!-- User Card -->
                    <div class="card mb-6">
                        <div class="card mb-6">
                            <div class="card-body pt-12">
                                <div class="user-avatar-section">
                                    <div class=" d-flex align-items-center flex-column">
                                        <img class="img-fluid rounded mb-4" src="{{asset($user->avatar ?? 'backend/assets/img/avatars/1.png')}}" height="120" width="120" alt="User avatar">
                                        <div class="user-info text-center">
                                            <h5>{{$user->name ?? 'N/A'}}</h5>
                                            <span class="badge bg-label-secondary">{{$user->role ?? 'N/A'}}</span>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="pb-4 border-bottom mb-4">Details</h5>
                                <div class="info-container">
                                    <ul class="list-unstyled mb-6">
                                        <li class="mb-2">
                                            <span class="h6">Username:</span>
                                            <span>{{$user->name ?? 'N/A'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6">Email:</span>
                                            <span>{{$user->email ?? 'N/A'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6">Role:</span>
                                            <span>{{$user->role ?? 'N/A'}}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6">Phone:</span>
                                            <span>{{$user->phone ?? 'N/A'}}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-7 order-0 order-md-1">
                    <!-- Activity Timeline -->
                    <div class="card mb-6">
                        <h5 class="card-header">Account Information</h5>
                        <div class="card-body pt-1">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Request Amount:</span>
                                    <span>{{$bank_info->amount ?? 'N/A'}}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Bank Information:</span>
                                    <span>{{$bank_info->bank_info ?? 'N/A'}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Activity Timeline -->
                </div>
            </div>

        </div>
    </div>

    </div>
@endsection
