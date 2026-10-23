<x-public-layout title="Reportar datos incorrectos · Tu Gurú Local" :noindex="true">
    <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">
    <div class="mx-auto max-w-2xl"><a class="inline-flex min-h-12 items-center rounded-lg font-semibold underline underline-offset-4" href="{{ \App\Support\PublicUrl::listing($listing) }}">Volver a {{ $listing->name }}</a>
        <section class="mt-5 rounded-3xl border border-brand-border bg-white p-6 sm:p-9"><h1 class="font-display text-3xl font-extrabold">Reportar datos incorrectos</h1><p class="mt-4 leading-relaxed text-brand-muted">Indica qué debemos revisar en la ficha de {{ $listing->name }}. Revisaremos tu reporte antes de modificar la información. No incluyas datos personales.</p>
        @if(session('report_sent'))<p role="status" class="mt-6 rounded-xl bg-teal-50 p-4 text-teal-950">Recibimos tu reporte. Gracias por ayudarnos a revisar la información.</p>

@else

            @if($errors->any())<div role="alert" class="mt-6 rounded-xl border border-red-300 bg-red-50 p-4 text-red-950"><p class="font-semibold">Revisa el formulario.</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>
@endforeach
</ul></div>
@endif

            <form method="POST" action="{{ route('listings.report.store',['listing'=>$listing->slug]) }}" class="mt-7 grid gap-6">
                @csrf
                <input type="hidden" name="form_token" value="{{ $token }}">
                <div class="hidden" aria-hidden="true"><label>Deja este campo vacío<input name="website" tabindex="-1" autocomplete="off"></label></div>
                <div><label for="field" class="block font-semibold">Dato que debemos revisar</label><select id="field" name="field" required class="mt-2 min-h-12 w-full rounded-xl border border-brand-muted bg-white px-3"><option value="">Selecciona una opción</option>@foreach(\App\Actions\Listings\SubmitListingReport::FIELDS as $value=>$label)<option value="{{ $value }}" @selected(old('field')===$value)>{{ $label }}</option>
@endforeach
</select></div>
                <div><label for="explanation" class="block font-semibold">¿Qué información está incorrecta?</label><textarea id="explanation" name="explanation" required minlength="10" maxlength="2000" rows="6" aria-describedby="explanation-help" class="mt-2 w-full rounded-xl border border-brand-muted p-3">{{ old('explanation') }}</textarea><p id="explanation-help" class="mt-2 text-sm text-brand-muted">Entre 10 y 2000 caracteres. No compartas teléfonos, correos ni información privada de otras personas.</p></div>
                <button class="min-h-12 rounded-xl bg-brand-action px-6 py-3 font-bold text-brand-text" type="submit">Enviar reporte</button>
            </form>

@endif
</section>
    </div>
    </div>
</x-public-layout>
