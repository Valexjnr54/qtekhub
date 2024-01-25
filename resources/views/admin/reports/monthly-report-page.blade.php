@extends('layouts.admin-layout')
@section('content')
<section class="section">
          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Monthly Report</h4>
                  </div>
                  <div class="card-body">
                    {!! Form::open(['action' => 'App\Http\Controllers\Admin\ReportController@getMonthlyReport', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <select name="month" class="form-control selectric" required="">
                                <option value="">Select Month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                            <select name="year" class="form-control selectric" required="">
                                <option value="">Select Year</option>
                                <?php
                                    $year=date('Y', strtotime('now'));
                                    for($i=$year; $i>=2023; $i--){
                                       echo"<option value=$i>$i</option>"; 
                                    }
                                  ?>                           
                            </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <!--<button type="submit" class="btn btn-outline-primary btn-lg-12 form-control" name="action" value="Daily Report" id="Daily Report">View Report</button>-->
                                {{ Form::submit('View Report',['class' => 'btn btn-outline-primary btn-lg-12 form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                      </div>
                    {!! Form::close() !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
@endsection
@section('extra-js')

@endsection