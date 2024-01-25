@extends('layouts.admin-layout')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Categories</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="tableExport">
              <thead>
                <tr>
                  <th class="text-center">
                    #
                  </th>
                  <th>Product/Food Name</th>
                  <th>Quantity</th>
                  <th>Price per One</th>
                  <th>Total Price</th>
                </tr>
              </thead>
              <tbody>
                @if (count($orders) > 0)
                <?php $count =0;?>
                @foreach ($orders as $order)
                <?php $count++;?>
                    <tr>
                        <td>
                            <?= $count?>
                        </td>
                        <td>{!! $order->product_name !!}</td>
                        <td>{!! $order->qty !!}</td>
                        <td>{!! $order->price !!}</td>
                        <td>{!! $order->price * $order->qty !!}</td>
                    </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('extra-js')

@endsection