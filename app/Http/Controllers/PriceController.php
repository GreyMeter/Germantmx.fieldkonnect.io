<?php

namespace App\Http\Controllers;

use App\DataTables\PriceDataTable;
use App\Models\AdditionalPrice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Customers;
use App\Models\Price;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class PriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->price = new Price();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PriceDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('prices_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('price.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('price_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->price = Price::first()?? new Price();
        // $brand_id = 1;
        // $grade_id = 2;
        // $size_id = 2;
        // $base_price = $this->price->base_price;
        // $brand_price = AdditionalPrice::where(['model_name'=>'brand','model_id'=>$brand_id])->pluck('price_adjustment')->first();
        // $grade_price = AdditionalPrice::where(['model_name'=>'grade','model_id'=>$grade_id])->pluck('price_adjustment')->first();
        // $size_price = AdditionalPrice::where(['model_name'=>'size','model_id'=>$size_id])->pluck('price_adjustment')->first();
        // $final_price = $base_price+$brand_price+$grade_price+$size_price;
        // dd($base_price,$brand_price,$grade_price,$size_price,$final_price);
        $sizes = Category::where('active', '=', 'Y')->select('id', 'category_name')->get();
        $brands = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();
        $grades = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name')->get();
        $zones = City::where('active', '=', 'Y')->select('id', 'city_name')->get();
        $distributors = Customers::where('active', '=', 'Y')->where('customertype', '1')->select('id', 'name')->get();
        return view('price.create', compact('sizes', 'brands', 'grades', 'zones', 'distributors'))->with('price', $this->price);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'brand_id' => 'required|array',
            'grade_id' => 'required|array',
            // 'zone_id' => 'required|integer',
            'size_id' => 'required|integer',
            'base_price' => 'required|numeric|min:0',
            'size.id' => 'sometimes|array',
            'size.price' => 'sometimes|array',
            'grade.id' => 'sometimes|array',
            'grade.price' => 'sometimes|array',
            'brand.id' => 'sometimes|array',
            'brand.price' => 'sometimes|array',
            'distributor.id' => 'sometimes|array',
            'distributor.price' => 'sometimes|array',
        ]);

        // Create the base price entry
        $price = Price::create([
            'brand_id' => implode(',',$request->input('brand_id')),
            'grade_id' => implode(',',$request->input('grade_id')),
            'zone_id' => $request->input('zone_id'),
            'size_id' => $request->input('size_id'),
            'base_price' => $request->input('base_price'),
        ]);

        // Store the additional price adjustments (if any)
        if (!empty($validatedData['size']['id'])) {
            foreach ($validatedData['size']['id'] as $index => $sizeId) {
                AdditionalPrice::create([
                    'price_id' => $price->id,
                    'model_name' => 'size', // Specify that this is a size adjustment
                    'model_id' => $sizeId,
                    'price_adjustment' => $validatedData['size']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for grades
        if (!empty($validatedData['grade']['id'])) {
            foreach ($validatedData['grade']['id'] as $index => $gradeId) {
                AdditionalPrice::create([
                    'price_id' => $price->id,
                    'model_name' => 'grade', // Specify that this is a grade adjustment
                    'model_id' => $gradeId,
                    'price_adjustment' => $validatedData['grade']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for brands
        if (!empty($validatedData['brand']['id'])) {
            foreach ($validatedData['brand']['id'] as $index => $brandId) {
                AdditionalPrice::create([
                    'price_id' => $price->id,
                    'model_name' => 'brand', // Specify that this is a brand adjustment
                    'model_id' => $brandId,
                    'price_adjustment' => $validatedData['brand']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for distributors
        if (!empty($validatedData['distributor']['id'])) {
            foreach ($validatedData['distributor']['id'] as $index => $distributorId) {
                AdditionalPrice::create([
                    'price_id' => $price->id,
                    'model_name' => 'distributor', // Specify that this is a distributor adjustment
                    'model_id' => $distributorId,
                    'price_adjustment' => $validatedData['distributor']['price'][$index] ?? 0,
                ]);
            }
        }

        return redirect()->route('prices.create')->with('success', 'Price created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function edit(Price $price)
    {
        abort_if(Gate::denies('price_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->price = Price::with('additionalPrices', 'size', 'brands', 'grade', 'zone')->find($price->id);
        $sizes = Category::where('active', '=', 'Y')->select('id', 'category_name')->get();
        $brands = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();
        $grades = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name')->get();
        $zones = City::where('active', '=', 'Y')->select('id', 'city_name')->get();
        return view('price.create',compact('sizes','brands','grades', 'zones') )->with('price', $this->price);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Price $price)
    {
        $validatedData = $request->validate([
            'brand_id' => 'required|array',
            'grade_id' => 'required|array',
            // 'zone_id' => 'required|integer',
            'size_id' => 'required|integer',
            'base_price' => 'required|numeric|min:0',
            'size.id' => 'sometimes|array',
            'size.price' => 'sometimes|array',
            'grade.id' => 'sometimes|array',
            'grade.price' => 'sometimes|array',
            'brand.id' => 'sometimes|array',
            'brand.price' => 'sometimes|array',
            'distributor.id' => 'sometimes|array',
            'distributor.price' => 'sometimes|array',
        ]);

        $price->brand_id = implode(',',$request->brand_id);
        $price->grade_id = implode(',',$request->grade_id);
        $price->zone_id = $request->zone_id;
        $price->size_id = $request->size_id;
        $price->base_price = $request->base_price;
        $price->save();

        if (!empty($validatedData['size']['id'])) {
            foreach ($validatedData['size']['id'] as $index => $sizeId) {
                AdditionalPrice::updateOrCreate(['model_id' => $sizeId,'model_name' => 'size'],[
                    'price_id' => $price->id,
                    'model_name' => 'size', // Specify that this is a size adjustment
                    'model_id' => $sizeId,
                    'price_adjustment' => $validatedData['size']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for grades
        if (!empty($validatedData['grade']['id'])) {
            foreach ($validatedData['grade']['id'] as $index => $gradeId) {
                AdditionalPrice::updateOrCreate(['model_id' => $gradeId,'model_name' => 'grade'],[
                    'price_id' => $price->id,
                    'model_name' => 'grade', // Specify that this is a grade adjustment
                    'model_id' => $gradeId,
                    'price_adjustment' => $validatedData['grade']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for brands
        if (!empty($validatedData['brand']['id'])) {
            foreach ($validatedData['brand']['id'] as $index => $brandId) {
                AdditionalPrice::updateOrCreate(['model_id' => $brandId,'model_name' => 'brand'],[
                    'price_id' => $price->id,
                    'model_name' => 'brand', // Specify that this is a brand adjustment
                    'model_id' => $brandId,
                    'price_adjustment' => $validatedData['brand']['price'][$index] ?? 0,
                ]);
            }
        }

        // Store additional prices for distributors
        if (!empty($validatedData['distributor']['id'])) {
            foreach ($validatedData['distributor']['id'] as $index => $distributorId) {
                AdditionalPrice::updateOrCreate(['model_id' => $distributorId,'model_name' => 'distributor'],[
                    'price_id' => $price->id,
                    'model_name' => 'distributor', // Specify that this is a distributor adjustment
                    'model_id' => $distributorId,
                    'price_adjustment' => $validatedData['distributor']['price'][$index] ?? 0,
                ]);
            }
        }

        return redirect()->route('prices.create')->with('success', 'Price updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function destroy(Price $price)
    {
        //
    }
}
