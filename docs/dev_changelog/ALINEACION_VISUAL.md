# Alineación visual de pantallas públicas

**Fecha:** 23 de octubre de 2026. **Estado:** implementación verificada e integrada en `master` mediante PR #8. Cuatro comprobaciones asistidas aplazadas a VS15 por decisión del propietario. **Rama de implementación:** `codex/alineacion-visual-superficies-publicas`. **Base:** `master` y `origin/master` en `0b986e22e4b37d3cb52aa9ba1ad87e56d82fcf2d`, con árbol limpio antes de crear la rama.

## Objetivo y dirección

Alinear Inicio, directorios de negocios/categorías/zonas, fichas y 404 con [design.md](../design.md), tokens e inventario integrados por PR #6, manteniendo el contrato y contenido público existentes. Dirección editorial cercana y legible; índigo para orientación, lavanda para superficies y menta para acciones. Diales **ENERGY 2 / RHYTHM 2 / MOTION 1**.

Antislop activo: `during`, regla del proyecto. Skills aplicadas: `antislop` como base; `antislop-ui`, `antislop-human` y `antislop-layoutmobile` para composición, accesibilidad y reflujo; `tailwindcss-development` y `laravel-best-practices` para las vistas Blade y estilos Tailwind; `testing-best-practices` para reutilizar la cobertura existente. No se añadieron dependencias ni pruebas nuevas.

## Cambios y diferencias intencionales

- Shell público con cabecera sticky, navegación superior desde 768 px y navegación inferior móvil con los dos destinos implementados. Se reservó espacio bajo el contenido y en el pie para la navegación fija y el safe area.
- Hero ancho completo, jerarquía tipográfica y retícula de categorías de una/dos/tres columnas. Las categorías usan tarjetas claras con acento lateral según su color de identidad.
- Directorios en filas de negocio con logo autorizado y metadatos, manteniendo filtros GET, botón Aplicar, estados vacíos, paginación de doce y orden orgánico. Ficha en cabecera oscura y paneles con espaciado y radio del manual. 404 en español con destinos útiles y sin repetir la URL solicitada.
- Se ajustó el ancho del formulario de reporte para conservar gutters al pasar el shell público a ancho completo; su flujo no cambió.
- Se conservaron Nunito Sans e Instrument Sans, ya instaladas localmente. Baloo 2 e Inter son las familias observadas en Base44, pero el proyecto no dispone de esos archivos locales ni se autorizó añadir dependencias o assets; queda pendiente revisar licencia y aprovisionamiento antes de sustituirlas.
- La navegación móvil muestra solo Inicio y Negocios, los destinos públicos disponibles. No incorpora acceso, ranking, voto, favoritos ni enlaces a módulos futuros. Filament y sus patrones administrativos no se tocaron.

## Comprobaciones

| Verificación | Resultado observado |
| --- | --- |
| Pruebas enfocadas `PublicDiscoveryTest` y `ListingPublicTest` | PASS: 42 pruebas, 423 aserciones. |
| Suite completa `php artisan test --compact` | PASS: 155 pruebas, 2.078 aserciones, MySQL local de pruebas. |
| `vendor/bin/pint --dirty --format agent` | PASS. |
| `npm run build` | PASS con Tailwind 4.3.3 / Vite 8.3.5 instalados. |
| `git diff --check` | PASS. |
| Herd: Inicio, directorio, categoría Servicios, zona DEMO Centro, ficha DEMO Estudio Plano, vacío `Servicios?zona=quinta-avenida` y ruta 404 | PASS: contenido, navegación, estados y enlaces presentes; el 404 no muestra la ruta pedida dentro de su mensaje. El directorio anuncia 30 negocios y páginas de 12. |
| Responsive | PASS en revisión de navegador con viewports 390×844, 768×1024 y 1280×800; distribución y navegación cambian según el ancho. Sin desplazamiento horizontal visible en las capturas revisadas. |
| Teclado y foco | PASS: enlace para saltar al contenido visible al enfocar; Enter lo activa. Selector de categoría operado con flechas/Enter; Tab hasta Aplicar y Enter conservaron el filtro en la URL y mostraron Limpiar filtros. |
| Contraste medido con `contrast-check.py` | PASS: texto/lavanda 15,89:1; secundario/blanco 5,92:1; blanco/índigo 13,90:1; menta/índigo 8,47:1; acento de ficha/fondo oscuro 9,34:1; foco violeta/lavanda 5,36:1. Colores de identidad mantienen el contraste calculado por sus métodos existentes. |
| Zoom nativo 200 %, lector de pantalla, teclado virtual y preferencia de movimiento reducido | Aplazadas a VS15 por decisión del propietario del 23-10-2026. Conservan estado pendiente de ejecución. La continuación de abajo añade mediciones de reflujo, semántica, foco y reglas CSS cargadas; no las presenta como ejecución de estas cuatro pruebas. |

