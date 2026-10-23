# Sistema de diseño de Tu Gurú Local

Fecha de extracción: 22 de octubre de 2026. Fuente visual principal: [sitio público Base44](https://preview--turu-guia-local.base44.app/). Estado: manual integrado mediante PR #6 y alineación de las superficies públicas integrada mediante [PR #8](https://github.com/luisfridev/tugurulocal/pull/8) en `master` el 23 de octubre de 2026 (merge `3fbacd06087ef6166574efe239792cadeb394a9d`). Cuatro comprobaciones asistidas se trasladaron a VS15 y siguen pendientes. No acredita producción.

Este documento gobierna el diseño de la web pública y servirá de base al futuro panel de usuario. La [especificación maestra](ESPECIFICACION_MAESTRA.md) gobierna las funciones y [TECH-STACK.md](TECH-STACK.md), la tecnología. Filament conserva su sistema administrativo. Las pantallas de cuenta son referencias futuras y no autorizan cuentas públicas en el lanzamiento.

## 1. Cómo leer y mantener este sistema

Los valores serializados están en [design-tokens.json](design-ref/design-tokens.json). Este manual define su intención y aplicación; el JSON conserva valores y procedencia. Ante una discrepancia, contrastar la evidencia y corregir ambos en la misma intervención, antes de implementar. No resolverla escogiendo silenciosamente otra guía.

| Clasificación | Significado y evidencia requerida |
| --- | --- |
| Medido | Valor obtenido del DOM, CSS calculado, variable o regla CSS. Incluye pantalla, selector y propiedad. |
| Observado | Apariencia o interacción comprobada. Incluye captura o recorrido registrado. |
| Inferido | Decisión de normalización o adaptación para este proyecto. Incluye motivo; no se atribuye a Base44. |
| No verificado | Información que la inspección no pudo confirmar. No sirve como prueba de funcionamiento. |

Los grupos `reference`, `semantic` y `component` del JSON separan extracción, decisiones de uso y asignaciones por componente. Un alias semántico es una decisión inferida aunque su valor de origen sea medido. Los números de este manual se expresan en píxeles CSS; al implementar, convertir dimensiones de texto y espaciado a rem con una raíz de 16 px, manteniendo el reflujo al ampliar texto.

Los nombres de captura ficha-premium y ficha-gratis son etiquetas de análisis según insignias y bloques visibles; no prueban una asignación de plan, pago ni elegibilidad del backend de Base44. En este proyecto esos estados se obtienen de sus reglas de dominio.

Evidencia consultable sin volver al sitio:

- [Manifiesto de capturas](design-ref/screenshots/manifest.json): 17 páginas × 3 tamaños, con viewport y página completa, más estados de interacción.
- [Medidas DOM/CSS](design-ref/measurements.json), [declaraciones seleccionadas](design-ref/css-observations.json) y [comprobaciones responsive](design-ref/responsive-checks.json).
- [Interacciones comprobadas](design-ref/interaction-checks.json), [contraste calculado](design-ref/contrast-checks.json) e [inventario de componentes](design-ref/component-inventory.md).
- [Guía de implementación y comparación](design-ref/implementation-guide.md): adopción, diferencias actuales y procedimiento de regresión.

Las medidas son muestras de estilos por firma de elemento, no una copia exhaustiva del DOM ni una auditoría de todos los controles. Las capturas son JPEG originales del navegador, con recorte explícito, sin reescalado posterior. El viewport es 1440×900, 768×1024 o 390×844, DPR 1. En páginas con barra de desplazamiento, el contenido medido puede ocupar 1425, 753 o 375 px; esa diferencia no es un gutter.

## 2. Dirección visual

Interpretación inferida: guía editorial de ciudad para residentes y visitantes, cercana y legible, con índigo para orientación, lavanda para separar superficies y menta para acciones. ENERGY 2 / RHYTHM 2 / MOTION 1 para la adaptación.

| Decisión | Regla y motivo |
| --- | --- |
| Jerarquía de color | Hero y navegación oscuros; lectura sobre tarjetas blancas. Se distingue orientación de información práctica. |
| Tipografía | Baloo 2 en títulos e Inter en interfaz/cuerpo. La primera aporta voz cercana; la segunda facilita lectura y exploración repetida. |
| Composición | Categorías en retícula; negocios en filas; ficha con columna informativa. Cada disposición responde a una tarea diferente. |
| Ritmo | Secciones relacionadas separadas por 24-40 px; cambios de tema por 64-80 px. No repetir el mismo encabezado centrado y retícula en todos los bloques. |
| Acento | Menta para acciones y navegación activa; violeta reservado a señales Premium y contenido comercial. No convertir cada borde o texto en un acento. |
| Elevación | Sombra suave para superficies agrupadas; elevación al interactuar solo en enlaces de tarjeta. No elevar todos los elementos. |
| Identidad | Mantener la pareja tipográfica y el contraste índigo/menta. El búho y las imágenes del sitio son activos ajenos a esta extracción; conservar la marca textual hasta disponer del logotipo oficial autorizado. |

La referencia declara entrada de ficha de 450 ms y elevación al hover de 300 ms. La adaptación conserva MOTION 1: feedback discreto al interactuar y entrada estática por defecto. No añadir movimiento continuo, temas alternativos ni una nueva colección de ilustraciones.

## 3. Color y contraste

Valores medidos en `inicio-1440`, `negocios-1440`, `ficha-premium-1440` y `cuenta-acceso-390`. Los selectores concretos están en cada token.

| Función | Referencia medida | Uso en el proyecto |
| --- | --- | --- |
| Marca profunda | `#2D2652` | Hero, pie, acciones sobre menta. |
| Acción | `#40E0D0` | Fondo de acción principal con texto índigo. |
| Premium | `#A958A9` | Borde orgánico; relleno comercial únicamente con contraste verificado. |
| Página | `#F6F4FB` | Fondo público lavanda. |
| Superficie | `#FFFFFF` | Tarjetas y formularios. |
| Texto | `#1B1730` | Lectura sobre claro. |
| Secundario sólido | `#666080` | Metadatos sobre página o blanco; sustituye opacidades insuficientes. |
| Cabecera de ficha | `#202B50` | Información de identidad y contactos. |
| Borde de cabecera | `#39466C` | Delimitación decorativa del bloque de ficha. |
| Acento de ficha | `#80E6D8` | Iconos y datos sobre la cabecera oscura. |
| Navegación global | `rgba(45,38,82,.85)` con blur de 8 px | Valor calculado observado; el color resultante depende del contenido posterior. El CSS declara respaldo al 95 %. |
| Separador de tarjeta | `rgba(228,223,240,.9)` | Separador tenue, nunca único indicador de un control o estado. |

Hay dos paletas declaradas en la fuente: las páginas públicas usan HEX directos y los formularios usan variables HSL. Por ejemplo, la menta del formulario se calcula como `#3EE0CF` y su título como `#17132A`. Son variantes medidas, no errores de extracción. Decisión inferida: el futuro formulario del proyecto reutilizará los alias semánticos públicos, sin crear una segunda identidad.

Resultados calculados con composición alfa antes de luminancia:

| Pareja | Contraste | Decisión |
| --- | --- | --- |
| Índigo sobre menta | 8.47:1 | Admitida para acciones. |
| Blanco sobre índigo | 13.90:1 | Admitida. |
| Texto principal sobre lavanda | 15.89:1 | Admitida. |
| Violeta sobre blanco | 4.51:1 | Cumple por margen pequeño; no bajar opacidad ni asumir que cumple sobre un tinte violeta. |
| Menta sobre blanco | 1.64:1 | No usar para texto normal ni como único límite funcional. |
| Texto principal al 60 % sobre lavanda | 4.44:1 | Sustituir por secundario sólido. |
| Foco menta `#55E0CE` sobre lavanda | 1.49:1 | No reutilizarlo como foco sobre claro. |

Adaptación inferida: foco de 3 px, separado 4 px, `#8D4C94` sobre claro; `#40E0D0` sobre oscuro. El primero alcanza 5.36:1 sobre lavanda. El foco puede usar halo blanco para separar bordes Premium. No usar solo un cambio de color de texto.

Texto normal: mínimo 4.5:1; texto grande, 3:1 (24 px regular o aproximadamente 18.67 px en negrita). Controles y señales funcionales deben distinguirse a 3:1 cuando corresponda. Los separadores decorativos no sustituyen señales funcionales. [Referencia de contraste W3C](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html).

Taxonomías conservan su color administrable: aplicarlo dentro de su región, con texto negro o blanco calculado a 4.5:1, y respaldo de marca si falta. No sustituir los colores de todas las páginas por el color de una categoría. Texto sobre fotos necesita fondo sólido o scrim y comprobación del peor punto; esta extracción no certifica todas las fotografías.

## 4. Tipografía

Familias y estilos calculados medidos; archivo efectivo por glifo no verificado. La fuente enlaza Google Fonts con Baloo 2 (500, 600, 700, 800) e Inter (400, 500, 600, 700). El puente devuelve `document.fonts.status=loaded` pero `faces=[]`; no permite certificar cada archivo. La futura implementación debe servir fuentes locales y comprobar su carga.

| Contexto | <640 px | 640-1023 px | ≥1024 px | Peso |
| --- | --- | --- | --- | --- |
| H1 Inicio | 36 / 37.8 | 48 / 48 | 60 / 60 | 800 |
| H1 listado/categoría | 30 / 36 | 36 / 40 | 36 / 40 | 800 |
| H2 de sección pública | 24 / 32 | 30 / 36 | 30 / 36 | 800 |
| H1 ficha | 24 / 26.88 | 32 / 35.84 | 32 / 35.84 | 800 |
| H2 ficha | 18 / 28 | 18 / 28 | 18 / 28 | 700 |
| Nombre en fila de negocio | 16 / 24 | 16 / 24 | 16 / 24 | 700 |
| Cuerpo base | 16 / 24 | 16 / 24 | 16 / 24 | 400 |
| Metadatos compactos | 12 / 16 | 12 / 16 | 12 / 16 | 500-600 |

Notación: tamaño / interlineado, en px CSS. El H1 de ficha tiene tracking de -0.8 px; cuerpo y títulos públicos no deben heredar ese ajuste. En Inicio, la introducción es 16 px en móvil y 18/28 desde 640 px. Su ancho máximo medido es 576 px; el del H1, 768 px.

Los SVG inspeccionados incluyen iconos de 14 y 16 px, con trazos de 1.75, 2 y 2.5 según función; no hay un único grosor universal. Adaptación: usar marcas relevantes del sistema existente, mantener tamaños coherentes por contexto, ocultar al lector los iconos decorativos y dar nombre accesible a los controles solo con icono. No importar una biblioteca por su apariencia ni copiar los trazados del logotipo.

Usar jerarquía HTML por significado, no para obtener un tamaño. El cuerpo editorial propio tendrá límite propuesto de 70ch. Permitir nombres largos, acentos y emojis administrados; no imponer saltos de línea tomados de los textos originales. La fila de referencia trunca el nombre y limita el resumen; adaptar para que el nombre completo siga disponible y el contenido no desaparezca al ampliar texto.

## 5. Retícula, espaciado y formas

Contenedor medido: ancho 100 %, máximo 1152 px, centrado, `box-sizing: border-box`. Padding horizontal 20 px por lado hasta 639 px y 32 px desde 640 px. El máximo incluye esos gutters: a 1440 px con barra de 15 px, el contenedor mide 1152 px y el contenido 1088 px. Hero y pie alcanzan el ancho disponible de la página; su contenido usa el contenedor común.

| Umbral inclusivo | Cambio comprobado |
| --- | --- |
| 640 px | Gutter 20→32; H1 de Inicio 36→48; categorías 1→2 columnas; reaparece miniatura de fila. |
| 768 px | Navegación superior visible; navegación inferior oculta. |
| 1024 px | H1 de Inicio 48→60; categorías 2→3 columnas; ficha pasa a cuerpo 2/3 + lateral 1/3. |
| 1280 / 1536 px | Media queries declaradas; sin cambio funcional adicional certificado. |

Se probaron los pares 639/640, 767/768 y 1023/1024 sin desbordamiento de página en Inicio. No equivale a probar todas las páginas en todos los anchos intermedios.

Espaciado observado: 4, 6, 8, 12, 14, 16, 20, 24, 28, 32, 40, 48, 56, 64, 80 y 96 px. Base de composición inferida: pasos de 4 px, admitiendo los medios pasos de componentes medidos. No redondear 14 a 16 en una reproducción sin registrar el cambio.

| Elemento | Medida y comportamiento |
| --- | --- |
| Cabecera | Fila interior de 64 px más borde inferior de 1 px; sticky arriba, z-index 40. |
| Navegación inferior | 60 px medidos, más safe area; fija abajo, z-index 40. Reservar espacio al final de todo el documento. La fuente añade 80 px al main; comprobar también el pie. |
| Hero Inicio | Padding vertical 64 px móvil y 96 px desde 640. Altura según contenido, sin 100vh. |
| Secciones de Inicio | Primera separación 40/56 px; cambio de tema 64/80 px, móvil/ancho. |
| Filas de negocio | Una columna; gap vertical 12 px. Padding 14 px móvil y 16 px desde 640; gap interno 16 px. |
| Tarjetas de categoría | Gap 16 px, padding 20 px, retícula 1/2/3 columnas. Las cinco categorías producen 3+2, sin inventar una sexta. |
| Cuerpo de ficha | Gap 24 px; columnas iguales de base con contenido ocupando dos y lateral una desde 1024. Antes, una sola columna en orden DOM. |
| Panel de ficha | Radio 22 px; padding 28×24 móvil y 30 arriba, 34 horizontal, 28 abajo desde 640. |
| Tarjeta pública | Radio 24 px; borde decorativo 1 px. |
| Campo / botón de formulario futuro | Alto 48 px; radio 14 px. |
| Contactos de ficha | Radio 12 px; padding 10×15, gap 9 px; alto observado cercano a 39 px. Ampliar hit area a 44 px. |
| Chip | Radio completo; alto observado 36 px. Área táctil mínima de adaptación: 44 px. |
| Miniatura de fila | 64×64, radio 16, visible desde 640 en la fuente. El logo obligatorio del negocio debe seguir disponible en la adaptación móvil. |
| Logo de ficha | Región circular 72×72. Usar logo autorizado con `object-contain`; portada y galería son medios separados. No tratar la foto de ejemplo como logo verificado. |
| Formulario futuro | Contenedor máximo 448 px; gutter exterior 16 px; tarjeta radio 16 y padding 32. |

Sombra base medida: `0 1px 2px rgba(45,38,82,.04), 0 12px 32px -16px rgba(45,38,82,.18)`. En hover declarado: `0 2px 4px rgba(45,38,82,.06), 0 24px 48px -20px rgba(45,38,82,.28)`, con desplazamiento -3 px durante 300 ms. Son valores de referencia; no se ejecutó hover con puntero. Con movimiento reducido, eliminar traslación y animación de entrada, conservando foco y feedback textual.

## 6. Composición por familia

### Inicio

Medido/observado: hero de ancho completo; accesos horizontales a categorías; vitrina con un elemento principal y secundarios; retícula de categorías; filas de negocios; bloque comercial y pie. La retícula de ofertas tiene gap 16 y cambia de una columna a una composición principal/secundarias a 1024 px.

Adaptación: mantener hero, accesos y ritmo; reemplazar ranking por selección editorial y recientes, conforme a VS03. Mostrar zonas con resultados. Omitir cifras y promesas de la referencia. Los módulos comerciales solo aparecen cuando hay contenido elegible. El carrusel Super Premium, su cupo, pausa y rótulo Patrocinado vienen de la maestra; no se observaron como carrusel en Base44.

### Negocios, categoría y zona

Medido/observado: cabecera índigo; chips horizontales solapados visualmente con la base; conteo; selector de orden; filas. Las cinco categorías usan la misma estructura y no muestran cinco sistemas cromáticos distintos.

Adaptación: la página Base44 `/explorar` se corresponde con `/{city}/negocios`. Conservar filtros GET de categoría/zona con Aplicar y limpieza por contexto, 12 resultados y orden orgánico vigente. Chips son accesos de navegación o filtros compatibles con ese contrato, sin introducir actualización automática obligatoria. Eliminar selector por votos, números de puesto, columna de votación y favoritos; el espacio liberado lo ocupa el contenido. No dejar huecos simulando controles retirados. Categoría y zona reutilizan identidad, contenedor, filtros y filas.

### Ficha

Medido/observado: cabecera oscura con regreso, imagen circular, nombre, metadatos y contactos; promoción opcional; tarjetas de descripción, productos/ofertas, PDF, información y etiquetas. Premium añade bloques que no aparecen en la ficha Gratis observada. Lugar comparte la estructura básica.

Adaptación: mantener la ficha Gratis completa. Reutilizar los datos aprobados del proyecto, sin inventar precios, contactos, calificaciones ni horarios. Logo, portada, galería y PDF conservan sus permisos independientes. No ocultar el PDF por plan. Nombre largo refluye; contactos ausentes se omiten. No copiar contador ni botón de voto. La ficha Lugar futura necesita negocios hijos verificados: esa relación no quedó acreditada por la página Base44.

### Premium

Medido/observado: hero comercial; tarjetas de beneficios/comparación; bloques explicativos y CTA. La referencia no acredita los precios, impuestos, condiciones ni los tres planes del proyecto.

Adaptación: comparar Gratis, Premium y Super Premium con información aprobada, sin promesas de ranking. La puerta comercial gobierna página, navegación, CTA y sitemap. No añadir un botón comercial deshabilitado para suplir datos pendientes.

### 404 y cuenta futura

La 404 observada es una pantalla genérica en inglés, fondo claro, número 72/72 y botón de regreso; no comparte el shell público. La adaptación debe usar español, identidad pública y salida útil sin repetir la ruta solicitada. Es una diferencia intencional, no un diseño fiel pendiente de copiar.

Acceso, registro y recuperación usan una columna centrada y tarjeta de 448 px. Registro añade confirmación de contraseña. Restablecimiento sin token muestra un error de enlace inválido; no se observó formulario válido de cambio. La invitación `/beneficios` usa el shell público y botones sociales. Todo este grupo queda reservado: no implementar autenticación, proveedores sociales, votos ni favoritos. El diseño interno de `/mi-panel` sigue no verificado.

## 7. Estados, navegación y accesibilidad de la adaptación

| Estado/comportamiento | Contrato del proyecto y procedencia |
| --- | --- |
| Enlace activo | `aria-current="page"` y señal visible además del color; adaptación a rutas reales. |
| Filtro seleccionado | Valor preservado en URL y control; Aplicar reinicia página. Comportamiento vigente del proyecto, no derivado del filtro automático de Base44. |
| Foco | 3 px con offset 4 y contraste según fondo; orden DOM coherente, sin tabindex positivo. Mantener enlace para saltar al contenido. |
| Tacto | Área mínima 44×44 por decisión del proyecto; no afirmar que ese es el umbral AA de WCAG 2.2. Separar acciones contiguas. [Criterio W3C](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html). |
| Carga | Conservar contenido útil y anunciar la operación con texto; no sustituir toda la página por un spinner. El texto transitorio se observó durante navegación; no se obtuvo una captura estable de todos los estados de carga. |
| Vacío | Distinguir falta de publicaciones de una combinación sin resultados y ofrecer limpieza o Negocios. Contrato existente; no se obtuvo un vacío de datos reproducible en Base44. |
| Error | Mensaje concreto y camino útil; no conservar el inglés ni la ruta reflejada de su 404. Los errores de formulario remoto no se provocaron con envíos. |
| Disabled | Texto y semántica de indisponibilidad cuando una operación real lo necesite. Variante visual no certificada en la referencia; no deshabilitar navegación hacia módulos no disponibles: omitirla. |
| Contenido largo | Reflujo, min-width 0 y nombres completos; sin altura fija en bloques de texto. Verificar 200 % y anchos intermedios durante la alineación. |
| Medios ausentes | Omitir portada/galería/PDF faltantes; no usar fotografía de otro negocio. Un logo no elegible conserva las reglas de publicación. |
| Plan y publicidad | Borde Premium en tarjeta orgánica, selección por texto/ubicación y anuncio con Patrocinado visible son señales diferentes. |
| Overlay futuro | Solo si un flujo autorizado lo necesita: nombre accesible, Escape, foco contenido y retorno al disparador. No se verificó un modal público en esta extracción. |

Comprobaciones observadas: categoría con clic y Enter; selector Recientes con Enter y URL preservada; Tab hacia el siguiente control; foco en el enlace de recuperación; retorno de 404 mediante Enter; redirección del panel a beneficios. Es una muestra, no certificación completa de teclado o lector de pantalla. Zoom nativo 200 %, teclado virtual, dispositivo físico y preferencia de movimiento reducido quedan pendientes para la alineación.

## 8. Diferencias con la guía y aplicación anteriores

| Aspecto | Anterior / actual | Valor o decisión de adopción |
| --- | --- | --- |
| Fuentes | Nunito Sans e Instrument Sans locales | Baloo 2 e Inter declaradas por Base44; instalación local futura con licencia revisada. |
| Marca | `#2C254E` | `#2D2652`. |
| Acción | `#74DDCF` | `#40E0D0`. |
| Fondo | `#F6F3FA` | `#F6F4FB`. |
| Premium | `#9E5CA4` | `#A958A9`; mantener separación de publicidad. |
| Texto | `#2B283D` | `#1B1730`; secundario sólido `#666080`. |
| Listados actuales | Retícula 1/2/3 de tarjetas verticales | Filas de una columna, adaptadas sin votos, con filtros GET y paginación. |
| Cabecera actual | Navegación en flujo, dos destinos reales | Shell visual de referencia; enlaces solo a destinos disponibles, con patrón móvil adaptado. |
| Logo/ornamento | Marca textual provisional | Mantener hasta recibir original autorizado; no copiar búho ni ilustración de hero. |

La guía anterior queda conservada en Git como antecedente y su archivo actual remite aquí. Sus capturas originales siguen siendo referencias históricas. No mezclar valores aproximados de JPG con valores CSS medidos sin documentar el motivo.

## 9. Límites y puerta de adopción

La documentación no modifica aplicación, dependencias, datos, medios ni pruebas. El sistema se considerará aplicado cuando se alinee la web existente y se complete la comparación por región descrita en la guía de implementación. Hasta entonces, no declarar las pantallas actuales conformes con este manual.

Toda intervención con UI debe registrar versión/base de este manual, componentes afectados, diferencias intencionales, capturas locales, teclado, foco, contraste, estados y pendientes en su registro. La auditoría completa se repetirá en VS15. Las verificaciones no ejecutadas permanecen pendientes; las funciones y superficies futuras no se consideran implementadas por estar documentadas aquí.
