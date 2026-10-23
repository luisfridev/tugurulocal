# Tu Gurú Local — Plan de implementación del proyecto nuevo

Este plan define la secuencia y las comprobaciones de las entregas verticales VS00–VS17. La [especificación maestra](ESPECIFICACION_MAESTRA.md) define R01–R10 y AC01–AC10; el [diseño técnico](DISENO_TECNICO.md) define el contrato de datos y servicios. El [tablero MEMORY.md](MEMORY.md#secuencia-y-dependencias) resume los estados y dependencias, los registros por tarea conservan las comprobaciones y GitHub acredita las integraciones. Ninguna fase interna es un lanzamiento parcial.

## 0. Preparación y reglas de ejecución

Como esta carpeta documental ya ocupa el destino, generar Laravel 13 limpio en un directorio temporal vacío y fusionar solo los archivos del esqueleto en este proyecto, preservando README.md, AGENTS.md, docs/ y las capturas. Configurar PHP 8.4, MySQL, Livewire 4, Filament 5, Tailwind 4 y DaisyUI 5 según [stack](TECH-STACK.md). Confirmar versiones instaladas antes de integrar APIs. No importar código, tablas, datos, categorías, slugs ni redirecciones de la aplicación anterior. Mantener el repositorio con las fuentes documentales y las 26 capturas; excluir secretos, bases locales, archivos subidos y medios privados.

Cada tarea termina con pruebas de comportamiento enfocadas. Al cerrar una fase, ejecutar suite en MySQL, `vendor/bin/pint --test` o el chequeo no mutante equivalente en CI, `npm run build`, inspección de rutas y `git diff --check`. Cuando se escriba PHP localmente, aplicar Pint según las reglas del nuevo checkout antes de validar. Registrar qué se probó realmente; SQLite no sustituye una comprobación MySQL.

Para la revisión local de UI, abrir la aplicación servida por Herd en `http://tugurulocal-nuevo.test/`; configurar `APP_URL` en el `.env` ignorado por Git y mantener `npm run dev` activo durante la edición. Herd sirve PHP sin `php artisan serve`. En cada VSXX con pantallas nuevas o modificadas, revisar las URLs afectadas y enlazarlas desde su registro `docs/dev_changelog/VSXX.md`. La base MySQL de desarrollo y pruebas se configura por separado.

Ejecutar VS00–VS17 según las dependencias del tablero. Cada entrega funcional incluye sus datos, reglas de dominio, administración, UI pública, autorización, SEO, medición aplicable y pruebas. El sistema visual empieza en la primera pantalla; SEO y accesibilidad no se posponen a una fase final. VS00–VS01 son los habilitadores mínimos. Investigar contenido e identificar insumos externos desde el inicio; su aprobación de salida ocurre en VS16–VS17. Este documento conserva T01–T15 como agrupaciones de trazabilidad, no como una segunda secuencia de ejecución ni un registro de tareas completadas.

Cada entrega, normalmente una `VSXX`, usa una rama y una PR hasta su fusión. Antes de editar, comprobar Git y las dependencias, y localizar la rama y PR existentes. Reutilizarlas entre sesiones para la implementación, documentación, pruebas y correcciones de revisión, conservando los cambios sin confirmar. Crear `codex/vsXX-descripcion` desde `master` sincronizado solo al comenzar una entrega nueva. Una continuación no exige nueva rama ni sufijo; conservar los nombres de las ramas activas existentes. Una entrega independiente o una corrección posterior a la fusión requiere otra rama y PR con nombre descriptivo. No mezclar implementaciones de tareas distintas ni trabajar directamente en `master`. Registrar la rama usada en el changelog y el tablero.

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

Pedir el cierre de una `VSXX` significa preparar los cambios locales verificados para revisión del propietario. Por decisión del propietario del 23 de octubre de 2026, hacer commit, push y crear o actualizar una PR requiere su instrucción explícita para cada acción y alcance. Implementar, verificar, corregir o pedir el cierre no concede esa autorización. El flujo vigente es:

1. Completar implementación, comprobaciones aplicables y documentación en la rama de trabajo, conservando la evidencia real y los pendientes.
2. Entregar los cambios locales sin confirmar, explicar qué cambió, qué se comprobó y qué falta, y dejar que el propietario los revise. El propietario decide cuándo autorizar los commits, el push y la creación o actualización de la PR; puede indicar varias acciones juntas. Autorizar un commit no autoriza el push ni una PR. Las correcciones posteriores también quedan locales hasta una nueva instrucción que las incluya.
3. Ejecutar solo las acciones autorizadas. Para publicar, subir únicamente la rama de trabajo y reutilizar la PR existente; si autoriza crearla, abrirla hacia `master` con título `VSXX: descripción`. La PR reúne cambios, pruebas, documentación y pendientes de esa entrega.
4. Cuando exista PR, registrar número, URL y rama en `docs/MEMORY.md` y `docs/dev_changelog/VSXX.md`; confirmar y subir ese registro solo si el alcance autorizado lo incluye. Adjuntar la PR al chat y entregar su enlace. Una PR existente no autoriza actualizaciones automáticas.
5. Dejar la PR abierta. El propietario decide cuándo fusionarla. El agente no fusiona la PR, no integra la rama directamente en `master` ni hace push a `master`.

Mantener la tarea `en curso` hasta comprobar su integración. Con las comprobaciones completas y los cambios aún locales, indicar «implementación verificada; revisión local del propietario pendiente». Si existe PR sin fusionar, indicar «implementación verificada; integración pendiente en PR» y distinguir cualquier cambio local sin publicar. No declarar integración ni cierre completo por revisar los cambios, crear el commit, subir la rama o abrir la PR.

Después del merge, comprobar en GitHub que la PR esté fusionada hacia `master` y sincronizar `master` local solo por avance rápido desde `origin/master`. Con la aceptación satisfecha y la integración comprobada, la dependencia queda cerrada y se puede iniciar la siguiente tarea aunque el tablero todavía indique integración pendiente. Informar del resultado en el chat. Nunca escribir ni publicar directamente en `master`.

No abrir una PR solo para registrar un merge, cambiar un estado del tablero o copiar metadatos, aunque cierre una fase o desbloquee trabajo. Actualizar el tablero y la evidencia del cierre anterior dentro de la siguiente PR con trabajo sustantivo. Esta actualización documental puede acompañar la siguiente VSXX sin mezclar sus implementaciones. Si no hay otra entrega prevista, GitHub y la confirmación en el chat conservan el cierre; no se crea una PR de registro. Las PR documentales con un cambio propio de contrato o de flujo sí son entregas independientes.

GitHub es la fuente de verdad para estado, fecha y commit de integración; MEMORY resume el avance y el changelog de cada tarea conserva sus cambios y comprobaciones. README y documentos de contrato enlazan estas fuentes sin exigir copiar cada merge. Conservar los registros históricos, incluidas sus ramas de continuación y PR de cierre, como evidencia del flujo anterior; no repetir ni rehacer esas entregas.

| Situación | Acción |
| --- | --- |
| Retomar la misma entrega antes de fusionarla | Reutilizar rama y PR, aunque cambie la sesión. |
| Implementación verificada o petición de cierre | Entregar cambios locales sin confirmar para revisión; esperar la instrucción de commit, push o PR. |
| Corregir una observación de revisión | Corregir y verificar localmente; incorporar a la misma PR solo cuando el propietario autorice las acciones correspondientes. |
| Autorización solo para commit | Crear el commit del alcance indicado; conservarlo local sin push ni cambios en la PR. |
| Dependencia con PR todavía abierta o aceptación pendiente | Mantener la dependencia sin cerrar; no iniciar la tarea dependiente. |
| PR fusionada y aceptación satisfecha | Comprobar GitHub, sincronizar master por avance rápido y continuar la dependencia. |
| Tablero pendiente de reflejar el merge | Actualizarlo en la siguiente entrega; no bloquear trabajo por ese desfase. |
| Solo hay metadatos o un estado que actualizar | No crear PR; conservar la evidencia en GitHub hasta la siguiente entrega. |
| Última entrega fusionada, sin otra entrega prevista | Confirmar en GitHub y en el chat; no crear PR para cerrar el registro. |
| Corrección posterior a la fusión o entrega independiente | Usar otra rama para ese cambio sustantivo; crear su PR cuando el propietario lo autorice. |

## 1. Trazabilidad de tareas y evidencia

Las dependencias y checklists completos están en [MEMORY.md](MEMORY.md). Una agrupación T se satisface cuando se verifican todas sus partes en las entregas indicadas; no exige completar otra agrupación T entera como prerrequisito. Así, las listas y Lugares se implementan antes de sus bloques contextuales, sin dependencia circular. VS00 está completada localmente; las siguientes entregas conservan sus puertas de aceptación.

| ID de referencia | Entregas verticales | Contrato y resultado verificable | Prueba o revisión |
| --- | --- | --- | --- |
| **T01 · Base** | VS00 | R09: Laravel limpio, manifiestos, MySQL, entorno, PHPUnit y build; secretos fuera de Git y fechas consistentes. | Versiones, migración MySQL, suite inicial y build. |
| **T02 · Base** | VS01 | R08/AC08: panel exclusivo, primera cuenta segura, 2FA, policies y auditoría; sin autorregistro público. | Acceso, permisos directos/masivos y 2FA. |
| **T03 · Editorial** | VS02, VS12 | R01–R02: gestión desde panel de ciudad, zonas y cinco categorías, business/place, slugs y medios con plugin Filament/Spatie y etiqueta PDF editable; Lugar público se completa con hijos verificados en VS12. | Migraciones, restricciones, taxonomía, medios y vínculos en MySQL. |
| **T04 · Editorial** | VS02, VS03 | R01–R02/AC01–AC02: publicar ficha Gratis y descubrirla en ciudad/Negocios/categoría/zona con filtros y orden estable. | HTTP, estados privados, rutas, colisiones, filtros, cierre y permisos. |
| **T05 · Editorial** | VS05 | R06/AC06: recomendar, limitar y moderar a borrador; atribución consentida y datos privados protegidos. | Validación, bots, límites, duplicados y publicación separada. |
| **T06 · Transversal** | VS02–VS05, VS15 | R09/AC09: SEO y eventos desde cada superficie; consolidar URLs/sitemap en VS04 y comprobar el conjunto en VS15. Aviso, conservación y eliminación después de la primera publicación, sin bloquear formularios. | Canonical, noindex, sitemap, 301 internos, metadatos y eventos sin PII. |
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
- **Editorial completa (VS02–VS05):** fichas Gratis, descubrimiento, URLs/SEO y recomendaciones moderadas con estados privados verificables; ningún enlace apunta a pantallas futuras. Recomendaciones y reportes públicos funcionan desde la primera versión sin aviso ni aprobación previa. Evidencia por enlace opcional, sin adjuntos. VS05 no despliega.
- **Comercio completo — VS06–VS10:** plan/ingreso separados, /premium condicional probada, catálogo elegible, cupos, fechas, Patrocinado y ajustes desde panel. VS07 y VS08 dependen de VS06; ambas ramas deben cerrarse. La falta de valores comerciales permite cerrar la implementación local con la puerta de /premium oculta, pero impide activarla públicamente. Los bloques contextuales quedan para Colecciones.
- **Colecciones completas — VS11–VS14:** lista manual, Lugar con hijo verificado, bloque contextual independiente y blog programable, todos con rutas, SEO y permisos.
- **Integración completa — VS15:** recorridos cruzados, retirada inmediata de beneficios y contenido inelegible, métricas privadas, responsive, accesibilidad y regresión global comprobados; no se atribuyen resultados de producción a tests locales.
- **Contenido aprobado (VS16):** inventario real 40/3/5/5/3, distribución por categorías, vínculos, fuentes y medios revisados y aprobados según operación. El aviso y la conservación/eliminación quedan para después de la primera publicación.
- **Salida aprobada (VS17):** revalidar inventario y vigencias, alojamiento, identidad final, protección de datos privados, copias/restauración y pruebas integrales; desplegar una sola vez y verificar producción. El aviso y conservación/eliminación se preparan después. Precios, IVA, WhatsApp o responsable faltantes mantienen /premium oculta; alojamiento, logotipo oficial o contenido obligatorio faltantes impiden despliegue.

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

En cada intervención, registrar también modo Antislop, skills aplicadas y motivo, resultados por criterio y pendientes de la [revisión final](#antislop-en-la-ejecución) en el mismo registro VSXX, con evidencia resumida en el tablero. Para una actualización general del flujo, usar el tablero y, cuando el propietario autorice su publicación, la PR correspondiente, sin abrir una VSXX ni crear registros vacíos.

Entregar primero los cambios locales verificados para revisión del propietario según el [flujo de cierre](#cierre-mediante-pull-requests). Hacer commit, push y crear o actualizar la PR solo por su instrucción explícita. Cuando exista PR, registrar sus datos en ambos documentos y enlazarla en el chat. Distinguir la implementación verificada de la integración comprobada: la tarea sigue `en curso` hasta verificar su fusión.

