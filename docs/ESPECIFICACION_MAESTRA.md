# Tu Gurú Local — Especificación maestra del primer lanzamiento

**Estado:** contrato funcional para un proyecto nuevo. **Fecha:** 20 de octubre de 2026. **Ciudad pública inicial:** Playa del Carmen.  
**Autoridad:** este documento define qué debe hacer el producto. El [diseño técnico](DISENO_TECNICO.md), el [plan](PLAN_IMPLEMENTACION.md), la [guía visual](GUIA_VISUAL.md) y [operación](CONTENIDO_Y_OPERACION.md) lo desarrollan sin ampliar su alcance.

## 1. Propósito y alcance

Tu Gurú Local ayuda a residentes y visitantes a descubrir negocios y Lugares públicos mediante información útil y verificable. La medida principal es el **descubrimiento útil**: consultas de fichas y acciones de intención (abrir Maps, llamar, escribir o visitar una web). Un clic no equivale a visita física ni a venta.

El lanzamiento se desarrolla en módulos internos y se publica de una vez cuando todos los criterios de la sección 12 se cumplan. Empieza con un proyecto y una base MySQL nuevos; no importa código, datos, contenido ni redirecciones de otra aplicación. La interfaz, correos y mensajes al público estarán en español. Solo el super admin gestiona el contenido y los planes; el público puede enviar recomendaciones sin crear cuenta.

Se incluyen: Inicio; listado de Negocios; cinco categorías y zonas; fichas de negocio y Lugar; catálogo Explorar con productos, servicios y ofertas; planes asistidos; campañas patrocinadas; lista editorial; blog; recomendaciones moderadas; SEO y analítica sin datos personales. El aviso de privacidad y la conservación/eliminación se preparan después de la primera publicación. Quedan fuera cuentas públicas, favoritos, votos, ranking, reseñas propias, búsqueda por texto, panel de propietarios, cobro y renovación automáticos, sincronización de Google Maps, apps nativas y ciudades públicas adicionales. Ninguna pantalla mostrará controles, cifras o promesas de esas funciones pospuestas.

## 2. Navegación, taxonomía y URLs (R01)

Las categorías públicas, con su slug inicial, son:

| Nombre | Slug | Uso |
| --- | --- | --- |
| Dónde comer y beber | `donde-comer-y-beber` | Negocios de comida y bebida. |
| Qué hacer | `que-hacer` | Actividades, servicios recreativos y opciones para visitar; la clasificación se decide editorialmente. |
| Servicios | `servicios` | Servicios locales y profesionales. |
| Experiencias | `experiencias` | Actividades guiadas o vivencias; puede coexistir con Qué hacer en una ficha. |
| Lugares públicos | `lugares-publicos` | Fichas Lugar que agrupan negocios comprobados. |

Un negocio puede pertenecer a varias de las cuatro categorías de negocio y tiene una principal elegida en el panel. El Lugar pertenece a Lugares públicos. La zona geográfica (Area) filtra y orienta; no es una ficha. Los slugs se crean desde cero; no existe la categoría Compras ni se trasladan slugs antiguos.

| Recurso | Ruta pública | Comportamiento |
| --- | --- | --- |
| Raíz | `/` | Redirección temporal a `/playa-del-carmen`. |
| Ciudad | `/{city}` | Inicio con contenido real. |
| Negocios | `/{city}/negocios` | Todos los negocios publicados, incluidos Gratis. |
| Explorar | `/{city}/explorar` | Catálogo Premium con secciones de productos/servicios y ofertas. |
| Categoría | `/{city}/{category}` | Fichas asociadas a la categoría. |
| Zona | `/{city}/zonas/{area}` | Fichas de la zona. |
| Ficha | `/@{listing}` | URL canónica con slug global único. |
| Lista | `/{city}/listas/{slug}` | Colección editorial publicada. |
| Blog | `/{city}/blog` y `/{city}/blog/{slug}` | Índice y entrada publicada. |
| Recomendar | `/recomendar` | Formulario y confirmación. |
| Planes | `/premium` | Solo cuando se complete la puerta comercial de la sección 9. |
| Aviso de privacidad | `/aviso-de-privacidad` (posterior) | Se preparará después de la primera publicación; no condiciona recomendaciones ni reportes. Sin ruta ni enlaces en VS05. |
| Sitemap | `/sitemap.xml` | Solo URLs públicas indexables. |

