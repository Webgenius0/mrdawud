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

                        <div class="card-body pt-12">
                            <div class="user-avatar-section">
                                <div class="d-flex align-items-center flex-column">
                                    <!-- Show user image or default avatar if no image is set -->
                                    <img class="img-fluid rounded mb-4"
                                        src="{{ asset($imagePath) }}"
                                        height="120" width="120" alt="User avatar">
                                    <div class="user-info text-center">
                                        <h5>{{ $blockedUser->username ?? 'N/A' }}</h5>
                                        <span class="badge bg-label-secondary">{{ $user->role ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <h5 class="pb-4 border-bottom mb-4">Details</h5>
                            <div class="info-container">
                                <ul class="list-unstyled mb-6">
                                    <li class="mb-2">
                                        <span class="h6">Username:</span>
                                        <span>{{$blockedUser->username ?? 'N/A'}}</span>
                                    </li>
                                    <li class="mb-2">
                                        <span class="h6">Email:</span>
                                        <span>{{$blockedUser->email ?? 'N/A'}}</span>
                                    </li>

                                    <li class="mb-2">
                                        <span class="h6">Phone:</span>
                                        <span>{{$blockedUser->phone ?? 'N/A'}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xl-8 col-lg-7 order-0 order-md-1">
                    <!-- Activity Timeline -->
                    <div class="card mb-6">
                        <h5 class="card-header">Report</h5>
                        <div class="card-body pt-1">
                            <ul class="list-unstyled mb-6">
                                @foreach ($report as $item)
                                <li class="mb-2">
                                    <span class="h6">Detailes:</span>
                                    <span>{{$item->report ?? 'N/A'}}</span>
                                </li>
                                @endforeach
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