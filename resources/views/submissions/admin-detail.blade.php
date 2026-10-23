<div class="grid gap-6">
    <p>Propuesta original para revisión privada. Los enlaces se muestran como texto; no se descargan ni se incrustan.</p>
    <dl class="grid gap-4">
        @foreach(['name' => 'Nombre', 'address' => 'Ubicación', 'reason' => 'Motivo privado', 'maps_url' => 'Maps', 'evidence_url' => 'Evidencia privada', 'email' => 'Correo privado', 'whatsapp' => 'WhatsApp privado', 'instagram' => 'Instagram', 'moderation_reason' => 'Motivo privado de moderación'] as $field => $label)
            <div><dt class="font-semibold">{{ $label }}</dt><dd class="whitespace-pre-line break-words">{{ $submission->{$field} ?: 'Sin dato' }}</dd></div>
        @endforeach
        <div><dt class="font-semibold">Consentimiento de crédito</dt><dd>{{ $submission->credit_consent ? 'Sí' : 'No' }}</dd></div>
        <div><dt class="font-semibold">Moderación</dt><dd>{{ $submission->moderator?->name ?? 'Pendiente' }} {{ $submission->moderated_at?->timezone('America/Cancun')->format('d/m/Y H:i') }}</dd></div>
    </dl>
    <section>
        <h2 class="font-semibold">Posibles coincidencias</h2>
        <p>Hasta 20 fichas y 20 propuestas con nombre, ubicación o enlace normalizados coincidentes. Son señales para revisar, sin bloqueo automático.</p>
        @forelse($matches['listings'] as $match)<p>Ficha #{{ $match['id'] }}: {{ $match['name'] }} ({{ $match['status'] }})</p>@empty<p>Sin fichas coincidentes.</p>@endforelse
        @forelse($matches['submissions'] as $match)<p>Recomendación #{{ $match['id'] }}: {{ $match['name'] }} ({{ \App\Models\Submission::STATUSES[$match['status']] }})</p>@empty<p>Sin otras recomendaciones coincidentes.</p>@endforelse
    </section>
</div>