Los destinos específicos se resuelven antes de la categoría comodín. `negocios`, `explorar`, `zonas`, `listas` y `blog` son slugs reservados dentro de una ciudad. Las URLs de la aplicación anterior no reciben redirecciones. Un cambio de slug publicado **dentro de este proyecto** sí crea 301 a la nueva URL, sin revelar borradores.

### Inicio y listados

Inicio muestra propuesta de valor, accesos a categorías y zonas, selección editorial, fichas recientes, acceso a Recomendar y módulos comerciales con contenido vigente. No muestra tarjetas vacías ni cifras de demostración. `/{city}/negocios` admite filtros compartibles por categoría y zona; la categoría y zona también tienen páginas propias. El orden orgánico es `published_at DESC, id DESC`. Plan, calificación de Google y presencia en listas no lo alteran. Un patrocinio se presenta en un espacio separado y no cambia el orden relativo de los resultados orgánicos.

Las tarjetas orgánicas Premium y Super Premium usan **borde** diferenciado, sin insignia de plan ni adelanto de productos. Identificar el plan dentro de la ficha está permitido. Una categoría puede mostrar una vitrina Premium separada **después de los primeros negocios**, sin presentarla como resultado orgánico. Los estados vacíos ofrecen un camino útil a otras categorías o Negocios. Los filtros no prometen búsqueda por texto.

### Decisiones aprobadas para VS03 (22-10-2026)

Negocios pagina de doce en doce y combina categoría y zona mediante formularios GET con botón Aplicar. La categoría incluye asociaciones principales y secundarias sin duplicar fichas; su página filtra por zona y la página de zona filtra por categoría. Aplicar reinicia la página y limpiar mantiene el contexto de la ruta. Recursos, filtros inválidos y páginas fuera de rango responden 404; una combinación válida vacía responde 200 con camino útil y noindex. Los parámetros desconocidos no se propagan. Se reserva también el slug `ofertas`.

El super admin elige y ordena hasta seis negocios para Inicio, mediante una selección propia de la ciudad, separada de las listas de VS11 y de los planes. Se muestran hasta seis recientes adicionales, sin repetir seleccionados visibles. Los cierres permanentes no aparecen en ambos bloques, pero conservan presencia orgánica informativa con su estado. Una selección que pierde elegibilidad se oculta sin borrar la relación administrativa. Inicio enlaza categorías y zonas con resultados; las páginas activas vacías conservan su estado útil. Lugares públicos no se enlaza ni se sirve hasta VS12. Las superficies con solo DEMO no se indexan.

## 3. Fichas de negocios y Lugares (R02)

Solo hay dos tipos de ficha: `business` y `place`. Cada ficha pertenece a una ciudad, tiene slug global único, categoría principal, categoría(s) secundaria(s) cuando sea negocio, zona opcional, estado editorial y estado operativo separados. Un negocio puede estar abierto, temporalmente cerrado o permanentemente cerrado. Una ficha permanentemente cerrada puede seguir accesible por valor informativo, pero no aparece en vitrinas ni campañas.

La ficha publicada ofrece, solo si existen datos aprobados: nombre, resumen y descripción editorial; razones para visitar o contratar; dirección o referencia; horario; contactos y acciones verificadas de Maps, teléfono, WhatsApp, web y redes; imagen principal y galería; menú o folleto PDF autorizado con texto de botón editable desde el panel; preguntas frecuentes visibles; forma de reportar datos incorrectos y acción de compartir la URL canónica. La calificación y cantidad de reseñas de Google se introducen manualmente con fecha de revisión; si faltan, no se sustituyen por cero. Los datos privados de remitentes jamás aparecen en metadatos, vistas o enlaces.

La ficha Gratis de negocio conserva toda la información práctica y el PDF publicado. Las fichas se publican manualmente de forma inmediata, sin programación. Las fichas en borrador, pausadas o archivadas no son públicas por URL directa, listas, catálogo o sitemap. El super admin puede previsualizar, publicar, despublicar, archivar y recuperar sin pérdida de historial. La atribución a quien recomendó requiere identificador social válido y consentimiento específico.

