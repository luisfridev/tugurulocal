# Alineación visual de pantallas públicas

**Fecha:** 23 de octubre de 2026. **Estado:** implementación verificada; integración pendiente en PR. **Rama:** `codex/alineacion-visual-superficies-publicas`. **Base:** `master` y `origin/master` en `0b986e22d4e37d3cb52aa9ba1ad87e56d82fcf2d`, con árbol limpio antes de crear la rama.

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
| Zoom nativo 200 %, lector de pantalla, teclado virtual y preferencia de movimiento reducido | Pendiente: no quedaron verificables con la sesión de navegador disponible; no se declaran PASS. |

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
