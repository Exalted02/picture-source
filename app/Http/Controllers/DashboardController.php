<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use App\Models\Wistlists;
use App\Models\Products;

class DashboardController extends Controller
{
    public function index()
    {
		$data = [];
		$data['tot_costomers'] = User::where('user_type',1)->where('status','!=',2)->count();
		$data['tot_retailers'] = User::where('user_type',2)->where('status','!=',2)->count();
		$data['tot_orders'] = Orders::where('status','!=',3)->count();
		$data['tot_wishlist'] = Wistlists::where('status','!=',2)->count();
		$data['tot_products'] = Products::where('status','!=',2)->count();
		
        return view('dashboard', $data);
    }
}