Los reportes de datos incorrectos se envían desde un formulario público de la ficha, sin adjuntos ni contacto personal solicitado. Quedan pendientes y solo el super admin los resuelve o descarta; no alteran la ficha automáticamente. Aplican CSRF, honeypot, token de sesión de 3 segundos a 2 horas y límites de 3/hora y 10/24 horas por IP. Su recepción no depende del entorno ni del aviso de privacidad. Ciudades, zonas y categorías tienen gestión administrativa con permisos y auditoría. Inicialmente solo Playa del Carmen puede ser pública. Los medios se gestionan con `filament/spatie-laravel-media-library-plugin` y permanecen protegidos mientras la ficha sea privada.

### Ampliación aprobada de VS02 (22-10-2026)

Los borradores pueden guardarse sin logo. Publicar un negocio exige logo existente y autorizado, con alt, fecha manual de verificación y aprobación propios. La autorización del super admin confirma el permiso de uso, sin solicitar fuente ni permiso/licencia por escrito. Logo, portada, galería y PDF son colecciones independientes del plugin Filament/Spatie. No se exige tener otros medios ni aprobarlos junto al logo. Despublicar antes de retirar o sustituir el logo. La consulta pública exige logo autorizado; OG prioriza portada autorizada y usa logo si falta. El logotipo oficial de Tu Gurú Local sigue pendiente.

Ciudades, categorías y zonas administran nombre con emojis compuestos, color HEX primario, imagen OG, icono independiente, tagline y descripción larga enriquecida. Son opcionales: respaldo de color de marca y omisión de bloques vacíos. Los slugs conservan sus restricciones sin emojis. RichEditor admite párrafos, encabezados, negritas, cursivas, listas, citas y enlaces, con HTML sanitizado, sin HTML arbitrario ni imágenes adjuntas. Conservar el texto existente de categorías. Medios JPEG/PNG/WebP con transparencia, alt, fecha manual y autorización. El texto sobre colores personalizados alcanza contraste 4.5:1. Las páginas públicas completas pertenecen a VS03.

Un seeder DEMO separado y explícito funciona solo en local/testing. Por ampliación solicitada el 22-10-2026, prepara 30 negocios públicos distribuidos entre las cuatro categorías de negocio y las dos zonas DEMO, además de un borrador y uno pausado, con medios sintéticos identificados. Conserva las seis fichas originales y añade 26 públicas. Los datos y medios permanecen disponibles para futuras etapas. Es repetible, conserva archivos y cambios manuales, no reinicia datos ni modifica cuentas. Completa solo identidad estructural vacía. DEMO queda excluido de producción y del inventario real. No inventa contactos, ubicaciones, calificaciones ni reseñas reales.

### Simplificación multimedia aprobada (22-10-2026)

En fichas de negocio/Lugar, ciudades, categorías y zonas se retiran los campos de fuente y permiso/licencia del flujo multimedia. Se conservan archivos, alt, fechas de verificación editables y autorización; no se admiten fechas futuras en America/Cancun. Autorizar requiere fecha, y texto alternativo cuando corresponda. Columnas y valores anteriores permanecen como histórico privado, sin mostrarse, exigirse o sobrescribirse al guardar. No requiere migración. Las fuentes y fechas editoriales, de Google y de vínculos de Lugares se mantienen.

## 4. Explorar, productos y ofertas (R03)

`/{city}/explorar` es el catálogo de productos, servicios, ofertas y promociones de negocios Premium o Super Premium publicados con plan activo. No es el listado general de fichas. Tiene dos secciones: **productos y servicios destacados** y **ofertas vigentes**. El filtro `tipo` acepta `todos` (predeterminado), `productos` u `ofertas`; el filtro `categoria` acepta un slug de categoría de negocio. “Ver todo” de ofertas dirige a `/{city}/explorar?tipo=ofertas`, y las variantes filtradas conservan enlaces compartibles. No se crea `/{city}/ofertas`.

