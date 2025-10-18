<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'from'    => ['required','string','size:3'], // IATA
                'to'      => ['required','string','size:3'],
                'date'    => ['required','date'],
                'adults'  => ['nullable','integer','min:1','max:9'],

                // filteri
                'stops'   => ['nullable','in:0,1,2'],
                'carrier' => ['nullable','string'], // "JU,KL"
                'cabin'   => ['nullable','in:ECONOMY,BUSINESS,FIRST'],

                // sortiranje
                'sort'    => ['nullable','in:price_asc,price_desc,duration_asc,duration_desc,dep_asc,dep_desc'],

                // paginacija/PAGES
                'page'    => ['nullable','integer','min:1'],
                'per_page'=> ['nullable','integer','min:1','max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.size' => 'IATA kod je tačno 3 slova.',
            'to.size'   => 'IATA kod je tačno 3 slova.',
        ];
    }
}
