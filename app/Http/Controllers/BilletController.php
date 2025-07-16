<?php

namespace App\Http\Controllers;

use App\DataTables\BilletDataTable;
use App\Exports\BillteExport;
use App\Exports\ZoneExport;
use App\Imports\BillteImport;
use App\Imports\ZoneImport;
use App\Models\Billet;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class BilletController extends Controller
{
    public function index(BilletDataTable $dataTable, Request $request)
    {
        return $dataTable->render('billets.index');
    }

    public function create()
    {
        $billet = new Billet();
        $plants = Plant::where('active', 'Y')->get();
        return view('billets.create', compact('plants'))->with('billet', $billet);
    }

    public function store(Request $request)
    {
        try {
            $permission = !empty($request['id']) ? 'billet_edit' : 'billet_create';
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $data = $request->except('_token');
            $data['created_by'] = Auth::user()->id;
            Billet::create($data);
            return Redirect::to('billets')->with('message_success', 'Billet Store Successfully');
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $id = decrypt($id);
        $zone = Zone::find($id);
        return response()->json($zone);
    }

    public function edit(Billet $billet)
    {
        abort_if(Gate::denies('billet_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $plants = Plant::where('active', 'Y')->get();
        return view('billets.create', compact('plants'))->with('billet', $billet);
    }

    public function update(Request $request, Billet $billet)
    {
        $update = $billet->update($request->except('_token', '_method', 'id'));
        if ($update) {
            return Redirect::to('billets')->with('message_success', 'Billet Updated Successfully');
        }else{
            return Redirect::to('billets')->with('message_error', 'Billet Not Updated');
        }
    }

    public function destroy(Zone $zone)
    {
        //
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('billet_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BillteImport, request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('billet_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BillteExport, 'Billets.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('billet_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UnitTemplate, 'units.xlsx');
    }
}
