# Tu Gurú Local — Plan de implementación del proyecto nuevo

**Estado:** plan en ejecución; VS00 y VS01 completaron la fase Base local, incluida la revisión visual y de teclado de la administración el 22 de octubre de 2026; VS02 completó localmente ficha Gratis, logo obligatorio, identidad de taxonomías, DEMO persistente y simplificación multimedia con fechas manuales; VS03–VS17 siguen pendientes. La [especificación maestra](ESPECIFICACION_MAESTRA.md) define R01–R10 y AC01–AC10; el [diseño técnico](DISENO_TECNICO.md) define el contrato de datos y servicios. El [tablero MEMORY.md](MEMORY.md) detalla la secuencia de entregas verticales VS00–VS17, sus estados y evidencia. Ninguna fase interna es un lanzamiento parcial.

## 0. Preparación y reglas de ejecución

Como esta carpeta documental ya ocupa el destino, generar Laravel 13 limpio en un directorio temporal vacío y fusionar solo los archivos del esqueleto en este proyecto, preservando README.md, AGENTS.md, docs/ y las capturas. Configurar PHP 8.4, MySQL, Livewire 4, Filament 5, Tailwind 4 y DaisyUI 5 según [stack](TECH-STACK.md). Confirmar versiones instaladas antes de integrar APIs. No importar código, tablas, datos, categorías, slugs ni redirecciones de la aplicación anterior. Mantener el repositorio con las fuentes documentales y las 26 capturas; excluir secretos, bases locales, archivos subidos y medios privados.

Cada tarea termina con pruebas de comportamiento enfocadas. Al cerrar una fase, ejecutar suite en MySQL, `vendor/bin/pint --test` o el chequeo no mutante equivalente en CI, `npm run build`, inspección de rutas y `git diff --check`. Cuando se escriba PHP localmente, aplicar Pint según las reglas del nuevo checkout antes de validar. Registrar qué se probó realmente; SQLite no sustituye una comprobación MySQL.

Para la revisión local de UI, abrir la aplicación servida por Herd en `http://tugurulocal-nuevo.test/`; configurar `APP_URL` en el `.env` ignorado por Git y mantener `npm run dev` activo durante la edición. Herd sirve PHP sin `php artisan serve`. En cada VSXX con pantallas nuevas o modificadas, revisar las URLs afectadas y enlazarlas desde su registro `docs/dev_changelog/VSXX.md`. La base MySQL de desarrollo y pruebas se configura por separado.

Ejecutar VS00–VS17 según las dependencias del tablero. Cada entrega funcional incluye sus datos, reglas de dominio, administración, UI pública, autorización, SEO, medición aplicable y pruebas. El sistema visual empieza en la primera pantalla; SEO y accesibilidad no se posponen a una fase final. VS00–VS01 son los habilitadores mínimos. Investigar contenido e identificar insumos externos desde el inicio; su aprobación de salida ocurre en VS16–VS17. Este documento conserva T01–T15 como agrupaciones de trazabilidad, no como una segunda secuencia de ejecución ni un registro de tareas completadas.

Cada intervención en una tarea `VSXX` comienza en una rama Git nueva antes de modificar archivos: `codex/vsXX-descripcion` para la primera y `codex/vsXX-descripcion-cont-AAAAMMDD-N` para cada continuación. Comprobar el estado de Git y partir de una base que ya contenga las dependencias cerradas y el trabajo previo de esa tarea, integrado o en su rama anterior. No mezclar tareas ni implementar directamente en `master`. Anotar todas las ramas utilizadas en el registro de la tarea y en la evidencia resumida del tablero.

## 1. Trazabilidad de tareas y evidencia

Las dependencias y checklists completos están en [MEMORY.md](MEMORY.md). Una agrupación T se satisface cuando se verifican todas sus partes en las entregas indicadas; no exige completar otra agrupación T entera como prerrequisito. Así, las listas y Lugares se implementan antes de sus bloques contextuales, sin dependencia circular. VS00 está completada localmente; las siguientes entregas conservan sus puertas de aceptación.

