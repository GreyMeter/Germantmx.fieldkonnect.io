<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderEmailExport;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Mail\OrderMailWithAttachment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\Price;
use Validator;
use Gate;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Attachment;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customers;
use App\Models\OrderConfirm;
use App\Models\Product;
use App\Models\User;
use App\Models\Sales;
use App\Models\AdditionalPrice;
use App\Models\ConsigneeDetail;
use App\Models\EmployeeDetail;
use App\Models\OrderDispatch;
use App\Models\Settings;
use App\Models\UnitMeasure;
use Excel;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->orders = new Order();

        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function getOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $customer_id = $request->customer_id ?? '';
            $user_ids = getUsersReportingToAuth($user->id);
            $pageSize = $request->input('pageSize');
            $customer_ids = EmployeeDetail::where('user_id', $user->id)->pluck('customer_id');
            $query = Order::with('customer:id,name')
                ->select('id', 'po_no', 'qty', 'base_price', 'discount_amt', 'created_at', 'customer_id', 'ordering')
                ->selectRaw('base_price + discount_amt as base_price')
                ->orderBy('ordering', 'asc')
                ->latest();

            if (!$user->hasRole('superadmin')) {
                $query->whereIn('customer_id', $customer_ids);
            }

            $data = !empty($pageSize) ? $query->paginate($pageSize) : $query->get();

            if ($data->isNotEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data retrieved successfully.',
                    'data' => $data
                ], $this->successStatus);
            }

            return response(['status' => 'error', 'message' => 'No Record Found.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getOrderDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $user = $request->user();
            $user_id = $user->id;
            $order_id = $request->input('order_id');
            $data = $this->orders->with('orderdetails', 'orderdetails.products', 'statusname', 'orderdetails.productdetails', 'createdbyname', 'getsalesdetail')->where('id', $order_id)->first();
            $salesdetails = Sales::where('order_id', $order_id)->first() ?? [];

            $data['schme_amount'] = (string)$data['schme_amount'];
            $data['order_status'] = isset($data['statusname']) ? $data['statusname']['status_name'] : 'Pending';
            $data['dispatch_date'] = isset($salesdetails) ? (isset($salesdetails['dispatch_date']) ? Carbon::parse($salesdetails['dispatch_date'])->format('d-m-Y') : '') : '';
            $data['lr_no'] = isset($salesdetails) ? isset($salesdetails['lr_no']) ? (string)$salesdetails['lr_no']  : '' : '';
            $data['invoice_no'] = isset($salesdetails) ? (isset($salesdetails['invoice_no']) ? $salesdetails['invoice_no']  : '') : '';
            $data['invoice_date'] = isset($salesdetails) ? (isset($salesdetails['invoice_date']) ? Carbon::parse($salesdetails['invoice_date'])->format('d-m-Y') : '') : '';
            $data['transport_name'] = isset($salesdetails) ? (isset($salesdetails['transport_details']) ? $salesdetails['transport_details']  : '') : '';
            $data['ebd_amount'] = (string)$data['ebd_amount'];
            $data['ebd_discount'] = (string)$data['ebd_discount'];
            $data['special_discount'] = (string)$data['special_discount'];
            $data['special_amount'] = (string)$data['special_amount'];
            $data['cluster_discount'] = (string)$data['cluster_discount'];
            $data['cluster_amount'] = (string)$data['cluster_amount'];
            $data['deal_discount'] = (string)$data['deal_discount'];
            $data['deal_amount'] = (string)$data['deal_amount'];
            $data['distributor_discount'] = (string)$data['distributor_discount'];
            $data['distributor_amount'] = (string)$data['distributor_amount'];
            $data['frieght_discount'] = (string)$data['frieght_discount'];
            $data['frieght_amount'] = (string)$data['frieght_amount'];
            $data['cash_discount'] = (string)$data['cash_discount'];
            $data['cash_amount'] = (string)$data['cash_amount'];
            $data['total_discount'] = (string)$data['total_discount'];
            $data['total_amount'] = (string)$data['total_amount'];
            $data['gst5_amt'] = (string)$data['gst5_amt'];
            $data['gst12_amt'] = (string)$data['gst12_amt'];
            $data['gst18_amt'] = (string)$data['gst18_amt'];
            $data['gst28_amt'] = (string)$data['gst28_amt'];
            $data['status_id'] = ($data['status_id'] && $data['status_id'] != NULL) ? (string)$data['status_id'] : "0";
            $data['address_id'] = (string)$data['address_id'];
            $data['suc_del'] = (string)$data['suc_del'];
            $data['gst_amount'] = (string)$data['gst_amount'];
            $data['order_remark'] = (string)$data['order_remark'];
            $data['dod_discount'] = (string)$data['dod_discount'];
            $data['special_distribution_discount'] = (string)$data['special_distribution_discount'];
            $data['distribution_margin_discount'] = (string)$data['distribution_margin_discount'];
            $data['total_fan_discount'] = (string)$data['total_fan_discount'];
            $data['total_fan_discount_amount'] = (string)$data['total_fan_discount_amount'];
            $data['cash_discount'] = (string)$data['cash_discount'];
            $data['cash_amount'] = (string)$data['cash_amount'];
            $data['product_cat_id'] = (string)$data['product_cat_id'];
            $data['extra_discount_amount'] = isset($data['extra_discount_amount']) && $data['extra_discount_amount'] > 0  ? (string)$data['extra_discount_amount'] : '';
            $data['special_distribution_discount_amount'] = isset($data['special_distribution_discount_amount']) && $data['special_distribution_discount_amount'] > 0  ? (string)$data['special_distribution_discount_amount'] : '';
            $data['distribution_margin_discount_amount'] = isset($data['distribution_margin_discount_amount']) && $data['distribution_margin_discount_amount'] > 0  ? (string)$data['distribution_margin_discount_amount'] : '';
            $data['fan_extra_discount'] = isset($data['fan_extra_discount']) && $data['fan_extra_discount'] > 0  ? (string)$data['fan_extra_discount'] : '';
            $data['fan_extra_discount_amount'] = isset($data['fan_extra_discount_amount']) && $data['fan_extra_discount_amount'] > 0  ? (string)$data['fan_extra_discount_amount'] : '';
            $data['cash_discount'] = isset($data['cash_discount']) && $data['cash_discount'] > 0  ? (string)$data['cash_discount'] : '';
            $data['cash_amount'] = isset($data['cash_amount']) && $data['cash_amount'] > 0  ? (string)$data['cash_amount'] : '';
            $data['dod_discount_amount'] = isset($data['dod_discount_amount']) && $data['dod_discount_amount'] > 0  ? (string)$data['dod_discount_amount'] : '';


            if (!empty($data['orderdetails'])) {
                $orderdetails = collect([]);
                foreach ($data['orderdetails'] as $key => $value) {
                    $orderdetails->push([
                        'orderdetail_id' => isset($value['id']) ? $value['id'] : 0,
                        'product_id' =>  isset($value['product_id']) ? $value['product_id'] : 0,
                        'product_name' =>  isset($value['products']['product_name']) ? $value['products']['product_name'] : '',
                        'product_image' =>  isset($value['products']['product_image']) ? $value['products']['product_image'] : '',
                        'product_detail_id' =>  isset($value['product_detail_id']) ? $value['product_detail_id'] : $value['product_id'],
                        //'detail_title' =>  isset($value['productdetails']['detail_title']) ? $value['productdetails']['detail_title'] : '',
                        'detail_title' =>  isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'quantity' =>  isset($value['quantity']) ? $value['quantity'] : 0,
                        'ebd_amount' =>  isset($value['ebd_amount']) ? $value['ebd_amount'] : 0,
                        'gst' =>  isset($value['products']['productpriceinfo']) ? $value['products']['productpriceinfo']['gst'] : 0,
                        'shipped_qty'  =>  isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'price'  =>  isset($value['price']) ? $value['price'] : 0.00,
                        'tax_amount'  =>  isset($value['tax_amount']) ? $value['tax_amount'] : 0.00,
                        'line_total'  =>  isset($value['line_total']) ? $value['line_total'] : 0.00,
                        'status_id'  =>  isset($value['status_id']) ? $value['status_id'] : 0,
                        'specification' => isset($value['products']['suc_del']) ? $value['products']['suc_del'] : '',
                        'part_no' => isset($value['products']['part_no']) ? $value['products']['part_no'] : '',
                        'product_no' => isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'hp' => isset($value['products']['specification']) ? $value['products']['specification'] : '',
                        'model_no' => isset($value['products']['model_no']) ? $value['products']['model_no'] : '',
                        'phase' => isset($value['products']['phase']) ? $value['products']['phase'] : '',
                        'brand_name' => isset($value['products']['brands']) ? $value['products']['brands']['brand_name'] : '',
                    ]);
                }
                unset($data['orderdetails']);
                $data['seller_name'] = isset($data['sellers']['name']) ? $data['sellers']['name'] : '';
                $data['seller_address'] = isset($data['sellers']['customeraddress']) ? $data['sellers']['customeraddress'] : '';
                $data['buyer_name'] = isset($data['buyers']['name']) ? $data['buyers']['name'] : '';
                $data['buyer_address'] = isset($data['buyers']['customeraddress']) ? $data['buyers']['customeraddress'] : '';
                $data['buyer_type'] = isset($data['buyers']['customertypes']) ? $data['buyers']['customertypes']['customertype_name'] : '';
                $data['orderdetails'] = $orderdetails;
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertOrder(Request $request)
    {
        try {
            $user = $request->user();
            $currentTime = Carbon::now('Asia/Kolkata');
            $hour = $currentTime->hour;
            $booking_start_time = Settings::where('key_name', 'booking_start_time')->first();
            $booking_end_time = Settings::where('key_name', 'booking_end_time')->first();
            if ($hour >= (int)$booking_end_time->value || $hour < (int)$booking_start_time->value) {
                //convert the time to 12 hours
                $start_time = date('g:i A', strtotime($booking_start_time->value));
                $end_time = date('g:i A', strtotime($booking_end_time->value));
                return response()->json(['status' => 'error', 'message' =>  'You can book a booking between ' . $start_time . ' to ' . $end_time . '.'], $this->badrequest);
            }
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), [
                'qty' => 'required',
                'customer_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $today_order_qty = Order::where('customer_id', $request['customer_id'])
                ->whereDate('created_at', today())
                ->whereNot('status', '4')
                ->sum('qty');
            $order_limit_remain = (int)($customer->order_limit ?? 500) - $today_order_qty;
            if ($order_limit_remain < $request->qty) {
                return response()->json(['status' => 'error', 'message' =>  'The quantity is greater than today\'s limit.'], $this->badrequest);
            }

            $customer = Customers::find($request['customer_id']);
            $customer_parity = $customer->customer_parity;
            if ($customer_parity == 'South Parity') {
                $price = Price::where('id', 2)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 2)->where('model_id', $customer->id)->first();
            } else {
                $price = Price::where('id', 1)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 1)->where('model_id', $customer->id)->first();
            }
            if ($check_additional_price) {
                $price = number_format((floatval($price) + floatval($check_additional_price->price_adjustment)), 2, '.', '');
            }

            $request['base_price'] = $price;
            $request['po_no'] = generatePoNumber();
            $soda = Order::create($request->all());

            $data['type'] = 'Booking Created';
            $data['data'] = 'New booking created successfully with PO Number is ' . $request['po_no'] . '.';
            $data['customer_id'] = $request['customer_id'];
            addNotification($data);

            if ($soda) {
                return response()->json(['status' => 'success', 'message' => 'Soda create successfully.', 'data' => $soda], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Error in create soda.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addCartItems(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'product_detail_id' => 'required|exists:product_details,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $data =  Cart::create([
                'customer_id' => isset($request->customer_id) ? $request->customer_id : null,
                'product_id' => isset($request->product_id) ? $request->product_id : null,
                'product_detail_id' => isset($request->product_detail_id) ? $request->product_detail_id : null,
                'quantity' => isset($request->quantity) ? $request->quantity : 1,
                'price' => isset($request->price) ? $request->price : 0.00,
                'discount' => isset($request->discount) ? $request->discount : 0.00,
                'total' => isset($request->total) ? $request->total : 0.00,
                'user_id' => isset($request->user_id) ? $request->user_id : $user->id,
                'created_at' => getcurentDateTime(),
            ]);
            if ($data) {
                return response(['status' => 'success', 'message' => 'Cart item added successfully.', 'data' => $data], 200);
            }
            return response(['status' => 'error', 'message' => 'Error in cart added.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getCartItems(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $data =  Cart::with(array('products' => function ($query) {
                $query->select('id', 'product_name', 'product_image');
            }, 'productdetails' => function ($query) {
                $query->select('id', 'detail_title');
            }))->where('customer_id', $request->customer_id)->get();
            if ($data) {
                $date = strtotime("+5 day");
                $expected_date = date('M d, Y', $date);
                return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'expected_date' => $expected_date], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getOrderPfd(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'customer_type_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = [
                'order' => Order::with('sellers', 'buyers', 'orderdetails', 'orderdetails.products', 'orderdetails.products.productdetails')->find($request->order_id),
            ];
            if ($request->customer_type_id == '2') {
                $html = view('order_pdf.order_pdf_retailer', $data)->render();
                $pdfDirectory = public_path('pdf/orders/');
                File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
                $pdfFilePath = $pdfDirectory . 'order_retailer_' . $request->order_id . '.pdf';
            } else {
                $html = view('order_pdf.order_pdf_dealer', $data)->render();
                $pdfDirectory = public_path('pdf/orders/');
                File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
                $pdfFilePath = $pdfDirectory . 'order_dealer_' . $request->order_id . '.pdf';
            }

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($pdfFilePath, $dompdf->output());
            $data_main['pdf_url'] = $url = url(str_replace('/var/www/html/', '', $pdfFilePath));
            return response(['status' => 'Success', 'message' => 'Data retrieved successfully.', 'data' => $data_main], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getClusterOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $user_ids = getUsersReportingToAuth($user_id);

            $query = $this->orders->where(function ($query) use ($user_ids) {
                $query->whereIn('created_by', $user_ids);
            })
                ->latest()
                ->where('cluster_discount', '!=', NULL);

            $start_date = $request->startdate ?? '';
            $end_date   = $request->enddate ?? '';
            $selecteduser_id = $request->user_id ?? '';
            $selectedstatus_id = $request->status_id ?? '';

            if (!empty($start_date) && !empty($end_date)) {
                $startDate = date('Y-m-d', strtotime($start_date));
                $endDate = date('Y-m-d', strtotime($end_date));
                $query->whereDate('order_date', '>=', $startDate)
                    ->whereDate('order_date', '<=', $endDate);
            }

            if (!empty($selecteduser_id)) {
                $query->where('created_by', $selecteduser_id);
            }

            if ((isset($selectedstatus_id) || $selectedstatus_id == 0) && $selectedstatus_id != '') {
                $query->where('discount_status', $selectedstatus_id);
            }
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Reject']];
            $users = User::where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->get();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity') ?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'cluster_discount' => isset($value['cluster_discount']) ? $value['cluster_discount'] : 0.00,
                        'cluster_amount' => isset($value['cluster_amount']) ? $value['cluster_amount'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'discount_status' => (($value['discount_status'] == '1') ? 'Approved' : (($value['discount_status'] == '2') ? 'Reject' : 'Pending')),
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSpecialOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $user_ids = getUsersReportingToAuth($user_id);

            $query = $this->orders->where(function ($query) use ($user_ids) {
                $query->whereIn('created_by', $user_ids);
            })
                ->latest()
                ->where(function ($query) {
                    $query->where('special_discount', '>', 0)
                        ->orWhere('deal_discount', '>', 0);
                });

            $start_date = $request->startdate ?? '';
            $end_date   = $request->enddate ?? '';
            $selecteduser_id = $request->user_id ?? '';
            $selectedstatus_id = $request->status_id ?? '';

            if (!empty($start_date) && !empty($end_date)) {
                $startDate = date('Y-m-d', strtotime($start_date));
                $endDate = date('Y-m-d', strtotime($end_date));
                $query->whereDate('order_date', '>=', $startDate)
                    ->whereDate('order_date', '<=', $endDate);
            }

            if (!empty($selecteduser_id)) {
                $query->where('created_by', $selecteduser_id);
            }

            if ((isset($selectedstatus_id) || $selectedstatus_id == 0) && $selectedstatus_id != '') {
                $query->where('discount_status', $selectedstatus_id);
            }
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Reject']];
            $users = User::where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->get();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity') ?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'special_discount' => isset($value['special_discount']) ? $value['special_discount'] : 0.00,
                        'special_amount' => isset($value['special_amount']) ? $value['special_amount'] : 0.00,
                        'deal_discount' => isset($value['deal_discount']) ? $value['deal_discount'] : 0.00,
                        'deal_amount' => isset($value['deal_amount']) ? $value['deal_amount'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'discount_status' => (($value['discount_status'] == '1') ? 'Approved' : (($value['discount_status'] == '2') ? 'Reject' : 'Pending')),
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function updateClusterOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'sub_total' => 'required',
                'grand_total' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $order = Order::find($request->order_id);
            if ($order) {
                $order->sub_total = $request->sub_total;
                $order->grand_total = $request->grand_total;
                $order->gst_amount = $request->gst_amount ?? '';
                $order->cluster_discount = $request->cluster_discount ?? '';
                $order->cluster_amount = $request->cluster_amount ?? '';
                $order->deal_discount = $request->deal_discount ?? '';
                $order->deal_amount = $request->deal_amount ?? '';
                $order->distributor_discount = $request->distributor_discount ?? '';
                $order->distributor_amount = $request->distributor_amount ?? '';
                $order->frieght_discount = $request->frieght_discount ?? '';
                $order->frieght_amount = $request->frieght_amount ?? '';
                $order->discount_status = $request->discount_status ?? '0';
                $order->sp_discount_status = $request->sp_discount_status ?? '0';
                $order->cash_discount = $request->cash_discount ?? '0';
                $order->cash_amount = $request->cash_amount ?? '0';
                $order->total_discount = $request->total_discount ?? '0';
                $order->total_amount = $request->total_amount ?? '0';
                $order->gst5_amt = $request->gst5_amt ?? NULL;
                $order->gst12_amt = $request->gst12_amt ?? NULL;
                $order->gst18_amt = $request->gst18_amt ?? NULL;
                $order->gst28_amt = $request->gst28_amt ?? NULL;
                $order->ebd_discount = $request->ebd_discount ?? NULL;
                $order->ebd_amount = $request->ebd_amount ?? NULL;
                $order->special_discount = $request->special_discount ?? NULL;
                $order->special_amount = $request->special_amount ?? NULL;
                $order->updated_at = getcurentDateTime();
                $order->updated_by = auth()->user()->id;
                $order->save();
                return response()->json(['status' => 'success', 'message' => 'Data updated successfully.', 'data' => $order], 200);
            } else {
                return response(['status' => 'error', 'message' => 'Order Not Found.', 'data' => NULL], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function deleteOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }

        OrderDetails::where('order_id', $request->order_id)->delete();
        Order::where('id', $request->order_id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Order deleted successfully.'], 200);
    }

    public function customerSodaList(Request $request)
    {
        try {
            $customer = $request->user();
            $data = Order::where('customer_id', $customer->id)
                ->select('id', 'po_no', 'qty', 'base_price', 'discount_amt', 'created_at')
                ->selectRaw('base_price + discount_amt as base_price')
                ->orderBy("ordering", "asc")->latest()
                ->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function get_brand(Request $request)
    {
        try {
            $data = Brand::where('active', '=', 'Y')->select('id', 'brand_name')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function get_grade(Request $request)
    {
        try {
            $data = UnitMeasure::where('active', '=', 'Y')->select('id', 'unit_name as grade_name')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function get_size(Request $request)
    {
        try {
            $data = Category::where('active', '=', 'Y')->select('id', 'category_name as size')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function get_material(Request $request)
    {
        try {
            $data = config('constants.material');

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSodaCreateDetails(Request $request)
    {
        try {
            $customer = $request->user();
            $today_order_qty = Order::where('customer_id', $customer->id)
                ->whereDate('created_at', today())
                ->whereNot('status', '4')
                ->sum('qty');

            $customer_parity = $customer->customer_parity;

            if ($customer_parity == 'South Parity') {
                $price = Price::where('id', 2)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 2)->where('model_id', $customer->id)->first();
            } else {
                $price = Price::where('id', 1)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 1)->where('model_id', $customer->id)->first();
            }
            if ($check_additional_price) {
                $price = number_format((floatval($price) + floatval($check_additional_price->price_adjustment)), 2, '.', '');
            }

            $data['order_limit_remain'] = (int)($customer->order_limit ?? 0) - $today_order_qty;
            $data['po_no'] = generatePoNumber();
            $data['base_price'] = $price;


            return response()->json(['status' => 'success', 'message' => 'data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSodaCreateDetailsUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $user = $request->user();
            $customer = Customers::where('id', $request['customer_id'])->first();
            $customer_parity = $customer->customer_parity;
            $today_order_qty = Order::where('customer_id', $request['customer_id'])
                ->whereDate('created_at', today())
                ->whereNot('status', '4')
                ->sum('qty');

            if ($customer_parity == 'South Parity') {
                $price = Price::where('id', 2)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 2)->where('model_id', $request['customer_id'])->first();
            } else {
                $price = Price::where('id', 1)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 1)->where('model_id', $request['customer_id'])->first();
            }
            if ($check_additional_price) {
                $price = number_format((floatval($price) + floatval($check_additional_price->price_adjustment)), 2, '.', '');
            }

            $data['order_limit_remain'] = (int)($customer->order_limit ?? 500) - $today_order_qty;
            $data['po_no'] = generatePoNumber();
            $data['base_price'] = $price;


            return response()->json(['status' => 'success', 'message' => 'data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertSoda(Request $request)
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
                return response()->json(['status' => 'error', 'message' =>  'You can book a booking between ' . $start_time . ' to ' . $end_time . '.'], $this->badrequest);
            }
            $customer = $request->user();
            $customer_parity = $customer->customer_parity;
            $validator = Validator::make($request->all(), [
                'qty' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $today_order_qty = Order::where('customer_id', $customer->id)
                ->whereDate('created_at', today())
                ->whereNot('status', '4')
                ->sum('qty');
            $order_limit_remain = (int)($customer->order_limit ?? 500) - $today_order_qty;
            if ($order_limit_remain < $request->qty) {
                return response()->json(['status' => 'error', 'message' =>  'The quantity is greater than today\'s limit.'], $this->badrequest);
            }
            if ($customer_parity == 'South Parity') {
                $price = Price::where('id', 2)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 2)->where('model_id', $customer->id)->first();
            } else {
                $price = Price::where('id', 1)->first()->base_price;
                $check_additional_price = AdditionalPrice::where('model_name', 'distributor')->where('price_id', 1)->where('model_id', $customer->id)->first();
            }
            if ($check_additional_price) {
                $price = number_format((floatval($price) + floatval($check_additional_price->price_adjustment)), 2, '.', '');
            }

            $request['base_price'] = $price;
            $request['customer_id'] = $customer->id;
            $request['created_by'] = NULL;
            $request['po_no'] = generatePoNumber();
            $soda = Order::create($request->all());

            $data['type'] = 'Booking Created';
            $data['data'] = 'New booking created successfully with PO Number is ' . $request['po_no'] . '.';
            $data['customer_id'] = $request['customer_id'];
            addNotification($data);

            if ($soda) {
                return response()->json(['status' => 'success', 'message' => 'Soda create successfully.', 'data' => $soda], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Error in create soda.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSoda(Request $request)
    {
        try {
            $customer = $request->user();
            $validator = Validator::make($request->all(), [
                'soda_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $soda = Order::with('customer')->find($request->soda_id);
            $confirm_qty = OrderConfirm::where('order_id', $request->soda_id)->sum('qty');
            $soda['confirm_qty'] = $confirm_qty;
            $soda['base_price'] = $soda['base_price'] + $soda['discount_amt'];
            $totalOrderConfirmQty = OrderConfirm::where('order_id', $request->soda_id)->sum('qty');
            if ($soda) {
                $soda->customer_address = $soda->customer->customeraddress ? $soda->customer->customeraddress?->cityname?->city_name . ',' . $soda->customer->customeraddress->districtname?->district_name . ',' . $soda->customer->customeraddress->statename?->state_name . ',' . $soda->customer->customeraddress->countryname?->country_name . ',' . $soda->customer->customeraddress->pincodename?->pincode : '-';
                $soda->totalOrderConfirmQty = $totalOrderConfirmQty;
                return response()->json(['status' => 'success', 'message' => 'data retrieved successfully.', 'data' => $soda], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Soda not found.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertOrderConfirm(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'soda_id' => 'required',
                'consignee_details' => 'required',
                'qty' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
                // 'grade_id' => [
                //     'required',
                //     'array',
                //     function ($attribute, $value, $fail) {
                //         if (empty($value)) {
                //             $fail($attribute . ' cannot be an empty array.');
                //         } elseif (!isset($value[0]) || $value[0] === null) {
                //             $fail($attribute . ' must have a non-null value at index 0.');
                //         }
                //     },
                // ],
                'brand_id' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
                'size_id' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $tqty = 0;
            $soda = Order::with('customer')->find($request->soda_id);

            // $firstOrder = Order::where('customer_id', $soda->customer->id)->where('id', '<', $request->soda_id)->count() == 0;

            // if (!$firstOrder) {
            //     // Check for older orders with pending quantity
            //     $pendingOrders = Order::where('customer_id', $soda->customer->id)
            //         ->where('id', '<', $request->soda_id)
            //         ->whereNot('status', '4')
            //         ->get();

            //     foreach ($pendingOrders as $pendingOrder) {
            //         $totalOrderedQty = $pendingOrder->qty;

            //         // Get total confirmed quantity for this order
            //         $confirmedQty = OrderConfirm::where('order_id', $pendingOrder->id)->sum('qty');

            //         // Calculate pending quantity
            //         $pendingQty = $totalOrderedQty - $confirmedQty;

            //         if ($pendingQty > 0) {
            //             return response()->json([
            //                 'status' => 'error',
            //                 'message' => 'Order confirmation blocked. Older orders have pending quantity.',
            //                 'pending_orders' => [
            //                     'po_no' => $pendingOrder->po_no,
            //                     'pending_qty' => $pendingQty
            //                 ]
            //             ], 400);
            //         }
            //     }
            // }

            $totalOrderConfirm = OrderConfirm::where('order_id', $request->soda_id)->distinct('confirm_po_no')->count('confirm_po_no');
            ConsigneeDetail::updateOrCreate([
                'consignee_detail' => $request->consignee_details
            ]);
            foreach ($request->qty as $k => $qty) {
                if ($soda->customer->customer_parity == 'South Parity') {
                    $additional_price_parity = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => $soda->customer->customer_parity])->first())->price_adjustment;
                    if ($request->brand_id[$k] == '2') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '2', 'model_name' => 'grade'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => 'size'])->first())->price_adjustment;
                    } else if ($request->brand_id[$k] == '1') {
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => 'size_jindal'])->first())->price_adjustment;
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '2', 'model_name' => 'grade_jindal'])->first())->price_adjustment;
                    }
                    $additional_price_brand = optional(AdditionalPrice::where(['model_id' => $request->brand_id[$k], 'price_id' => '2', 'model_name' => 'brand'])->first())->price_adjustment;
                } else {
                    $additional_price_parity = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => $soda->customer->customer_parity])->first())->price_adjustment;
                    if ($request->brand_id[$k] == '2') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '1', 'model_name' => 'grade'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => 'size'])->first())->price_adjustment;
                    } else if ($request->brand_id[$k] == '1') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '1', 'model_name' => 'grade_jindal'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => 'size_jindal'])->first())->price_adjustment;
                    }
                    $additional_price_brand = optional(AdditionalPrice::where(['model_id' => $request->brand_id[$k], 'price_id' => '1', 'model_name' => 'brand'])->first())->price_adjustment;
                }

                $after_soda_price = ($soda->base_price + $soda->discount_amt)
                    + $additional_price_brand
                    + $additional_price_grade
                    + $additional_price_size
                    + $additional_price_parity;

                $additional_rate = $request->additional_rate[$k] ?? 0;
                $special_cut = $request->special_cut[$k] ?? 0;

                $total_soda_price = ($after_soda_price
                    + ($additional_rate * $qty)) * $qty;

                $data['confirm_po_no'] = $soda->po_no . '-' . $totalOrderConfirm + 1;
                $data['order_id'] = $request->soda_id;
                $data['created_by'] = NULL;
                $data['po_no'] = $soda->po_no;
                $data['consignee_details'] = $request->consignee_details;
                $data['qty'] = $qty;
                $data['unit_id'] = $request->grade_id[$k];
                $data['brand_id'] = $request->brand_id[$k];
                $data['additional_rate'] = $additional_rate;
                $data['random_cut'] = $request->random_cut[$k] ?? NULL;
                $data['special_cut'] = $special_cut;
                $data['remark'] = $request->remark[$k] ?? NULL;
                $data['category_id'] = $request->size_id[$k];
                $data['material'] = $request->material[$k];
                $data['base_price'] = $after_soda_price;
                $data['soda_price'] = $total_soda_price;
                $tqty += $qty;

                $orderConfirm = OrderConfirm::create($data);
            }
            $totalOrderConfirmQty = OrderConfirm::where('order_id', $request->soda_id)->sum('qty');
            if ($totalOrderConfirmQty >= $soda->qty) {
                Order::where('id', $request->soda_id)->update(['ordering' => 3]);
            } else {
                Order::where('id', $request->soda_id)->update(['ordering' => 2]);
            }

            $Ndata['type'] = 'Order Comfirmed';
            $Ndata['data'] = $tqty . ' Quantity confirmed of PO Number ' . $soda->po_no . ' .';
            $Ndata['customer_id'] = $soda->customer_id;
            addNotification($Ndata);
            if ($soda) {
                return response()->json(['status' => 'success', 'message' => 'Order Confirm Successfully.'], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Error in order confirm.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertOrderConfirmUser(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'soda_id' => 'required',
                'consignee_details' => 'required',
                'qty' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
                // 'grade_id' => [
                //     'required',
                //     'array',
                //     function ($attribute, $value, $fail) {
                //         if (empty($value)) {
                //             $fail($attribute . ' cannot be an empty array.');
                //         } elseif (!isset($value[0]) || $value[0] === null) {
                //             $fail($attribute . ' must have a non-null value at index 0.');
                //         }
                //     },
                // ],
                'brand_id' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
                'size_id' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            $fail($attribute . ' cannot be an empty array.');
                        } elseif (!isset($value[0]) || $value[0] === null) {
                            $fail($attribute . ' must have a non-null value at index 0.');
                        }
                    },
                ],
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $tqty = 0;
            $soda = Order::with('customer')->find($request->soda_id);

            // $firstOrder = Order::where('customer_id', $soda->customer->id)->where('id', '<', $request->soda_id)->count() == 0;

            // if (!$firstOrder) {
            //     // Check for older orders with pending quantity
            //     $pendingOrders = Order::where('customer_id', $soda->customer->id)
            //         ->where('id', '<', $request->soda_id)
            //         ->whereNot('status', '4')
            //         ->get();

            //     foreach ($pendingOrders as $pendingOrder) {
            //         $totalOrderedQty = $pendingOrder->qty;

            //         // Get total confirmed quantity for this order
            //         $confirmedQty = OrderConfirm::where('order_id', $pendingOrder->id)->sum('qty');

            //         // Calculate pending quantity
            //         $pendingQty = $totalOrderedQty - $confirmedQty;

            //         if ($pendingQty > 0) {
            //             return response()->json([
            //                 'status' => 'error',
            //                 'message' => 'Order confirmation blocked. Older orders have pending quantity.',
            //                 'pending_orders' => [
            //                     'po_no' => $pendingOrder->po_no,
            //                     'pending_qty' => $pendingQty
            //                 ]
            //             ], 400);
            //         }
            //     }
            // }

            $totalOrderConfirm = OrderConfirm::where('order_id', $request->soda_id)->distinct('confirm_po_no')->count('confirm_po_no');
            ConsigneeDetail::updateOrCreate([
                'consignee_detail' => $request->consignee_details
            ]);
            foreach ($request->qty as $k => $qty) {
                if ($soda->customer->customer_parity == 'South Parity') {
                    $additional_price_parity = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => $soda->customer->customer_parity])->first())->price_adjustment;
                    if ($request->brand_id[$k] == '2') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '2', 'model_name' => 'grade'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => 'size'])->first())->price_adjustment;
                    } else if ($request->brand_id[$k] == '1') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '2', 'model_name' => 'grade_jindal'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '2', 'model_name' => 'size_jindal'])->first())->price_adjustment;
                    }
                    $additional_price_brand = optional(AdditionalPrice::where(['model_id' => $request->brand_id[$k], 'price_id' => '2', 'model_name' => 'brand'])->first())->price_adjustment;
                } else {
                    $additional_price_parity = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => $soda->customer->customer_parity])->first())->price_adjustment;
                    if ($request->brand_id[$k] == '2') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '1', 'model_name' => 'grade'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => 'size'])->first())->price_adjustment;
                    } else if ($request->brand_id[$k] == '1') {
                        $additional_price_grade = optional(AdditionalPrice::where(['model_id' => $request->grade_id[$k], 'price_id' => '1', 'model_name' => 'grade_jindal'])->first())->price_adjustment;
                        $additional_price_size = optional(AdditionalPrice::where(['model_id' => $request->size_id[$k], 'price_id' => '1', 'model_name' => 'size_jindal'])->first())->price_adjustment;
                    }
                    $additional_price_brand = optional(AdditionalPrice::where(['model_id' => $request->brand_id[$k], 'price_id' => '1', 'model_name' => 'brand'])->first())->price_adjustment;
                }

                $after_soda_price = ($soda->base_price + $soda->discount_amt)
                    + $additional_price_brand
                    + $additional_price_grade
                    + $additional_price_size
                    + $additional_price_parity;

                $additional_rate = $request->additional_rate[$k] ?? 0;
                $special_cut = $request->special_cut[$k] ?? 0;

                $total_soda_price = ($after_soda_price
                    + ($additional_rate * $qty)
                    + ($special_cut * $qty)) * $qty;

                $data['confirm_po_no'] = $soda->po_no . '-' . $totalOrderConfirm + 1;
                $data['order_id'] = $request->soda_id;
                $data['created_by'] = NULL;
                $data['po_no'] = $soda->po_no;
                $data['consignee_details'] = $request->consignee_details;
                $data['qty'] = $qty;
                $data['unit_id'] = $request->grade_id[$k];
                $data['brand_id'] = $request->brand_id[$k];
                $data['additional_rate'] = $additional_rate;
                $data['random_cut'] = $request->random_cut[$k] ?? NULL;
                $data['special_cut'] = $special_cut;
                $data['remark'] = $request->remark[$k] ?? NULL;
                $data['category_id'] = $request->size_id[$k];
                $data['material'] = $request->material[$k];
                $data['base_price'] = $after_soda_price;
                $data['soda_price'] = $total_soda_price;
                $tqty += $qty;

                $orderConfirm = OrderConfirm::create($data);
            }

            $totalOrderConfirmQty = OrderConfirm::where('order_id', $request->soda_id)->sum('qty');
            if ($totalOrderConfirmQty >= $soda->qty) {
                Order::where('id', $request->soda_id)->update(['ordering' => 3]);
            } else {
                Order::where('id', $request->soda_id)->update(['ordering' => 2]);
            }

            $Ndata['type'] = 'Order Comfirmed';
            $Ndata['data'] = $tqty . ' Quantity confirmed of PO Number ' . $soda->po_no . ' .';
            $Ndata['customer_id'] = $soda->customer_id;
            addNotification($Ndata);
            if ($soda) {
                return response()->json(['status' => 'success', 'message' => 'Order Confirm Successfully.'], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Error in order confirm.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function customerorderList(Request $request)
    {
        try {
            $customer = $request->user();
            $sodas = Order::where('customer_id', $customer->id)->pluck('id');
            $data = OrderConfirm::whereIn('order_id', $sodas)->with('order.customer', 'createdbyname')->selectRaw('*, SUM(qty) as total_qty')
                ->groupBy('confirm_po_no')->orderBy('created_at', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function userrorderList(Request $request)
    {
        try {
            $user = $request->user();
            $customer_ids = EmployeeDetail::where('user_id', $user->id)->pluck('customer_id');
            if (!$user->hasRole('superadmin')) {
                $sodas = Order::WhereIn('customer_id', $customer_ids)->pluck('id');
            } else {
                $sodas = Order::pluck('id');
            }
            $data = OrderConfirm::whereIn('order_id', $sodas)->with('order.customer', 'createdbyname')->selectRaw('*, SUM(qty) as total_qty')
                ->groupBy('confirm_po_no')->orderBy('created_at', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function customerdispatchList(Request $request)
    {
        try {
            $customer = $request->user();
            $sodas = Order::where('customer_id', $customer->id)->pluck('id');
            $data = OrderDispatch::whereIn('order_id', $sodas)->with('order.customer', 'createdbyname')->selectRaw('*, SUM(qty) as total_qty')->groupBy('dispatch_po_no')->orderBy('id', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function userdispatchList(Request $request)
    {
        try {
            $user = $request->user();
            $customer_ids = EmployeeDetail::where('user_id', $user->id)->pluck('customer_id');
            $sodas = Order::where('created_by', $user->id)->orWhereIn('customer_id', $customer_ids)->pluck('id');
            $data = OrderDispatch::whereIn('order_id', $sodas)->with('order.customer', 'createdbyname')->selectRaw('*, SUM(qty) as total_qty')->groupBy('dispatch_po_no')->orderBy('id', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Order retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getConfirmOrder(Request $request)
    {
        try {
            $customer = $request->user();
            $validator = Validator::make($request->all(), [
                'confirm_order_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $order_chain =  OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname')->where(['confirm_po_no' => $request->confirm_order_id])->where('qty', '>', 0)->get();
            $confirm_qty = OrderConfirm::where('order_id', $order_chain[0]->order_id)->sum('qty');
            // if ($order) {
            //     $order->soda = $order->order;
            //     $order->customer = $order->order->customer;
            //     $order->customer_address = $order->order->customer->customeraddress ? $order->order->customer->customeraddress->cityname->city_name . ',' . $order->order->customer->customeraddress->districtname->district_name . ',' . $order->order->customer->customeraddress->statename->state_name . ',' . $order->order->customer->customeraddress->countryname->country_name . ',' . $order->order->customer->customeraddress->pincodename?->pincode : '-';
            // }
            return response()->json(['status' => 'success', 'message' => 'data retrieved successfully.', 'data' => $order_chain, 'confirm_qty' => $confirm_qty], 200);
            return response()->json(['status' => 'error', 'message' =>  'Soda not found.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getDispatchOrder(Request $request)
    {
        try {
            $customer = $request->user();
            $validator = Validator::make($request->all(), [
                'dispatch_order_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $order = OrderDispatch::with('brands:id,brand_name', 'order.customer', 'sizes:id,category_name', 'grades:id,unit_name', 'order_dispatch_details.media')->where('dispatch_po_no', $request->dispatch_order_id)->get();
            if ($order && $order->count() > 0) {
                return response()->json(['status' => 'success', 'message' => 'data retrieved successfully.', 'data' => $order], 200);
            }
            return response()->json(['status' => 'error', 'message' =>  'Order not found.'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'soda_id' => 'required',
            'remark' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = Order::find($request->soda_id);
        if ($orders) {
            $orders->status = '4';
            $orders->cancel_remark = $request->remark;
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Booking cancle successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Booking not found !!']);
        }
    }

    public function updateBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'soda_id' => 'required',
            'qty' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = Order::find($request->soda_id);
        if ($orders) {
            $orders->qty = $request->qty;
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Booking Updated successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Booking not found !!']);
        }
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'soda_id' => 'required',
            'qty' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = Order::find($request->soda_id);
        if ($orders) {
            $orders->qty = $request->qty;
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Booking Update successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Booking not found !!']);
        }
    }

    public function cancelConfirmOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_confirm_id' => 'required',
            'cancel_qty' => 'required',
            'remark' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = OrderConfirm::find($request->order_confirm_id);
        if ($orders) {
            // if ($orders->cancel_qty + $request->cancel_qty > 3) {
            //     return response()->json(['status' => 'error', 'message' => 'You can not cancel more than 3 Ton !!']);
            // }
            $orders->qty = $orders->qty - $request->cancel_qty;
            $orders->cancel_qty = $orders->cancel_qty + $request->cancel_qty;
            $orders->cancel_remark = $request->remark;
            $orders->status = '4';
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Order cancle successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Order not found !!']);
        }
    }

    public function cancelConfirmOrderUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_confirm_id' => 'required',
            'cancel_qty' => 'required',
            'remark' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $orders = OrderConfirm::find($request->order_confirm_id);
        if ($orders) {
            // if ($orders->cancel_qty + $request->cancel_qty > 3) {
            //     return response()->json(['status' => 'error', 'message' => 'You can not cancel more than 3 Ton !!']);
            // }
            $orders->qty = $orders->qty - $request->cancel_qty;
            $orders->cancel_qty = $orders->cancel_qty + $request->cancel_qty;
            $orders->cancel_remark = $request->remark;
            $orders->status = '4';
            $orders->save();
            return response()->json(['status' => 'success', 'message' => 'Order cancle successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Order not found !!']);
        }
    }

    public function getPriceCalculation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required',
            // 'grade' => 'required',
            'size' => 'required',
            'customer_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $brand = $request->brand ?? '';
        $grade = $request->grade ?? '';
        $size = $request->size ?? '';

        $parity = Customers::where('id', $request->customer_id)->value('customer_parity');

        $additional_price = 0;
        if (isset($brand) && isset($grade) && isset($size) && isset($parity)) {
            if ($parity == 'South Parity') {
                $brand_price = AdditionalPrice::where(['model_name' => 'brand', 'price_id' => '2', 'model_id' => $brand])->first();
                if ($brand == '2') {
                    $grade_price  = AdditionalPrice::where(['model_name' => 'grade', 'price_id' => '2', 'model_id' => $grade])->first();
                    $size_price  = AdditionalPrice::where(['model_name' => 'size', 'price_id' => '2', 'model_id' => $size])->first();
                } else if ($brand == '1') {
                    $grade_price  = AdditionalPrice::where(['model_name' => 'grade_jindal', 'price_id' => '2', 'model_id' => $grade])->first();
                    $size_price  = AdditionalPrice::where(['model_name' => 'size_jindal', 'price_id' => '2', 'model_id' => $size])->first();
                }
                $parity_price  = AdditionalPrice::where(['model_name' => $parity, 'price_id' => '2', 'model_id' => $size])->first();
            } else {
                $brand_price = AdditionalPrice::where(['model_name' => 'brand', 'price_id' => '1', 'model_id' => $brand])->first();
                if ($brand == '2') {
                    $grade_price  = AdditionalPrice::where(['model_name' => 'grade', 'price_id' => '1', 'model_id' => $grade])->first();
                    $size_price  = AdditionalPrice::where(['model_name' => 'size', 'price_id' => '1', 'model_id' => $size])->first();
                } else if ($brand == '1') {
                    $grade_price  = AdditionalPrice::where(['model_name' => 'grade_jindal', 'price_id' => '1', 'model_id' => $grade])->first();
                    $size_price  = AdditionalPrice::where(['model_name' => 'size_jindal', 'price_id' => '1', 'model_id' => $size])->first();
                }
                $parity_price  = AdditionalPrice::where(['model_name' => $parity, 'price_id' => '1', 'model_id' => $size])->first();
            }
            //calculate addition price according to brand , size , grade    
            $additional_price = $additional_price + (isset($brand_price->price_adjustment) ? $brand_price->price_adjustment : 0) + (isset($grade_price->price_adjustment) ? $grade_price->price_adjustment : 0) + (isset($size_price->price_adjustment) ? $size_price->price_adjustment : 0) + (isset($parity_price->price_adjustment) ? $parity_price->price_adjustment : 0);
        } else if (isset($brand) && isset($grade) && isset($size)) {
            $brand_price = AdditionalPrice::where(['model_name' => 'brand', 'price_id' => '1', 'model_id' => $brand])->first();
            if ($brand == '2') {
                $grade_price  = AdditionalPrice::where(['model_name' => 'grade', 'price_id' => '1', 'model_id' => $grade])->first();
                $size_price  = AdditionalPrice::where(['model_name' => 'size', 'price_id' => '1', 'model_id' => $size])->first();
            } else if ($brand == '1') {
                $grade_price  = AdditionalPrice::where(['model_name' => 'grade_jindal', 'price_id' => '1', 'model_id' => $grade])->first();
                $size_price  = AdditionalPrice::where(['model_name' => 'size_jindal', 'price_id' => '1', 'model_id' => $size])->first();
            }
            $parity_price  = AdditionalPrice::where(['model_name' => $parity, 'price_id' => '1', 'model_id' => $size])->first();

            //calculate addition price according to brand , size , grade
            $additional_price = $additional_price + (isset($brand_price->price_adjustment) ? $brand_price->price_adjustment : 0) + (isset($grade_price->price_adjustment) ? $grade_price->price_adjustment : 0) + (isset($size_price->price_adjustment) ? $size_price->price_adjustment : 0);
        }
        $final_price = $additional_price ?? 0;
        return response()->json(['status' => true, 'additional_price' => $final_price]);
    }

    public function updateFinalOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'brand_id' => 'required',
                'qty' => 'required',
                'additional_rate' => 'required',
                'category_id' => 'required',
                // 'random_cut' => 'required',
                'material' => 'required',
                // 'loading_add' => 'required',
                'base_price' => 'required',
                'soda_price' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            if (isset($request->consignee_details) && !empty($request->consignee_details)) {
                $fOrder = OrderConfirm::where('id', $request->id)->first();
                OrderConfirm::where('confirm_po_no', $fOrder->confirm_po_no)->update([
                    'consignee_details' => $request->consignee_details,
                ]);
            }
            OrderConfirm::where('id', $request->id)->update([
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id ?? null,
                'qty' => $request->qty,
                'category_id' => $request->category_id,
                'additional_rate' => $request->additional_rate ?? 0.00,
                'random_cut' => $request->random_cut ?? NULL,
                'special_cut' => $request->special_cut ?? 0.00,
                'material' => $request->material,
                'loading_add' => $request->loading_add,
                'base_price' => $request->base_price,
                'soda_price' => $request->soda_price
            ]);
            return response()->json(['status' => 'success', 'message' => 'Final Order Update Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong !!']);
        }
    }
}
