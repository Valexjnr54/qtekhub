@extends('layouts.admin-layout')
@section('content')
<section class="section">
          <div class="section-body">
            <div class="row">
              <div class="col-12 col-md-4 col-lg-4">
                <div class="pricing">
                  <div class="pricing-title">
                    Daily
                  </div>
                  <div class="pricing-padding">
                    <div class="pricing-price">
                      <h4>Daily Report</h4>
                    </div>
                    <div class="pricing-details">
                      <div class="pricing-item">
                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                        <div class="pricing-item-label">Generate Daily Report</div>
                      </div>
                    </div>
                  </div>
                  <div class="pricing-cta">
                    <a href="{{ route('admin.daily-report') }}">Proceed <i class="fas fa-arrow-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4 col-lg-4">
                <div class="pricing pricing-highlight">
                  <div class="pricing-title">
                    Monthly Report
                  </div>
                  <div class="pricing-padding">
                    <div class="pricing-price">
                      <h4>Monthly Report</h4>
                    </div>
                    <div class="pricing-details">
                      <div class="pricing-item">
                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                        <div class="pricing-item-label">Generate Monthly Report</div>
                      </div>
                    </div>
                  </div>
                  <div class="pricing-cta">
                    <a href="{{ route('admin.monthly-report') }}">Proceed <i class="fas fa-arrow-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4 col-lg-4">
                <div class="pricing">
                  <div class="pricing-title">
                    Annual
                  </div>
                  <div class="pricing-padding">
                    <div class="pricing-price">
                      <h4>Annual Report</h4>
                    </div>
                    <div class="pricing-details">
                      <div class="pricing-item">
                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                        <div class="pricing-item-label">Generate Annual Report</div>
                      </div>
                    </div>
                  </div>
                  <div class="pricing-cta">
                    <a href="{{ route('admin.yearly-report') }}">Proceed <i class="fas fa-arrow-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
@endsection
@section('extra-js')

@endsection