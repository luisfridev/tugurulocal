# Tu Gurú Local — Tablero de entregas verticales

**Fecha:** 23 de octubre de 2026. **Estado:** VS00–VS03 completadas localmente; VS03 integrada en `master` mediante PR #4; fase Base cerrada y Editorial en curso; VS04–VS17 pendientes. No hay lanzamiento ni verificación de producción.

## Uso, autoridad y cierre

La [especificación maestra](ESPECIFICACION_MAESTRA.md) manda en producto; el [diseño técnico](DISENO_TECNICO.md) define los contratos y el [plan de implementación](PLAN_IMPLEMENTACION.md) conserva fases, puertas y trazabilidad T01–T15. Este archivo registra el orden operativo VS00–VS17, sus dependencias y evidencia. Si cambia un contrato, actualizar los documentos afectados antes de implementarlo.

Una entrega funcional conecta esquema y relaciones, acciones/consultas de dominio, administración Filament, experiencia pública, autorización y pruebas observables. SEO, medición aplicable, privacidad, medios autorizados y [sistema visual público](design.md) se incorporan al crear cada superficie; VS04 y VS15 completan y verifican el conjunto. VS00–VS01 son habilitadores técnicos mínimos, no fases para construir todas las capas por separado. Filament conserva sus patrones administrativos.

Estados permitidos: `pendiente`, `en curso`, `bloqueada`, `completada`. Marcar `en curso` solo al empezar la ejecución con dependencias cerradas; una tarea `bloqueada` identifica causa, responsable de resolución y condición para continuar. En los próximos cierres, marcar `completada` solo con checklist y aceptación satisfechos, evidencia real y fusión de su PR hacia `master` comprobada en GitHub. Mientras la PR no esté fusionada, conservar `en curso` e indicar «implementación verificada; integración pendiente en PR». Los campos de evidencia permanecen vacíos de resultados hasta ejecutar verificaciones; no sustituirlos por expectativas. No guardar secretos, datos personales ni evidencia privada en este archivo.

Antes de cada intervención en `VSXX`, comprobar el estado de Git y crear una rama nueva exclusiva antes de editar: `codex/vsXX-descripcion` para la primera y `codex/vsXX-descripcion-cont-AAAAMMDD-N` para las continuaciones. Partir de una base que incluya dependencias cerradas y trabajo previo de la misma tarea: la base integrada cuando ya contiene ese trabajo, o la rama anterior de `VSXX` si aún no se integró. No trabajar tareas VSXX en `master` ni mezclar tareas distintas en una rama. Conservar los cambios sin confirmar al cambiar de rama y anotar cada rama usada en el registro y la evidencia de la tarea.

