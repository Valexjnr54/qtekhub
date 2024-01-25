@extends('layouts.admin-layout')
@section('content')

    <div class="row ">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                        <h5 class="font-15">New Booking</h5>
                        <h2 class="mb-3 font-18">258</h2>
                        <p class="mb-0"><span class="col-green">10%</span> Increase</p>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                        <img src="{{ asset('assets/img/banner/1.png') }}" alt="">
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                        <h5 class="font-15"> Customers</h5>
                        <h2 class="mb-3 font-18">1,287</h2>
                        <p class="mb-0"><span class="col-orange">09%</span> Decrease</p>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                        <img src="{{ asset('assets/img/banner/2.png') }}" alt="">
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                        <h5 class="font-15">New Project</h5>
                        <h2 class="mb-3 font-18">128</h2>
                        <p class="mb-0"><span class="col-green">18%</span>
                        Increase</p>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                        <img src="{{ asset('assets/img/banner/3.png') }}" alt="">
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                        <h5 class="font-15">Revenue</h5>
                        <h2 class="mb-3 font-18">$48,697</h2>
                        <p class="mb-0"><span class="col-green">42%</span> Increase</p>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                        <img src="{{ asset('assets/img/banner/4.png') }}" alt="">
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
          
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4>Product Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-3">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($productOrders) > 0)
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach($productOrders as $productOrder)
                                        @php
                                            $count++;
                                        @endphp
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>{{ $productOrder->product_name }}</td>
                                            <td>{{ $productOrder->price }}</td>
                                            <td>{{ $productOrder->qty }}</td>
                                            <td>{{ $productOrder->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        
@endsection