Cada producto/servicio pertenece a un negocio, lleva título, descripción breve, imagen opcional, precio/moneda opcionales, orden dentro de su ficha y estado editorial. Se publican **máximo diez por negocio**; los borradores no consumen cupo. Aparecen en la ficha y catálogo; en categorías e Inicio se usan vitrinas separadas si hay contenido. El catálogo global ordena productos por fecha de publicación descendente con ID de desempate; la ficha respeta el orden manual.

Cada oferta/promoción tiene tipo, título, descripción, condiciones, inicio, fin, imagen opcional y estado editorial. No hay cupo numérico, pero solo se muestra si está aprobada y vigente en la zona horaria de Playa del Carmen, el negocio está publicado y su plan elegible está activo. El catálogo ordena ofertas por vencimiento próximo, luego ID; la ficha muestra sus ofertas vigentes. La tarjeta lleva a la ficha y al elemento identificable dentro de ella. Una oferta vencida, pausada o retirada por fin del plan desaparece de espacios públicos sin perder historial. No se prometen condiciones fuera de la vigencia.

## 5. Listas editoriales y Lugares (R04)

El super admin crea listas con título, introducción, criterio editorial, medios, estado, slug y SEO; elige fichas publicadas y fija su orden. La membresía no compra inclusión ni cambia posición. Si una ficha deja de estar publicada, se oculta públicamente sin borrar su relación administrativa. La lista piloto será **«Dónde aprender a bailar en Playa del Carmen»**, con mínimo cinco fichas reales verificadas y una explicación del criterio de selección.

Un Lugar público representa una plaza, parque o sitio delimitado **con negocios ubicados dentro y vinculados explícitamente**. Tiene ficha propia solo si al menos un negocio hijo publicado y comprobado puede aparecer en ella. Cada negocio puede ser hijo de **un único Lugar**. El super admin registra fuente fiable o comprobación directa, fecha y responsable del vínculo. Compartir nombre, zona o categoría no demuestra pertenencia. La ficha de Lugar muestra solo negocios hijos publicados y operativamente activos. El plan del negocio no crea el vínculo. Un bloque contratado en la página del Lugar se muestra separado del listado de hijos y con «Patrocinado».

## 6. Blog editorial (R05)

El blog inicia en Playa del Carmen con una categoría editorial propia, **Guías locales**, separada de categorías de fichas. Las etiquetas precisan temas. Cada entrada tiene ciudad, slug único dentro del blog de esa ciudad, título, extracto, cuerpo, medios, estado, fecha, firma pública elegible entre marca y persona, metadatos SEO y revisiones recuperables. Solo el super admin crea, previsualiza, programa, publica, despublica y archiva.

El editor permite párrafos, encabezados, listas, citas, enlaces, imágenes con alt y pie, galerías sencillas, tablas simples y URLs validadas de YouTube/Vimeo. Se sanitiza el HTML final. Índice y posts muestran solo entradas publicadas de esa ciudad; el índice ordena por fecha de publicación descendente con desempate estable y paginación. Un post programado no se publica antes de su hora. Los enlaces internos llevan a fichas y listas públicas válidas. No hay comentarios ni autores externos.

## 7. Recomendar y moderar (R06)

Cualquier visitante puede proponer un negocio o Lugar con datos básicos, ubicación, motivo, evidencia opcional, contacto privado opcional y consentimiento separado para atribución social. El envío queda pendiente y no crea una ficha publicada. El super admin aprueba, rechaza, marca duplicado o spam; al aprobar, crea o vincula un borrador que requiere publicación adicional. La confirmación no garantiza aceptación.

El formulario Blade de una página exige nombre, tipo negocio/Lugar, categoría compatible, ubicación y motivo; Playa del Carmen queda fija. Maps es opcional. Evidencia solo mediante enlace opcional HTTP/HTTPS, sin adjuntos, descargas ni incrustaciones. Correo y WhatsApp son opcionales y privados, sin mensajes automáticos. Instagram es opcional, con consentimiento separado y desmarcado para mostrar crédito. Solo se muestra un crédito seleccionado por el administrador por ficha publicada, con consentimiento e identificador válido.

