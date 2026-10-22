# Inventario de componentes de la referencia Base44

Extracción del 22-10-2026 desde [Base44](https://preview--turu-guia-local.base44.app/). Autoridad de diseño: [design.md](../design.md). Valores serializados: [tokens](design-tokens.json). Aplicación al código pendiente; Filament excluido. Las variantes de cuenta quedan reservadas al futuro.

## Evidencia y convenciones

Los identificadores de pantalla de las tablas resuelven en [measurements.json](measurements.json) y el [manifiesto](screenshots/manifest.json). Por ejemplo, `negocios-390` corresponde a `negocios-390-viewport.jpg` y `negocios-390-full.jpg`. Cada muestra identifica selector, rectángulo y CSS. Los selectores describen la referencia en la fecha de captura; no son API del proyecto ni nombres que deban copiarse a Blade.

Medido significa propiedad DOM/CSS; observado significa captura o acción ejecutada; inferido significa adaptación propuesta; no verificado significa que no existe evidencia suficiente. Un comportamiento descrito en CSS no equivale a una interacción ejecutada.

## Shell, navegación y orientación

| ID / componente | Anatomía y variantes de referencia | Evidencia | Adaptación y reutilización |
| --- | --- | --- | --- |
| C01 Contenedor | 100 %, máximo 1152, centrado; gutters 20 o 32 desde 640. | Medido: `negocios-1440`, `inicio-390`; `.guru-shell` en CSS. | Un contenedor común para todas las superficies públicas; hero y pie con fondo de ancho completo. |
| C02 Cabecera | Marca izquierda, navegación central desde 768 y acción de acceso derecha; interior 64 + borde 1; sticky, z 40; fondo índigo translúcido. | Medido/observado: `inicio-1440`, `inicio-768`, `inicio-390`. | Evolucionar `public-layout`; marca textual autorizada y enlaces vigentes. Retirar acceso público y ranking. |
| C03 Navegación inferior | Cuatro destinos de referencia, icono y texto; altura 60, fija bajo 768, safe area; activo menta. | Medido/observado: `categoria-comer-390`; frontera 767/768. | Conservar solo destinos implementados; no rellenar posiciones con funciones futuras. Área de enlace ≥44; reservar espacio también para el final del pie. |
| C04 Pie | Región índigo; retícula de cuatro columnas desde 768, con bloque de marca ancho y grupos de enlaces; gap 48, padding vertical 56; franja inferior 24. | Medido/observado: `negocios-1440`, `ficha-gratis-1440`, capturas completas. | Grupos según rutas reales. No trasladar promesas de comunidad, cuentas o ciudades adicionales. |
| C05 Regreso / breadcrumb | Ficha con enlace de regreso de 13/19.5, gap 8; breadcrumb público no observado. | Medido: `ficha-premium-1440`. | Conservar `breadcrumbs` existentes y URL base del contexto. Breadcrumbs son adaptación del proyecto. |
| C06 Hero público | Fondo índigo, contenido alineado a izquierda, título con acento menta, introducción; Inicio 64/96 de padding vertical. | Medido/observado: `inicio-*`, `premium-*`. | Evolucionar `discovery-identity`; sin copiar búho, copys ni cifras. Nombre, tagline, icono y color de taxonomía siguen sus permisos. |

## Navegación de contenido y tarjetas

| ID / componente | Anatomía y variantes de referencia | Evidencia | Adaptación y comportamiento |
| --- | --- | --- | --- |
| C07 Chip de categoría | Cápsula, padding 8×16, texto 14/20 600, gap 8; activo índigo/blanco; resto blanco/texto oscuro. Alto observado 36. Fila con desplazamiento horizontal. | Medido: `categoria-comer-390`; observado filtro con clic y Enter. | Navegación a categoría o filtro compatible con GET; área táctil ≥44. Fila desplazable contenida, accesible por teclado, sin scroll horizontal de página. |
| C08 Selector de orden | Grupo compacto blanco, opciones en cápsulas; opción activa menta; botones 28 de alto. | Medido: `negocios-390`; `estado-filtro-recientes-390.jpg`. | No trasladar orden por votos. Mantener orden orgánico fijo del proyecto y su texto informativo. |
| C09 Conteo | Texto compacto encima del listado. | Observado en las cinco categorías; medido `negocios-*`. | Derivado de elegibilidad real antes de paginar; no cifras de demostración en producción. |
| C10 Fila de negocio | Tarjeta blanca radio 24, borde 1, sombra; padding 14/16, gap 16; miniatura 64 desde 640, título 16/24 700, metadatos 12/16, resumen, contenidos opcionales. | Medido: `negocios-1440`, `categoria-comer-390`. | Reutilizar `listing-card`. Una columna; retirar toda la zona de voto/favorito/puesto y los adelantos comerciales. Logo autorizado y nombre completo también en móvil. La altura crece con contenido. |
| C11 Variante orgánica Premium | Misma fila; ring medido de 2 px violeta al 45 %; insignia de plan en la fuente. | Medido/observado: primer article de `negocios-1440`. | Solo borde en tarjeta orgánica; no insignia. No reordenar por plan. La selección editorial y Patrocinado tienen señales propias. |
| C12 Tarjeta de categoría | Enlace con icono, título y descripción; padding 20, gap 16, radio 24. Retícula 1/2/3. | Medido/observado: `inicio-*`. | Datos de taxonomía del proyecto; cinco categorías no implican publicar Lugares antes de VS12. Omitir accesos sin resultados según contrato actual. |
| C13 Tarjeta comercial visual | Imagen de fondo, tratamiento oscuro, tipo, título y metadatos. Variante principal: altura mínima declarada 320/420; secundaria 190. Radio 24; gaps 16. | Medido: `inicio-1440`, `inicio-390`. | Solo usar con contenido autorizado. No reutilizar la tarjeta comercial como resultado orgánico. La publicidad debe decir Patrocinado; el carrusel futuro se rige por la maestra. |
| C14 Etiqueta / metadato | Cápsulas Premium, selección, tipo de oferta y etiquetas temáticas; tamaños y funciones distintos. | Observado: `ficha-premium-*`, `negocios-*`. | Mantener únicamente señales que correspondan al contenido. No dar el mismo estilo a estado operativo, selección y publicidad. Recalcular contraste sobre tintes. |
| C15 Bloque comercial | Panel índigo radio 24; padding 48×24 móvil y 64×48 ancho; texto y acciones en región de ancho 672. | Medido: final de `inicio-1440` / `inicio-390`. | Renderizar únicamente con puerta comercial abierta. No convertirlo en un placeholder de precios o WhatsApp. |

## Ficha e información práctica

| ID / componente | Anatomía y variantes de referencia | Evidencia | Adaptación y comportamiento |
| --- | --- | --- | --- |
| C16 Identidad de ficha | Radio 22, fondo `#202B50`, borde `#39466C`; regreso, región circular 72, nombre, categoría/zona y contactos. Padding 28×24 móvil, 30/34/28 ancho; gap 24. | Medido/observado: las tres fichas. | Evolucionar `listings/show`; distinguir logo de portada. Sin voto ni contador. En móvil el nombre refluye, contactos se distribuyen en filas. |
| C17 Enlace de contacto | Icono, etiqueta, fondo oscuro sutil, borde y radio 12; texto 13/16.9, padding 10×15, gap 9. | Medido: `ficha-premium-1440`. | Maps/teléfono/WhatsApp/web/redes solo con datos aprobados; área táctil ≥44. Destinos inspeccionados visualmente, no se iniciaron llamadas ni mensajes. |
| C18 Aviso de oferta | Banda violeta radio 24, padding 16×20, gap 12; etiqueta, título y condición; se apila en móvil. | Medido/observado: `ficha-premium-*`. | Solo oferta aprobada y vigente con condiciones reales; no trasladar fecha o precio del ejemplo. Ausente en Gratis observado. |
| C19 Panel informativo | Tarjeta blanca radio 24; título 18/28 700, contenido; variante descripción, información en dl, etiquetas. | Medido/observado: `ficha-gratis-*`, `ficha-lugar-*`. | Reutilizar estilos de panel y contenido sanitizado. Omitir filas ausentes, sin textos inventados para rellenar. |
| C20 Producto / servicio | Imagen opcional y bloque de título, precio y descripción; retícula 1/2 desde 640 dentro de la ficha. | Medido/observado: `ficha-premium-*`. | Futura VS08: cupo, plan, permisos y orden provienen de la maestra; diseño no los redefine. |
| C21 Oferta detallada | Tarjeta de radio 16, fondo lavanda, padding/gap 16; imagen y condiciones. | Medido: `ficha-premium-1440`. | Futura VS09: título/ancla identificable y vigencia real. No duplicar ofertas como datos orgánicos de tarjeta. |
| C22 Documento / PDF | Sección y enlace descriptivo. En la referencia el enlace de menú apunta a una imagen, por lo que no acredita PDF. | Observado: `ficha-premium-*`. | Conservar PDF autorizado y texto de botón editable de VS02; comprobar MIME, acceso y archivo reales en la aplicación. |
| C23 Distribución de ficha | Contenido principal y lateral desde 1024, proporción 2/3+1/3, gap 24; una columna antes. | Medido: `ficha-premium-*`. | Orden DOM: descripción/contenido antes de información secundaria, manteniendo acceso claro a contactos. Lugar incorpora hijos solo cuando VS12 los verifique. |

## Cuenta futura y estados

| ID / componente | Anatomía y variantes de referencia | Evidencia | Límite y aplicación |
| --- | --- | --- | --- |
| C24 Shell de cuenta | Sin cabecera/pie público; columna centrada, máximo 448, gutter 16; icono de 56, título 30/36; tarjeta radio 16/padding 32. | Medido/observado: `cuenta-acceso-*`, `cuenta-registro-*`. | Solo referencia futura; no crear rutas, cuentas ni CTA en el lanzamiento. Español al implementarse bajo un alcance aprobado. |
| C25 Campo de formulario | Label, input, icono opcional, placeholder; alto 48, radio 14, padding horizontal 12 con 40 a izquierda si hay icono; 16 px móvil y 14 desde md en acceso. | Medido: `cuenta-acceso-390`, `cuenta-acceso-1440`. | Mantener 16 px en campos móviles y etiquetas reales; autocomplete semántico. No copiar credenciales ni placeholders como datos. |
| C26 Acción primaria/secundaria | Cuenta: alto 48, radio 14, texto 14/20, padding 8×16. Primaria menta; Google con borde. | Medido/observado: `cuenta-acceso-*`. | Botón común futuro sin incluir proveedor social. Variante hover CSS declarada; disabled/error de envío no certificados. |
| C27 Separador de cuenta | Líneas y texto breve entre proveedor y correo. | Observado: `cuenta-acceso-*`. | Solo tendría función si existiesen dos métodos autorizados de acceso. No añadir por decoración. |
| C28 Enlace inválido | Mensaje y tarjeta informativa; enlace de recuperación. Sin campos de restablecimiento. | Observado: `cuenta-restablecer-*`. | No deducir formulario, confirmación de éxito ni política de contraseña a partir de esta pantalla. |
| C29 404 | Pantalla aislada, número 72/72 tenue, explicación y botón de 38 px. | Medido: `404-390`; Enter devuelve a Inicio. | Reutilizar `errors/404` del proyecto y adaptar a español/marca/44 px; no reflejar la URL sensible. |
| C30 Estados de datos | Carga transitoria observada durante navegación; vacío estable, error remoto de envío y disabled sin prueba completa. | Límites en `interaction-checks.json`. | Estados vacíos/carga/error de filtros y formularios se especifican desde los contratos existentes; no atribuirlos a Base44. |

## Componentes propios sin equivalente verificado

Breadcrumbs completos, paginación de doce resultados, filtros GET por zona, selección editorial administrable, reporte de información, compartir URL, galería autorizada con alternativas, FAQs, lista editorial, blog y carrusel patrocinado no tienen equivalente funcional certificado en esta extracción. Reutilizar sus componentes/contratos existentes o especificarlos en su VSXX; aplicar los tokens y patrones anteriores, marcando la composición nueva como adaptación. No inventar capturas originales ni interacciones para completar este inventario.

El panel de usuario interno no se pudo observar: `/mi-panel` redirige a `/beneficios?returnTo=%2Fmi-panel`. Heredará fundamentos, campos, acciones y estados cuando se apruebe su alcance. Las pantallas administrativas Base44, rankings, votos y favoritos no se integran.

## Interacciones y revisión

Ver [interaction-checks.json](interaction-checks.json) para acciones y resultados exactos. Se comprobó una muestra de navegación con clic/Enter/Tab, selección de filtro, URL de recientes, foco de recuperación y retorno de 404. No se enviaron formularios, votos, mensajes ni solicitudes OAuth. Las clases CSS de hover y active se midieron; no se certifican por ejecutar un clic en otro control.

La aceptación de cada componente futuro exige móvil/tableta/escritorio, teclado, foco, contenido largo, 200 %, contraste y estados aplicables. Dialog/lightbox, accordion y carrusel solo se prueban al existir un flujo aprobado; su comportamiento no debe darse por implementado por aparecer como requisito en una guía.
