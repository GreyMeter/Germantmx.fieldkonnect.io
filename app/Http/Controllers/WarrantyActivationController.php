<?php

namespace App\Http\Controllers;

use App\DataTables\WarrantyActivationDataTable;
use App\Exports\WarrantyActivactionExport;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\EndUser;
use App\Models\Pincode;
use App\Models\SchemeHeader;
use App\Models\State;
use App\Models\TransactionHistory;
use App\Models\WarrantyActivation;
use App\Models\WarrantyTimeline;
use Illuminate\Http\Request;
use Gate;
use Excel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class WarrantyActivationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->warranty_activation = new WarrantyActivation();
        $this->path = 'warranty_activation';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WarrantyActivationDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('warranty_activation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $parent_customers = [];
        $scheme_names = SchemeHeader::where('active', 'Y')->select('id', 'scheme_name')->get();
        if ($request->status_is && $request->status_is != '' && $request->status_is != NULL) {
            $currunt_status = $request->status_is;
        } else {
            $currunt_status = '0';
        }
        return $dataTable->render('warranty_activation.index', compact('branches', 'parent_customers', 'scheme_names', 'currunt_status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('warranty_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $customers = Customers::select('id', 'name', 'mobile')->get();
        $customers_dealer = Customers::where('customertype', ['1', '3'])->select('id', 'name', 'mobile')->get();
        $pincodes = Pincode::all();
        $states = State::all();
        return view('warranty_activation.create', compact('customers', 'pincodes', 'branches', 'request', 'states'))->with('warranty_activation', $this->warranty_activation);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            abort_if(Gate::denies('warranty_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (!$request->end_user_id || $request->end_user_id == NULL || $request->end_user_id == '') {
                if (!empty($request['customer_pindcode'])) {
                    $pincodes = Pincode::with('cityname', 'cityname.districtname')->where('pincode', '=', $request['customer_pindcode'])->first();
                    $request['customer_state'] = !empty($pincodes['cityname']['districtname']['statename']) ? $pincodes['cityname']['districtname']['statename']['state_name'] : '';
                    $request['state_id'] = !empty($pincodes['cityname']['districtname']['state_id']) ? $pincodes['cityname']['districtname']['state_id'] : '';
                    $request['customer_district'] = !empty($pincodes['cityname']['districtname']) ? $pincodes['cityname']['districtname']['district_name'] : '';
                    $request['customer_city'] = !empty($pincodes['cityname']) ? $pincodes['cityname']['city_name'] : '';
                    $request['customer_country'] = !empty($pincodes['cityname']['districtname']['statename']['countryname']) ? $pincodes['cityname']['districtname']['statename']['countryname']['country_name'] : '';
                } else {
                    $state_is = State::find($request['customer_state']);
                    $request['customer_state'] = $state_is->state_name;
                    $request['state_id'] = $state_is->id;
                }
                $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                    'customer_name' => $request->customer_name ?? '',
                    'customer_number' => $request->customer_number ?? '',
                    'customer_email' => $request->customer_email ?? '',
                    'customer_address' => $request->customer_address ?? '',
                    'customer_place' => $request->customer_place ?? '',
                    'customer_pindcode' => $request->customer_pindcode ?? '',
                    'customer_country' => $request->customer_country ?? '',
                    'customer_state' => $request->customer_state ?? '',
                    'state_id' => $request->state_id ?? '',
                    'customer_district' => $request->customer_district ?? '',
                    'customer_city' => $request->customer_city ?? '',
                    'status' => $request->customer_status ?? ''
                ]);
                $request->end_user_id = $end_user->id;
            }
            $data = WarrantyActivation::where('product_serail_number', $request->product_serail_number)->where('status', '!=', '3')->first();
            if ($data) {
                return Redirect::to('warranty_activation')->with('message_info', 'This serial number('.$request->product_serail_number.') already in Warranty Activation.');
            } else {
                $wararanty = WarrantyActivation::create([
                    'product_serail_number' => $request->product_serail_number ?? NULL,
                    'product_id' => $request->product_id ?? NULL,
                    'end_user_id' => $request->end_user_id ?? NULL,
                    'branch_id' => $request->branch_id ?? NULL,
                    'customer_id' => $request->customer_id ?? NULL,
                    'status' => $request->status ?? 0,
                    'sale_bill_no' => $request->sale_bill_no ?? NULL,
                    'sale_bill_date' => $request->sale_bill_date ?? NULL,
                    'warranty_date' => $request->warranty_date ?? NULL,
                    'created_by' => auth()->user()->id
                ]);
                if ($request->status == '1') {
                    $checkTrans = TransactionHistory::where('coupon_code', $wararanty->product_serail_number)->first();
                    if ($checkTrans) {
                        TransactionHistory::where('coupon_code', $wararanty->product_serail_number)->update(['status' => '1']);
                        $customer = Customers::find($wararanty->customer_id);
                        $noti_data = [
                            'fcm_token' =>  $customer->customerdetails->fcm_token,
                            'title' => 'Points Activated ✅',
                            'msg' => $customer->name . ' your ' . $checkTrans->point . ' provisional points are successfully activated in Silver Saarthi.',
                        ];
                        $send_notification = SendNotifications::send($noti_data);
                    }
                }
                if ($request->hasFile('warranty_activation_attach')) {
                    $file = $request->file('warranty_activation_attach');
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $wararanty->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('warranty_activation_attach');
                }
            }

            return Redirect::to($request->previous_url.'?serial_no='.$request->product_serail_number)->with('message_success', 'Warranty Activation Store Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WarrantyActivation  $warrantyactivation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->warranty_activation = WarrantyActivation::find(decrypt($id));
        $warranty_timeline = WarrantyTimeline::where('warranty_id', decrypt($id))->orderBy('created_at', 'desc')->get();
        return view('warranty_activation.show', compact('warranty_timeline'))->with('warrantyactivation', $this->warranty_activation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WarrantyActivation  $warrantyactivation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->warranty_activation = WarrantyActivation::find(decrypt($id));
        abort_if(Gate::denies('warranty_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $customers = Customers::where('customertype', '2')->select('id', 'name', 'mobile')->get();
        $customers_dealer = Customers::where('customertype', ['1', '3'])->select('id', 'name', 'mobile')->get();
        $pincodes = Pincode::all();
        $states = State::all();
        $serial_no = $this->warranty_activation->product_serail_number;
        return view('warranty_activation.create', compact('customers', 'pincodes', 'branches', 'states'))->with('warranty_activation', $this->warranty_activation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WarrantyActivation  $warrantyactivation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WarrantyActivation $warrantyactivation)
    {
        try {
            // dd($request->all());
            $warrantyactivation = WarrantyActivation::find($request->warranty_id);
            if ($warrantyactivation->status != $request->status) {
                WarrantyTimeline::create([
                    'warranty_id' => $request->warranty_id,
                    'created_by' => auth()->user()->id,
                    'status' => $request->status,
                    'remark' => $request->remark ?? NULL,
                ]);
            }
            $warrantyactivation->product_serail_number = $request->product_serail_number ?? NULL;
            $warrantyactivation->product_id = $request->select_product_id ?? NULL;
            $warrantyactivation->end_user_id = $request->end_user_id ?? NULL;
            $warrantyactivation->branch_id = $request->branch_id ?? NULL;
            $warrantyactivation->customer_id = $request->customer_id ?? NULL;
            $warrantyactivation->status = $request->status ?? 0;
            $warrantyactivation->remark = $request->remark ?? NULL;
            $warrantyactivation->sale_bill_no = $request->sale_bill_no ?? NULL;
            $warrantyactivation->sale_bill_date = $request->sale_bill_date ?? NULL;
            $warrantyactivation->warranty_date = $request->warranty_date ?? NULL;
            $warrantyactivation->created_by = auth()->user()->id;
            $warrantyactivation->save();
            if ($request->hasFile('warranty_activation_attach')) {
                $file = $request->file('warranty_activation_attach');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $warrantyactivation->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('warranty_activation_attach');
            }
            if ($request->status == '1') {
                TransactionHistory::where('coupon_code', $request->product_serail_number)->update(['status' => '1']);
            }

            return Redirect::to('warranty_activation')->with('message_success', 'Warranty Activation Update Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WarrantyActivation  $warrantyactivation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $warrantyactivation = WarrantyActivation::find($id);
        if ($warrantyactivation) {
            TransactionHistory::where('coupon_code', $warrantyactivation->product_serail_number)->update(['status' => '0']);
            if ($warrantyactivation->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Warranty Activation deleted successfully!']);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Warranty Activation Delete!']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('warranty_activation_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new WarrantyActivactionExport($request), 'WarrantyActivation.xlsx');
    }

    public function statuschange(Request $request)
    {
        if ($request->status == '3') {
            $remark = $request->remark;
        } else {
            $remark = '';
        }
        WarrantyActivation::where('id', $request->id)->update(['status' => $request->status, 'remark' => $remark,]);

        WarrantyTimeline::create([
            'warranty_id' => $request->id,
            'created_by' => auth()->user()->id,
            'status' => $request->status,
            'remark' => $remark,
        ]);

        $wararanty = WarrantyActivation::find($request->id);

        if ($request->status == '1') {
            $checkTrans = TransactionHistory::where('coupon_code', $wararanty->product_serail_number)->first();
            if ($checkTrans) {
                TransactionHistory::where('coupon_code', $wararanty->product_serail_number)->update(['status' => '1']);
                $customer = Customers::find($wararanty->customer_id);
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => 'Points Activated ✅',
                    'msg' => $customer->name . ' your ' . $checkTrans->point . ' provisional points are successfully activated in Silver Saarthi.',
                ];
                $send_notification = SendNotifications::send($noti_data);
            }
        }

        return response()->json(['status' => true,]);
    }
}
