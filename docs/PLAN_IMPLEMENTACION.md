# Tu Gurú Local — Plan de implementación del proyecto nuevo

**Estado:** plan en ejecución; VS00 y VS01 completaron la fase Base local, incluida la revisión visual y de teclado de la administración el 22 de octubre de 2026; VS02 completó localmente ficha Gratis, logo obligatorio, identidad de taxonomías, DEMO persistente y simplificación multimedia con fechas manuales. VS03 está completada e integrada en `master` mediante PR #4, merge `78e67bd`, comprobado el 22-10-2026. VS04 está en curso: implementación verificada localmente, pendiente de integración mediante PR. VS05–VS17 siguen pendientes. La [especificación maestra](ESPECIFICACION_MAESTRA.md) define R01–R10 y AC01–AC10; el [diseño técnico](DISENO_TECNICO.md) define el contrato de datos y servicios. El [tablero MEMORY.md](MEMORY.md) detalla la secuencia de entregas verticales VS00–VS17, sus estados y evidencia. Ninguna fase interna es un lanzamiento parcial.

## 0. Preparación y reglas de ejecución

Como esta carpeta documental ya ocupa el destino, generar Laravel 13 limpio en un directorio temporal vacío y fusionar solo los archivos del esqueleto en este proyecto, preservando README.md, AGENTS.md, docs/ y las capturas. Configurar PHP 8.4, MySQL, Livewire 4, Filament 5, Tailwind 4 y DaisyUI 5 según [stack](TECH-STACK.md). Confirmar versiones instaladas antes de integrar APIs. No importar código, tablas, datos, categorías, slugs ni redirecciones de la aplicación anterior. Mantener el repositorio con las fuentes documentales y las 26 capturas; excluir secretos, bases locales, archivos subidos y medios privados.

Cada tarea termina con pruebas de comportamiento enfocadas. Al cerrar una fase, ejecutar suite en MySQL, `vendor/bin/pint --test` o el chequeo no mutante equivalente en CI, `npm run build`, inspección de rutas y `git diff --check`. Cuando se escriba PHP localmente, aplicar Pint según las reglas del nuevo checkout antes de validar. Registrar qué se probó realmente; SQLite no sustituye una comprobación MySQL.

Para la revisión local de UI, abrir la aplicación servida por Herd en `http://tugurulocal-nuevo.test/`; configurar `APP_URL` en el `.env` ignorado por Git y mantener `npm run dev` activo durante la edición. Herd sirve PHP sin `php artisan serve`. En cada VSXX con pantallas nuevas o modificadas, revisar las URLs afectadas y enlazarlas desde su registro `docs/dev_changelog/VSXX.md`. La base MySQL de desarrollo y pruebas se configura por separado.

Ejecutar VS00–VS17 según las dependencias del tablero. Cada entrega funcional incluye sus datos, reglas de dominio, administración, UI pública, autorización, SEO, medición aplicable y pruebas. El sistema visual empieza en la primera pantalla; SEO y accesibilidad no se posponen a una fase final. VS00–VS01 son los habilitadores mínimos. Investigar contenido e identificar insumos externos desde el inicio; su aprobación de salida ocurre en VS16–VS17. Este documento conserva T01–T15 como agrupaciones de trazabilidad, no como una segunda secuencia de ejecución ni un registro de tareas completadas.

Cada intervención en una tarea `VSXX` comienza en una rama Git nueva antes de modificar archivos: `codex/vsXX-descripcion` para la primera y `codex/vsXX-descripcion-cont-AAAAMMDD-N` para cada continuación. Comprobar el estado de Git y partir de una base que ya contenga las dependencias cerradas y el trabajo previo de esa tarea, integrado o en su rama anterior. No mezclar tareas ni implementar directamente en `master`. Anotar todas las ramas utilizadas en el registro de la tarea y en la evidencia resumida del tablero.

### Puerta de diseño previa a VS04