Recomendaciones y reportes están disponibles al público desde la primera versión, en cualquier entorno, sin depender de la existencia o aprobación del aviso. El aviso y las reglas de conservación y eliminación se prepararán después de la primera publicación. VS05 no crea su ruta ni enlaces y no incluye despliegue. El formulario protege con CSRF, honeypot, token ligado a sesión válido entre 3 segundos y 2 horas y límites independientes de **3 envíos por hora y 10 en 24 horas por IP**. Las coincidencias normalizadas se señalan al administrador sin bloquear el envío. No se guarda IP en `submissions`. Contacto, evidencia y notas quedan restringidos; analítica y auditoría solo registran referencias y acciones. La moderación transaccional impide decisiones repetidas/concurrentes. Aprobar permite seleccionar datos para un borrador nuevo o vincular uno compatible; marcar duplicado puede referenciar una ficha publicada sin modificarla. El contenido de terceros se escapa y modera. Formulario y confirmación llevan `noindex` y `private, no-store`, conservan datos válidos tras errores y no ofrecen seguimiento público.

## 8. Planes y patrocinios (R07)

| Plan | Beneficios |
| --- | --- |
| Gratis | Ficha completa y ubicación orgánica en categorías. |
| Premium | Gratis más hasta diez productos/servicios publicados y ofertas/promociones aprobadas ilimitadas. Borde en tarjeta orgánica y presencia en catálogo/vitrinas. |
| Super Premium | Premium más campaña contratada de carrusel de Inicio y posición de categoría, ambas identificadas como «Patrocinado». |

La venta y el cobro ocurren fuera del sitio. El super admin asigna plan con inicio, motivo (`pagada`, `prueba` o `cortesia`), responsable, vigencia, estado y referencia interna. Una activación pagada exige acuerdo y pago confirmado; solo dinero confirmado cuenta como ingreso. Pruebas con vencimiento y cortesías con o sin fin registran **ingreso cero**. Pueden pausarse o revocarse; al vencer o terminar vuelven a Gratis y se ocultan beneficios exclusivos, sin borrar la ficha ni el historial. La renovación es manual y se registra como nueva activación. Super Premium incluye Premium, sin dos asignaciones simultáneas.

Hay **máximo tres campañas Super Premium simultáneas por ciudad**. El carrusel de Inicio muestra únicamente campañas elegibles, con avance automático, pausa y controles manuales. Cada campaña ocupa una posición contratada única en la categoría principal del negocio, antes del listado orgánico. No se solapan campañas en el mismo espacio y período. Una cortesía Super Premium consume cupo y sus espacios dicen «Patrocinado». Cada lista y cada Lugar admite **un bloque patrocinado adicional por página**, contratado aparte y fuera de la selección editorial; no forma parte del paquete Super Premium. El panel registra contrato, material aprobado, fechas, responsable e impresiones/clics por espacio.

## 9. Página comercial y administración (R08)

El panel administrativo se sirve en `/dashboard`, con acceso en `/dashboard/login`. Solo el super admin autenticado administra ciudad, zonas, categorías, fichas, medios, relaciones, recomendaciones, listas, blog, productos, ofertas, planes, campañas, precios y contacto comercial. La autorización se aplica en servidor a cada acción, incluido acceso directo y acciones masivas. Hay segundo factor administrativo, límite de intentos, sesiones seguras, registro de cambios y copias de seguridad.

El panel permite editar **precio mensual público en MXN** de Premium y Super Premium y el texto que indica si incluye IVA. También configura número de WhatsApp Business, responsable y plazo de respuesta real. `/premium` y todos sus CTA comerciales permanecen ocultos y fuera del sitemap hasta que ambos precios, tratamiento de IVA, WhatsApp y responsable estén completos y aprobados; el plazo de respuesta solo se muestra si se aprueba uno. Cuando se habiliten, la página compara Gratis, Premium y Super Premium sin prometer votos, ranking ni activación automática. Una solicitud por WhatsApp inicia venta asistida; el plan pagado se activa tras acuerdo, pago confirmado y contenido aprobado.

## 10. SEO, accesibilidad, seguridad y medición (R09)

Cada página pública indexable tiene título, descripción, canonical, metadatos sociales y breadcrumbs coherentes. El sitemap incluye solo ciudad, categorías y zonas útiles, fichas, listas, índice/posts y Explorar con contenido real. Variantes de filtros, páginas vacías, borradores, programados y archivos llevan `noindex` y no entran al sitemap. El canonical de filtros apunta a la página base pertinente. Un cambio de slug publicado dentro del proyecto crea 301. Los datos estructurados (`BlogPosting`, negocio o Lugar y FAQ cuando aplique) reflejan contenido visible.