### Delivery Gate de Antislop

| Criterio | Resultado y evidencia |
| --- | --- |
| R-02 redacción | PASS: copy de negocio conservado; etiqueta de error en español. |
| R-03 responsive | PASS en los viewports registrados; el zoom al 200 % queda pendiente. |
| R-17 cifras | PASS: no se añadieron cifras de negocio; contenido DEMO se mantiene rotulado. |
| R-18 testimonios | No aplica: no se añadieron testimonios. |
| R-23 activos | PASS: medios existentes; navegación solo hacia destinos implementados. |
| R-24 enlaces | PASS: navegación, ficha, filtros, vacío, paginación y 404 inspeccionados; destinos conservados. |
| R-25 contraste | PASS para los pares de marca medidos arriba; identidad de taxonomías conserva cálculo existente. |
| R-26 interacción | PASS: filtro GET aplicado por teclado; enlaces y paginación disponibles; cobertura de interacción existente pasó. |
| R-27 estados | PASS: vacío por filtros y 404 comprobados; filtros envían una petición GET, sin estado asíncrono de carga nuevo. |
| R-28 FAQ | No aplica: no se añadieron preguntas; las FAQs existentes de ficha se conservan. |
| R-32 teclado | PASS: salto al contenido, foco visible y filtro operados con teclado. |
| R-33 cambios | PASS: edición directa de fuentes; sin scripts de parcheo. |
| R-34 temas | No aplica: no hay selector de tema; se conserva el tema público claro. |
| R-35 ejecución | PASS: build, pruebas y recorridos locales ejecutados; límites de zoom/lector/teclado virtual arriba. |
| R-36 afirmaciones | PASS: no se añadieron promesas ni afirmaciones de producción. |
| R-37 dirección | PASS: design.md integrado; ENERGY 2 / RHYTHM 2 / MOTION 1 declarados antes de editar. |
| R-38 contenido | PASS: contenido y recursos DEMO existentes siguen identificados; no se inventó información comercial. |
| R-01 color | PASS: paleta tomada de tokens del manual, sin gradientes nuevos. |
| R-04 iconos | No aplica: no se añadieron iconos. |
| R-06 tipografía | Diferencia intencional documentada: se mantienen las fuentes locales hasta resolver licencias y archivos locales de las fuentes observadas. |
| R-07 fondo / R-08 flechas | No aplica: no se añadieron patrones ni flechas decorativas. |
| R-09 etiquetas | PASS: no se añadieron insignias comerciales o funcionales. |
| R-10 blur | PASS: blur limitado a la cabecera sticky. |
| R-12 sombra | PASS: sombra aplicada a filas para separación y respuesta hover; no a todas las superficies. |
| R-13 glow | No aplica: no se añadió glow. |
| R-14 tarjetas | PASS: categorías y negocios usan composiciones distintas según su contenido. |
| R-19 movimiento | PASS en fuente: feedback de hover discreto y desactivado bajo `prefers-reduced-motion`; no se verificó esa preferencia en navegador. |
| R-22 ilustraciones | No aplica: no se añadieron ilustraciones. |
| R-05/R-11/R-15/R-16/R-20/R-21/R-29/R-30/R-31 | PASS: composición por tarea, radios diferenciados, acciones con nombre, sin buzzwords, paleta e identidad del manual, tema claro y decisiones anotadas arriba. |
| C-1/C-2/C-3/C-5 | PASS: decisiones justificadas, interacciones preservadas, contenido DEMO no presentado como real y afirmaciones limitadas a evidencia local. |
| C-4 | Pendiente en zoom 200 %, lector de pantalla y teclado virtual; se registraron por separado y no se afirma cobertura completa. |

## Revisión local en Herd

