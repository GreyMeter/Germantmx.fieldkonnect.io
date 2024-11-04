<?php

namespace App\Http\Controllers;

use App\DataTables\UnitDataTable;
use App\Exports\UnitExport;
use App\Models\Plant;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Http\Response;
use Auth;
use Excel;
use Illuminate\Support\Facades\Redirect;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UnitDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('plants.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $permission = !empty($request['id']) ? 'unit_edit' : 'unit_create';
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (!empty($request['id'])) {
                $status = Plant::where('id', $request['id'])->update($request->except(['_token', 'id']));
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = Plant::create($request->except(['_token']));
            }
            if ($status) {
                return Redirect::to('plants')->with('message_success', 'Unit Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plant  $plant
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decrypt($id);
        $unit = Plant::find($id);
        return response()->json($unit);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Plant  $plant
     * @return \Illuminate\Http\Response
     */
    public function edit(Plant $plant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plant  $plant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plant $plant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plant  $plant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plant $plant)
    {
        //
    }

    public function download()
    {
        abort_if(Gate::denies('unit_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UnitExport, 'Unit.xlsx');
    }
}
