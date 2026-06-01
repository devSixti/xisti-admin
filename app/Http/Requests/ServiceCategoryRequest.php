<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|unique:service_category,name,' . $this->get('id'),
            'icon' => 'required|mimes:png|dimensions:max_width=200,max_height=200|image',
            'status' => 'required'
        ];
        if (!empty($this->get('id'))) {
            $rules = [
                'icon' => 'nullable|dimensions:max_width=200,max_height=200|image',
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
//            'icon.max' => 'Icon max size 100kb',
            'icon.dimensions' => 'Icon Max Dimension 200*200',
        ];
    }
}
