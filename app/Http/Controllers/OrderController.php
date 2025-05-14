<?php

namespace App\Http\Controllers;

use App\DataTables\OrderConfirmDataTable;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customers;
use App\Models\Status;
use App\Models\City;
use App\Models\Plant;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BranchStock;
use App\Models\RandomStock;


use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\OrderDataTable;
use App\DataTables\OrderDispatchDataTable;
use App\Exports\FinalOrderExport;
use App\Exports\OrderEmailExport;
use App\Imports\OrderImport;
use App\Exports\OrderExport;
use App\Exports\OrderTemplate;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderMailWithAttachment;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\OrderConfirm;
use App\Models\OrderDispatch;
use App\Models\Price;
use App\Models\AdditionalPrice;
use App\Models\ConsigneeDetail;
use App\Models\UnitMeasure;
use App\Models\OrderDispactchDetails;
use App\Models\Settings;
use Carbon\Carbon;
// use App\Models\Customers;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->orders = new Order();
    }

    public function index(OrderDataTable $dataTable)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customers::where('active', 'Y')->get();
        return $dataTable->render('orders.index', compact('customers'));
    }

    public function confirm_orders(OrderConfirmDataTable $dataTable)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $divisions = Category::where('active', 'Y')->get();
        $customer_types = CustomerType::where('active', 'Y')->get();
        return $dataTable->render('orders.confirm_orders', compact('divisions', 'customer_types'));
    }

    public function order_dispatch(OrderDispatchDataTable $dataTable)
    {
        abort_if(Gate::denies('order_dispatch'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $divisions = Category::where('active', 'Y')->get();
        $customer_types = CustomerType::where('active', 'Y')->get();
        return $dataTable->render('orders.dispatch_order', compact('divisions', 'customer_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::where('active', '=', 'Y')->select('id', 'category_name')->get();
        $customers = Customers::where('active', '=', 'Y')->select('id', 'name', 'order_limit')->get();
        $brands = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();
        $units = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name')->get();
        $base_price = optional(Price::select('base_price')->first())->base_price;
        $po_no = generatePoNumber();
        $totalOrderConfirmQty = 0;
        return view('orders.create', compact('categories', 'brands', 'customers', 'units', 'base_price', 'po_no', 'totalOrderConfirmQty'))->with('orders', $this->orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        try {
            $currentTime = Carbon::now('Asia/Kolkata');
            $hour = $currentTime->hour;
            $booking_start_time = Settings::where('key_name', 'booking_start_time')->first();
            $booking_end_time = Settings::where('key_name', 'booking_end_time')->first();
            if ($hour >= (int)$booking_end_time->value || $hour < (int)$booking_start_time->value) {
                //convert the time to 12 hours
                $start_time = date('g:i A', strtotime($booking_start_time->value));
                $end_time = date('g:i A', strtotime($booking_end_time->value));
                return Redirect::to('orders')->with('message_danger', 'You can book a booking between ' . $start_time . ' to ' . $end_time . '.');
            }

            abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['created_by'] = Auth::user()->id;
            $request['po_no'] = generatePoNumber();
            $order = Order::create($request->all());

            $data['type'] = 'Booking Created';
            $data['data'] = 'New booking created successfully with PO Number is ' . $request['po_no'] . '.';
            $data['customer_id'] = $request['customer_id'];
            addNotification($data);




            return Redirect::to('orders')->with('message_success', 'Booking Store Successfully And order PO Number is <span title="Copy" id="copyText">' . $request['po_no'] . '</span>');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $orders = $this->orders->with('brands', 'sizes', 'grades', 'customer', 'createdbyname')->find($id);
        $f_order = false;
        if ($orders->created_by == Auth::user()->id) {
            $f_order = true;
        }
        $totalOrderConfirmQty = OrderConfirm::where('order_id', $id)->sum('qty');

        return view('orders.show', compact('orders', 'totalOrderConfirmQty', 'f_order'));
    }

    public function confirm_orders_show($id, Request $request)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $totalOrderDispatchQty = OrderDispatch::where('order_confirm_id', $id)->sum('qty');
        $orders = OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname')->find($id);
        $order_chain =  OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname')->where(['confirm_po_no' => $orders->confirm_po_no])->get();
        $plants = Plant::where('active', 'Y')->latest()->get();
        return view('orders.confirm_show', compact('orders', 'totalOrderDispatchQty', 'order_chain', 'plants'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $orders = Order::find($id);
        $totalOrderConfirmQty = OrderConfirm::where('order_id', $id)->sum('qty');
        $categories = Category::where('active', '=', 'Y')->select('id', 'category_name')->get();
        $customers = Customers::where('active', '=', 'Y')->select('id', 'name', 'order_limit')->get();
        $brands = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();
        $units = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name')->get();
        $base_price = $orders->base_price;
        $cnf = $request->cnf ?? false;
        $materials = config('constants.material');
        return view('orders.create', compact('categories', 'customers', 'brands', 'units', 'base_price', 'cnf', 'totalOrderConfirmQty', 'materials'))->with('orders', $orders);
    }

    public function confirm_orders_edit($id, Request $request)
    {
        abort_if(Gate::denies('final_order_revised_size'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $totalOrderDispatchQty = OrderDispatch::where('order_confirm_id', $id)->sum('qty');
        $orders = OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname')->find($id);
        $order_chain =  OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname')->where(['confirm_po_no' => $orders->confirm_po_no])->get();
        $sizes = Category::where('active', 'Y')->get();
        $categories = Category::where('active', '=', 'Y')->select('id', 'category_name')->get();
        $brands = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();
        $units = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name')->get();
        $plants = Plant::where('active', 'Y')->latest()->get();
        return view('orders.confirm_edit', compact('orders', 'totalOrderDispatchQty', 'order_chain', 'plants', 'sizes', 'categories', 'brands', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, $id)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);

        $orders = Order::find($id);
        if ($orders->update($request->all())) {
            return Redirect::to('orders')->with('message_success', 'Booking update Successfully');
        }

        return redirect()->back()->with('message_danger', 'Error in Purchases Store')->withInput();
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        OrderDetails::where('order_id', $id)->delete();
        $product = Order::find($id);
        if ($product->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Order deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in User Delete!']);
    }

    public function active(Request $request)
    {
        if (Order::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Order ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('order_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new OrderImport, request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        abort_if(Gate::denies('order_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderExport($request), 'Booking.xlsx');
    }

    public function final_order_download(Request $request)
    {
        if($request->ip() != '111.118.252.250'){ 
            return view('work_in_progress');
        }
        abort_if(Gate::denies('order_confirm_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new FinalOrderExport($request), 'Final Ordes.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('order_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderTemplate, 'orders.xlsx');
    }

    public function ordersInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with('sellers', 'buyers')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->editColumn('order_date', function ($data) {
                    return isset($data->order_date) ? showdateformat($data->order_date) : '';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    if (auth()->user()->can(['order_show'])) {
                        $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.orders.title_singular') . '">
                                            <i class="material-icons">visibility</i>
                                        </a>';
                    }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                        ' . $btn . '
                                    </div>';
                })
                ->filter(function ($query) use ($request) {
                    if (!empty($request['buyer_id'])) {
                        $query->where('buyer_id', $request['buyer_id'])->orWhere('seller_id', $request['buyer_id']);
                    }
                    if (!empty($request['seller_id'])) {
                        $query->where('seller_id', $request['seller_id'])->orWhere('buyer_id', $request['buyer_id']);
                    }
                    if (!empty($request['created_by'])) {
                        $query->where('created_by', $request['created_by']);
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function ordertopoint()
    {

        $orders = Order::with('orderdetails')->whereNotNull(['buyer_id', 'seller_id'])->select('orderno as invoice_no', 'id', 'buyer_id', 'seller_id', 'grand_total', 'order_date as invoice_date', 'id as order_id', 'total_qty', 'shipped_qty', 'total_gst', 'status_id')->get();

        foreach ($orders as $key => $order) {
            $details = collect([]);
            $data = collect([
                'order_id' => isset($order['order_id']) ? $order['order_id'] : null,
                'invoice_no' => isset($order['invoice_no']) ? $order['invoice_no'] : null,
                'buyer_id' => isset($order['buyer_id']) ? $order['buyer_id'] : null,
                'seller_id' => isset($order['seller_id']) ? $order['seller_id'] : null,
                'grand_total' => isset($order['grand_total']) ? $order['grand_total'] : 0.00,
                'invoice_date' => isset($order['invoice_date']) ? $order['invoice_date'] : null,
                'order_id' => isset($order['order_id']) ? $order['order_id'] : null,
                'total_qty' => isset($order['total_qty']) ? $order['total_qty'] : null,
                'shipped_qty' => isset($order['shipped_qty']) ? $order['shipped_qty'] : null,
                'total_gst' => isset($order['total_gst']) ? $order['total_gst'] : null,
                'status_id' => isset($order['status_id']) ? $order['status_id'] : null,
            ]);
            if (!empty($order['orderdetails'])) {
                foreach ($order['orderdetails'] as $key => $rows) {
                    $details->push([
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] : null,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                        'price' => isset($rows['price']) ? $rows['price'] : 0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                    ]);
                }
            }
            $data['saledetail'] =  $details;
            $finaldata = collect([$data]);
            insertSales($finaldata);
        }
    }

    public function orderDispatched($orderid)
    {
        // $orderid = decrypt($orderid);
        // $status_id = Status::where('status_name','=','Dispatched')->pluck('id')->first();
        // Order::where('id','=',$orderid)->update(['status_id' => $status_id]);
        // $orders = $this->orders->with('orderdetails')->find($orderid);
        // $orders['invoice_date'] = date('Y-m-d');
        // $orders['invoice_no'] = $orderid.'-'.autoIncrementId('Sales','id') ;
        // $orders['order_id'] = $orderid ;
        // $orders['saledetail'] = $orders['orderdetails'];
        // $data = collect([$orders]);
        // $response = insertSales($data);
        // if($response['status'] == 'success')
        // {
        //     OrderDetails::where('order_id','=',$orderid)->update(['status_id' => $status_id]);
        //   return Redirect::to('orders')->with('message_success', 'Sales Store Successfully');
        // }
        // else
        // {
        //     Order::where('id','=',$orderid)->update(['status_id' => null]);
        // }

        $orderid = decrypt($orderid);
        $orders = $this->orders->with('orderdetails')->find($orderid);
        $category = Category::where('active', 'Y')->get();
        return view('orders.full_dispatched', compact('category'))->with('orders', $orders);
    }

    public function submitFullyDispatched(Request $request)
    {
        //$orderid = decrypt($orderid);
        try {

            $validator = Validator::make($request->all(), [
                'invoice_no'       => 'required',
                'order_id'         => 'required',
                'invoice_date'     => 'required',
                // 'transport_name'   => 'required',
                'lr_no'            => 'required',
                'dispatch_date'    => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $orderid = $request['order_id'];
            $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();
            Order::where('id', '=', $orderid)->update(['status_id' => $status_id, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'sub_total' => $request->sub_total, 'grand_total' => $request->grand_total, 'order_remark' => $request->order_remark]);
            $orders = $this->orders->with('orderdetails')->find($orderid);
            $orders['invoice_date'] = $request['invoice_date'];
            $orders['invoice_no'] = $request['invoice_no'];
            // $orders['transport_name'] = $request['transport_name'];
            $orders['lr_no'] = $request['lr_no'];
            $orders['dispatch_date'] = $request['dispatch_date'];
            $orders['transport_details'] = $request['transport_details'];
            $orders['order_id'] = $orderid;
            $orders['saledetail'] = $orders['orderdetails'];
            $data = collect([$orders]);
            $response = insertSales($data);
            if ($response['status'] == 'success') {

                $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();
                $partiallystatus = Status::where('status_name', '=', 'Partially Dispatched')->pluck('id')->first();

                if ($request['orderdetail']) {
                    foreach ($request['orderdetail'] as $key => $rows) {
                        // code chnanges
                        // $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                        //     ->where('product_detail_id', '=', $rows['product_detail'])->first();
                        $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                            ->where('product_id', '=', ($rows['product_id'] ?? ''))->first();

                        if (isset($orderdetail)) {
                            if ($orderdetail['shipped_qty'] + $rows['quantity'] == $orderdetail['quantity']) {
                                $orderdetail->status_id = $status_id;
                            } else {
                                $orderdetail->status_id = $partiallystatus;
                            }
                            $orderdetail->increment('shipped_qty', $rows['quantity']);
                            $orderdetail->save();
                        }
                    }
                }

                if (OrderDetails::where('order_id', '=', $request['order_id'])->where('status_id', '=', $partiallystatus)->exists()) {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus]);
                } else {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $status_id]);
                }
                return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');

                // OrderDetails::where('order_id','=',$orderid)->update(['status_id' => $status_id]);
                // return Redirect::to('orders')->with('message_success', 'Sales Store Successfully');
            } else {
                Order::where('id', '=', $orderid)->update(['status_id' => null]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }




    public function orderPartiallyDispatched($orderid)
    {
        $orderid = decrypt($orderid);
        $orders = $this->orders->with('orderdetails')->find($orderid);
        $category = Category::where('active', 'Y')->get();
        return view('orders.dispatched', compact('category'))->with('orders', $orders);
    }

    public function orderCancle($orderid, Request $request)
    {
        $orderid = decrypt($orderid);
        $orders = $this->orders->find($orderid);
        if ($orders) {
            $orders->status = '4';
            $orders->cancel_remark = $request->remark;
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Booking cancle successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Booking not found !!']);
        }
    }

    public function submitDispatched(Request $request)
    {
        try {
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'buyer_id' => 'required',
                'seller_id' => 'required',
                'invoice_no' => 'required',
                'order_id' => 'required',
                'grand_total' => 'required',
                'lr_no'            => 'required',
                'dispatch_date'    => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $request['saledetail'] = $request['orderdetail'];
            $request['status_id'] = 2;
            $data = collect([$request]);
            $response = insertSales($data);
            if ($response['status'] == 'success') {
                $partiallystatus = 2;
                if (isset($request['orderdetail'])) {
                    foreach ($request['orderdetail'] as $key => $rows) {
                        $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                            ->where('product_id', '=', ($rows['product_id'] ?? ''))->first();
                        if (isset($orderdetail)) {
                            $orderdetail->cash_dis = $rows['cash_dis'];
                            $orderdetail->cash_amounts = $rows['cash_amounts'];
                            $orderdetail->status_id = $partiallystatus;
                            $orderdetail->increment('shipped_qty', $rows['quantity']);
                            $orderdetail->save();
                        }
                    }
                }
                if (OrderDetails::where('order_id', '=', $request['order_id'])->where('status_id', '=', $partiallystatus)->exists()) {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'order_remark' => $request->order_remark]);
                } else {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'order_remark' => $request->order_remark]);
                }
                return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Sales Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function expectedDelivery(Request $request)
    {
        $cities = City::select('id', 'city_name')->get();
        $palaces = PlaceDispatch::select('city_name', 'pincode', 'days')->get();
        return view('orders.delivery', compact('cities', 'palaces'));
    }

    public function submitExpectedDelivery(Request $request)
    {
        foreach ($request['detail'] as $key => $rows) {
            if (!empty($rows['pincode'])) {
                PlaceDispatch::updateOrCreate(['pincode' => $rows['pincode']], [
                    'city_name'      => isset($rows['city_name']) ? $rows['city_name'] : null,
                    'pincode'      => isset($rows['pincode']) ? $rows['pincode'] : null,
                    'days'      => isset($rows['days']) ? $rows['days'] : null,
                ]);
            }
        }
        return Redirect::to('expected-delivery')->with('message_success', 'PlaceDispatch Update Successfully');
    }

    public function deleteOrderDtails(Request $request)
    {
        OrderDetails::where('id', $request->detailID)->delete();

        return response()->json(['status' => 'success']);
    }


    public function confirm($id, Request $request)
    {
        $id = decrypt($id);
        $orders = Order::with('customer')->find($id);
        // $firstOrder = Order::where('customer_id', $orders->customer->id)->where('id', '<', $id)->count() == 0;

        // if (!$firstOrder) {
        //     // Check for older orders with pending quantity
        //     $pendingOrders = Order::where('customer_id', $orders->customer->id)
        //         ->where('id', '<', $id)
        //         ->whereNot('status', '4')
        //         ->get();

        //     foreach ($pendingOrders as $pendingOrder) {
        //         $totalOrderedQty = $pendingOrder->qty;

        //         // Get total confirmed quantity for this order
        //         $confirmedQty = OrderConfirm::where('order_id', $pendingOrder->id)->sum('qty');

        //         // Calculate pending quantity
        //         $pendingQty = $totalOrderedQty - $confirmedQty;

        //         if ($pendingQty > 0) {
        //             return Redirect::to('orders')->with('message_danger', 'Order confirmation blocked. Older orders(' . $pendingOrder->po_no . ') have pending quantity.');
        //         }
        //     }
        // }
        $tqty = 0;
        $totalOrderConfirm = OrderConfirm::where('order_id', $id)->distinct('confirm_po_no')->count('confirm_po_no');
        ConsigneeDetail::updateOrCreate([
            'consignee_detail' => $request->consignee_details
        ]);
        foreach ($request->qty as $k => $qty) {
            $data['confirm_po_no'] = $orders->po_no . '-' . $totalOrderConfirm + 1;
            $data['order_id'] = $id;
            $data['created_by'] = Auth::user()->id;
            $data['po_no'] = $orders->po_no;
            $data['consignee_details'] = $request->consignee_details;
            $data['qty'] = $qty;
            $data['unit_id'] = $request->grade_id[$k];
            $data['brand_id'] = $request->brand_id[$k];
            $data['category_id'] = $request->category_id[$k];
            $data['material'] = $request->material[$k];
            $data['loading_add'] = $request->loading_add[$k];
            $data['additional_rate'] = $request->additional_rate[$k] ?? 0.00;
            $data['random_cut'] = $request->random_cut[$k] ?? NULL;
            $data['special_cut'] = $request->special_cut[$k] ?? 0.00;
            $data['remark'] = $request->remark[$k];
            $data['base_price'] = $request->booking_price[$k];
            $data['soda_price'] = $request->total_price[$k];
            $tqty += $qty;

            $soda = OrderConfirm::create($data);
        }

        $Ndata['type'] = 'Order Comfirmed';
        $Ndata['data'] = $tqty . ' Quantity confirmed of PO Number ' . $request['po_no'] . ' .';
        $Ndata['customer_id'] = $orders['customer_id'];
        addNotification($Ndata);

        return Redirect::to('orders_confirm')->with('message_success', 'Booking Confirm Successfully.');
    }

    public function dispatch_order($id, Request $request)
    {
        $id = decrypt($id);
        $orders = OrderConfirm::find($id);
        $stockA = manageStock($request->all());

        if (!$stockA) {
            return redirect()->back()->with('message_danger', 'Stock not available')->withInput();
        }

        $totalOrderDispacth = OrderDispatch::where('order_confirm_id', $id)->count('id');
        $request['dispatch_po_no'] = $orders->confirm_po_no . '-' . $totalOrderDispacth + 1;
        $request['order_confirm_id'] = $id;
        $request['order_id'] = $orders->order_id;
        $request['confirm_po_no'] = $orders->confirm_po_no;
        $request['created_by'] = Auth::user()->id;

        $orderConfirm = OrderDispatch::create($request->all());


        $Ndata['type'] = 'Order Disapatch';
        $Ndata['data'] = $request['qty'] . ' Quantity dispatch of order Number ' . $orders->confirm_po_no . ' .';
        $Ndata['customer_id'] = $orders->order->customer_id;
        addNotification($Ndata);

        return Redirect::to('orders_confirm')->with('message_success', 'Order Dispatch Successfully.');
    }

    // multiple order dispach 

    public function dispatch_order_multi($id, Request $request)
    {
        $id = decrypt($id);
        $orders = OrderConfirm::where(['confirm_po_no' => $id])->get();
        $disorder = OrderDispatch::where('confirm_po_no', $id)->get();
        if ((collect($request->dispatch_qty)->sum() + $disorder->sum('qty')) > $orders->sum('qty')) {
            return Redirect::back()->with('message_error', 'Please Check your remaining quantity');
        }
        $check_stock = true;
        foreach ($request->dispatch_qty as $key => $qty) {
            if ($qty > 0) {
                $check_stock = checkStock($orders[$key], $qty, $request->plant_id[$key]);
            }
            if (!$check_stock) {
                return Redirect::back()->with('message_error', 'Please Check your available stock');
            }
        }

        if (count($request->dispatch_qty) > 0) {
            if (!getOrderQuantityByPo($id)) {
                $totalOrderDispacth = OrderDispatch::where('order_confirm_id', $orders[0]->id)->count('id');
                $dispatch_po_no     = $id . '-' . $totalOrderDispacth + 1;
                $order_dispatch = false;
                foreach ($request->dispatch_qty as $key => $qty) {
                    if ($qty > 0) {
                        if (getOrderQuantity($orders[$key]->id) >= $qty) {
                            $orderDispatch = OrderDispatch::create([
                                'order_confirm_id' => $orders[$key]->id,
                                'order_id'         => $orders[$key]->order_id,
                                'po_no'            => $orders[$key]->po_no,
                                'confirm_po_no'    => $id,
                                'created_by'       => Auth::user()->id,
                                'dispatch_po_no'   => $dispatch_po_no,
                                'qty'              => $qty,
                                'unit_id'          => $orders[$key]->unit_id,
                                'brand_id'         => $orders[$key]->brand_id,
                                'category_id'      => $orders[$key]->category_id,
                                'plant_id'         => $request->plant_id[$key],
                                'base_price'       => $request->dispatch_base_price[$key],
                                'soda_price'       => $request->dispatch_soda_price[$key],
                                'rate'             => $request->additional_rate[$key],
                                'final_rate'       => $request->dispatch_soda_price[$key]
                            ]);
                            $Ndata['type'] = 'Order Disapatch';
                            $Ndata['data'] = $request['qty'] . ' Quantity dispatch of order Number ' . $id . ' .';
                            $Ndata['customer_id'] = $orders[$key]->order->customer_id;
                            addNotification($Ndata);
                            manageStockMulti($orders[$key], $qty, $request->plant_id[$key]);

                            $order_dispatch = true;
                        }
                    }
                }
                if ($order_dispatch == true) {
                    $order_dis = OrderDispactchDetails::updateOrCreate(
                        ['order_dispatch_po_no' => $dispatch_po_no],
                        [
                            'driver_name' => $request->driver_name ?? '',
                            'driver_contact_number' => $request->driver_contact_number,
                            'vehicle_number' => $request->vehicle_number,
                        ]
                    );

                    if ($request->hasFile('tc')) {
                        $file = $request->file('tc');

                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $order_dis->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('tc', 'public');
                    }
                    if ($request->hasFile('invoice')) {
                        $file = $request->file('invoice');

                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $order_dis->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('invoice', 'public');
                    }
                    if ($request->hasFile('e_way_bill')) {
                        $file = $request->file('e_way_bill');

                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $order_dis->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('e_way_bill', 'public');
                    }
                    if ($request->hasFile('wevrage_slip')) {
                        $file = $request->file('wevrage_slip');

                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $order_dis->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('wevrage_slip', 'public');
                    }
                }
                return Redirect::to('orders_confirm')->with('message_success', 'Order Dispatch Successfully.');
            } else {
                return Redirect::back()->with('message_error', 'Order Quantity must less then remaining qty');
            }
        }
    }

    // orderdispatch show 
    public function orders_dispatch($id, Request $request)
    {
        $id = decrypt($id);
        $orders = OrderDispatch::find($id);
        $plants = Plant::where('active', 'Y')->latest()->get();
        $dispatch_orders = OrderDispatch::with('order_confirm')->where(['dispatch_po_no' => $orders->dispatch_po_no])->get();
        return view('orders.order_dispatch_show', compact('dispatch_orders', 'orders', 'plants'));
    }

    public function orders_dispatch_update(OrderDispactchDetails $id, Request $request)
    {
        try {
            foreach ($request->order_id as $key => $order) {
                $old_data = OrderDispatch::find($order);
                if ($request->unit_id != null) {
                    BranchStock::where([
                        'plant_id' => $old_data->plant_id,
                        'brand_id' => $old_data->brand_id,
                        'unit_id' => $old_data->unit_id,
                        'category_id' => $old_data->category_id,
                    ])->increment('stock', $old_data->qty);            
                    BranchStock::where([
                        'plant_id' => $request->plant_id[$key],
                        'brand_id' => $old_data->brand_id,
                        'unit_id' => $old_data->unit_id,
                        'category_id' => $old_data->category_id,
                    ])->decrement('stock', $request->qty[$key]);            
                } elseif ($request->random_cut != null) {
                    RandomStock::where([
                        'plant_id' => $old_data->plant_id,
                        'random_cut' => $old_data->random_cut,
                        'category_id' => $old_data->category_id,
                    ])->increment('stock', $old_data->qty);
                    RandomStock::where([
                        'plant_id' => $request->plant_id[$key],
                        'random_cut' => $old_data->random_cut,
                        'category_id' => $old_data->category_id,
                    ])->decrement('stock', $request->qty[$key]);
                }
                $old_data->qty = $request->qty[$key];
                $old_data->plant_id = $request->plant_id[$key];
                $old_data->save();
            }
            if ($request->hasFile('tc')) {
                $file = $request->file('tc');

                $customname = time() . '.' . $file->getClientOriginalExtension();
                $id->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('tc', 'public');
            }
            if ($request->hasFile('invoice')) {
                $file = $request->file('invoice');

                $customname = time() . '.' . $file->getClientOriginalExtension();
                $id->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('invoice', 'public');
            }
            if ($request->hasFile('e_way_bill')) {
                $file = $request->file('e_way_bill');

                $customname = time() . '.' . $file->getClientOriginalExtension();
                $id->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('e_way_bill', 'public');
            }
            if ($request->hasFile('wevrage_slip')) {
                $file = $request->file('wevrage_slip');

                $customname = time() . '.' . $file->getClientOriginalExtension();
                $id->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('wevrage_slip', 'public');
            }
            $id->update($request->all());
            return Redirect::back()->with('message_success', 'Driver Details Updated Successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('message_error', 'Something went wrong.');
        }
    }

    public function confirm_orders_update(Request $request)
    {
        try {
            foreach ($request->order_ids as $key => $value) {
                OrderConfirm::where('id', $value)->update([
                    'category_id' => $request->category_id[$key],
                    'brand_id' => $request->brand_id[$key],
                    'unit_id' => $request->grade_id[$key] ?? null,
                    'qty' => $request->qty[$key],
                    'category_id' => $request->category_id[$key],
                    'additional_rate' => $request->additional_rate[$key] ?? 0.00,
                    'random_cut' => $request->random_cut[$key] ?? NULL,
                    'special_cut' => $request->special_cut[$key] ?? 0.00,
                    'material' => $request->material[$key],
                    'loading_add' => $request->loading_add[$key],
                    'base_price' => $request->dispatch_base_price[$key],
                    'soda_price' => $request->dispatch_soda_price[$key]
                ]);
            }
            return Redirect::to('orders_confirm')->with('message_success', 'Final Order Update Successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('message_error', 'Something went wrong.');
        }
    }

    public function confirm_orders_cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_confirm_id' => 'required',
            'remark' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = OrderConfirm::where('confirm_po_no', $request->order_confirm_id)->get();
        if ($orders) {
            foreach ($orders as $order) {
                $order->cancel_qty = $order->qty;
                $order->qty = 0;
                $order->cancel_remark = $request->remark;
                $order->status = '4';
                $order->save();
            }
            return response()->json(['status' => 'success', 'message' => 'Order cancle successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Order not found !!']);
        }
    }

    public function checkStock(Request $request)
    {
        $result = false;
        if ($request->unit_id != null) {
            $PlantStock = BranchStock::where(['plant_id' => $request->plant_id, 'brand_id' => $request->brand_id, 'unit_id' => $request->unit_id, 'category_id' => $request->category_id])->first();
    
            if ($PlantStock) {
                if ($PlantStock->stock < $request->qty) {
                    $result = false;
                }else{
                    $result = true;
                }
            } else {
                $result = false;
            }
        } elseif ($request->random_cut != null) {
            $PlantStock = RandomStock::where(['plant_id' => $$request->plant_id, 'category_id' => $request->category_id, 'random_cut' => $request->random_cut])->first();
            if ($PlantStock) {
                if ($PlantStock->stock < $qty) {
                    $result = false;
                }else{
                    $result = true;
                }
            } else {
                $result = false;
            }
        }
        return response()->json($result);
    }
}
