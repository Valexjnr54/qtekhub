<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportPage()
    {
        return view('admin.reports.report-page');
    }
    
    public function dailyReportPage()
    {
        return view('admin.reports.daily-report-page');
    }
    
    public function monthlyReportPage()
    {
        return view('admin.reports.monthly-report-page');
    }
    
    public function yearlyReportPage()
    {
        return view('admin.reports.yearly-report-page');
    } 
    
    public function getDailyReport(Request $request)
    {
        $this->validate($request,[
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);
        
        $orders = Order::where(['day' => $request->day,'month' => $request->month,'year' => $request->year])->get();
        return view('admin.reports.report-order')->with(['orders' => $orders]);
    }
    
    public function getMonthlyReport(Request $request)
    {
        $this->validate($request,[
            'month' => 'required',
            'year' => 'required',
        ]);
        
        $orders = Order::where(['month' => $request->month,'year' => $request->year])->get();
        return view('admin.reports.report-order')->with(['orders' => $orders]);
    }
    
    public function getYearlyReport(Request $request)
    {
        $this->validate($request,[
            'year' => 'required',
        ]);
        
        $orders = Order::where(['year' => $request->year])->get();
        return view('admin.reports.report-order')->with(['orders' => $orders]);
    }
    
    
    
    
    
}
