<?php

namespace App\Http\Requests;

use App\Actions\Listings\SubmitListingReport;
use App\Queries\PublicListingQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreListingReportRequest extends FormRequest
{
    public function authorize(PublicListingQuery $listings): bool
    {
        $listings->find((string) $this->route('listing'));

        return true;
    }

    public function rules(): array
    {
        return ['field' => ['required', Rule::in(array_keys(SubmitListingReport::FIELDS))], 'explanation' => ['required', 'string', 'min:10', 'max:2000'], 'website' => ['nullable', 'string', 'max:0'], 'form_token' => ['required', 'string']];
    }

    public function messages(): array
    {
        return ['explanation.required' => 'Explica qué dato debemos revisar.', 'explanation.min' => 'Escribe al menos 10 caracteres.', 'explanation.max' => 'La explicación admite hasta 2000 caracteres.', 'website.max' => 'No fue posible validar el envío.'];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->allFiles() !== []) {
                $validator->errors()->add('attachments', 'No se admiten archivos en los reportes.');
            }
        }];
    }
}
