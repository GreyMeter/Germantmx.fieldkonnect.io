<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Plant;
use App\Models\UnitMeasure;
use App\Models\ProductDetails;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
// use Validator;
use Gate;
use App\DataTables\ProductDataTable;
use App\Exports\BranchStockExport;
use App\Exports\BranchStockTemplate;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use App\Exports\ProductTemplate;
use App\Imports\BranchStockImport;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Price;
use Excel;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->products = new Product();
        $this->path = 'products';
    }
    
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('products.index');
    }

    public function productList(ProductDataTable $dataTable)
    {
        return $dataTable->render('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::where('active','=','Y')->select('id', 'category_name')->get();
        $subcategories = Subcategory::where('active','=','Y')->select('id', 'subcategory_name')->get();
        $brands = Brand::where('active','=','Y')->select('id', 'brand_name')->get();
        $units = UnitMeasure::where('active','=','Y')->select('id', 'unit_name')->get();
        return view('products.create',compact('categories','subcategories','brands','units') )->with('products',$this->products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        { 
            abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'product_code' => 'unique:products',
            ]);
        
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $request['product_image'] = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'product'.autoIncrementId('Product','id');
                unset($request['image']);
                $request['product_image'] = fileupload($image, $this->path, $filename);
            }
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            if($product_id = Product::insertGetId([
                'active'        => 'Y', 
                'product_code'  => !empty($request['product_code']) ? $request['product_code'] :'',                
                'description'   => !empty($request['standard_weight']) ? $request['standard_weight'] :'',
                'category_id'   => !empty($request['category_id']) ? $request['category_id'] :null,
                'brand_id'      => !empty($request['brand_id']) ? $request['brand_id'] :null,
                'unit_id'       => !empty($request['unit_id']) ? $request['unit_id'] :null,
                'created_by'    => Auth::user()->id,
                'created_at'    => getcurentDateTime(),
                'part_no'       => !empty($request['weight_per_bundle']) ? $request['weight_per_bundle'] :'',
                'product_no'    => !empty($request['pcs_per_forty']) ? $request['pcs_per_forty'] :'',
                'gst'       => !empty($request['gst']) ? $request['gst'] :'18',
            ]))
            {
                if(!empty($request['detail']))
                {
                    $details = collect([]);
                    foreach ($request['detail'] as $key => $rows) {
                        if(!empty($rows['mrp'])){
                            $price = $rows['mrp'];
                            if(!empty($request['gst']) && $request['gst'] > 0){
                                $price = ($rows['mrp']+(($rows['mrp']*$request['gst'])/100));
                            }
                            if(!empty($request['discount']) && $request['discount'] > 0){
                                $price = ($price-(($rows['mrp']*$request['discount'])/100));
                            }
                        }
                        $details->push([
                            'active'        => 'Y',
                            'product_id'    => $product_id,
                            'detail_title'  => !empty($rows['detail_title']) ? $rows['detail_title'] :'',
                            'detail_description' => !empty($rows['detail_description']) ? $rows['detail_description'] :'',
                            'detail_image'  => !empty($rows['detail_image']) ? $rows['detail_image'] :'',
                            'mrp'       => !empty($rows['mrp']) ? $rows['mrp'] :0.00,
                            'price'     => !empty($rows['mrp']) ? $rows['mrp'] :$rows['mrp'],
                            //'price'     => $price,
                            'selling_price' => !empty($rows['selling_price']) ? $rows['selling_price'] :$rows['mrp'],
                            'discount' => !empty($request['discount']) ? $request['discount'] :0.00,
                            'max_discount' => !empty($request['max_discount']) ? $request['max_discount'] :0.00,
                            
                            'hsn_code'      => !empty($rows['hsn_code']) ? $rows['hsn_code'] :null,
                            'ean_code'      => !empty($rows['ean_code']) ? $rows['ean_code'] :null,
                            'isprimary'      => !empty($rows['isprimary']) ? $rows['isprimary'] :0,
                            'created_at'    => getcurentDateTime(),
                            'updated_at'    => getcurentDateTime(),
                        ]);
                    }

                    if(!empty($details))
                    {
                        ProductDetails::insert($details->toArray());
                    }
                }
              return Redirect::to('products')->with('message_success', 'Product Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Product Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $products = Product::find($id);
        return view('products.show')->with('products',$products);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $products = Product::find($id);
        $categories = Category::where('active','=','Y')->select('id', 'category_name')->get();
        $subcategories = Subcategory::where('active','=','Y')->select('id', 'subcategory_name')->get();
        $brands = Brand::where('active','=','Y')->select('id', 'brand_name')->get();
        $units = UnitMeasure::where('active','=','Y')->select('id', 'unit_name')->get();
        return view('products.create',compact('categories','subcategories','brands','units') )->with('products',$products);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        try
        { 
            abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'product_code' => 'unique:products,product_code,'.decrypt($id),
            ]);
        
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $id = decrypt($id);
            $product = Product::find($id);
            $product->product_code = !empty($request['product_code'])? $request['product_code'] :'';
            $product->description = !empty($request['standard_weight']) ? $request['standard_weight'] :'';
            $product->category_id = !empty($request['category_id']) ? $request['category_id'] :null;
            $product->brand_id = !empty($request['brand_id']) ? $request['brand_id'] :null;
            $product->unit_id = !empty($request['unit_id']) ? $request['unit_id'] :null;
            $product->part_no = !empty($request['weight_per_bundle']) ? $request['weight_per_bundle'] :'';
            $product->product_no = !empty($request['pcs_per_forty']) ? $request['pcs_per_forty'] :'';
            $product->gst = !empty($request['gst']) ? $request['gst'] :'';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'category'.$id;
                unset($request['image']);
                $product->product_image = fileupload($image, $this->path, $filename);
            }
            $product->updated_by = Auth::user()->id;
            if($product->save())
            {
                if(!empty($request['detail']))
                {
                    $details = collect([]);
                    // $detailsids = $request['detail']->get('detail_id');
                    // dd($request);
                    // ProductDetails::whereNotIn('id',$detailsids)->delete();
                    foreach ($request['detail'] as $key => $rows) {
                        $price = 0;
                        if(!empty($rows['mrp'])){
                            $price = $rows['mrp'];
                            if(!empty($request['gst']) && $request['gst'] > 0){
                                $price = ($rows['mrp']+(($rows['mrp']*$request['gst'])/100));
                            }
                            if(!empty($request['discount']) && $request['discount'] > 0){
                                $price = ($price-(($rows['mrp']*$request['discount'])/100));
                            }
                        }
                        if(empty($rows['detail_id']))
                        {
                            $details->push([
                                'active'        => 'Y',
                                'product_id'    => $id,
                                'detail_title'  => !empty($rows['detail_title']) ? $rows['detail_title'] :'',
                                'detail_description' => !empty($rows['detail_description']) ? $rows['detail_description'] :'',
                                'detail_image'  => !empty($rows['detail_image']) ? $rows['detail_image'] :'',
                                'mrp'       => !empty($rows['mrp']) ? $rows['mrp'] :0.00,
                                'price'     => !empty($rows['price']) ? $rows['price'] :0.00,
                                //'price'     => $price,
                                'selling_price' => !empty($rows['selling_price']) ? $rows['selling_price'] :0.00,
                                'discount' => !empty($request['discount']) ? $request['discount'] :0.00,
                                'max_discount' => !empty($request['max_discount']) ? $request['max_discount'] :0.00,
                                'gst'       => !empty($request['gst']) ? $request['gst'] :0,
                                'hsn_code'      => !empty($rows['hsn_code']) ? $rows['hsn_code'] :null,
                                'ean_code'      => !empty($rows['ean_code']) ? $rows['ean_code'] :null,
                                'created_at'    => getcurentDateTime(),
                                'updated_at'    => getcurentDateTime(),
                            ]);
                        }
                        else
                        {
                            ProductDetails::where('id',$rows['detail_id'])->update([
                                'detail_title'  => isset($rows['detail_title']) ? $rows['detail_title'] :'',
                                'detail_description' => isset($rows['detail_description']) ? $rows['detail_description'] :'',
                                'detail_image'  => isset($rows['detail_image']) ? $rows['detail_image'] :'',
                                'mrp'       => isset($rows['mrp']) ? $rows['mrp'] :0.00,
                                // 'price'     => isset($rows['price']) ? $rows['price'] :0.00,
                                'price'     => $price,
                                'selling_price' => isset($rows['selling_price']) ? $rows['selling_price'] :0.00,
                                'discount' => isset($request['discount']) ? $request['discount'] :0.00,
                                'max_discount' => isset($request['max_discount']) ? $request['max_discount'] :0.00,
                                'gst'       => isset($request['gst']) ? $request['gst'] :0,
                                'hsn_code'      => isset($rows['hsn_code']) ? $rows['hsn_code'] :null,
                                'ean_code'      => isset($rows['ean_code']) ? $rows['ean_code'] :null,
                                'updated_at'    => getcurentDateTime(),
                            ]);
                        }
                    }

                    if(!empty($details))
                    {
                        ProductDetails::insert($details->toArray());
                    }
                }
                
              return Redirect::to('products')->with('message_success', 'Product Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Category Update')->withInput();
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

    }

    public function destroy($id)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        ProductDetails::where('product_id',$id)->delete();
        $product = Product::find($id);
        if($product->delete())
        {
            return response()->json(['status' => 'success','message' => 'Product deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Product::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Product '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
    public function upload(Request $request) 
    {
        abort_if(Gate::denies('product_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ProductImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('product_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ProductExport, 'products.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('product_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ProductTemplate, 'products_template.xlsx');
    }

    // public function stockInfo(Request $request)
    // {
    //     $products = ProductDetails::select('id','product_id','stock_qty','detail_title','mrp')->get();
    //     return view('products.stock',compact('products') );
    // }

    // public function stockUpdate(Request $request)
    // {
    //     foreach ($request['detail'] as $key => $rows) {
    //         ProductDetails::where('id',$rows['detail_id'])->update([
    //             'stock_qty'      => isset($rows['stock_qty']) ? $rows['stock_qty'] :null,
    //             'updated_at'    => getcurentDateTime(),
    //         ]);
    //     }
    //     return Redirect::to('stockinfo')->with('message_success', 'Product Update Successfully');
    // }

    public function production(Request $request)
    {
        $products = ProductDetails::select('id','product_id','production_qty','detail_title','mrp')->get();
        return view('products.production',compact('products') );
    }

    public function productionUpdate(Request $request)
    {
        foreach ($request['detail'] as $key => $rows) {
            ProductDetails::where('id',$rows['detail_id'])->update([
                'production_qty'      => isset($rows['production_qty']) ? $rows['production_qty'] :null,
                'updated_at'    => getcurentDateTime(),
            ]);
        }
        return Redirect::to('production')->with('message_success', 'Product Update Successfully');
    }

    public function checkProductCode(Request $request){
        $check_code = Product::where('product_code', $request->product_code)->first();
        if($check_code){
            return false;
        }else{
            return true;
        }
    }

    public function stock(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $brands = Brand::where('active', 'Y')->latest()->get();
        $sizes = Category::where('active', 'Y')->latest()->get();
        $grades = UnitMeasure::where('active', 'Y')->latest()->get();
        $plants = Plant::where('active', 'Y')->latest()->get();

        if ($request->ajax()) {
            $data = BranchStock::with('plant','brands','sizes','grades');
            if($request->plant_id && !empty($request->plant_id)){
                $data->where('plant_id', $request->plant_id);
            }
            if($request->brand_id && !empty($request->brand_id)){
                $data->where('brand_id', $request->brand_id);
            }
            if($request->category_id && !empty($request->category_id)){
                $data->where('category_id', $request->category_id);
            }
            if($request->unit_id && !empty($request->unit_id)){
                $data->where('unit_id', $request->unit_id);
            }
            
            return Datatables::of($data)
                ->addIndexColumn()
                
                ->rawColumns([])
                ->make(true);
        }

        return view('products.stock', compact('plants','brands','sizes','grades'));
    }

    public function stock_upload(Request $request)
    {
        abort_if(Gate::denies('stock_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BranchStockImport, request()->file('import_file'));

        return back()->with('success', 'Primary Sales Import successfully !!');
    }

    public function stock_template(Request $request)
    {
        abort_if(Gate::denies('stock_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BranchStockTemplate, 'Stock Template.xlsx');
    }

    public function stock_download(Request $request)
    {
        // abort_if(Gate::denies('stock_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BranchStockExport($request), 'stock.xlsx');
    }
}
