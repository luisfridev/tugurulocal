# Implementar y comprobar el sistema visual

Fecha: 22-10-2026. Manual: [design.md](../design.md). Fuente: [Base44](https://preview--turu-guia-local.base44.app/). Esta entrega documenta la referencia y prepara su adopción; no modifica aplicación, datos ni dependencias. No es una verificación de producción.

## 1. Punto de partida y orden

Base de esta intervención: `9647d0d2d47a4d7fbf6163f4c80ad89eebeb42dd`, con PR #5 de VS03 fusionada hacia master y fetch comprobado. Rama documental: `codex/sistema-diseno-referencia`. El árbol estaba limpio al comenzar. No se reabren VS02/VS03 ni se crean registros vacíos.

El [tablero](../MEMORY.md#puerta-transversal-de-diseno-antes-de-vs04) establece dos hitos: integrar este manual por PR y, después, alinear las superficies existentes mediante una rama/PR independiente antes de VS04. La documentación verificada pendiente de fusión no se marca integrada. La alineación aún no está implementada ni verificada.

Secuencia concreta de la futura alineación:

1. Comprobar fusión del manual hacia master, sincronizar por avance rápido y crear `codex/alineacion-visual-publica`; conservar cualquier cambio pendiente.
2. Confirmar versiones y permiso para cualquier nueva dependencia de fuentes. Preparar tokens y fuentes locales, sin cambiar el tema de Filament.
3. Adaptar shell, contenedores, navegación y pie; después Inicio, listados/categorías/zonas, ficha y 404. Reutilizar componentes y mantener contratos HTTP, filtros y SEO.
4. Comprobar los estados con DEMO existente o fixtures de pruebas aislados; no reiniciar base ni volver a sembrar por motivos visuales.
5. Revisar matriz visual y accesibilidad, ejecutar pruebas afectadas y controles requeridos; documentar y entregar PR abierta. Registrar su fusión antes de habilitar el inicio de VS04.

## 2. Stack y tokens

Versiones instaladas comprobadas: Laravel 13.38.0, Livewire 4.4.8, Filament 5.10.3, Tailwind 4.3.3, DaisyUI 5.7.50, Vite 8.3.5. La aplicación usa Blade para público y fuentes Fontsource locales. No trasladar React, componentes Base44 ni una SPA.

El JSON usa un esquema propio, no declara conformidad con un estándar externo. Cada token tiene `type`, `value`, `evidence`, `purpose` y `unit` cuando corresponde. Los alias se expresan como `{reference.color.deep}`. Resolverlos a un token existente, sin ciclos; `reference` conserva originales y `semantic`/`component` expresan el contrato de adopción. No copiar las variables HSL auxiliares de cuentas sobre el tema público.

Mapeo futuro al CSS público, mostrado como especificación y no aplicado en esta entrega:

```css
@theme {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --font-display: 'Baloo 2', ui-rounded, system-ui, sans-serif;
    --color-brand-deep: #2d2652;
    --color-brand-action: #40e0d0;
    --color-brand-page: #f6f4fb;
    --color-brand-text: #1b1730;
    --color-brand-muted: #666080;
    --color-brand-premium: #a958a9;
}
```

Mantener el nombre real de la familia de los archivos instalados: si el paquete declara un nombre distinto, ajustar el alias y probarlo, sin asumir que basta con renombrar CSS. Revisar licencias y pesos en [Baloo 2](https://fontsource.org/fonts/baloo-2) e [Inter](https://fontsource.org/fonts/inter); ambas fichas consultadas indican OFL-1.1. No descargar archivos de fuente desde el sitio de referencia. Su incorporación local corresponde a la siguiente intervención y no está incluida en el lockfile actual.

Para DaisyUI, definir un tema público con nombre propio y asignarlo solo al shell público. Mapear base-100 a blanco, base-200 a página, base-content a texto, primary a menta y primary-content a índigo. Usar radios de componente explícitos: el radio de tarjeta no es el de campo. La [documentación de temas v5](https://daisyui.com/docs/themes/) permite `@plugin "daisyui/theme"`; verificar sus variables al implementar. No activar dark mode ni cambiar defaults globales de administración para obtener estos colores.

Mantener breakpoints mobile first 640/768/1024. Los utilitarios `sm:`, `md:` y `lg:` de Tailwind deben corresponder con esas fronteras; [variables de tema](https://tailwindcss.com/docs/theme) documenta el enfoque CSS-first. No introducir un tailwind.config.js de v3. Los temas de DaisyUI son soporte técnico; sus dimensiones por defecto no sustituyen el manual.

Blade debe componer presentación; dominio y autorización permanecen en app. Mantener filtros GET y mejora progresiva actual. Livewire solo se usa si un flujo aprobado necesita estado servidor; Alpine puede encargarse de interacción local apropiada. No añadir bibliotecas de iconos o animación para imitar el aspecto de la referencia.

## 3. Mapa de adaptación y diferencias actuales

| Referencia | Destino del proyecto | Diferencia intencional o trabajo pendiente |
| --- | --- | --- |
| `/` | `/{city}` | Mantener ritmo; selección editorial/recientes/zonas sustituyen ranking y métricas de ejemplo. |
| `/explorar` | `/{city}/negocios` | Convertir tarjetas verticales actuales a filas; conservar GET, Aplicar, 12 resultados, orden y limpieza por contexto. |
| Categoría en query | `/{city}/{category}` | Conservar slugs del nuevo proyecto y su identidad administrable. No copiar `comer-beber` como slug. |
| Sin página de zona equivalente | `/{city}/zonas/{area}` | Adaptación con los mismos tokens y composición de listado. |
| `/negocio/{slug}` | `/@{listing}` | Cabecera y lateral de referencia; logos propios, permisos, contactos y medios vigentes. |
| Vitrina/oferta | Catálogo Explorar, ficha y vitrinas futuras | No confundir catálogo comercial con listado general ni adelantar fases. |
| `/premium` | `/premium` condicional | Tres planes del contrato; precios/contacto reales. No publicar la pantalla por haber documentado su estilo. |
| 404 genérica | 404 del proyecto | Español, marca, retorno útil y no reflejar ruta. |
| Cuenta/beneficios | Ámbito futuro | No rutas ni funciones nuevas. Panel interno no observado. |

Componentes actuales a reutilizar: `public-layout`, `discovery-identity`, `listing-card`, `breadcrumbs`, `discovery-pagination`, vistas de descubrimiento, ficha, reporte y 404. Las diferencias de fuentes/paleta están tabuladas en el manual. El CSS actual aún contiene Nunito Sans, Instrument Sans y colores previos; no afirmar que el proyecto ya consume el JSON.

Conservar íntegramente datos, medios DEMO, permisos, auditoría, rutas canónicas, noindex y reglas de elegibilidad. Plan, orden orgánico y selección manual no se derivan de estilos. Las tarjetas orgánicas no muestran insignia de plan ni productos; un anuncio visible dice Patrocinado.

## 4. Captura reproducible

La [matriz](screenshots/manifest.json) contiene 51 combinaciones y 102 archivos base. Hay tres capturas adicionales de interacción. Todas son JPEG originales, DPR 1, recortadas explícitamente a su región, sin redimensionar después. Los archivos full incluyen la altura real del documento, conservando el ancho del viewport. Las medidas DOM se tomaron antes de la recaptura exacta; el manifiesto conserva ambos instantes.

El primer método automático produjo imágenes menores al excluir o ajustar la barra de desplazamiento; fue descartado y sustituido. No igualar tamaños mediante upscale: comprobar dimensiones del archivo contra el manifiesto. La barra puede dejar el contenido 15 px más estrecho que el viewport; comparar con la misma política de scrollbar.

Para repetir la referencia o capturar la implementación:

1. Fijar navegador, versión, viewport CSS y DPR; sesión anónima, misma preferencia de tema/movimiento e idioma. Registrar versión y sistema disponibles; no dar por idénticos motores diferentes.
2. Abrir URL exacta con query, esperar contenido esperado y después inactividad de red: h1 en ficha, al menos una fila en los listados de referencia con resultados. La red inactiva por sí sola no prueba que el render asíncrono haya terminado. Comprobar imágenes completas y no rotas. Esperar fuentes y comprobar la familia real en la implementación, además de la declarada. Si falla una espera, registrar incidencia y verificar el estado final antes de capturar.
3. Volver al inicio de página y sacar viewport y página completa. En la herramienta usada, indicar `clip: {x:0,y:0,width,height}`; para full, altura del documento y `fullPage:true`. Verificar dimensiones reales con un lector de imágenes.
4. Guardar originales, fecha UTC, zona horaria, query/estado, scroll, viewport, DPR y medidas. El nombre es `{pantalla}-{ancho}-{viewport|full}.jpg`; estados usan `estado-{caso}-{ancho}.jpg`.
5. Guardar capturas locales futuras bajo `screenshots/implementation/{revision}/` y comparaciones bajo `screenshots/comparisons/{revision}/`, sin sobrescribir los originales Base44. No crear carpetas vacías antes de tener evidencia.

Herd sirve la implementación: resolver URLs con Boost y usar el dominio local indicado en README. Durante cambios de interfaz, mantener Vite dev activo; no ejecutar artisan serve. Las capturas de esta entrega son externas: no se presenta el código local como visualmente alineado.

<a id="5-comparacion-y-correccion"></a>

## 5. Comparación y corrección

Hay dos comparaciones diferentes:

- Base44 contra adaptación: comparar regiones equivalentes y geometría compartida. El texto, fotografías, controles excluidos y módulos propios cambian deliberadamente. No calcular un aprobado global por porcentaje de píxeles entre páginas distintas.
- Implementación contra referencia local aprobada: mismo fixture DEMO, orden, estado, fecha y revisión. Sirve para detectar regresiones posteriores. Una nueva captura no se aprueba automáticamente como nueva referencia.

Por cada región, registrar captura de origen/destino, rectángulos de recorte, condición y diferencia intencional. Conservar el original y excluir de la métrica solo las regiones justificadas; no enmascarar padding, foco, solapamientos o cortes. Las páginas completas permiten detectar omisiones y pie tapado, pero no se superponen verticalmente tras retirar secciones distintas. Comparar esas secciones individualmente.

Procedimiento de diagnóstico con Python y Pillow disponibles en el entorno de QA, fuera de dependencias de la aplicación:

```sh
python3 - referencia.jpg implementacion.jpg /tmp/comparacion-visual <<'PY'
import json, sys
from pathlib import Path
from PIL import Image, ImageChops, ImageEnhance

source, actual, output = sys.argv[1:]
a = Image.open(source).convert('RGB')
b = Image.open(actual).convert('RGB')
if a.size != b.size:
    raise SystemExit(f'Dimensiones distintas: {a.size} / {b.size}; revisar captura o recorte')
out = Path(output)
out.mkdir(parents=True, exist_ok=True)
Image.blend(a, b, 0.5).save(out / 'overlay.png')
diff = ImageChops.difference(a, b)
ImageEnhance.Contrast(diff).enhance(4).save(out / 'diff.png')
channels = diff.tobytes()
pixels = zip(channels[0::3], channels[1::3], channels[2::3])
report = {
    'size': a.size,
    'mean_absolute_channel_difference': sum(channels) / len(channels),
    'fraction_pixels_over_8': sum(max(p) > 8 for p in pixels) / (a.width * a.height),
    'interpretation': 'Diagnóstico, no umbral automático de aceptación',
}
(out / 'metrics.json').write_text(json.dumps(report, indent=2) + '\n')
print(json.dumps(report))
PY
```

El umbral 8 solo reduce ruido en el indicador diagnóstico; no autoriza diferencias. Los originales JPEG incorporan compresión y antialiasing: no exigir igualdad binaria al compararlos con PNG ni alterar el diseño para reproducir artefactos. Usar PNG sin pérdida para las referencias locales futuras si la herramienta lo permite, o conservar el formato disponible y declararlo.

Corregir en este orden: contenedor/gutters y retícula; fuentes/pesos/interlineado; padding/gaps/altura; color y contraste; radio/borde/sombra; foco y estados. Volver a capturar solo las regiones afectadas y sus tamaños, sin ampliar la suite salvo cambios o fallos nuevos.

Aceptar una región cuando tokens y estilos configurados coincidan con el contrato, la geometría esperada no presente desplazamientos inexplicados, todas las diferencias intencionales estén registradas y el recorrido funcione. Un parecido visual no compensa errores de navegación, contraste, contenido o privacidad.

## 6. Evidencia de esta extracción y revisión pendiente

Ejecutado: navegación anónima por 17 páginas; 51 combinaciones; comparación de fronteras 639/640, 767/768, 1023/1024 en Inicio; lectura de CSS calculado y reglas; siete comprobaciones de interacción; cálculo de parejas de contraste; dimensiones exactas de los JPEG y revisión visual de sus hojas de contacto.

Límites explícitos: familias de fuente declaradas, sin certificación de archivo por glifo; CSSOM externo de Google Fonts inaccesible; hover/active solo declarados; reset válido y panel interno no accesibles anónimamente; carga/vacío/error remoto/disabled no auditados de forma exhaustiva. No se enviaron formularios, llamadas, mensajes, votos ni OAuth.

La alineación debe verificar además zoom real 200 %, todos los controles con teclado, lector de pantalla, foco sobre cada fondo, teclado virtual/dispositivo cuando esté disponible, pausa/movimiento reducido si se añade el carrusel, estados de datos y contenido largo. Conservar estos puntos como pendientes hasta ejecutarlos. Backend o formularios nuevos llevarán las pruebas de su VSXX; no se anticipa su funcionamiento.

El registro de revisión final de esta entrega está en [delivery-review.md](delivery-review.md), con resultados por criterio y límites. La PR documental y el tablero registran integración pendiente; crear el manual no completa la alineación visual ni VS04.