Por decisión del propietario del 22-10-2026, antes de VS04 se integraron dos entregas transversales separadas: el [manual visual](design.md) con tokens, componentes y capturas de Base44 (PR #6) y la alineación de Inicio, listados/categorías/zonas, fichas y 404 (PR #8). El [tablero](MEMORY.md#puerta-transversal-de-diseno-antes-de-vs04) registra su integración y evidencia. No renumeran ni reabren VS00–VS03. Cuatro comprobaciones asistidas de accesibilidad quedaron para VS15 y siguen pendientes; la integración no acredita producción.

design.md es la autoridad visual pública; la maestra conserva la funcional y el stack la técnica. GUIA_VISUAL remite al manual. Filament mantiene sus patrones. El panel de usuario se reserva al futuro, sin incorporar cuentas al lanzamiento. Las capturas históricas solo sirven de apoyo; la fuente principal es el sitio Base44 identificado en el manual.

En cada trabajo con UI pública, leer el manual y los tokens, registrar revisión/base y componentes afectados, y aplicar el [procedimiento de comparación](design-ref/implementation-guide.md#5-comparacion-y-correccion). Capturar móvil/tableta/escritorio en condiciones equivalentes, explicar adaptaciones, revisar foco/contraste/estados y conservar pendientes reales. Una regla visual nueva actualiza manual, tokens y evidencia en la misma PR. VS15 revisa el conjunto con la referencia local aprobada tras la alineación.

### Intervención VS03 (22-10-2026)

La rama `codex/vs03-descubrir-negocios` parte de la base integrada `b59d920`. La continuación `codex/vs03-descubrir-negocios-cont-20261022-1` amplía el DEMO persistente a 30 negocios públicos y conserva los dos privados, los registros y medios anteriores. La continuación `-2` registra la confirmación del propietario sobre filtros sin JavaScript y la `-3` entrega la implementación mediante [PR #4](https://github.com/luisfridev/tugurulocal/pull/4), fusionada hacia `master` el 22-10-2026 a las 16:17:35 (America/Cancun), merge `78e67bd3cee9e7b5389161ddb106ba354b306fef`. La continuación `-4`, desde esa base integrada, registra la comprobación en GitHub y la sincronización local mediante `git merge --ff-only origin/master`. VS03 está completada; [su registro](dev_changelog/VS03.md) y el tablero conservan la evidencia local y de integración, sin acreditar producción.

La implementación incorpora Inicio, Negocios y páginas de categoría/zona, filtros GET con «Aplicar», 12 resultados por página y orden orgánico estable. Comparte la elegibilidad del logo antes de contar o paginar. Añade selección manual de hasta seis negocios desde Ciudades, separada de listas y comercio, identidad pública autorizada, metadatos y eventos de descubrimiento sin datos personales.

Las migraciones crean la relación ordenada de Inicio y amplían los eventos existentes. Se ejecutan incrementalmente, conservando cuentas, datos y medios DEMO. No requieren `migrate:fresh` ni volver a sembrar el contenido. El cierre debe comprobar la regresión VS02, medios revocados, selección y permisos, filtros/paginación/SEO, deduplicación de eventos y recorridos de Herd con teclado, móvil, tableta, escritorio y zoom. Registrar solo resultados ejecutados, incluida la revisión final Antislop, antes de declarar la implementación verificada.

VS04 conserva sitemap y redirecciones internas y ya tiene su dependencia VS03 cerrada; Lugares públicos espera VS12. Los módulos de comercio, catálogo, recomendaciones y listas no se adelantan. La PR #10 registra la integración de la #9 y actualiza el flujo general de PR; no inicia ni reabre una VSXX.

### Antislop en la ejecución

Antes de planificar o editar una tarea, aplica la [regla Antislop por tarea](../AGENTS.md#antislop-por-tarea): carga la base y selecciona los complementos por el trabajo real, combinándolos cuando corresponda. Usa `during` como valor del proyecto salvo elección explícita del usuario para la sesión, anuncia las skills y su motivo y conserva la configuración global. Resuelve sus archivos desde el catálogo de la sesión, sin copiar skills ni guardar rutas personales en Git. Si falta una skill necesaria, informa la revisión pendiente y continúa las partes independientes.

La documentación usa `antislop-copywriting`. Una pantalla responsive combina `antislop-ui`, `antislop-human` y `antislop-layoutmobile`, y añade copywriting si se trabajan textos. El backend conserva las skills técnicas y solo incorpora complementos por los textos, comentarios o interfaces afectados; `antislop-code` se limita a comentarios. La selección se hace por intervención, porque una misma VSXX puede incluir trabajos distintos.

Para UI pública, parte de [design.md](design.md), declara la interpretación y los valores ENERGY/RHYTHM/MOTION, y explica las decisiones visuales relevantes. Filament conserva sus patrones administrativos. La maestra y las decisiones del propietario conservan su autoridad: Antislop no añade funciones ni sustituye las skills técnicas o el sistema visual aprobado.

Antes de entregar, ejecuta la revisión final de Antislop y las listas de los complementos usados. Registra cada criterio aplicable como `PASS` con evidencia o `FAIL` con el defecto; usa `no aplica` con motivo para lo ajeno al alcance y deja pendientes las comprobaciones no ejecutadas. Corrige los fallos antes de declarar la revisión superada. En UI conserva las comprobaciones de Herd, móvil, tableta, escritorio, teclado, foco, contraste, estados e interacciones y las pruebas técnicas correspondientes. Un cambio exclusivamente documental requiere revisión de redacción, coherencia, enlaces y `git diff --check`; no requiere repetir PHPUnit, Pint ni build. Un cambio de backend sin UI no exige revisión visual.

### Cierre mediante pull requests

Desde el 22 de octubre de 2026, pedir el cierre de una `VSXX` significa preparar y dejar abierta una PR hacia `master` para revisión del propietario. El flujo es:

1. Completar implementación, comprobaciones aplicables y documentación en la rama de trabajo, conservando la evidencia real y los pendientes.
2. Crear los commits, subir únicamente esa rama y abrir una PR con título `VSXX: descripción`. Su descripción explica los cambios, las pruebas ejecutadas con sus resultados y lo pendiente.
3. Registrar número, URL y rama de la PR en `docs/MEMORY.md` y `docs/dev_changelog/VSXX.md`; confirmar y subir ese registro a la misma rama para incluirlo en la PR. Adjuntar la PR al chat y entregar su enlace.
4. Dejar la PR abierta. El propietario decide cuándo fusionarla. El agente no fusiona la PR, no integra la rama directamente en `master` ni hace push a `master`.

Mientras la PR no esté fusionada, mantener la tarea `en curso`, con «implementación verificada; integración pendiente en PR», aunque las comprobaciones locales hayan pasado. No declarar integración ni cierre completo por crear el commit, subir la rama o abrir la PR.

Después del merge, comprobar en GitHub que la PR esté fusionada hacia `master` y sincronizar `master` local solo por avance rápido desde `origin/master`. GitHub es la fuente de verdad para estado, fecha y commit. Actualizar el tablero en una rama y mediante PR cuando el cambio cierre una fase o sea necesario para desbloquear una dependencia. Si la confirmación no cambia el tablero ni habilita trabajo, no abrir una PR solo para copiar metadatos; conservarlos en GitHub e incluirlos en la siguiente actualización documental sustantiva. Nunca escribir ni publicar directamente en `master`.

Si hace falta una PR dedicada para actualizar un estado que cierre una fase o desbloquee trabajo, esa PR será de registro terminal: al fusionarse, verificarla y sincronizar `master`, sin abrir otra PR solo para documentar su propio merge. Una PR cuyo único propósito es registrar una integración tampoco genera otra PR de registro. Los cierres anteriores conservan su evidencia histórica; no rehacer la integración de VS02 ni crear una PR retrospectiva. Las dependencias de las siguientes VSXX requieren la integración comprobada de sus predecesoras.

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
| **T13 · Transversal e Integración** | VS02–VS03, VS05–VS15 | R01–R09: sistema visual público incorporado en cada pantalla; alineación previa a VS04 e integración final en VS15 según [design.md](design.md). | Responsive, teclado, zoom, contraste, lector de pantalla y movimiento reducido. |
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

En cada intervención, registrar también modo Antislop, skills aplicadas y motivo, resultados por criterio y pendientes de la [revisión final](#antislop-en-la-ejecución) en el mismo registro VSXX, con evidencia resumida en el tablero. Para una actualización general del flujo, usar el tablero y la PR correspondiente, sin abrir una VSXX ni crear registros vacíos.

Entregar el cierre mediante la [PR abierta para revisión del propietario](#cierre-mediante-pull-requests), con número, URL y rama registrados en ambos documentos y el enlace en el chat. Distinguir la implementación verificada de la integración comprobada: una PR sin fusionar conserva la tarea `en curso`.

