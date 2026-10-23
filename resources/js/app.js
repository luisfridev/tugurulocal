document.querySelectorAll('[data-share-url]').forEach((button) => {
    button.addEventListener('click', async () => {
        const status = document.querySelector('[data-share-status]');
        try {
            if (navigator.share) {
                await navigator.share({ title: button.dataset.shareTitle, url: button.dataset.shareUrl });
            } else {
                await navigator.clipboard.writeText(button.dataset.shareUrl);
                status.textContent = 'Enlace copiado.';
            }
        } catch (error) {
            if (error.name !== 'AbortError') status.textContent = 'Puedes copiar el enlace del campo de abajo.';
        }
    });
});

document.querySelectorAll('[data-discovery-filters]').forEach((form) => {
    const button = form.querySelector('[data-filter-submit]');
    const status = form.querySelector('[data-filter-status]');

    form.addEventListener('submit', () => {
        form.setAttribute('aria-busy', 'true');
        button.textContent = 'Aplicando…';
        status.textContent = 'Cargando negocios con los filtros seleccionados.';
    });

    window.addEventListener('pageshow', () => {
        form.removeAttribute('aria-busy');
        button.textContent = 'Aplicar';
        status.textContent = '';
    });
});

document.querySelectorAll('[data-public-form]').forEach((form) => {
    const button = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-public-form-status]');
    const label = button.textContent;

    form.addEventListener('submit', () => {
        form.setAttribute('aria-busy', 'true');
        button.textContent = 'Enviando recomendación…';
        status.textContent = 'Estamos enviando tu propuesta. Espera la confirmación.';
    });
    window.addEventListener('pageshow', () => {
        form.removeAttribute('aria-busy');
        button.textContent = label;
        status.textContent = '';
    });
});

document.getElementById('form-errors')?.focus();
