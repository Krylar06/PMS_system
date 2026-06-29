<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_type_id' => ['required', 'exists:device_types,id'],

            'property_number' => [
                'required',
                'string',
                'max:50',
                'regex:' . StoreDeviceRequest::PROPERTY_NUMBER_REGEX,
                'unique:devices,property_number,' . $this->route('device')->id,
            ],

            'serial_number' => [
                'nullable',
                'string',
                'max:100',
                'regex:' . StoreDeviceRequest::SERIAL_NUMBER_REGEX,
            ],

            'brand' => ['nullable', 'string', 'max:100', 'regex:' . StoreDeviceRequest::BRAND_MODEL_REGEX],
            'model' => ['nullable', 'string', 'max:100', 'regex:' . StoreDeviceRequest::BRAND_MODEL_REGEX],
            'mac_address' => ['nullable', 'string', 'regex:' . StoreDeviceRequest::MAC_ADDRESS_REGEX],

            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'date_acquired' => ['nullable', 'date', 'before_or_equal:today'],

            /*
            |--------------------------------------------------------------------------
            | Keep status nullable
            |--------------------------------------------------------------------------
            | The UI does not show Availability anymore, but the hidden field still
            | sends it. Nullable prevents validation issues if the field is missing.
            */
            'status' => ['nullable', 'in:available,issued,repair,retired'],

            /*
            |--------------------------------------------------------------------------
            | Device Condition
            |--------------------------------------------------------------------------
            */
            'condition' => ['nullable', 'in:serviceable,unserviceable'],

            'notes' => ['nullable', 'string', 'max:2000'],

            'last_maintenance_date' => ['nullable', 'date', 'before_or_equal:today'],
            'maintenance_remarks' => ['nullable', 'string', 'max:1000'],

            /*
            |--------------------------------------------------------------------------
            | Computer Specs
            |--------------------------------------------------------------------------
            */
            'specs' => ['nullable', 'array'],
            'specs.os' => ['nullable', 'string', 'max:100'],
            'specs.memory' => ['nullable', 'string', 'max:50'],
            'specs.storage' => ['nullable', 'string', 'max:50'],
            'specs.form_factor' => ['nullable', 'string', 'max:50'],
            'specs.os' => ['nullable', 'string', 'max:255'],
            'specs.os_version' => ['nullable', 'string', 'max:255'],
            'specs.os_license' => ['nullable', 'string', 'max:255'],
            'specs.memory' => ['nullable', 'string', 'max:255'],
            'specs.storage' => ['nullable', 'string', 'max:255'],
            'specs.form_factor' => ['nullable', 'string', 'max:255'],
            'specs.office_version' => ['nullable', 'string', 'max:255'],
            'specs.office_license' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_number.regex' => 'Property number may only contain letters, numbers, hyphens, and slashes.',
            'serial_number.regex' => 'Serial number may only contain letters, numbers, and hyphens.',
            'brand.regex' => 'Brand may only contain letters and numbers.',
            'model.regex' => 'Model may only contain letters and numbers.',
            'mac_address.regex' => 'Please enter a valid MAC address, e.g. 00:1A:2B:3C:4D:5E.',

            'unit_price.numeric' => 'The unit price must be a valid number.',
            'unit_price.min' => 'The unit price cannot be negative.',
            'unit_price.max' => 'The unit price is too large. Please enter a valid amount, for example 13000 or 25500.',

            'date_acquired.before_or_equal' => 'Date acquired cannot be in the future.',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',

            'condition.in' => 'The condition must be either serviceable or unserviceable.',

            'serial_number.max' => 'The serial number must not exceed 100 characters.',

            'specs.os.max' => 'The operating system field must not exceed 100 characters.',
            'specs.memory.max' => 'The memory field must not exceed 50 characters.',
            'specs.storage.max' => 'The storage field must not exceed 50 characters.',
            'specs.form_factor.max' => 'The form factor field must not exceed 50 characters.',
            'specs.os.max' => 'The operating system field must not exceed 255 characters.',
            'specs.os_version.max' => 'The operating system version field must not exceed 255 characters.',
            'specs.os_license.max' => 'The operating system license field must not exceed 255 characters.',
            'specs.memory.max' => 'The memory field must not exceed 255 characters.',
            'specs.storage.max' => 'The storage field must not exceed 255 characters.',
            'specs.form_factor.max' => 'The form factor field must not exceed 255 characters.',
            'specs.office_version.max' => 'The Microsoft Office field must not exceed 255 characters.',
            'specs.office_license.max' => 'The Microsoft Office license field must not exceed 255 characters.',
        ];
    }
}