**Cierre por PR (22-10-2026):** después de verificar y documentar la implementación, crear los commits, subir únicamente la rama de trabajo y abrir una PR hacia `master` con título `VSXX: descripción`, cambios, pruebas y pendientes. Registrar número, URL y rama en esta tarea y en `docs/dev_changelog/VSXX.md`, subiendo ese registro a la misma PR; adjuntarla al chat y entregar el enlace. El agente deja la PR abierta para que el propietario la revise y fusione: no fusiona, no integra directamente en `master` ni hace push a `master`. Al retomar el trabajo después de la fusión, comprobarla en GitHub, sincronizar `master` local por avance rápido desde `origin/master` y registrar fecha y commit reales antes de marcar `completada`; la actualización documental también se entrega en rama y PR. Los cierres anteriores mantienen su evidencia histórica, incluido VS02, sin rehacer su integración ni crear una PR retrospectiva. [Flujo completo](PLAN_IMPLEMENTACION.md#cierre-mediante-pull-requests).

**Actualización documental del flujo (22-10-2026):** rama `codex/flujo-cierre-vsxx-pr`; [PR #2](https://github.com/luisfridev/tugurulocal/pull/2) entregada abierta para revisión y fusionada hacia `master` el 22 de octubre de 2026 a las 14:40:49 (America/Cancun), commit `878b5faeac0eff4aa5e9e9b911cefce764ce168f`. Integración comprobada en GitHub y `master` local sincronizado por avance rápido desde `origin/master` el mismo día. Cambio exclusivo de las reglas generales; no modifica los cierres históricos de las VSXX.

Antes de cada intervención, seleccionar las skills según la [regla Antislop por tarea](../AGENTS.md#antislop-por-tarea) y el [procedimiento del plan](PLAN_IMPLEMENTACION.md#antislop-en-la-ejecución). Usar `during` como valor del proyecto salvo elección explícita del usuario para la sesión. Registrar modo, skills y motivo, resultados por criterio aplicable y pendientes en el registro de la VSXX, con evidencia resumida aquí. La base y los complementos pertinentes se suman a las skills técnicas; las revisiones visuales corresponden al trabajo con UI.

**Actualización Antislop del flujo (22-10-2026):** rama `codex/flujo-antislop-por-tarea`, creada desde `master` sincronizado en `878b5faeac0eff4aa5e9e9b911cefce764ce168f`. Incorpora selección de skills, modo `during` del proyecto y revisión final por alcance en AGENTS, README y plan. Skills aplicadas: `antislop` como base y `antislop-copywriting` para las instrucciones en español. Conserva la configuración global, las skills instaladas y el bloque generado por Boost. Cambio documental general, sin iniciar una VSXX ni modificar sus cierres históricos. [PR #3](https://github.com/luisfridev/tugurulocal/pull/3) fusionada hacia `master` el 22 de octubre de 2026 a las 14:58:35 (America/Cancun), commit `b59d92010b13fcb46e34c3cbb20c92c6afd836a0`. Integración comprobada en GitHub al iniciar VS03; `master` local sincronizado por avance rápido desde `origin/master` antes de crear su rama.

Revisión local de esta actualización, 22 de octubre de 2026:

| Criterio | Resultado y evidencia |
| --- | --- |
| Hechos y afirmaciones (R-17, R-18, R-36, R-38; C-5) | PASS: nombres y alcances contrastados con los `SKILL.md` instalados; fecha y commit de la PR #2 comprobados en GitHub; sin cifras comerciales ni testimonios añadidos. |
| Puntuación (R-02) | PASS: inspección del texto añadido sin rayas largas; texto histórico conservado. |
| Precisión (R-16) | PASS: la matriz nombra tareas y skills concretas; las instrucciones definen inicio, revisión y evidencia, sin promesas de resultados del producto. |
| Comillas y énfasis (copywriting) | PASS: comillas reservadas a etiquetas; código en línea para modos, skills y comandos; sin mayúsculas para enfatizar frases. |
| Actor y voz (R-02, R-37; copywriting) | PASS: instrucciones dirigidas al agente en español, elección del propietario identificada y tono operativo coherente con el documento. |
| Ritmo y composición (C-1, C-3; copywriting) | PASS: tabla para comparar tareas y pasos para activación; revisión de lectura sin cierres promocionales, aforismos ni listas de relleno. |
| Selección por tarea | PASS: documentación activa copywriting; pantalla responsive combina UI, human y layoutmobile; backend conserva skills técnicas sin exigir revisión visual; code se limita a comentarios. |
| Enlaces y alcance documental | PASS: 61 enlaces locales y sus anclas comprobados; `git diff --check` correcto; solo cuatro documentos cambiados. Comparación contra la base confirma bloque Boost, tabla de tareas y evidencia histórica VSXX intactos. |
| CTA de producto (R-15) | No aplica: esta actualización contiene instrucciones de trabajo, sin cambios de CTA públicos. |
| Diseño, activos, UI y sus interacciones | No aplica: no se modifican pantallas, recursos visuales ni código; no se ejecutaron Herd, PHPUnit, Pint ni build. No acredita producción. |

Al trabajar cualquier `VSXX`, abrir o crear `docs/dev_changelog/VSXX.md` con ese ID exacto y actualizarlo al terminar cada intervención. Escribir en lenguaje claro qué se hizo y por qué, qué se verificó realmente y qué falta; fechar las continuaciones en el mismo archivo. Enlazar el registro desde la tarea correspondiente y mantenerlo coherente con su estado y evidencia resumida aquí. `docs/dev_changelog/` contiene únicamente registros Markdown, sin imágenes ni capturas; documentar la revisión visual en texto con URLs locales, tamaños de pantalla, interacciones y resultados observables. No generar registros vacíos para tareas pendientes ni atribuir a producción resultados locales.

En cada tarea ejecutar pruebas enfocadas de comportamiento; en tareas con UI revisar en Herd las pantallas afectadas en móvil, tableta, escritorio y teclado, y enlazar sus URLs locales en `docs/dev_changelog/VSXX.md` para abrirlas en el navegador. Mantener `npm run dev` activo durante la edición visual. Registrar comandos, entorno, resultado, fecha y revisión visual; anotar «no aplica» con motivo cuando corresponda. Al cerrar cada fase ejecutar suite completa en MySQL, Pint, build, inspección de rutas y revisión visual; añadir `git diff --check` cuando exista repositorio. SQLite no acredita integración MySQL. Las comprobaciones locales no acreditan producción.

No hay lanzamientos parciales. No copiar del proyecto anterior ni añadir cuentas públicas, favoritos, votos, ranking, reseñas propias, búsqueda por texto, panel de propietarios o cobros en línea. Las capturas solo orientan el diseño. Investigar contenido e identificar insumos externos desde el inicio sin inventar datos; mantener evidencias sensibles fuera de Git. Aprobar el inventario al cerrar VS16 y la salida en VS17.

<a id="puerta-transversal-de-diseno-antes-de-vs04"></a>

## Puerta transversal de diseño antes de VS04

Decisión del propietario del 22-10-2026: integrar el sistema extraído de [Base44](https://preview--turu-guia-local.base44.app/) y, después, alinear las pantallas públicas existentes antes de iniciar VS04. Esta puerta no renumera ni reabre VS00–VS03 y no habilita cuentas públicas. design.md gobierna la web pública y el futuro panel de usuario; Filament conserva su sistema administrativo.

| Hito | Estado | Evidencia y condición de cierre |
| --- | --- | --- |
| Manual y referencias | completada | Documentación verificada e integrada mediante [PR #6](https://github.com/luisfridev/tugurulocal/pull/6), fusionada hacia `master` el 22-10-2026 a las 17:49:51 (America/Cancun), commit `e543736ed6074e72d5e927eda5131b800b69d30d`. Integración comprobada el 23-10-2026. |
| Alineación de superficies existentes | completada e integrada | [PR #8](https://github.com/luisfridev/tugurulocal/pull/8), fusionada hacia `master` el 23-10-2026 a las 01:29:09 (America/Cancun), commit `3fbacd06087ef6166574efe239792cadeb394a9d`. Cuatro comprobaciones asistidas se trasladaron a VS15 y no bloquean este hito. Evidencia en [registro de alineación](dev_changelog/ALINEACION_VISUAL.md). |

Rama documental original: `codex/sistema-diseno-referencia`, desde master sincronizado en `9647d0d2d47a4d7fbf6163f4c80ad89eebeb42dd`. Se comprobó en GitHub la fusión de PR #5 hacia master y se ejecutó fetch y avance rápido local antes de crearla, con árbol limpio. La [PR #6: Diseño, documentar referencia Base44 y puerta de adopción](https://github.com/luisfridev/tugurulocal/pull/6) se entregó abierta y el propietario la fusionó hacia `master` en la fecha y el commit indicados arriba. Commit documental inicial publicado: `025b9b1e000c42c95bcfb75073f7393275aca215`, con 123 archivos; el registro de la PR quedó en `3493db6647631851fcfbb9856e714572f8461ead`. La conexión GitHub confirmó igualdad de blobs y del árbol inicial `85874440b8ed7f8125c3fc8c46fd0989808032ce` respecto al commit local `466b2b2`. La integración del manual no acredita alineación de la aplicación ni despliegue.

Entregables: [manual](design.md), [tokens](design-ref/design-tokens.json), [inventario](design-ref/component-inventory.md), [manifiesto de capturas](design-ref/screenshots/manifest.json) y [guía de adopción](design-ref/implementation-guide.md). Evidencia del manual: 17 páginas, 51 combinaciones de viewport/DPR 1, 102 capturas base y tres de interacción; muestras DOM/CSS, fronteras responsive, contraste calculado y siete comprobaciones de navegación/foco. La [revisión de entrega](design-ref/delivery-review.md) conserva criterios y límites. La PR #6 no cambió el código; la alineación de la aplicación se completó posteriormente mediante PR #8. Ninguna de las dos acredita producción.

Comprobación documental local: [reporte](design-ref/validation-report.json) correcto, con 108 tokens, 25 alias resueltos sin ciclos, referencias de medidas y enlaces/anclas válidos, 105 JPEG de dimensiones exactas y SHA-256 registrados. El procedimiento de comparación se ejecutó con autocopia, cambio sintético y rechazo de dimensiones distintas. `git diff --check` correcto; bloque generado de Boost, código, lockfiles y registros históricos VSXX intactos. PHPUnit, Pint, build y una nueva revisión de UI en Herd no aplican a esta entrega sin cambios de aplicación.

Antislop `during`, regla del proyecto; skills base, UI, human, layoutmobile y copywriting para extracción/documentación visual, más Tailwind para su adaptación al stack. Las comprobaciones no ejecutadas quedan identificadas: hover con puntero, zoom nativo 200 %, lector de pantalla, dispositivo/teclado virtual y auditoría completa de estados. La extracción no se presenta como certificación de la interfaz ni de producción.

Antes de cada intervención con UI pública, registrar revisión/base de design.md, componentes y diferencias intencionales; comparar capturas con el procedimiento de la guía y guardar la revisión en el registro VSXX correspondiente. VS15 revisa el conjunto. Los archivos de capturas permanecen en design-ref; dev_changelog conserva únicamente Markdown por tarea.

**Corrección del registro de integración (23-10-2026):** rama `codex/registrar-integracion-diseno`, desde `master` en `e543736ed6074e72d5e927eda5131b800b69d30d`. Se comprobó en GitHub que la PR #6 estaba cerrada, fusionada y dirigida a `master`, con fecha real `2026-10-22T22:49:51Z` (17:49:51 de America/Cancun). Se ejecutaron `git fetch origin` y `git merge --ff-only origin/master`; `master` ya estaba actualizado y el árbol estaba limpio antes de crear la rama. Se corrigieron el tablero, la guía de implementación y la revisión de entrega. Antislop `during`, regla del proyecto; base y copywriting para coherencia, evidencia y redacción documental. [Comprobaciones y revisión final](design-ref/delivery-review.md#5-correccion-documental-de-integracion-23-10-2026). En ese momento la alineación seguía pendiente; su integración posterior se registra a continuación.

Entrega de esta corrección: [PR #7: Docs, registrar integración del sistema de diseño](https://github.com/luisfridev/tugurulocal/pull/7), desde `codex/registrar-integracion-diseno`; el propietario la fusionó hacia `master` el 23-10-2026 a las 00:40:54 (America/Cancun), commit `0b986e22e4b37d3cb52aa9ba1ad87e56d82fcf2d`. El manual de la PR #6 ya estaba integrado. La alineación visual permaneció pendiente hasta el merge de la PR #8.

**Implementación de alineación visual (23-10-2026):** rama `codex/alineacion-visual-superficies-publicas`, desde `master` sincronizado en `0b986e22e4b37d3cb52aa9ba1ad87e56d82fcf2d`, con árbol limpio. Actualiza el shell, Inicio, directorios, filas de negocio, ficha y 404; filtros, rutas y paginación permanecen iguales. Pruebas enfocadas: 42/42; suite: 155/155; Pint, build y `git diff --check` correctos. Herd verificó Inicio, Negocios, Servicios, zona DEMO Centro, ficha, vacío y 404, además de filtro por teclado, en viewports móvil/tableta/escritorio. Zoom nativo 200 %, lector de pantalla, teclado virtual y movimiento reducido quedan pendientes, sin declararse PASS. Se conservan las fuentes locales actuales hasta resolver licencia/archivos de las familias de referencia. [Delivery Gate y resultados por criterio](dev_changelog/ALINEACION_VISUAL.md). La PR #8 quedó integrada en el merge `3fbacd06087ef6166574efe239792cadeb394a9d`; no hay despliegue.

**Comprobaciones complementarias de alineación (23-10-2026):** realizadas en la misma rama de la PR #8 antes de su fusión. Antislop `during`; base, human, layoutmobile y UI para la revisión, y copywriting para su registro. Las siete pantallas se midieron a 640×400 y 320×600 píxeles CSS reales, sin desbordamiento horizontal; conservan `lang="es"`, un `main`, un `h1`, alternativas de imagen y etiquetas de campos. Enter expande la FAQ y actualiza el árbol AX. En el reporte DEMO, el campo enfocado queda visible a 390×400, por encima de la navegación inferior. El navegador cargó las reglas que eliminan transición y desplazamiento bajo movimiento reducido. Zoom nativo 200 %, lector de pantalla, teclado virtual real y activación de movimiento reducido se trasladaron a VS15 por decisión del propietario; siguen pendientes de ejecución. [Métodos, medidas y límites](dev_changelog/ALINEACION_VISUAL.md#continuación-comprobaciones-de-accesibilidad-23-10-2026).

**Aplazamiento autorizado (23-10-2026):** el propietario decidió omitir por ahora zoom nativo 200 %, lector de pantalla, teclado virtual real y movimiento reducido activado. Se trasladan a la validación final de recorridos en VS15, antes del lanzamiento, como pendientes de ejecución. No se consideran pruebas aprobadas ni bloquearon la revisión e integración de la alineación, fusionada después mediante PR #8. Antislop `during`, base y copywriting; revisión de coherencia y `git diff --check` correctos. [Decisión y alcance](dev_changelog/ALINEACION_VISUAL.md#decisión-aplazar-las-cuatro-comprobaciones-a-la-validación-final-23-10-2026).

## Secuencia y dependencias

| ID | Entrega | Estado | Depende de | Fase |
| --- | --- | --- | --- | --- |
| VS00 | Base ejecutable | completada | Ninguna | Base |
| VS01 | Administración segura | completada | VS00 | Base |
| VS02 | Publicar y consultar una ficha Gratis | completada | VS01 | Editorial |
| VS03 | Descubrir negocios | completada | VS02 | Editorial |
| VS04 | Mantener URLs e indexación | pendiente | VS03 | Editorial |
| VS05 | Recomendar y moderar | pendiente | VS04 | Editorial |
| VS06 | Activar y terminar planes | pendiente | VS05 | Comercio |
| VS07 | Consultar planes comerciales | pendiente | VS06 | Comercio |
| VS08 | Publicar productos y servicios | pendiente | VS06 | Comercio |
| VS09 | Publicar ofertas vigentes | pendiente | VS08 | Comercio |
| VS10 | Exhibir campañas Super Premium | pendiente | VS09 | Comercio |
| VS11 | Publicar listas editoriales | pendiente | VS10 | Colecciones |
| VS12 | Publicar Lugares con negocios comprobados | pendiente | VS11 | Colecciones |
| VS13 | Contratar bloques contextuales | pendiente | VS12 | Colecciones |
| VS14 | Publicar guías del blog | pendiente | VS13 | Colecciones |
| VS15 | Validar recorridos integrados | pendiente | VS07, VS14 | Integración |
| VS16 | Completar contenido de salida | pendiente | VS15 | Contenido |
| VS17 | Preparar y realizar el lanzamiento único | pendiente | VS16 | Salida |

Ejecutar en el orden de la tabla como recorrido predeterminado. VS07 y VS08 comparten dependencia, pero no requieren trabajo con agentes en paralelo. No cerrar Comercio hasta completar ambas ramas. No enlazar módulos pendientes desde superficies públicas.

## Tareas

### VS00 — Base ejecutable

**Estado:** completada el 21 de octubre de 2026. **Dependencias:** ninguna. **Trazabilidad:** R09 / AC09; T01.

**Registro de cambios:** [VS00.md](dev_changelog/VS00.md).

**Objetivo:** arrancar una aplicación nueva con MySQL de desarrollo/pruebas y build reproducible, preservando el paquete documental.

- [x] Verificar compatibilidad real del [stack](TECH-STACK.md), fijar versiones en manifiestos y generar Laravel en una carpeta temporal vacía antes de fusionar el esqueleto sin sobrescribir documentación ni capturas.
- [x] Configurar PHP, MySQL, PHPUnit, Livewire, Filament y UI según el stack; almacenamiento consistente de instantes y presentación America/Cancun. Añadir dependencias con uso definido, no integraciones especulativas.
- [x] Preparar entorno de ejemplo sin secretos, exclusiones de archivos privados y ejecución local de pruebas/build. Mantener Debugbar solo en desarrollo.

**Aceptación y fallos:** aplicación arranca, migraciones limpias conectan a MySQL y suite inicial/build pasan; una incompatibilidad de paquetes se resuelve antes de integrar APIs. No se importa contenido heredado ni se crean negocios ficticios de producción.

**Pruebas y revisión:** instalación y versiones efectivas, conexión/migración MySQL, PHPUnit inicial, build y pantalla de arranque; comprobar preservación documental y exclusión de secretos. Pint cuando haya PHP; chequeo Git solo si existe repositorio.

**Evidencia local (21-10-2026):** responsable: agente Codex, rama `codex/vs00-base-ejecutable`. Laravel se generó en `/private/tmp` y se fusionó conservando README, AGENTS, docs y las 26 capturas. PHP 8.4.25, MySQL 8.4.5 aislado por socket, Laravel 13.38.0, Livewire 4.4.8, Filament 5.10.3, Tailwind 4.3.3 y DaisyUI 5.7.50 resueltos en lockfiles. `php artisan migrate:fresh --force` pasó en `tugurulocal_nuevo_dev`, incluida la tabla de medios. `php artisan test` pasó sobre `tugurulocal_nuevo_test`: 5 pruebas, 14 aserciones; `vendor/bin/pint --test`, `npm ci --ignore-scripts --no-audit --no-fund`, `npm run build`, `composer install --dry-run --no-scripts`, `composer validate --no-check-publish`, inspección de rutas y `git diff --check` pasaron. Pantalla local revisada en navegador a 390×844, 768×1024 y 1280×800, con aviso temporal en español, sin controles de módulos pendientes. `.env`, `.env.testing`, SQLite local, vendor, node_modules y build están ignorados; ninguna cuenta o negocio fue sembrado. Esto no acredita producción.

**Vista previa Herd (21-10-2026):** [página temporal local](http://tugurulocal-nuevo.test/) abierta en el navegador; Vite sirvió CSS y JavaScript con respuestas 200. `APP_URL` se ajustó solo en `.env`. Las instrucciones de cada VSXX con UI ahora requieren revisar y enlazar sus URLs locales; [detalle en VS00.md](dev_changelog/VS00.md#continuación--vista-previa-con-herd-21-de-octubre-de-2026).

**Continuación Boost (21-10-2026):** rama `codex/vs00-base-ejecutable-cont-20261021-1`. Boost 2.10.3, ya presente en `require-dev`, quedó configurado para Codex con guías, cinco skills y MCP; `boost.json` y la configuración del proyecto registran la integración. El servidor respondió a una inicialización MCP local. PHPUnit MySQL pasó con 5 pruebas y 14 aserciones; Composer, Pint, build y `git diff --check` pasaron. No hubo cambios de UI ni verificación de producción; [detalle en VS00.md](dev_changelog/VS00.md#continuación--configuración-de-laravel-boost-21-de-octubre-de-2026).

**Bloqueos:** ninguno para VS00. La instancia MySQL usada en esta comprobación está en `/private/tmp` y es temporal; cada entorno de desarrollo debe configurar sus propias bases y credenciales fuera de Git. **Siguiente acción:** VS01, administración segura y puerta Base.

### VS01 — Administración segura

**Estado:** completada localmente el 22 de octubre de 2026. **Dependencias:** VS00 completada. **Trazabilidad:** R08–R09 / AC08–AC09; T02.

**Registro de cambios:** [VS01.md](dev_changelog/VS01.md). **Ramas:** `codex/vs01-administracion-segura`, creada desde `master` con VS00 integrada; continuaciones `codex/vs01-administracion-segura-cont-20261021-1`, `codex/vs01-administracion-segura-cont-20261021-2`, `codex/vs01-administracion-segura-cont-20261021-3`, `codex/vs01-administracion-segura-cont-20261022-1`, `codex/vs01-administracion-segura-cont-20261022-2`, `codex/vs01-administracion-segura-cont-20261022-3`, `codex/vs01-administracion-segura-cont-20261022-4` y `codex/vs01-administracion-segura-cont-20261022-5`, conservando el trabajo previo. La segunda cambia el prefijo administrativo a `/dashboard`; la tercera corrige el retorno a `/admin` guardado en sesiones anteriores; la primera del día 22 completa la revisión visual y el cierre local; la segunda actualiza el flujo para mantener solo registros Markdown y retira la captura; la tercera restaura el respaldo MySQL original y documenta la configuración mediante `.env`; la cuarta prepara el registro del commit sin llegar a crearlo; la quinta confirma en Git el conjunto de VS01 y corrige aquella evidencia anticipada. El propietario eligió `tugurulocal_2026` y autorizó reiniciar sus datos; el nombre local se toma de `DB_DATABASE` en `.env`, con `'laravel'` únicamente como respaldo en la configuración MySQL. PHPUnit usa la base separada `tugurulocal_2026_test`.

**Objetivo:** permitir operación administrativa exclusivamente al super admin, con autenticación reforzada y cambios auditables.

- [x] Crear el panel y bootstrap seguro de primera cuenta; `is_super_admin` falso por defecto y promoción explícita. Deshabilitar autorregistro público.
- [x] Aplicar 2FA, límites de intentos, sesiones seguras y policies para acciones directas, recursos y operaciones masivas; reutilizar esta protección en las tareas siguientes.
- [x] Registrar actor, fecha y resultado de cambios sensibles sin almacenar secretos; presentar errores y estados administrativos comprensibles.
- [x] Completar la revisión visual en móvil, tableta y escritorio y la navegación por teclado en Herd para cerrar Base.

**Aceptación y fallos:** visitante y usuario sin privilegios no acceden ni ejecutan acciones por URL directa; el super admin cumple 2FA antes de operar. Ocultar un botón no sustituye autorización del servidor.

**Pruebas y revisión:** accesos de visitante/no super admin/super admin, límites de login, exigencia de 2FA y operaciones directas/masivas disponibles. Revisar login/panel con teclado y ausencia de registro público. Cerrar puerta Base.

**Evidencia local (21-10-2026):** ramas indicadas arriba. Base `tugurulocal_2026` reiniciada con autorización del propietario y seis migraciones correctas; `tugurulocal_2026_test` preparada por separado y forzada en PHPUnit. Suite completa: 38 pruebas y 273 aserciones sobre MySQL. Cobertura de creación inicial, validación, asignación de privilegios, alta TOTP, desafío, recuperación de un solo uso, rechazo de TOTP reutilizado, límites, permisos, auditoría, cambio de contraseña, cierre de sesión y peticiones HTTP Livewire tras revocar acceso/2FA. Pint, Composer, build, rutas y `git diff --check` correctos. Herd: login y recursos CSS/JS/fuentes con HTTP 200; rutas privadas redirigen al login y registro público/Premium devuelven 404. Vite activo. [Detalle y URLs locales](dev_changelog/VS01.md).

**Continuación de ruta (21-10-2026):** rama `codex/vs01-administracion-segura-cont-20261021-2`. Panel, login, perfil, 2FA, auditoría y logout usan `/dashboard`; las rutas anteriores `/admin` devuelven 404. Se actualizaron contratos, README y pruebas. Pruebas enfocadas: 28 pruebas y 234 aserciones; suite completa MySQL: 39 pruebas y 279 aserciones. Pint, build, rutas y `git diff --check` correctos. Login revisado en Herd a 390×844, 768×1024 y 1280×800, con recorrido Tab y sin errores de consola capturados. [Detalle y URLs vigentes](dev_changelog/VS01.md#continuación--ruta-administrativa-dashboard-21-de-octubre-de-2026).

**Corrección de retorno de sesión (21-10-2026):** rama `codex/vs01-administracion-segura-cont-20261021-3`. Se reprodujo que Filament priorizaba `/admin` o sus subrutas al estar guardadas como destino de retorno. El login las descarta al montar el formulario y al autenticar, conservando los destinos vigentes y la exigencia de 2FA. Pruebas enfocadas: 19 pruebas y 194 aserciones; suite MySQL completa: 43 pruebas y 298 aserciones. Pint y `git diff --check` correctos. Login recargado en Herd sin errores de consola; la autenticación con destinos guardados se comprobó con usuarios de prueba. [Detalle](dev_changelog/VS01.md#continuación--destino-de-acceso-guardado-en-sesión-21-de-octubre-de-2026).

**Cierre visual local (22-10-2026):** rama `codex/vs01-administracion-segura-cont-20261022-1`, responsable: agente Codex. Se confirmó la cuenta del propietario con 2FA habilitado. Panel, perfil, auditoría, alta y desafío 2FA se revisaron en Herd a 390×844, 768×1024 y 1280×800, con teclado, foco y recursos cargados. Una cuenta temporal expresamente autorizada permitió completar el alta, acceder mediante TOTP y usar recuperación sin cambiar la cuenta real. Al terminar se eliminaron esa cuenta, sus nueve eventos sintéticos y los archivos temporales de credenciales; la consulta final devuelve una cuenta administrativa con 2FA y cero cuentas temporales. Se comprobó que las cuentas y la auditoría anteriores conservaron sus valores. Se corrigió el mensaje de auditoría sin coincidencias y se documentó su comprobación visual en texto. Pruebas enfocadas: 27 pruebas y 239 aserciones; suite MySQL: 43 pruebas y 298 aserciones. Pint, build, rutas y `git diff --check` correctos; consola del navegador sin errores ni advertencias capturados. [Detalle y URLs locales](dev_changelog/VS01.md#continuación--cierre-visual-y-cuenta-administrativa-22-de-octubre-de-2026).

**Ajuste del flujo documental (22-10-2026):** rama `codex/vs01-administracion-segura-cont-20261022-2`. Por instrucción del propietario, se eliminó la imagen de auditoría y sus enlaces; AGENTS, README, plan y este tablero establecen que `docs/dev_changelog/` contiene solo registros Markdown. La revisión visual conserva sus URLs y resultados escritos. Se comprobaron los enlaces locales, la ausencia de imágenes y `git diff --check`; VS01 conserva su cierre local. No cambió código ni se repitieron pruebas de aplicación.

**Base de datos mediante `.env` (22-10-2026):** rama `codex/vs01-administracion-segura-cont-20261022-3`, creada desde la anterior con los cambios pendientes conservados y VS00 completada. Se restauró `env('DB_DATABASE', 'laravel')` como configuración MySQL original; README, diseño técnico y stack aclaran que la base local se toma de `DB_DATABASE` en `.env`. Artisan confirmó `tugurulocal_2026` como valor efectivo. Sintaxis PHP, Pint, coincidencia de `config/database.php` con Git y `git diff --check` correctos; se verificó que los demás cambios previos permanecen intactos. No se editaron `.env`, `.env.example` ni PHPUnit; se conserva `tugurulocal_2026_test`. No aplica revisión visual ni repetir suite/build para este ajuste sin cambio de base efectiva o interfaz. VS01 conserva su cierre local; [detalle](dev_changelog/VS01.md#continuación--base-de-datos-mediante-env-22-de-octubre-de-2026).

**Preparación del commit (22-10-2026):** rama `codex/vs01-administracion-segura-cont-20261022-4`, desde la continuación anterior. Se preparó el registro de los 39 archivos de VS01, pero no se creó el commit: al revisar Git, HEAD seguía en `c2b146a` y los cambios continuaban sin confirmar. Se corrige la afirmación anterior que daba por terminada esa operación. [Detalle](dev_changelog/VS01.md#continuación--confirmación-de-cambios-en-git-22-de-octubre-de-2026).

**Confirmación de VS01 en Git (22-10-2026):** rama `codex/vs01-administracion-segura-cont-20261022-5`, creada desde la anterior con VS00 integrada y todos los cambios pendientes conservados, por instrucción del propietario. El commit que contiene esta actualización reúne los 39 archivos de implementación, pruebas, configuración de ejemplo y documentación. Se revisaron los archivos incluidos, la exclusión de `.env`, los patrones de claves privadas y tokens y `git diff --check`. Se conserva la evidencia local de pruebas y revisión visual; esta intervención solo corrige el registro y confirma el trabajo existente. [Detalle](dev_changelog/VS01.md#continuación--commit-del-conjunto-vs01-22-de-octubre-de-2026).

**Siguiente acción:** VS02. VS01 está integrada en `master` mediante PR #1, merge `678d790`, comprobado el 22-10-2026. La puerta Base está cerrada localmente. Producción, HTTPS del despliegue y respaldos siguen pendientes de VS17.

### VS02 — Publicar y consultar una ficha Gratis

**Estado:** completada e integrada en `master` local el 22 de octubre de 2026, incluida la simplificación multimedia con fechas manuales. **Dependencias:** VS01 completada e integrada.

**Registro:** [VS02.md](dev_changelog/VS02.md). **Ramas:** `codex/vs02-ficha-gratis`, `codex/vs02-ficha-gratis-cont-20261022-1`, `codex/vs02-ficha-gratis-cont-20261022-2` y `codex/vs02-ficha-gratis-cont-20261022-3`, continuaciones con todo el trabajo previo conservado; desde `master` en `678d790`. La tercera continuación corresponde al cierre en Git. **Trazabilidad:** R01–R02, R08–R09 / AC01–AC02, AC08–AC09; T03, T04, T06, T13.

**Simplificación verificada (22-10-2026):** rama `codex/vs02-ficha-gratis-cont-20261022-2`, desde la continuación anterior con sus cambios sin confirmar conservados. VS01 completada e integrada. Simplificación completada: se retiran fuente y permiso/licencia del flujo multimedia, conservando columnas y valores históricos privados sin sobrescribir. Fechas manuales obligatorias al autorizar y no futuras en America/Cancun, también en lectura pública; fuente/fecha editorial y Google conservadas. Factories y DEMO nuevos no rellenan campos retirados. Pruebas enfocadas: 42 pruebas y 673 aserciones; suite completa MySQL: **91 pruebas y 1.026 aserciones**. Pint, build, 18 rutas, diff y revisión Herd a 390×844, 768×1024 y 1280×800/teclado correctos. Persisten seis fichas DEMO y 32 medios, sin migración, reinicio ni cambios de cuenta. Al terminar esa continuación aún no había commit, integración ni despliegue. [Registro de esta continuación](dev_changelog/VS02.md#continuación--simplificar-multimedia-y-conservar-fechas-manuales-22-10-2026).

**Cierre en Git (22-10-2026):** rama `codex/vs02-ficha-gratis-cont-20261022-3`, desde la continuación anterior con los 89 archivos pendientes conservados y VS01 integrada. PHP 8.4.27; mismas versiones de Laravel y paquetes. Pruebas enfocadas: **48 pruebas y 728 aserciones**; suite completa sobre MySQL `tugurulocal_2026_test`: **91 pruebas y 1.026 aserciones**, correctas. Pint, build, 18 rutas y comprobaciones de diff, incluido el índice completo, correctos. Se revisaron los archivos del conjunto, la exclusión de entornos/medios privados y patrones de claves/tokens, sin coincidencias. Commit de implementación `8a9da67`, integrado localmente en `master` mediante `git merge --ff-only`; se comprobó la igualdad de referencias y el árbol limpio. Esta intervención conserva la revisión visual previa y no cambia funcionalidad ni UI. Sin push ni despliegue. [Registro del cierre](dev_changelog/VS02.md#continuación-cierre-en-git-22-10-2026).

**Ampliación verificada (22-10-2026):** logo obligatorio e independiente, protegido en publicación/edición y consulta directa; OG portada → logo. Identidad opcional de ciudades/categorías/zonas con emojis, HEX/contraste, RichEditor sanitizado y OG/icono privados. Migración incremental sin reinicio. Seeder local/testing conserva cuatro negocios públicos, borrador, pausado y 32 medios; segunda ejecución local mantiene cantidad, IDs y fecha de modificación de medios. Suite MySQL: **87 pruebas y 798 aserciones**; Pint, build, rutas y diff correctos. Formularios y fichas revisados a 390×844, 768×1024 y 1280×800 y teclado. Única cuenta super admin existente, sin cuentas DEMO. Datos/archivos persistentes excluidos de producción y del inventario real. Sin commit, integración ni despliegue. [Detalle y enlaces DEMO](dev_changelog/VS02.md#continuación--logo-identidad-y-demo-persistente-22-10-2026).

- [x] Logo autorizado obligatorio para publicación y lectura pública, con protección ante retirada/reemplazo.
- [x] Identidad opcional administrable y conversión segura de descripciones previas.
- [x] Demostración persistente y repetible, con protección por entorno y sin modificar cuentas.

**Objetivo:** que el super admin publique un negocio Gratis y un visitante consulte su ficha completa en `/@{listing}`.

- [x] Gestionar ciudades, zonas y categorías desde el panel, además del modelo business/place; sembrar solo Playa del Carmen y taxonomía estructural. Slug global único, categoría principal asociada y secundarias válidas; reservar palabras de rutas.
- [x] Implementar acciones de publicación/despublicación manual inmediata (sin programación), archivo, recuperación, previsualización autorizada e historial; separar estado editorial del operativo.
- [x] Gestionar datos prácticos, fuentes/fecha de revisión, contactos verificados, FAQs, imágenes y PDF autorizados con el plugin Filament/Spatie y etiqueta PDF editable. Validar medios y proteger evidencia privada; Maps ausente permanece vacío.
- [x] Entregar ficha Gratis adaptable con compartir URL, reporte de datos incorrectos, canonical/metadatos y eventos de consulta/contacto sin PII. Incorporar tokens visuales, foco y alternativas de medios desde esta pantalla.

**Aceptación y fallos:** solo ficha publicada con fecha alcanzada es pública; borrador, pausada o archivada no se filtra por acceso directo ni previsualización. Gratis conserva información práctica y PDF; cierre permanente permite detalle informativo sin elegibilidad para vitrinas/campañas.

**Pruebas y revisión:** publicación, estados/fechas, recuperación, unicidad global/categorías y permisos; contactos/Maps vacíos sin ceros, archivos inválidos rechazados y datos privados ausentes del HTML/metadatos. Revisar ficha larga, sin foto y con PDF en responsive/teclado.

**Evidencia de la primera intervención (22-10-2026, anterior a la ampliación):** rama `codex/vs02-ficha-gratis` desde `master` en `678d790`. Se completaron datos, acciones/policies, gestión administrativa de taxonomía/fichas, publicación manual sin programación, historial y recuperación, medios privados con plugin Filament/Spatie, etiqueta PDF editable, ficha pública y reportes moderados. Migración y seeder estructural aplicados sin reiniciar la base local. Suite completa MySQL: 74 pruebas y 534 aserciones correctas; Pint, build, rutas y `git diff --check` correctos. Herd: rutas administrativas y ficha/formulario en móvil, tableta y escritorio; teclado, recursos, etiqueta PDF guardada y reporte enviado/resuelto comprobados. Fixture temporal y limpieza expresamente autorizados por el propietario; datos previos conservados por comparación de hashes. Consulta final: una ciudad, cinco categorías, cero fichas/reportes y una cuenta super admin. [Detalle, límites y URLs](dev_changelog/VS02.md).

**Pendientes:** contenido y medios reales, privacidad aprobada para recepción pública de reportes, Lugares con hijos en VS12 y producción. No bloquean el cierre local documentado. VS02 está confirmada e integrada; al iniciar VS03 el 22-10-2026 se comprobó que `origin/master` contiene su trabajo y las PR documentales #2 y #3. VS03 continúa en su rama propia.

### VS03 — Descubrir negocios

**Estado:** completada el 22 de octubre de 2026, con integración comprobada en GitHub. **Dependencias:** VS02 integrada. **Trazabilidad:** R01–R02, R09 / AC01–AC02, AC09; T04, T06, T13.

**Registro:** [VS03.md](dev_changelog/VS03.md). **Rama inicial:** `codex/vs03-descubrir-negocios`, desde `master` sincronizado en `b59d920`. Implementación local del 22-10-2026. Selección manual, filtros GET con Aplicar y Antislop `after` elegidos por el propietario. **Rama de cierre:** `codex/vs03-descubrir-negocios-cont-20261022-3`, creada desde la continuación documental anterior con todos los cambios pendientes conservados; al preparar aquella entrega, `origin/master` estaba en `b59d920`. **Rama del registro de integración:** `codex/vs03-descubrir-negocios-cont-20261022-4`, creada desde `master` comprobado en el merge `78e67bd`.

**Continuación DEMO (22-10-2026):** rama `codex/vs03-descubrir-negocios-cont-20261022-1`, creada desde la anterior con todos los cambios pendientes conservados y VS02 integrada. Por petición del propietario, `DemoListingSeeder` mantiene **30 negocios públicos DEMO y dos privados** (borrador/pausado) para futuras etapas. Se añadieron 26 públicos con categorías secundarias y ambas zonas; quedan 32 fichas, 97 medios y la misma cuenta administrativa. Las seis fichas originales, los 32 medios anteriores y sus 59 archivos físicos conservan sus huellas. La repetición del seeder mantiene IDs, estados, fechas y nombres de archivo sin duplicar ni sobrescribir. Pruebas enfocadas: **3 pruebas y 687 aserciones**; suite completa MySQL: **155 pruebas y 2.078 aserciones**, correctas. Pint, build, 23 rutas y diff correctos. Paginación ejercitada en Herd: **12/12/6, 30 fichas únicas**, Anterior/Siguiente, historial y filtro Experiencias conservado al avanzar; Aplicar reinicia página. Los datos y archivos permanecen locales y excluidos de producción. [Registro de la ampliación](dev_changelog/VS03.md#continuación-demo-persistente-con-30-negocios-públicos-22-10-2026).

**Objetivo:** recorrer Inicio, Negocios, categorías y zonas hasta una ficha elegible mediante filtros compartibles.

- [x] Implementar `/` → 302 `/playa-del-carmen`, ciudad pública, rutas específicas antes del comodín de categoría, Negocios, categorías y zonas con paginación y filtros categoría/zona.
- [x] Mantener orden orgánico `published_at DESC, id DESC`, incluyendo Gratis. Preparar selección editorial y recientes de Inicio con contenido aprobado, sin tarjetas vacías ni enlaces a módulos pendientes.
- [x] Aplicar navegación adaptable, tarjetas, estados vacíos, carga y 404 en español; no mostrar insignias de plan ni adelantos del catálogo en tarjetas orgánicas.
- [x] Extender SEO y medición de las superficies creadas; no presentar Lugares vacíos antes de implementar sus vínculos en VS12.
- [x] Gestionar hasta seis seleccionados desde Ciudades con autorización, orden, transacción y auditoría; proteger medios públicos de taxonomías y conservar los datos DEMO.

**Aceptación y fallos:** filtros conservan una URL compartible y resultados estables; una ciudad no pública, una zona de otra ciudad o un slug no válido no expone contenido ajeno. No existen búsqueda por texto, ranking ni página separada de ofertas.

**Pruebas y revisión:** HTTP de rutas/reservas/404, filtros, paginación, empate de fechas, inclusión Gratis y exclusión de fichas privadas. Revisar navegación móvil, filtros con teclado y contenido vacío/largo sin obstrucciones.

**Evidencia local (22-10-2026):** suite completa MySQL: **154 pruebas y 1.564 aserciones**, correctas. Tras el ajuste final de reflujo y contraste, regresión de descubrimiento/fichas: **42 pruebas y 423 aserciones**, correcta. Pint, build, 23 rutas propias y `git diff --check` correctos. Dos migraciones incrementales aplicadas, sin reinicio ni seeder; seis fichas y 32 medios conservan sus huellas previas. Herd: móvil, tableta, escritorio, filtros y limpieza por contexto, historial, fichas, categoría/zona, estado vacío, 404 y selección administrativa con teclado, guardado, reordenación y retirada. El propietario confirmó lectura y uso al 200 %; la revisión posterior con vista ampliada no mostró desbordamiento. Antislop final: dos hallazgos de reflujo/contraste corregidos, con evidencia por criterio en el registro.

**Verificaciones locales completas (22-10-2026):** la paginación visual pendiente en la primera intervención quedó comprobada en la continuación de 30 DEMO. El propietario confirmó después que comprobó los filtros con JavaScript desactivado y funcionan. Esta comprobación manual del propietario complementa la cobertura PHPUnit HTTP/HTML; no se atribuye al navegador automatizado. Confirmación registrada en `codex/vs03-descubrir-negocios-cont-20261022-2`, creada desde la continuación anterior con todos sus cambios conservados. No se modificaron código, datos ni medios y no se repitieron pruebas técnicas por este registro documental.

**Entrega por PR (22-10-2026):** [PR #4 — VS03: Descubrir negocios](https://github.com/luisfridev/tugurulocal/pull/4), entregada hacia `master` desde `codex/vs03-descubrir-negocios-cont-20261022-3` y posteriormente fusionada por el propietario. Commits publicados: implementación `7ab7166e517b7e2b60003946ef9a801074fd602f` y registro de PR `cbb17bd2a84b6431e037d963369f06330a5099d0`, con 42 archivos en conjunto. Rama publicada mediante la conexión GitHub; igualdad de contenido comprobada antes de alinear la rama local. La PR está adjunta al chat. Se conserva la evidencia de 155 pruebas y 2.078 aserciones y las revisiones locales previas.

**Integración comprobada (22-10-2026):** GitHub confirmó `merged=true`, destino `master` y merge `78e67bd3cee9e7b5389161ddb106ba354b306fef`, del 22-10-2026 a las 16:17:35 (America/Cancun; 21:17:35 UTC). Después de `git fetch origin` y `git merge --ff-only origin/master`, `master` local y `origin/master` coincidían en ese commit; el checkout ya estaba actualizado y no fue necesario avanzar. El commit de entrega `cbb17bd` es ancestro de `master` y la comparación de árboles no presenta diferencias. La continuación `-4` registra este cierre mediante una nueva PR documental, sin cambios de implementación ni repetición de pruebas técnicas.

**Registro documental de integración:** [PR #5 — VS03: registrar integración y cierre](https://github.com/luisfridev/tugurulocal/pull/5), entregada inicialmente abierta hacia `master` desde `codex/vs03-descubrir-negocios-cont-20261022-4`, commit documental `54e0b8f3bac360fe0f5be600afd383285a8545c3`. El propietario la fusionó después el 22-10-2026 a las 16:24:39 (America/Cancun), commit `9647d0d2d47a4d7fbf6163f4c80ad89eebeb42dd`. La implementación de VS03 se integró mediante PR #4.

**Pendientes:** revisión de la PR #5 del registro documental; contenido de salida en VS16 y despliegue en VS17. VS03 está completada e integrada, sin comprobaciones locales pendientes identificadas. **Siguiente acción:** VS04, mantener URLs e indexación. No se ha desplegado.

### VS04 — Mantener URLs e indexación

**Estado:** pendiente. **Dependencias:** VS03 completada e integrada. **Trazabilidad:** R01–R02, R09 / AC01–AC02, AC09; T06.

**Puerta adicional previa al inicio:** ambos hitos de la puerta transversal de diseño están verificados e integrados. La dependencia VS03 está satisfecha. VS04 puede comenzar en una rama nueva; no se ha iniciado con esta actualización documental.

**Objetivo:** que URL pública, metadatos, enlaces y sitemap reflejen el mismo estado editorial.

- [ ] Unificar resolución de URL, canonical absoluto, metadatos sociales, breadcrumbs y datos estructurados fieles al contenido visible.
- [ ] Generar sitemap solo con URLs públicas útiles; excluir filtros, páginas vacías, privadas y módulos no disponibles. Aplicar `noindex` y canonical a la base pertinente en variantes filtradas.
- [ ] Registrar cambios de slug de contenido publicado de este proyecto, reservar su origen y emitir 301 al destino público actual sin revelar borradores; actualizar enlaces generados.
- [ ] Revisar políticas de indexación, redirección y eventos sin PII ya presentes; extender este contrato a cada módulo posterior.

**Aceptación y fallos:** despublicar retira la URL del sitemap; redirecciones no recuperan contenido privado ni reasignan orígenes reservados. Ninguna URL heredada obtiene tratamiento especial.

**Pruebas y revisión:** sitemap, canonical/noindex, metadatos/JSON-LD, cambio de slug, colisiones y despublicación del destino. Revisar breadcrumbs y enlaces visibles de las pantallas existentes.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** ninguno adicional confirmado.

### VS05 — Recomendar y moderar

**Estado:** pendiente. **Dependencias:** VS04. **Trazabilidad:** R06, R08–R09 / AC06, AC08–AC09; T05, T06, T13.

**Objetivo:** recibir una recomendación pendiente y permitir moderarla hacia un borrador sin publicación automática ni exposición de información privada.

- [ ] Crear formulario GET/POST `/recomendar`, confirmación neutra y aviso de privacidad; impedir recepción pública de datos sin texto aprobado.
- [ ] Aplicar CSRF, honeypot, controles adicionales antibot, validación de URLs/archivos, detección de duplicados y límites 3 por hora/10 por día por IP.
- [ ] Implementar moderación aprobada/rechazada/duplicada/spam con responsable y motivo; aprobar crea o vincula borrador y exige publicación posterior separada.
- [ ] Separar contacto/evidencia privada y consentimiento de atribución social con identificador válido; medir envío/moderación sin PII y mantener campos con acceso restringido.

**Aceptación y fallos:** envío público nunca crea ficha publicada; exceder un límite o enviar contenido inválido no elude validación. Sin consentimiento e identificador válido no hay crédito visible; correo, IP, notas y evidencia privada no aparecen en público.

**Pruebas y revisión:** envío y errores, CSRF, bots, límites horarios/diarios, duplicados, archivos/URLs inválidos, decisiones de moderación, permisos y atribución. Revisar formulario con teclado virtual, foco y errores accesibles. Cerrar puerta Editorial.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** aviso, retención y eliminación de datos requieren aprobación antes de recepción pública; desarrollo y pruebas locales pueden avanzar sin publicar el formulario.

### VS06 — Activar y terminar planes

**Estado:** pendiente. **Dependencias:** VS05. **Trazabilidad:** R07–R09 / AC07–AC09; T07, T13.

**Objetivo:** administrar beneficios e ingresos por separado y observar el plan efectivo correcto en una ficha sin depender de tareas programadas.

- [ ] Implementar asignación única Premium/Super Premium con período, estado, motivo, responsable, acuerdo y referencia; Gratis es el estado derivado sin asignación activa.
- [ ] Exigir acuerdo y pago confirmado para pagada, fin para prueba e ingreso cero para prueba/cortesía; gestionar recibos confirmados en centavos MXN e historial de renovaciones manuales.
- [ ] Implementar pausa, revocación y vencimiento con lectura de elegibilidad al instante; conservar ficha y contenido histórico. Super Premium incluye Premium sin asignaciones simultáneas.
- [ ] Mostrar borde diferenciado en tarjetas orgánicas y plan dentro de ficha cuando corresponda; panel de historial e ingresos autorizado y auditado. Mantener publicidad y selección editorial separadas.

**Aceptación y fallos:** Gratis → Premium → Super Premium → Gratis conserva datos; al terminar el plan se retiran beneficios inmediatamente. Asignaciones incompatibles o concurrentes no se solapan; cortesía no suma ingreso y renovación crea nueva asignación.

**Pruebas y revisión:** reloj fijado en inicio/fin America/Cancun, pago ausente/no confirmado, prueba sin fin, cortesía indefinida, pausa/revocación/renovación y concurrencia en MySQL. Revisar panel, ficha y borde orgánico; ausencia de insignias/adelantos en tarjetas.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** ninguno comercial impide desarrollar localmente; no activar planes pagados reales sin acuerdo y confirmación de pago.

### VS07 — Consultar planes comerciales

**Estado:** pendiente. **Dependencias:** VS06. **Trazabilidad:** R08–R09 / AC08–AC09; T07, T13.

**Objetivo:** publicar la comparación y contacto asistido solo con configuración comercial completa y aprobada.

- [ ] Administrar precios mensuales MXN de Premium/Super Premium, tratamiento de IVA, WhatsApp Business, responsable, aprobación e historial de cambios.
- [ ] Centralizar disponibilidad de `/premium`, navegación, CTA y sitemap; si falta un dato obligatorio o aprobación, responder 404 y ocultar todos los accesos comerciales.
- [ ] Crear comparación Gratis/Premium/Super Premium con WhatsApp para venta asistida; mostrar plazo de respuesta solo si está definido y aprobado. Incluir SEO y medición sin PII cuando la página sea elegible.

**Aceptación y fallos:** cada configuración parcial mantiene oculta la página; retirar aprobación la oculta de nuevo. Un mensaje por WhatsApp no activa el plan ni promete cobro/renovación automática.

**Pruebas y revisión:** combinaciones de campos ausentes, configuración no aprobada/completa, revocación, CTA, sitemap, permisos y plazo opcional. Revisar tabla comparativa adaptable, teclado y enlace de contacto.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** precios, IVA, número y responsable oficiales pendientes para activar la página; plazo pendiente solo bloquea su promesa. No inventar valores públicos.

### VS08 — Publicar productos y servicios

**Estado:** pendiente. **Dependencias:** VS06. **Trazabilidad:** R03, R07, R09 / AC03, AC07, AC09; T08, T13.

**Objetivo:** publicar productos/servicios de negocios elegibles y descubrirlos en ficha y Explorar.

- [ ] Administrar elementos con título, resumen, imagen autorizada/precio/moneda opcionales, estado, fecha y orden interno; validar máximo diez publicados por negocio mediante acción de dominio transaccional.
- [ ] Mostrar solo elementos aprobados de negocios publicados con Premium/Super Premium activo; retirar beneficios al instante sin borrar contenido. Borradores no consumen cupo.
- [ ] Entregar `/{city}/explorar` con filtros compartibles `tipo=todos|productos|ofertas` y `categoria`; sección de productos por fecha descendente/ID y ficha por orden manual. No simular ofertas antes de VS09.
- [ ] Añadir vitrinas separadas de Inicio/categoría con contenido real; en categoría, después de los primeros negocios. Enlazar ficha/ancla identificable, medir apertura y aplicar SEO/estados vacíos del catálogo.

**Aceptación y fallos:** undécimo publicado se rechaza, también con intentos concurrentes; un borrador adicional es válido. Sin plan o con ficha privada no hay producto público. Los resultados no modifican el orden orgánico de negocios.

**Pruebas y revisión:** límites 9/10/11, concurrencia MySQL, estados, plan vencido, filtro por categoría/tipo, orden global/manual, anclas y sitemap/noindex. Revisar catálogo vacío, imagen/precio ausentes y vitrinas con teclado/responsive.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** productos y medios reales pendientes de verificación para salida; probar con factories sin sembrar demostraciones en producción.

### VS09 — Publicar ofertas vigentes

**Estado:** pendiente. **Dependencias:** VS08. **Trazabilidad:** R03, R07, R09 / AC03, AC07, AC09; T08, T13.

**Objetivo:** mostrar ofertas y promociones aprobadas exclusivamente dentro de su período y elegibilidad comercial.

- [ ] Administrar tipo, título, descripción, condiciones, inicio/fin, imagen opcional y estado; exigir fin posterior al inicio, sin cupo numérico de ofertas.
- [ ] Integrar sección de ofertas en Explorar y ficha con `inicio <= ahora < fin`; ordenar por `ends_at ASC, id ASC` y ocultar por vencimiento, pausa o pérdida de elegibilidad sin borrar historial.
- [ ] Dirigir «Ver todo» a `/{city}/explorar?tipo=ofertas`, con filtros compartibles, enlace a ficha/ancla, condiciones visibles, SEO y apertura de catálogo sin PII. No crear `/{city}/ofertas`.

**Aceptación y fallos:** oferta futura/vencida/retirada no sale en ficha, catálogo ni vitrinas aunque el cron falle; una oferta sin condiciones válidas o intervalo correcto no se publica.

**Pruebas y revisión:** antes/inicio/durante/fin/después con reloj fijado, America/Cancun y cambio de día; plan/ficha no elegible, orden con empate, filtros y ausencia de ruta /ofertas. Revisar condiciones extensas y estado vacío.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** ofertas, condiciones y medios reales por aprobar antes de salida.

### VS10 — Exhibir campañas Super Premium

**Estado:** pendiente. **Dependencias:** VS09. **Trazabilidad:** R07–R09 / AC07–AC09; T09, T13.

**Objetivo:** contratar y mostrar carrusel de Inicio y posición de categoría con cupos, vigencias y señalización publicitaria verificables.

- [ ] Administrar contrato, negocio, ciudad, categoría principal, posición, material aprobado, responsable y período; exigir negocio apto y plan Super Premium durante toda la campaña.
- [ ] Reservar en transacción máximo tres campañas simultáneas por ciudad y posiciones de categoría sin solapamiento, con intervalos semiabiertos. Cortesías consumen cupo e ingresan cero.
- [ ] Mostrar cada campaña elegible en carrusel y espacio contratado antes del listado orgánico, con «Patrocinado» visible/accesible; no alterar orden orgánico.
- [ ] Incorporar avance automático, pausa y controles accesibles; desactivar avance con movimiento reducido. Registrar impresiones/clics por espacio sin PII y ocultar campañas inelegibles al consultar.

**Aceptación y fallos:** cuarto cupo o misma posición en períodos superpuestos se rechazan aun con solicitudes concurrentes; períodos contiguos no se solapan. Negocio cerrado permanentemente, plan vencido o material no aprobado nunca se exhiben.

**Pruebas y revisión:** tercer/cuarto cupo concurrente en MySQL, posición en conflicto, cobertura temporal del plan, inicio/fin, pausa y cortesía con ingreso cero. Revisar etiqueta en cada espacio, carrusel con teclado y movimiento reducido. Cerrar puerta Comercio con VS07–VS10 completos.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** campañas, acuerdos o cortesías y materiales reales pendientes para salida; los bloques contextuales se completan en VS13.

### VS11 — Publicar listas editoriales

**Estado:** pendiente. **Dependencias:** VS10. **Trazabilidad:** R04, R08–R09 / AC04, AC08–AC09; T10, T13.

**Objetivo:** crear una colección con criterio editorial visible y orden manual independiente de planes y patrocinios.

- [ ] Administrar ciudad, título, introducción, criterio, slug, estado, medios/SEO y membresía con posiciones únicas; publicar en `/{city}/listas/{slug}`.
- [ ] Seleccionar fichas válidas y preservar orden manual; ocultar fichas despublicadas conservando relación administrativa y orden relativo restante. Aplicar elegibilidad propia de cada ficha.
- [ ] Incorporar canonical, sitemap/indexabilidad según contenido, medición de consulta y diseño editorial accesible; preparar la lista de baile sin inventar sus integrantes.

**Aceptación y fallos:** planes o pagos no compran membresía ni posición; lista privada no se filtra por URL/sitemap y una lista sin fichas públicas no se indexa. Un destino retirado deja de enlazarse públicamente.

**Pruebas y revisión:** orden manual, unicidad de miembros/posiciones, retirada y recuperación de ficha, permisos, ciudad/slug y SEO. Revisar criterio, lectura extensa, tarjetas y vacíos con teclado.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** criterio e integrantes reales de la lista piloto por verificar; mínimo cinco al cerrar VS16. Bloque contratado pendiente de VS13.

### VS12 — Publicar Lugares con negocios comprobados

**Estado:** pendiente. **Dependencias:** VS11. **Trazabilidad:** R02, R04, R08–R09 / AC02, AC04, AC08–AC09; T03, T11, T13.

**Objetivo:** publicar una ficha Lugar respaldada por negocios ubicados dentro y vinculados con evidencia.

- [ ] Administrar vínculo entre Lugar y negocio con fuente/comprobación, fecha y super admin verificador; validar misma ciudad, tipos y un solo Lugar por negocio con restricción MySQL.
- [ ] Publicar Lugar en `/@{listing}` y categoría Lugares públicos solo con al menos un hijo público, activo y comprobado; no inferir pertenencia por nombre, zona o plan.
- [ ] Mostrar información del Lugar y sus negocios hijos elegibles, con borde Premium si corresponde; aplicar metadatos, sitemap y estados públicos según elegibilidad actual.

**Aceptación y fallos:** sin hijos elegibles no se publica como Lugar útil ni aparece en navegación/indexación; hijo privado, cerrado o sin evidencia no sale. Desaparecer un hijo no borra el historial del vínculo.

**Pruebas y revisión:** segundo Lugar rechazado, concurrencia/unicidad, ciudad y tipos inválidos, evidencia incompleta, retiro del último hijo y cambio de estado operativo. Revisar ficha padre, categoría y separación visual de hijos.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** tres Lugares reales y sus vínculos requieren evidencia antes de VS16; publicidad contextual pendiente de VS13.

### VS13 — Contratar bloques contextuales

**Estado:** pendiente. **Dependencias:** VS12. **Trazabilidad:** R04, R07–R09 / AC04, AC07–AC09; T09, T10, T11, T13.

**Objetivo:** mostrar un bloque contratado adicional por página de lista o Lugar sin confundirlo con sus relaciones editoriales.

- [ ] Completar administración y reserva transaccional del contrato contextual: destino lista/Lugar, negocio anunciado apto, período, material aprobado, responsable y recibo confirmado cuando sea pagado.
- [ ] Impedir más de un bloque simultáneo por página, con contrato adicional separado del paquete Super Premium.
- [ ] Mostrarlo separado de selección e hijos, siempre con «Patrocinado» y elegibilidad temporal en lectura; registrar impresiones/clics por espacio y conservar historial privado.

**Aceptación y fallos:** segundo contrato superpuesto se rechaza incluso concurrentemente; contratar bloque no añade fichas a listas ni vincula negocios a Lugares. Publicidad vencida o en página no pública no se exhibe y cortesía no suma ingresos.

**Pruebas y revisión:** solapamientos y límites temporales, concurrencia MySQL, contratación separada del paquete, retiro de anunciante/página, recibos, permisos y eventos sin PII. Revisar ambos tipos de página y señalización con teclado.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** contratos y materiales reales pendientes si se contratan estos bloques; no se añade un mínimo de bloques contextuales al inventario de salida.

### VS14 — Publicar guías del blog

**Estado:** pendiente. **Dependencias:** VS13. **Trazabilidad:** R05, R08–R09 / AC05, AC08–AC09; T12, T13.

**Objetivo:** editar, programar y consultar guías locales seguras con historial, firma y SEO coherentes.

- [ ] Crear taxonomía de blog separada con Guías locales y etiquetas; administrar ciudad, slug único por ciudad, extracto, cuerpo, firma marca/persona, medios, estado, fecha, SEO y revisiones recuperables.
- [ ] Incorporar párrafos, encabezados, listas, citas, enlaces, imágenes con alt/pie, galerías, tablas simples y URLs validadas de YouTube/Vimeo; sanitizar HTML final sin aceptar incrustaciones arbitrarias.
- [ ] Entregar índice paginado y post en `/{city}/blog` y `/{city}/blog/{slug}`, orden estable por fecha descendente/ID, enlaces internos públicos, BlogPosting y medición de consulta sin PII.
- [ ] Aplicar publicación programada al alcanzar la hora sin depender del cron, privacidad previa, archivo y restauración autorizada con auditoría.

**Aceptación y fallos:** posts privados/futuros/de otra ciudad no se filtran por URL ni sitemap; restaurar revisión no borra auditoría. HTML o vídeos no válidos se rechazan/sanitizan y los enlaces internos no exponen borradores.

**Pruebas y revisión:** permiso editorial, ciudad/slug, antes/en/después de publicación, revisiones, sanitización, URLs de vídeo, orden/paginación y SEO. Revisar artículo largo, tablas, galería, firma y teclado. Cerrar puerta Colecciones.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** cinco guías reales con fuentes, firma y medios autorizados pendientes para VS16.

### VS15 — Validar recorridos integrados

**Estado:** pendiente. **Dependencias:** VS07, VS14. **Trazabilidad:** R01–R09 / AC01–AC09; T06, T13, T15.

**Objetivo:** comprobar el sistema completo con recorridos públicos y administrativos consistentes antes de aprobar contenido real.

- [ ] Revisar las superficies públicas contra design.md y la referencia local aprobada tras la alineación, registrando componentes y diferencias intencionales; revisar la administración con sus patrones Filament. Completar navegación, estados de carga/vacío/error, fotos ausentes, PDF, ofertas vencidas y `/premium` inactiva; eliminar controles de funciones excluidas.
- [ ] Ejecutar regresiones cruzadas de publicación, cambios de slug, relaciones, Gratis → Premium → Super Premium → Gratis, cupos concurrentes y vigencias sin cron.
- [ ] Completar métricas de fichas/listas/blog, contactos, catálogo, recomendaciones/moderación y publicidad; revisar payloads sin PII y reportes que distinguen intención de ventas/visitas confirmadas.
- [ ] Auditar canonical/sitemap/noindex/datos estructurados, privacidad, validación de medios y autorización directa/masiva; comprobar rendimiento y accesibilidad del conjunto.
- [ ] Ejecutar las cuatro comprobaciones aplazadas por el propietario durante la alineación visual: zoom nativo 200 %, lector de pantalla, teclado virtual real y movimiento reducido activado. Registrar navegador/dispositivo, escenarios, resultados y defectos corregidos; conservar como referencia el [registro de alineación](dev_changelog/ALINEACION_VISUAL.md#decisión-aplazar-las-cuatro-comprobaciones-a-la-validación-final-23-10-2026).

**Aceptación y fallos:** retirar ficha, plan, oferta, hijo o campaña actualiza todas sus superficies en el momento correcto sin borrar historial. No quedan enlaces a módulos incompletos ni CTAs comerciales sin aprobación.

**Pruebas y revisión:** suite completa MySQL, Pint, build e inspección de rutas; móvil/tableta/escritorio, teclado, lector de pantalla, zoom 200 %, contraste, objetivos táctiles y movimiento reducido. Registrar resultados y defectos corregidos, no solo una lista de checks esperados.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** ninguno adicional confirmado; defectos de integración impiden cerrar esta tarea. No acredita contenido aprobado ni funcionamiento en producción.

### VS16 — Completar contenido de salida

**Estado:** pendiente. **Dependencias:** VS15. **Trazabilidad:** R10 / AC10; T14.

**Objetivo:** contar con inventario real aprobado, permisos y revisión pública conforme a [contenido y operación](CONTENIDO_Y_OPERACION.md).

- [ ] Verificar 40 negocios y al menos cinco con categoría principal en cada una de las cuatro categorías de negocio; tres Lugares con hijos ubicados dentro y comprobados.
- [ ] Aprobar lista de baile con mínimo cinco fichas verificadas, cinco guías duraderas y tres campañas Super Premium reales o de cortesía con material, cupo y vigencia válidos.
- [ ] Registrar fuentes, responsable y fecha de los datos editoriales; para medios, autorización del super admin y fecha manual de verificación; dejar vacíos datos no confirmados. No copiar demostraciones de capturas ni datos heredados.
- [ ] Revisar enlaces, medios, condiciones comerciales, atribución, textos legales y versión móvil; establecer mantenimiento mensual de datos cambiantes y revisión completa trimestral según operación.

**Aceptación y fallos:** inventario 40/3/5/5/3 comprobado; registro sin verificación no cuenta y permanece borrador. Cortesías consumen cupo pero no ingreso; materiales sin permiso no se publican.

**Pruebas y revisión:** checklist editorial con aprobación fechada y responsables, recuentos y cobertura por categoría, enlaces/medios, hijos de Lugares y recorrido real responsive/teclado. No guardar evidencia privada en Git.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** inventario, fuentes, permisos y textos legales aún no aprobados. No declarar esta tarea completada con factories o contenido de ejemplo.

### VS17 — Preparar y realizar el lanzamiento único

**Estado:** pendiente. **Dependencias:** VS16. **Trazabilidad:** R01–R10 / AC01–AC10; T15.

**Objetivo:** desplegar una sola vez todos los módulos incluidos y el contenido aprobado, con respaldo recuperable y verificación pública.

- [ ] Completar alojamiento/dominio, logotipo oficial, entorno seguro y textos legales aprobados; secretos fuera de Git, Debugbar apagada, sesiones/2FA, almacenamiento, cola y programación configurados según el entorno elegido.
- [ ] Preparar respaldo y procedimiento de restauración/recuperación; ejecutar restauración comprobable antes del despliegue y documentar procedimiento de reversión sin exponer credenciales.
- [ ] Revalidar inventario y vigencias al momento de salida, aprobación editorial y puerta comercial: `/premium` puede seguir oculta sin bloquear el lanzamiento si sus datos siguen pendientes.
- [ ] Ejecutar controles finales, desplegar el conjunto y comprobar HTTP público, enlaces, medios, SEO, medición y privacidad en producción; registrar resultado separado de los checks locales y resolver fallos de salida.

**Aceptación y fallos:** alojamiento, identidad final, contenido o privacidad faltantes impiden publicar; no se sustituye ningún módulo obligatorio por una pantalla incompleta. Sin precios/contacto aprobados no hay `/premium` ni CTA. Restauración fallida o verificación pública fallida impide declarar salida completada.

**Pruebas y revisión:** suite MySQL, Pint, build, rutas, regresiones AC01–AC10 y recorrido responsive/teclado; restauración ensayada con evidencia, revisión editorial final y comprobaciones reales posteriores al despliegue. Registrar fecha, entorno y seguimiento de fallos de la salida.

**Evidencia:** sin ejecución registrada.

**Bloqueos:** alojamiento/dominio y acceso de despliegue, logotipo oficial, aprobaciones legales/editoriales y cierre VS16 pendientes; datos comerciales solo bloquean `/premium`. No hay resultados de producción registrados.

## Cobertura de aceptación

| Criterio de la maestra | Entregas principales | Cierre integral |
| --- | --- | --- |
| AC01 — Rutas, filtros y orden | VS02, VS03, VS04 | VS15, VS17 |
| AC02 — Fichas públicas, datos y slugs | VS02, VS03, VS04, VS12 | VS15, VS17 |
| AC03 — Catálogo, cupos y vigencias | VS08, VS09 | VS15, VS17 |
| AC04 — Listas, Lugares y vínculos | VS11, VS12, VS13 | VS15, VS17 |
| AC05 — Blog, programación y permisos | VS14 | VS15, VS17 |
| AC06 — Recomendación, límites y privacidad | VS05 | VS15, VS17 |
| AC07 — Planes, ingresos y patrocinios | VS06, VS08, VS09, VS10, VS13 | VS15, VS17 |
| AC08 — Autorización y puerta comercial | VS01, VS07; policies en cada módulo | VS15, VS17 |
| AC09 — SEO, accesibilidad, seguridad y medición | VS00–VS15 según superficie; SEO consolidado en VS04 | VS15, VS17 |
| AC10 — Inventario, permisos y salida | VS16 | VS17 |

## Insumos externos y registro de seguimiento

| Insumo | Resolución | Puerta afectada |
| --- | --- | --- |
| Precios Premium/Super Premium e IVA | Propietario aporta valores; super admin aprueba ajustes | Activación pública de VS07 |
| WhatsApp Business y responsable | Propietario aporta contacto/responsable; aprobación administrativa | Activación pública de VS07 |
| Plazo de respuesta opcional | Equipo define y aprueba solo si se promete | Mostrar esa promesa en VS07 |
| Aviso, retención, eliminación y textos legales | Propietario obtiene aprobación y registra versión autorizada | Recepción pública de VS05, analítica y VS17 |
| Datos, fuentes, permisos y acuerdos/cortesías | Investigación y captura desde el inicio; aprobación editorial/comercial | VS16 y revalidación VS17 |
| Logotipo oficial | Propietario entrega archivo maestro autorizado | VS17 |
| Alojamiento, dominio y accesos | Propietario decide entorno; implementación configura sin guardar secretos | VS17 |

Al ejecutar, completar en la tarea correspondiente: fecha, responsable, estado, evidencia y siguiente acción. Un insumo pendiente no implica que una tarea ya esté `bloqueada`: cambiar ese estado cuando efectivamente impida el trabajo o su cierre. El mantenimiento editorial posterior sigue la pauta de operación; este tablero no crea automatizaciones ni un lanzamiento adicional.
