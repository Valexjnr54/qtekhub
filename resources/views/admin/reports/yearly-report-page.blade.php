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
                    {!! Form::open(['action' => 'App\Http\Controllers\Admin\ReportController@getYearlyReport', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                      <div class="row">
                        
                        <div class="col-md-12">
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
                      </div><br>
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