<?php

namespace App\Http\Controllers;

use App\DataTables\DealerAppointmentDataTable;
use App\Exports\DealerAppointmentExport;
use App\Models\Branch;
use App\Models\DealerAppointment;
use App\Models\DealerAppointmentKyc;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Excel;

class DealerAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DealerAppointmentDataTable $dataTable)
    {
        abort_if(Gate::denies('dealer_appointment'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('dealer_appointment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branchs = Branch::where('active', 'Y')->get();
        $districts = District::where('active', 'Y')->get();
        return view('dealer_appointment.form', compact('branchs', 'districts'));
    }

    public function create_kyc()
    {
        // $branchs = Branch::where('active', 'Y')->get();
        // $districts = District::where('active', 'Y')->get();
        $kyc_ckeckbox = config('constants.kyc_ckeckbox');
        return view('dealer_appointment.kyc_form', compact('kyc_ckeckbox'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $dealer_appointment = DealerAppointment::create($request->all());

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('profile_picture', 's3');
        }
        if ($request->hasFile('service_policy')) {
            $file = $request->file('service_policy');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('service_policy', 's3');
        }
        if ($request->hasFile('dealer_policy')) {
            $file = $request->file('dealer_policy');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('dealer_policy', 's3');
        }
        if ($request->hasFile('mou_sheet')) {
            $file = $request->file('mou_sheet');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mou_sheet', 's3');
        }
        if ($request->hasFile('mcl_cheque_1')) {
            $file = $request->file('mcl_cheque_1');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mcl_cheque_1', 's3');
        }
        if ($request->hasFile('mcl_cheque_2')) {
            $file = $request->file('mcl_cheque_2');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mcl_cheque_2', 's3');
        }
        if ($request->hasFile('gst_certificate')) {
            $file = $request->file('gst_certificate');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('gst_certificate', 's3');
        }
        if ($request->hasFile('adhar_card')) {
            $file = $request->file('adhar_card');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('adhar_card', 's3');
        }
        if ($request->hasFile('pan_card')) {
            $file = $request->file('pan_card');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('pan_card', 's3');
        }
        if ($request->hasFile('bank_statement')) {
            $file = $request->file('bank_statement');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('bank_statement', 's3');
        }
        if ($request->hasFile('shop_image')) {
            $file = $request->file('shop_image');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealer_appointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('shop_image', 's3');
        }
        $kyc_ckeckbox = config('constants.kyc_ckeckbox');
        return view('dealer_appointment.kyc_form', compact('kyc_ckeckbox', 'dealer_appointment'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function show(DealerAppointment $dealerAppointment)
    {
        $kyc_ckeckbox = config('constants.kyc_ckeckbox');
        return view('dealer_appointment.show', compact('dealerAppointment', 'kyc_ckeckbox'));
    }
    public function PDFshow(DealerAppointment $dealerAppointment)
    {
        $kyc_ckeckbox = config('constants.kyc_ckeckbox');
        return view('dealer_appointment.pdf', compact('dealerAppointment', 'kyc_ckeckbox'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function edit(DealerAppointment $dealerAppointment)
    {
        $branchs = Branch::where('active', 'Y')->get();
        $districts = District::where('active', 'Y')->get();
        $users = User::where('branch_id', $dealerAppointment->branch)->get();
        return view('dealer_appointment.edit', compact('dealerAppointment', 'branchs', 'districts', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DealerAppointment $dealerAppointment)
    {
        DealerAppointment::where('id', $dealerAppointment->id)->update($request->except(['_token','profile_picture','service_policy','dealer_policy','mou_sheet','mcl_cheque_1','mcl_cheque_2','gst_certificate','adhar_card','pan_card','bank_statement','shop_image']));

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('profile_picture', 's3');
        }
        if ($request->hasFile('shop_image')) {
            $file = $request->file('shop_image');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('shop_image', 's3');
        }
        if ($request->hasFile('service_policy')) {
            $file = $request->file('service_policy');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('service_policy', 's3');
        }
        if ($request->hasFile('dealer_policy')) {
            $file = $request->file('dealer_policy');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('dealer_policy', 's3');
        }
        if ($request->hasFile('mou_sheet')) {
            $file = $request->file('mou_sheet');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mou_sheet', 's3');
        }
        if ($request->hasFile('mcl_cheque_1')) {
            $file = $request->file('mcl_cheque_1');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mcl_cheque_1', 's3');
        }
        if ($request->hasFile('mcl_cheque_2')) {
            $file = $request->file('mcl_cheque_2');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('mcl_cheque_2', 's3');
        }
        if ($request->hasFile('gst_certificate')) {
            $file = $request->file('gst_certificate');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('gst_certificate', 's3');
        }
        if ($request->hasFile('adhar_card')) {
            $file = $request->file('adhar_card');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('adhar_card', 's3');
        }
        if ($request->hasFile('pan_card')) {
            $file = $request->file('pan_card');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('pan_card', 's3');
        }
        if ($request->hasFile('bank_statement')) {
            $file = $request->file('bank_statement');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('bank_statement', 's3');
        }
        return redirect(route('dealer-appointment'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DealerAppointment $dealerAppointment)
    {
        DealerAppointmentKyc::where('appointment_id', $dealerAppointment->id)->delete();
        $check = DealerAppointment::where('id', $dealerAppointment->id)->delete();
        if($check){
            return redirect()->back()->with('message_success', 'Appointment deleted successfully')->withInput();
        }else{
            return redirect()->back()->with('message_info', 'Appointment not deleted, Please try again leater.')->withInput();
        }
    }

    public function thanks(Request $request)
    {
        return view('dealer_appointment.thanks');
    }


    public function download(Request $request)
    {
        abort_if(Gate::denies('dealer_appointment_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // return $request;
        return Excel::download(new DealerAppointmentExport($request), 'new_dealer_appointment.xlsx');
    }


    public function kyc_store(Request $request)
    {
        DealerAppointmentKyc::updateOrCreate(['appointment_id' => $request->appointment_id,],[
            'appointment_id' => $request->appointment_id,
            'channel_partner' => $request->channel_partner,
            'place' => $request->place,
            'concerned_branch' => $request->concerned_branch,
            'dealer_code' => $request->dealer_code,
            'division' => $request->division,
            'proprietary_concern' => json_encode($request->proprietary_concern),
            'partnership_firm' => json_encode($request->partnership_firm),
            'ltd_pvt' => json_encode($request->ltd_pvt),
            'distribution_channel' => $request->distribution_channel,
        ]);
        return redirect(route('dealer-appointment-thanks'));
    }
}