| ID de referencia | Entregas verticales | Contrato y resultado verificable | Prueba o revisión |
| --- | --- | --- | --- |
| **T01 · Base** | VS00 | R09: Laravel limpio, manifiestos, MySQL, entorno, PHPUnit y build; secretos fuera de Git y fechas consistentes. | Versiones, migración MySQL, suite inicial y build. |
| **T02 · Base** | VS01 | R08/AC08: panel exclusivo, primera cuenta segura, 2FA, policies y auditoría; sin autorregistro público. | Acceso, permisos directos/masivos y 2FA. |
| **T03 · Editorial** | VS02, VS12 | R01–R02: gestión desde panel de ciudad, zonas y cinco categorías, business/place, slugs y medios con plugin Filament/Spatie y etiqueta PDF editable; Lugar público se completa con hijos verificados en VS12. | Migraciones, restricciones, taxonomía, medios y vínculos en MySQL. |
| **T04 · Editorial** | VS02, VS03 | R01–R02/AC01–AC02: publicar ficha Gratis y descubrirla en ciudad/Negocios/categoría/zona con filtros y orden estable. | HTTP, estados privados, rutas, colisiones, filtros, cierre y permisos. |
| **T05 · Editorial** | VS05 | R06/AC06: recomendar, limitar y moderar a borrador; atribución consentida y datos privados protegidos. | Validación, bots, límites, duplicados y publicación separada. |
| **T06 · Transversal** | VS02–VS05, VS15 | R09/AC09: SEO y eventos desde cada superficie; consolidar URLs/sitemap en VS04, aviso de privacidad en VS05 y comprobar el conjunto en VS15. | Canonical, noindex, sitemap, 301 internos, metadatos y eventos sin PII. |
| **T07 · Comercio** | VS06, VS07 | R07–R08: planes, recibos e ingresos separados; ajustes comerciales y /premium condicional. | Pago/prueba/cortesía, vencimiento, concurrencia, autorización y configuración parcial. |
| **T08 · Comercio** | VS08, VS09 | R03/AC03: productos y ofertas en ficha/Explorar, máximo diez productos y ofertas elegibles por vigencia. | Cupo, filtros, orden, fechas, pérdida de plan y ausencia de /ofertas. |
| **T09 · Comercio y Colecciones** | VS10, VS13 | R07/AC07: campañas Super Premium en VS10; bloques contextuales después de crear listas y Lugares en VS13. Cupos, contratos y Patrocinado verificables. | Solapamientos concurrentes, elegibilidad, etiquetas, caducidad e ingresos. |
| **T10 · Colecciones** | VS11, VS13 | R04/AC04: lista con selección/orden editorial en VS11 y bloque contratado separado en VS13. | Orden, retiro de fichas, permisos e independencia comercial. |
| **T11 · Colecciones** | VS12, VS13 | R04/AC04: Lugar con hijos comprobados en VS12 y bloque contextual separado en VS13. | Vínculo único, ciudad/tipos, evidencia, estados y publicidad separada. |
| **T12 · Colecciones** | VS14 | R05/AC05: Guías locales, editor, revisiones, programación, índice/post y SEO. | Ciudad/slug/estado, sanitización, firma, permisos y enlaces. |
| **T13 · Transversal e Integración** | VS02–VS03, VS05–VS15 | R01–R09: sistema visual incorporado en cada pantalla; integración final en VS15 según [guía visual](GUIA_VISUAL.md). | Responsive, teclado, zoom, contraste, lector de pantalla y movimiento reducido. |
| **T14 · Contenido** | VS16 | R10/AC10: investigación desde el inicio; cierre del inventario 40/3/5/5/3 con fuentes, permisos y aprobación. | Checklist de [operación](CONTENIDO_Y_OPERACION.md), enlaces y medios reales. |
| **T15 · Integración y Salida** | VS15, VS17 | AC01–AC10: integración en VS15; respaldo/restauración, alojamiento, identidad, revalidación editorial y despliegue único en VS17. | Suite MySQL, Pint, build, HTTP, visual, restauración y verificación real de producción. |

## 2. Puertas de fase

