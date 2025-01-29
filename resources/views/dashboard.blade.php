<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;

$user = User::count();
$product = Product::count();
$category = Category::count();
$order = Order::count();
?>
@extends('backend.app')


@section('title', 'Admin Dashboard')

@section('content')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Ecommerce Starts -->
            <section id="dashboard-ecommerce">
                <div class="row match-height">




                    <!-- Medal Card -->
                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="card card-congratulation-medal">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <!-- <h5>Congratulations 🎉 {{ auth()->user()->username }}!</h5> -->
                                <h1 class="card-text font-small-3">Total User</h1>
                                <h3 class="mb-75 mt-2 pt-50">
                                    <a href="javascript:void(0);">{{$user}}</a>

                                </h3>
                                <button type="button" class="btn btn-primary align-self-start">View Sales</button>
                                <!-- <img src="{{ asset('backend/app-assets/images/illustration/badge.svg') }}" class="congratulation-medal" alt="Medal Pic" /> -->
                            </div>
                        </div>
                    </div>
                    <!--/ Medal Card -->


                    <!-- Medal Card -->
                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="card card-congratulation-medal">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">

                                <h1 class="card-text font-small-3">Total Product</h1>
                                <h3 class="mb-75 mt-2 pt-50">
                                    <a href="javascript:void(0);">{{$product}}</a>
                                </h3>
                                <button type="button" class="btn btn-primary align-self-start">View Sales</button>

                            </div>
                        </div>
                    </div>
                    <!--/ Medal Card -->
                    <!--/ Medal Card -->
                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="card card-congratulation-medal">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center"> <!-- Center content vertically and horizontally -->

                                <!-- Title and Order Count -->
                                <h1 class="card-text font-small-3">Total Order</h1>
                                <h2 class="mb-75 mt-2 pt-50">
                                    <a href="javascript:void(0);" class="d-block">{{$order}}</a>
                                </h2>

                                <!-- Button aligned to the left -->
                                <button type="button" class="btn btn-primary align-self-start">View Order list</button>

                            </div>
                        </div>
                    </div>



                </div>



            </section>
            <!-- Dashboard Ecommerce ends -->

        </div>
    </div>
</div>
@endsection