La interfaz es adaptable, navegable por teclado, con foco visible, alternativas de imagen, contraste suficiente, objetivos táctiles utilizables, estado de carga y mensajes comprensibles. El carrusel se puede pausar y respeta movimiento reducido. Los medios y enlaces se validan; se optimizan imágenes, se limita tamaño de PDF y se evita HTML inseguro. El aviso de privacidad y las reglas de conservación y eliminación se prepararán después de la primera publicación, sin condicionar los formularios públicos.

Se miden consultas de fichas, listas y blog; clics de Maps, teléfono, WhatsApp, web y redes; aperturas de elementos de catálogo; envíos/moderación; e impresiones y clics de campañas. Los eventos no contienen datos personales. Los reportes distinguen intención de contacto de visitas o ventas confirmadas.

## 11. Contenido y operación de salida (R10)

La publicación exige 40 negocios verificados, con al menos cinco de categoría principal en cada una de las cuatro categorías de negocio; tres Lugares públicos con hijos comprobados; al menos cinco fichas verificadas en la lista de baile; cinco guías duraderas; y tres campañas Super Premium reales o de cortesía con materiales aprobados. Los datos editoriales conservan fuente, responsable y fecha de revisión. Los medios requieren autorización del super admin y fecha manual de verificación; no solicitan fuente ni permiso/licencia escrito. La pauta de mantenimiento está en [contenido y operación](CONTENIDO_Y_OPERACION.md). Los nombres, precios, reseñas y cifras de las [capturas](referencias/capturas/26-mapa-de-pantallas.jpg) son ejemplos, no contenido de salida.

## 12. Criterios de aceptación verificables

- **AC01 / R01:** las rutas públicas y los cinco slugs funcionan sin colisiones; Negocios incluye Gratis, los filtros son compartibles y el orden orgánico es estable. No existe una página separada de ofertas.
- **AC02 / R02:** solo fichas publicadas aparecen en URL, enlaces, vitrinas y sitemap; datos vacíos no muestran valores inventados; el slug canónico es global.
- **AC03 / R03:** Explorar muestra exclusivamente elementos elegibles; máximo diez productos por negocio; las ofertas vencen y se ordenan por fin cercano; “Ver todo” filtra Explorar.
- **AC04 / R04:** la lista respeta selección y orden manual; cada Lugar muestra solo hijos publicados, activos y documentados; ningún plan crea un vínculo editorial.
- **AC05 / R05:** el blog aísla ciudad y estado; posts programados no se adelantan, tienen SEO válido y solo el super admin puede publicarlos.
- **AC06 / R06:** un envío público queda pendiente, se limita y modera; atribución solo con consentimiento y ningún dato privado sale al público.
- **AC07 / R07:** cupos y períodos comerciales no se solapan; pago, prueba y cortesía se distinguen en historial e ingresos; vencimiento devuelve a Gratis; cada espacio visible dice «Patrocinado».
- **AC08 / R08:** acciones administrativas están autorizadas; /premium y CTA se ocultan hasta completar datos comerciales; ningún visitante compra ni gestiona fichas.
- **AC09 / R09:** SEO, accesibilidad, seguridad y medición respetan contenido visible y privacidad; estados vacíos y páginas no publicadas no se indexan.
- **AC10 / R10:** inventario de salida y permisos están documentados, comprobados y aprobados; se revisa en móvil, tableta, escritorio y teclado antes del único lanzamiento.

## 13. Decisiones operativas pendientes

Faltan importes mensuales y tratamiento de IVA, número y responsable de WhatsApp Business, plazo de respuesta si se desea prometer uno, alojamiento y archivo oficial del logotipo. Son **valores externos**, no decisiones de comportamiento abiertas. Se registrarán y validarán según [contenido y operación](CONTENIDO_Y_OPERACION.md). Su ausencia permite construir y probar localmente. Precios, IVA, WhatsApp y responsable bloquean /premium; alojamiento y logotipo final bloquean la salida, y no se promete un plazo de respuesta sin definirlo.

