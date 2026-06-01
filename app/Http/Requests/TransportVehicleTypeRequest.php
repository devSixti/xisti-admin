<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransportVehicleTypeRequest extends FormRequest
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
            'name' => 'required|unique:transport_vehicle_type,name,' . $this->get('id'),
            'icon' => 'required|dimensions:min_width=50,min_height=50,max_width=100,max_height=100|image|max:100|unique:transport_vehicle_type,icon_name,' . $this->get('id'),
            'cost_for_km' => 'nullable|numeric',
            'status' => 'required|integer',
            'service_id' => 'required|integer',
        ];
        if (!empty($this->get('id'))) {
            $rules = [
                'icon' => 'nullable|dimensions:min_width=50,min_height=50,max_width=100,max_height=100|max:100',
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'icon.max' => 'Icon max size 100kb',
            'icon.dimensions' => 'Icon Dimension between 50*50 to 100*100',
            'icon.unique' => 'Icon name unique',
            'service_id.required'=>'Please select vehicle service'
        ];
    }
}
