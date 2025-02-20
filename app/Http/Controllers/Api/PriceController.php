<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceHistory;
use Illuminate\Http\Request;


class PriceController extends Controller
{

    public function getPrice(Request $request)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $date = $request->date;
        $data = PriceHistory::where('date', '<', $date)->latest()->first()->price;
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
