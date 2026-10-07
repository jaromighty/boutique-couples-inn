<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingHoldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guest_email' => 'required|email',
            'guest_name' => 'required|string|max:255',
        ];
    }
}
