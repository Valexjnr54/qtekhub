@extends('layouts.admin-layout')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Create New Product</h4>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => ['App\Http\Controllers\Admin\ProductsController@fetchProductByCategory'], 'method' => 'GET', 'enctype' => 'multipart/form-data']) !!}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="category">Category</label>
                    <select class="form-control selectric" name="category_id">
                        <option value="">Choose Product Category</option>
                        @if (count($categories) > 0)
                        @foreach ($categories as $category)
                            <option value="{!! $category->id !!}">{!! $category->category_name !!}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
              </div>
    
              {{ Form::submit('Update Product',['class' => 'btn btn-primary']) }}
              {!! Form::hidden('_method','GET') !!}
            {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
@endsection