@extends('layouts.admin-layout')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Products</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
              <thead>
                <tr>
                  <th class="text-center">
                    #
                  </th>
                  <th>Product Name</th>
                  <th>Product Image</th>
                  <th>Price</th>
                  <th>Sales Price</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @if (count($products) > 0)
                <?php $count =0;?>
                @foreach ($products as $product)
                    @if ($product)
                <?php $count++;?>
                <tr>
                    <td>
                        <?= $count?>
                    </td>
                    <td>{!! $product->product_name !!}</td>
                    <td>
                        <img alt="image" src="{{ $product->product_image }}" width="75">
                    </td>
                    <td>{!! $product->price !!}</td>
                    <td>{!! $product->sales_price !!}</td>
                  <td>
                    <div class="badge badge-success badge-shadow">{!! $product->status !!}</div>
                  </td>
                </tr>
                    @endif
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