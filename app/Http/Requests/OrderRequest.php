<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_create') , Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'customer_id'      => 'required|numeric|exists:customers,id',
                    'po_no'     => 'required',
                    'qty'     => 'required|numeric',
                    'base_price'   => 'required|numeric'
                ];
                break;
            default :
                $rules = [
                    'customer_id'      => 'required|numeric|exists:customers,id',
                    'po_no'     => 'required',
                    'qty'     => 'required|numeric',
                    'base_price'   => 'required|numeric'
                ];
                break;
        }
        return $rules;
    }
}
