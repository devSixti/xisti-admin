<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderStoreRequest extends FormRequest
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
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'name' => 'nullable',
            'lname' => 'nullable',
            'email' => [
                'required',
                Rule::unique('users')->where(function($query) {
                    $query->where('email', '=', $this->get('email'));
                    $query->where('id', '!=', $this->get('id'));
                    $query->where('deleted_at', '=', null);
                })
            ],
            'avatar' => 'nullable|image|max:250',
            'vehicle_image' => 'nullable|image',
            'contact_number' => [
                'required ','numeric',
                Rule::unique('users')->where(function($query) {
                    $query->where('contact_number', '=', $this->get('contact_number'));
                    $query->where('country_code', '=', $this->get('country_code'));
                    $query->where('id', '!=', $this->get('id'));
                    $query->where('deleted_at', '=', null);
                })
            ],
//            'gender' => 'required',
            'vehicle_type_id' => 'required',
            'vehicle_company' => 'required',
            'vehicle_color' => 'required',
            'model_name' => 'required',
            'model_year' => 'required',
            'plat_no' => 'required',

        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'full_number.required' => 'please enter valid contact number!',
            'full_number.unique' => 'The contact number has already been taken!',
        ];
    }
}
