@extends('layouts.admin-layout')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
        <div class="card-header">
          <h4>Registered Customers</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th class="text-center">
                    #
                  </th>
                  <th>Customer Name</th>
                  <th>Phone Number</th>
                  <th>E-mail</th>
                  <th>Date Joined</th>
                </tr>
              </thead>
              <tbody>
                @if (count($users) > 0)
                <?php $count =0;?>
                @foreach ($users as $user)
                <?php $count++;?>
                <tr>
                    <td>
                        <?= $count?>
                    </td>
                  <td>{!! $user->first_name.' '.$user->last_name !!}</td>
                  <td>{!! $user->phone_number !!}</td>
                  <td>{!! $user->email !!}</td>
                  <td>{{ $user->created_at }}</td>
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