<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AvailableStockController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.available_stocks.index');
    }
}
