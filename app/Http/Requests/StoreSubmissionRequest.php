<?php

namespace App\Http\Requests;

use App\Support\SubmissionIdentity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('instagram'))) {
            $instagram = trim($this->input('instagram'));
            $this->merge(['instagram' => strtolower(str_starts_with($instagram, '@') ? substr($instagram, 1) : $instagram) ?: null]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['business', 'place'])],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('listing_type', is_string($this->input('type')) ? $this->input('type') : '')->where('is_active', true)],
            'address' => ['required', 'string', 'max:3000'],
            'reason' => ['required', 'string', 'min:10', 'max:5000'],
            'maps_url' => ['nullable', 'url:http,https', 'max:2048'],
            'evidence_url' => ['nullable', 'url:http,https', 'max:2048'],
            'email' => ['nullable', 'email:rfc', 'max:254'],
            'whatsapp' => ['nullable', 'regex:/\A\+?[1-9][0-9]{7,14}\z/'],
            'instagram' => ['nullable', 'string', 'regex:'.SubmissionIdentity::INSTAGRAM_PATTERN],
            'credit_consent' => ['sometimes', 'boolean'],
            'website' => ['nullable', 'string', 'max:0'],
            'form_token' => ['required', 'string', 'max:2048'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->allFiles() !== []) {
                $validator->errors()->add('attachments', 'Envía la evidencia mediante un enlace. No se admiten archivos.');
            }
            if ($this->boolean('credit_consent') && blank($this->input('instagram'))) {
                $validator->errors()->add('instagram', 'Indica tu usuario de Instagram para autorizar el crédito.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Escribe el nombre del negocio o Lugar.', 'name.max' => 'El nombre admite hasta 255 caracteres.',
            'type.required' => 'Elige si recomiendas un negocio o un Lugar.', 'type.in' => 'Elige negocio o Lugar.',
            'category_id.required' => 'Elige una categoría.', 'category_id.exists' => 'Elige una categoría activa compatible con el tipo.',
            'address.required' => 'Escribe una dirección o referencia de ubicación.', 'address.max' => 'La ubicación admite hasta 3000 caracteres.',
            'reason.required' => 'Cuéntanos por qué lo recomiendas.', 'reason.min' => 'Escribe al menos 10 caracteres en el motivo.', 'reason.max' => 'El motivo admite hasta 5000 caracteres.',
            'maps_url.url' => 'Usa un enlace completo de Maps con http o https.', 'evidence_url.url' => 'Usa un enlace de evidencia con http o https.',
            'email.email' => 'Revisa el formato del correo.', 'whatsapp.regex' => 'Usa de 8 a 15 dígitos con código de país, con + opcional.',
            'instagram.regex' => 'Usa hasta 30 letras, números, puntos o guiones bajos, sin puntos seguidos ni en los extremos.',
            'credit_consent.boolean' => 'Revisa la autorización del crédito.', 'website.max' => 'No fue posible validar el envío.',
            'form_token.required' => 'Abre de nuevo el formulario para enviar.',
        ];
    }
}
