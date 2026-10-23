<x-public-layout title="Recomendar un negocio o Lugar · Tu Gurú Local" :noindex="true">
    <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">
        <section class="mx-auto max-w-2xl rounded-3xl border border-brand-border bg-white p-6 sm:p-9">
            @if(session('submission_sent'))
                <h1 class="font-display text-3xl font-extrabold">Recibimos tu recomendación</h1>
                <p role="status" class="mt-5 leading-relaxed">Gracias por compartirla. Nuestro equipo la revisará antes de decidir si la incorpora al directorio. El envío no garantiza su aceptación.</p>
                <a href="{{ route('cities.show', ['city' => 'playa-del-carmen']) }}" class="mt-7 inline-flex min-h-12 items-center rounded-xl bg-brand-action px-6 py-3 font-bold text-brand-text">Volver a Inicio</a>
            @else
                <h1 class="font-display text-3xl font-extrabold sm:text-4xl">Recomendar un negocio o Lugar</h1>
                <p class="mt-4 leading-relaxed text-brand-muted">¿Qué vale la pena conocer en Playa del Carmen? Cuéntanos dónde está y por qué lo recomiendas. Revisaremos la propuesta antes de incorporarla.</p>
                <p class="mt-3 text-sm leading-6 text-brand-muted">Los campos que dicen «opcional» pueden quedar vacíos. No incluyas datos privados de otras personas.</p>
                @if($errors->any())
                    <div role="alert" tabindex="-1" id="form-errors" class="mt-6 rounded-xl border border-red-800 bg-red-50 p-4 text-red-950">
                        <h2 class="font-semibold">Revisa estos datos para enviar</h2>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach($errors->messages() as $field => $messages)
                                @foreach($messages as $message)<li><a href="#{{ in_array($field, ['form_token', 'website', 'attachments']) ? 'submission-form' : $field }}" class="inline-block min-h-11 py-2 underline underline-offset-4">{{ $message }}</a></li>@endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="submission-form" method="POST" action="{{ route('submissions.store') }}" class="mt-8 grid gap-8" data-public-form>
                    @csrf
                    <input type="hidden" name="form_token" value="{{ $token }}">
                    <div hidden aria-hidden="true"><label>Deja este campo vacío<input name="website" tabindex="-1" autocomplete="off"></label></div>
                    <fieldset class="grid min-w-0 gap-6">
                        <legend class="mb-5 font-display text-xl font-extrabold">Sobre tu recomendación</legend>
                        <p class="text-sm text-brand-muted">Ciudad: Playa del Carmen</p>
                        <x-public-field name="name" label="Nombre del negocio o Lugar">
                            <input id="name" name="name" required maxlength="255" value="{{ old('name') }}" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @error('name') aria-describedby="name-error" @enderror class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                        </x-public-field>
                        <x-public-field name="type" label="Tipo" help="Un Lugar agrupa negocios ubicados dentro, como una plaza o un parque.">
                            <select id="type" name="type" required aria-describedby="type-help {{ $errors->has('type') ? 'type-error' : '' }}" aria-invalid="{{ $errors->has('type') ? 'true' : 'false' }}" class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                                <option value="">Selecciona el tipo</option>
                                <option value="business" @selected(old('type') === 'business')>Negocio</option>
                                <option value="place" @selected(old('type') === 'place')>Lugar</option>
                            </select>
                        </x-public-field>
                        <x-public-field name="category_id" label="Categoría" help="Elige una categoría del mismo tipo que tu recomendación.">
                            <select id="category_id" name="category_id" required aria-describedby="category_id-help {{ $errors->has('category_id') ? 'category_id-error' : '' }}" aria-invalid="{{ $errors->has('category_id') ? 'true' : 'false' }}" class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                                <option value="">Selecciona una categoría</option>
                                @foreach(['business' => 'Negocios', 'place' => 'Lugares'] as $type => $label)
                                    <optgroup label="{{ $label }}">
                                        @foreach($categories->where('listing_type', $type) as $category)<option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </x-public-field>
                        <x-public-field name="address" label="Dirección o referencia de ubicación">
                            <textarea id="address" name="address" required maxlength="3000" rows="3" aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}" @error('address') aria-describedby="address-error" @enderror class="mt-2 w-full rounded-[14px] border border-brand-muted bg-white p-3">{{ old('address') }}</textarea>
                        </x-public-field>
                        <x-public-field name="reason" label="¿Por qué lo recomiendas?" help="Entre 10 y 5000 caracteres. Este motivo queda para revisión privada del equipo.">
                            <textarea id="reason" name="reason" required minlength="10" maxlength="5000" rows="5" aria-describedby="reason-help {{ $errors->has('reason') ? 'reason-error' : '' }}" aria-invalid="{{ $errors->has('reason') ? 'true' : 'false' }}" class="mt-2 w-full rounded-[14px] border border-brand-muted bg-white p-3">{{ old('reason') }}</textarea>
                        </x-public-field>
                        @foreach(['maps_url' => ['Enlace de Google Maps (opcional)', 'Pega el enlace completo con http o https.'], 'evidence_url' => ['Enlace de evidencia (opcional)', 'Solo lo verá el equipo. Se admite un enlace con http o https, sin archivos adjuntos.']] as $name => [$label, $help])
                            <x-public-field :name="$name" :label="$label" :help="$help">
                                <input type="url" id="{{ $name }}" name="{{ $name }}" maxlength="2048" value="{{ old($name) }}" aria-describedby="{{ $name }}-help {{ $errors->has($name) ? $name.'-error' : '' }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                            </x-public-field>
                        @endforeach
                    </fieldset>
                    <fieldset class="grid min-w-0 gap-6 border-t border-brand-border pt-6">
                        <legend class="pr-3 font-display text-xl font-extrabold">Contacto privado (opcional)</legend>
                        <p class="text-sm leading-6 text-brand-muted">Solo lo verá el equipo. No enviamos mensajes automáticos ni ofrecemos seguimiento público.</p>
                        @foreach(['email' => ['Correo (opcional)', 'email', 'Puedes dejarlo vacío.'], 'whatsapp' => ['WhatsApp (opcional)', 'tel', 'Incluye código de país y de 8 a 15 dígitos, con + opcional.']] as $name => [$label, $type, $help])
                            <x-public-field :name="$name" :label="$label" :help="$help">
                                <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" maxlength="{{ $name === 'email' ? 254 : 16 }}" autocomplete="{{ $name === 'email' ? 'email' : 'tel' }}" value="{{ old($name) }}" aria-describedby="{{ $name }}-help {{ $errors->has($name) ? $name.'-error' : '' }}" aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                            </x-public-field>
                        @endforeach
                    </fieldset>
                    <fieldset class="grid min-w-0 gap-6 border-t border-brand-border pt-6">
                        <legend class="pr-3 font-display text-xl font-extrabold">Crédito de Instagram (opcional)</legend>
                        <x-public-field name="instagram" label="Tu usuario de Instagram (opcional)" help="Solo el identificador, con o sin @. Hasta 30 letras, números, puntos o guiones bajos.">
                            <input id="instagram" name="instagram" maxlength="31" value="{{ old('instagram') }}" autocomplete="off" autocapitalize="none" spellcheck="false" aria-describedby="instagram-help {{ $errors->has('instagram') ? 'instagram-error' : '' }}" aria-invalid="{{ $errors->has('instagram') ? 'true' : 'false' }}" class="mt-2 min-h-12 w-full rounded-[14px] border border-brand-muted bg-white px-3 py-2">
                        </x-public-field>
                        <div>
                            <label for="credit_consent" class="flex min-h-12 cursor-pointer items-start gap-3 rounded-lg py-2">
                                <input type="checkbox" id="credit_consent" name="credit_consent" value="1" @checked(old('credit_consent') === '1') class="mt-1 size-5 shrink-0 accent-brand-deep">
                                <span class="leading-6">Autorizo mostrar mi usuario como crédito si el equipo lo selecciona y publica la ficha. Recomendar no requiere esta autorización.</span>
                            </label>
                            @error('credit_consent')<p class="mt-2 text-sm text-red-900">{{ $message }}</p>@enderror
                        </div>
                    </fieldset>
                    <div class="grid gap-3">
                        <button type="submit" class="min-h-12 w-full rounded-[14px] bg-brand-action px-6 py-3 font-bold text-brand-text active:brightness-95">Enviar recomendación</button>
                        <p data-public-form-status role="status" class="text-sm text-brand-muted"></p>
                        <p class="text-sm leading-6 text-brand-muted">Cada envío queda pendiente de revisión y no garantiza su aceptación.</p>
                    </div>
                </form>
            @endif
        </section>
    </div>
</x-public-layout>