URLs revisadas: [Inicio](https://tugurulocal-nuevo.test/playa-del-carmen), [Negocios](https://tugurulocal-nuevo.test/playa-del-carmen/negocios), [Servicios](https://tugurulocal-nuevo.test/playa-del-carmen/servicios), [DEMO Centro](https://tugurulocal-nuevo.test/playa-del-carmen/zonas/demo-zona-centro), [vacío con filtro](https://tugurulocal-nuevo.test/playa-del-carmen/servicios?zona=quinta-avenida), [ficha DEMO](https://tugurulocal-nuevo.test/@demo-estudio-plano) y [404 de revisión](https://tugurulocal-nuevo.test/no-encontrada-visual-check). Capturas comparativas del navegador se inspeccionaron frente al manual y las referencias de `docs/design-ref/screenshots/`; `docs/dev_changelog/` conserva solo esta evidencia en Markdown.

No hay despliegue ni verificación de producción. La diferencia de fuentes y las verificaciones accesibles pendientes no bloquean la apertura de la PR; deben permanecer visibles al revisar el alcance antes de la fusión.

## Entrega para revisión

PR [#8: Alineación visual de pantallas públicas](https://github.com/luisfridev/tugurulocal/pull/8), abierta hacia `master` desde `codex/alineacion-visual-superficies-publicas`. La implementación y las comprobaciones locales están documentadas arriba. La sección posterior registra su integración comprobada.

## Continuación: comprobaciones de accesibilidad (23-10-2026)

Se conserva Antislop `during` por regla del proyecto, la revisión de design.md y ENERGY 2 / RHYTHM 2 / MOTION 1. Se aplican las skills base, human, layoutmobile y UI a la revisión, y copywriting a este registro. No se modifica código, contenido comercial ni dependencias.

El navegador integrado ofrece controles de viewport y observación DOM/AX. No ofrece activación de lector de pantalla, teclado virtual ni emulación de `prefers-reduced-motion`. Los atajos de zoom enviados no produjeron un porcentaje nativo verificable. Por ello se midió `innerWidth`/`innerHeight` después de ajustar cada viewport; las dimensiones pedidas al controlador no se asumieron como dimensiones CSS.

| Comprobación complementaria | Resultado observado y límite |
| --- | --- |
| Reflujo, siete URLs de la revisión anterior | PASS en medición DOM: 640×400 y 320×600 píxeles CSS reales. `scrollWidth` coincide con `innerWidth` en las catorce mediciones; a 320 px no hay enlaces, botones o campos visibles cuyos límites salgan del ancho del viewport. 640×400 sirve como comprobación de espacio equivalente a una ventana de 1280×800 al 200 %; no acredita zoom nativo ni ampliación exclusiva de texto. |
| Semántica de las siete pantallas | PASS en inspección DOM: `lang="es"`, un `main` y un `h1` por página, cero imágenes sin atributo `alt` y cero campos visibles sin etiqueta asociada o nombre ARIA. Es una comprobación estructural, no una sesión con lector de pantalla. |
| Árbol accesible e interacción de FAQ | PASS en AX del navegador: título principal, navegación con nombre, campos Categoría/Zona y paginación expuestos. En la ficha DEMO, Enter sobre «¿Es un negocio real?» cambia el control de contraído a expandido y expone la respuesta. La lectura hablada con VoiceOver/NVDA queda pendiente. |
| Formulario con altura reducida | PASS para este escenario: [reporte de ficha DEMO](https://tugurulocal-nuevo.test/@demo-estudio-plano/reportar), viewport CSS 390×400, foco en `explanation`. El campo ocupa Y=115,18–284,99 px y la navegación inferior empieza en Y=339,09 px; el campo completo queda visible. No se escribió ni envió un reporte. Esto simula menos espacio disponible, sin abrir un teclado virtual real. |
| Movimiento reducido en CSS cargado | PASS en inspección de las reglas interpretadas por el navegador: `(prefers-reduced-motion: reduce)` contiene `.listing-row { transition: none; }` y `.listing-row:hover { transform: none; }`. La transición normal calculada es de 0,3 s. Falta activar la preferencia y comprobar el hover con ella. |
| Delivery Gate R-03, R-19, R-35 y C-4 | Reflujo medido, semántica, foco en altura reducida y presencia de reglas: PASS dentro de los escenarios descritos. Zoom nativo 200 %, lectura hablada, teclado virtual y activación de movimiento reducido: pendientes; no se declara completada la revisión accesible. |
| Registro y alcance documental | PASS: `git diff --check` correcto; se preserva la evidencia anterior, se distingue cada método de su límite y se corrige el SHA base transcrito. PHPUnit, Pint y build no se repiten porque no cambian fuentes de aplicación. |

Para cerrar los cuatro pendientes se necesita comprobar, en un navegador/dispositivo con esos controles: zoom nativo al 200 % sin recortes; lectura de títulos y filtros y operación de la FAQ con lector de pantalla; hover sin desplazamiento con «Reducir movimiento» activado; y campo del reporte visible con el teclado del teléfono abierto. Estos resultados siguen pendientes de observación. En esta revisión la PR #8 seguía abierta; se integró después, como se registra en la continuación final. Ninguna revisión local acredita producción.

## Decisión: aplazar las cuatro comprobaciones a la validación final (23-10-2026)

El propietario decidió omitir por ahora las pruebas de zoom nativo al 200 %, lector de pantalla, teclado virtual real y movimiento reducido activado. Se trasladan a [VS15: Validar recorridos integrados](../MEMORY.md#vs15--validar-recorridos-integrados), antes del lanzamiento. Siguen pendientes de ejecución y no se registran como PASS. Esta decisión permitió revisar e integrar la alineación sin esperar esas cuatro pruebas. La integración de la PR #8 cerró la puerta previa a VS04.

Antislop `during`, regla del proyecto; base y copywriting para documentar la decisión sin cambiar la evidencia anterior. Revisión documental: PASS en alcance, coherencia con el tablero y conservación de resultados y límites. `git diff --check`: PASS. No se modificó código ni se repitieron PHPUnit, Pint, build o revisión visual por este cambio documental. VS15 conserva su estado pendiente y no se crea un registro de ejecución anticipado.

<a id="continuacion-registrar-integracion-en-master-23-de-octubre-de-2026"></a>

## Continuación: registrar integración en master (23 de octubre de 2026)

GitHub confirmó que la [PR #8](https://github.com/luisfridev/tugurulocal/pull/8) está fusionada hacia `master`, con fecha `2026-10-23T06:29:09Z` (01:29:09 en America/Cancun) y merge `3fbacd06087ef6166574efe239792cadeb394a9d`. Ese merge es el `HEAD` de `master` y `origin/master` al iniciar esta corrección. VS03 y ambos hitos de la puerta de diseño están integrados; VS04 puede comenzar en su rama propia.

Los cuatro controles asistidos siguen trasladados a VS15: zoom nativo al 200 %, lector de pantalla, teclado virtual real y movimiento reducido activado. No se marcan como completados ni bloquean la alineación ya integrada. Las pruebas de la PR #8 son locales; no hubo despliegue ni verificación de producción.

Esta intervención documental corrige el estado vigente en el tablero, README, guía visual, plan, manual, stack y referencias de adopción/revisión; también aclara la fusión posterior de PR #5. Revisión Antislop `during` (regla del proyecto), base y copywriting:

| Criterio | Resultado y evidencia |
| --- | --- |
| Precisión, fecha, destino y estado (R-16, R-17, R-36; C-5) | PASS: PR #8, merge, fecha y commit contrastados con GitHub; el merge coincide con HEAD y `origin/master` al iniciar la corrección. Las cuatro comprobaciones aplazadas permanecen pendientes en VS15. |
| Coherencia y redacción (R-02; copywriting) | PASS: README, tablero, plan, GUIA_VISUAL, design.md, TECH-STACK y referencias de adopción/revisión describen la puerta integrada; VS04 figura pendiente y habilitada para iniciar en rama propia. |
| Enlaces y anclas locales | PASS: destinos revisados en los diez documentos modificados; todos existen y las anclas Markdown/HTML resuelven. |
| `git diff --check` | PASS: sin errores de whitespace. |
| Aplicación, pruebas, UI y producción | No aplica: cambio exclusivamente documental. No se repiten PHPUnit, Pint, build ni Herd; no acredita producción. |

La corrección conserva los registros de entrega como evidencia de su estado en aquel momento y añade las fusiones posteriores donde corresponde. No modifica código de aplicación, dependencias, datos ni las cuatro comprobaciones transferidas a VS15.

La corrección se entrega mediante [PR #9: Docs, registrar integración de la alineación visual](https://github.com/luisfridev/tugurulocal/pull/9), abierta hacia `master` desde `codex/registrar-integracion-alineacion`, commit inicial `5951633333ca5817dfcb43f7165607dfc86642b6`. El propietario conserva la revisión y fusión; el tablero mantiene esa integración documental como pendiente mientras la PR siga abierta.
