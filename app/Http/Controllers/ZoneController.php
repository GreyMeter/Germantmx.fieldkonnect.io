<?php

namespace App\Http\Controllers;

use App\DataTables\ZoneDataTable;
use App\Exports\ZoneExport;
use App\Imports\ZoneImport;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class ZoneController extends Controller
{
    public function index(ZoneDataTable $dataTable, Request $request)
    {
        return $dataTable->render('zones.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $permission = !empty($request['id']) ? 'zone_edit' : 'zone_create';
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (!empty($request['id'])) {
                $status = Zone::where('id', $request['id'])->update($request->except(['_token', 'id']));
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = Zone::create($request->except(['_token']));
            }
            if ($status) {
                return Redirect::to('zones')->with('message_success', 'Unit Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
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

    public function edit($id)
    {
        abort_if(Gate::denies('zone_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $zones = Zone::find($id);
         return view('zones.create')->with('zones',$zones);
    }

    public function update(Request $request, Zone $zone)
    {
        //
    }

    public function destroy(Zone $zone)
    {
        //
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('zone_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ZoneImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('zone_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ZoneExport, 'Zones.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('zone_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UnitTemplate, 'units.xlsx');
    }
}
