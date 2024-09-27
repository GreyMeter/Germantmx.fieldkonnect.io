<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('product_create') || Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'product_code'  =>  [
                        'required',
                        'min:2',
                        'max:100',
                        'string',
                        'regex:/[a-zA-Z0-9\s]+/',
                        Rule::unique('products', 'product_code')->ignore($this->id)
                    ],
                    'display_name'  =>  'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    // 'description'   =>  'required|min:2|max:450|string|regex:/[a-zA-Z0-9\s]+/',
                    'mrp'           =>  'nullable|numeric',
                    'price'         =>  'nullable|numeric',
                    'discount'      =>  'nullable|numeric',
                    'max_discount'  =>  'nullable|numeric',
                    'selling_price' =>  'nullable|numeric',
                    'gst'           =>  'nullable|numeric|max:90',
                    'subcategory_id'=>  'nullable|numeric|exists:subcategories,id',
                    'category_id'   =>  'nullable|numeric|exists:categories,id',
                    'brand_id'      =>  'nullable|numeric|exists:brands,id',
                    'unit_id'       =>  'nullable|numeric|exists:unit_measures,id',
                    'image'         =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'hsn_code'      =>  'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                    'ean_code'      =>  'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'product_code'  =>  [
                        'required',
                        'min:2',
                        'max:100',
                        'string',
                        'regex:/[a-zA-Z0-9\s]+/',
                        Rule::unique('products', 'product_code')
                    ],
                    'display_name'  =>  'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    // 'description'   =>  'required|min:2|max:450|string|regex:/[a-zA-Z0-9\s]+/',
                    'mrp'           =>  'nullable|numeric',
                    'price'         =>  'nullable|numeric',
                    'discount'      =>  'nullable|numeric',
                    'max_discount'  =>  'nullable|numeric',
                    'selling_price' =>  'nullable|numeric',
                    'gst'           =>  'nullable|numeric|max:90',
                    'subcategory_id'=>  'nullable|numeric|exists:subcategories,id',
                    'category_id'   =>  'nullable|numeric|exists:categories,id',
                    'brand_id'      =>  'nullable|numeric|exists:brands,id',
                    'unit_id'       =>  'nullable|numeric|exists:unit_measures,id',
                    'image'         =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'hsn_code'      =>  'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                    'ean_code'      =>  'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
