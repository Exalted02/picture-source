<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class CountryStateController extends Controller
{
    public function country_list_bkp(Request $request)
    {
		$country_data = Countries::all();
		$data = [];
		foreach($country_data as $country)
		{
			$data[] = [
				'id' => $country->id,
				'name' => $country->name,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
    }
    public function country_list(Request $request)
    {
		$country_data = Countries::query()
			->orderByRaw("id = 231 DESC, name ASC")
			->get();

		$data = $country_data->map(function ($country) {
			return [
				'id' => $country->id,
				'name' => $country->name,
			];
		});

		return response()->json([
			'status' => 200,
			'data' => $data,
		]);
    }
	
	public function state_list(Request $request)
	{
		$state_data = States::where('country_id', $request->country_id)->get();
		$data = [];
		foreach($state_data as $state)
		{
			$data[] = [
				'id' => $state->id,
				'name' => $state->name,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
	}
	public function city_list(Request $request)
	{
		$city_data = Cities::where('state_id', $request->state_id)->get();
		$data = [];
		foreach($city_data as $city)
		{
			$data[] = [
				'id' => $city->id,
				'name' => $city->name,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
	}
	
}