- **Base completa — VS00–VS01:** migraciones limpias, panel exclusivo, 2FA y MySQL de pruebas funcional. No se crean cuentas públicas.
- **Editorial completa — VS02–VS05:** fichas Gratis, descubrimiento, URLs/SEO y recomendaciones moderadas con estados privados verificables; ningún enlace apunta a pantallas futuras. El formulario no recibe datos públicos sin aviso aprobado; el flujo puede probarse localmente mientras se obtiene el texto.
- **Comercio completo — VS06–VS10:** plan/ingreso separados, /premium condicional probada, catálogo elegible, cupos, fechas, Patrocinado y ajustes desde panel. VS07 y VS08 dependen de VS06; ambas ramas deben cerrarse. La falta de valores comerciales permite cerrar la implementación local con la puerta de /premium oculta, pero impide activarla públicamente. Los bloques contextuales quedan para Colecciones.
- **Colecciones completas — VS11–VS14:** lista manual, Lugar con hijo verificado, bloque contextual independiente y blog programable, todos con rutas, SEO y permisos.
- **Integración completa — VS15:** recorridos cruzados, retirada inmediata de beneficios y contenido inelegible, métricas privadas, responsive, accesibilidad y regresión global comprobados; no se atribuyen resultados de producción a tests locales.
- **Contenido aprobado — VS16:** inventario real 40/3/5/5/3, distribución por categorías, vínculos, fuentes, medios y textos legales revisados y aprobados según operación.
- **Salida aprobada — VS17:** revalidar inventario y vigencias, alojamiento, identidad final, privacidad, copias/restauración y pruebas integrales; desplegar una sola vez y verificar producción. Precios, IVA, WhatsApp o responsable faltantes mantienen /premium oculta; alojamiento, logotipo oficial o contenido obligatorio faltantes impiden despliegue.

## 3. Escenarios de regresión prioritarios

1. El mismo negocio cambia Gratis → Premium → Super Premium → Gratis: conserva ficha y datos, muestra/oculta catálogo y campañas en el instante correcto; los ingresos solo incluyen pagos confirmados.
2. Dos contratos intentan el tercer y cuarto cupo o la misma posición de categoría en períodos superpuestos: solo se acepta el inventario disponible, incluso con solicitudes concurrentes.
3. Una cortesía Super Premium ocupa cupo, exhibe Patrocinado y suma cero al ingreso. Un bloque de lista/Lugar no se confunde con beneficio del plan.
4. Una oferta y un post cruzan su hora de inicio/fin: no se adelantan ni permanecen visibles por fallas de cron. Los enlaces directos, sitemap y filtros reflejan el estado.
5. Una ficha deja de estar publicada o un negocio hijo cierra: desaparece de lista/vitrina/Lugar según elegibilidad sin borrar el historial editorial.
6. Un envío público carece de consentimiento, excede límite o intenta archivo/URL no válido: no publica ni filtra datos privados.
7. Se configura parcialmente /premium: página, navegación, CTA y sitemap permanecen ocultos. Se activa solo con precios, IVA, WhatsApp y responsable aprobados.
8. Una ruta específica o slug publicado cambia: no colisiona con categoría, mantiene canonical nuevo y 301 solo dentro de este proyecto; rutas del sitio anterior no adquieren redirección.
9. Página y formulario se usan con teclado, zoom 200 %, lector de pantalla y móvil con teclado virtual; carrusel se pausa y respeta movimiento reducido.

## 4. Entrega de cada fase

Al comenzar cada tarea `VSXX`, crear o abrir `docs/dev_changelog/VSXX.md`; actualizar el mismo archivo al terminar cada intervención, con fecha, explicación comprensible de cambios y motivos, verificación real y pendientes. Enlazarlo desde su sección en [MEMORY.md](MEMORY.md), donde se actualizan estado, checklist y evidencia resumida. Anotar cambios, migraciones/configuración, comandos y pruebas ejecutadas con fecha y entorno, resultados de la revisión visual y riesgos pendientes. `docs/dev_changelog/` contiene únicamente registros Markdown: no guardar imágenes ni capturas. Documentar la revisión visual en texto con URLs locales, tamaños de pantalla, interacciones comprobadas y resultados observables. Una tarea completada exige evidencia real; conservar datos privados y secretos fuera de Git. No afirmar que producción funciona por un test local. Si una puerta falla, resolver el defecto antes de continuar al lanzamiento.

