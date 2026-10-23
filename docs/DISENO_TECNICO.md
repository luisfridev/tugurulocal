# Tu Gurú Local — Diseño técnico del proyecto nuevo

Contrato técnico para implementar la [especificación maestra](ESPECIFICACION_MAESTRA.md), que define R01–R10 y AC01–AC10. Usar [stack](TECH-STACK.md) y las fases del [plan](PLAN_IMPLEMENTACION.md). El [tablero](MEMORY.md#secuencia-y-dependencias) resume el avance y enlaza las comprobaciones de cada tarea; GitHub acredita sus integraciones.

## 1. Arquitectura y límites

Aplicación Laravel monolítica con vistas Blade/Livewire para público y Filament para super admin. MySQL es la fuente de verdad. Los controladores y recursos Filament validan, autorizan y delegan en acciones/servicios de dominio; no duplican las reglas de elegibilidad. Los procesos programados ejecutan tareas de mantenimiento, pero **las consultas públicas calculan la vigencia en el momento de leer** para que una cola retrasada nunca deje visible una oferta o campaña vencida.

Separar cuatro conceptos:

1. **Editorial:** fichas en borrador, publicadas, pausadas o archivadas, con publicación manual inmediata; el blog conserva programación.
2. **Operativo:** abierto, temporalmente cerrado o permanentemente cerrado.
3. **Comercial:** plan Gratis/Premium/Super Premium y motivo pagada/prueba/cortesía, con período.
4. **Publicidad:** campaña y espacio contratado, sujeto a su propio período y aprobación.

La selección editorial de Inicio y la de una lista no se derivan del plan. Inicio usa su propia relación ordenada; no adelanta el módulo de listas de VS11. Un registro puede permanecer en administración aunque se oculte al público.

## 2. Modelo de datos y restricciones

Todas las claves foráneas relevantes tienen índices y política explícita de borrado: contenido editorial y comercial se archiva o revoca; no se elimina en cascada mientras sea auditable. Fechas de vigencia se guardan como instantes y se muestran en America/Cancun.

| Entidad | Campos/relaciones mínimos y restricciones | Requisito |
| --- | --- | --- |
| `cities` | slug único, nombre, zona horaria, estado público. Solo Playa del Carmen activa inicialmente. | R01 |
| `areas` | ciudad, slug único por ciudad, nombre y estado. Zona para filtros; nunca tipo de ficha. | R01 |
| `categories` | slug único, nombre, descripción, tipo permitido y orden. Cinco registros iniciales. | R01 |
| `listings` | ciudad, tipo `business/place`, slug global único, categoría principal, zona opcional, estado editorial y operativo, `published_at`, textos, contactos, datos Maps manuales y fecha de revisión. Índices para ciudad/estado/fecha y categoría/estado/fecha. | R02 |
| `listing_categories` | ficha de negocio y categoría, par único; la principal también debe estar asociada. Lugar solo usa Lugares públicos. | R01–R02 |
| `home_selections` | ciudad, negocio, posición y timestamps; pares ciudad/negocio y ciudad/posición únicos. Hasta seis por ciudad, ordenados manualmente y separados de listas y planes. | R01 |
| `place_business_links` | Lugar, negocio, fuente o comprobación, fecha y super admin verificador; `business_listing_id` único. Validar misma ciudad, tipos correctos y vínculo real. | R04 |
| `slug_redirects` | `source_path` único y reservado, `listing_id` y timestamps; origen **creado en este proyecto** que resuelve a la identidad de la ficha. Impide borrar la ficha referenciada y retirar la tabla cuando hay reservas. | R01–R02 |
| `submissions` | propuesta original, tipo/categoría/ubicación/motivo, contacto privado y evidencia solo por enlace opcional HTTP/HTTPS; Instagram y consentimiento separado; estado, moderador/fecha/motivo y ficha vinculada opcional. Sin IP ni archivos. Claves normalizadas para coincidencias no bloqueantes. `listings.credit_submission_id` selecciona como máximo un crédito aprobado y consentido. | R06 |
| `catalog_items` | negocio, título, resumen, medio/precio opcionales, orden interno, estado y `published_at`. Índice negocio/estado; límite visible de diez. | R03 |
| `offers` | negocio, tipo oferta/promoción, condiciones, `starts_at`, `ends_at`, estado y medio opcional; índice de vigencia y negocio. Exigir fin posterior al inicio. | R03 |
| `editorial_lists` / `editorial_list_items` | ciudad, título, criterio, slug único por ciudad, estado, medios/SEO; pivot con posición única por lista y ficha única por lista. | R04 |
| `blog_categories` / `blog_tags` | taxonomía editorial separada; Guías locales inicial. | R05 |
| `blog_posts` / pivots | ciudad, slug único por ciudad, título, extracto, cuerpo sanitizado, firma de marca/persona, estado, fecha, medios/SEO, categoría y etiquetas. Índice ciudad/estado/fecha. | R05 |
| `content_revisions` | entidad, identificador, versión/snapshot acotado, autor y fecha; restauración autorizada sin sobrescribir auditoría. | R02/R05 |
| `plan_assignments` | negocio, nivel Premium/Super Premium, motivo, inicio/fin opcional, estado, referencia contractual, responsable. No se crean asignaciones Gratis: es el estado base derivado. | R07 |
| `commercial_receipts` | asignación o bloque contratado, importe recibido en centavos MXN, referencia, fecha de confirmación y responsable. Solo confirmados se suman como ingreso. Prueba/cortesía no generan recibo. | R07 |
| `sponsor_campaigns` | negocio anunciado, tipo Super Premium o contextual, ciudad, categoría/lista/Lugar cuando aplica, posición, período, material aprobado, contrato y estado. | R07 |
| `commercial_settings` | precios mensuales en centavos MXN, indicación de IVA, WhatsApp, responsable, mensaje/plazo si se promete y aprobación. Una fila vigente administrada con historial de cambios. | R08 |
| `interaction_events` | tipo e instante; `listing_id` opcional desde VS03, referencias opcionales de ciudad/categoría/zona y número de página. Conserva eventos de ficha/contacto y añade vistas de descubrimiento. No guarda IP, número, correo, texto de formulario ni URL completa. | R09 |
| `users` / `audit_events` | cuentas solo administrativas; `is_super_admin` falso por defecto, 2FA, acciones sensibles con actor/fecha/resultado. Registro público deshabilitado. | R08 |

Las restricciones de unicidad de slugs, pivots y posiciones se respaldan en MySQL. Las reglas que dependen de períodos superpuestos, ciudad, estado o relaciones de varios registros se validan en una transacción de dominio y se prueban con concurrencia representativa. Crear factories útiles para escenarios publicados, borradores, vencidos, cortesía y vínculos verificados; los seeders estructurales crean ciudad y categorías, nunca negocios ficticios para producción.

VS02 añade `listing_reports` (ficha, campo afectado, explicación, estado pendiente/resuelto/descartado, responsable y fecha) y el texto configurable del botón PDF en `listings`. No solicita datos de contacto. Las fichas no admiten programación: `published_at` registra su publicación manual. Ciudades, zonas y categorías tienen recursos administrativos; los seeders crean solo estructura. Los medios usan el plugin Filament/Spatie instalado y almacenamiento privado con entrega autorizada por estado de ficha.

## 3. Acciones y consultas de dominio

### Ampliación incremental VS02 (22-10-2026)

Nueva migración sobre VS02 ya ejecutada: identidad opcional en ciudades/categorías/zonas (primary_color, tagline, description y autorización/alt independientes para og_image e icon). Categorías reutilizan description; texto plano anterior se convierte a párrafos escapados. Nombre utf8mb4 conserva emojis compuestos; slug ASCII mantiene validación. HTML con lista permitida, sin style, scripts, imágenes ni adjuntos; HEX de seis dígitos y texto negro/blanco calculado por luminancia WCAG, respaldo de marca.

Ficha usa logo_approved, logo_alt, logo_reviewed_at y colección privada logo. Las columnas logo_source y logo_permission, como las equivalentes de otros medios, se conservan únicamente como histórico privado. Publicación y edición publicada exigen logo existente/autorizado; observador protege eliminación/reemplazo mientras esté publicada. Consulta pública y entrega privada comprueban autorización por colección; OG prioriza portada y luego logo. Taxonomías sirven archivos únicamente al super admin con 2FA en VS02.

DemoListingSeeder separado del estructural, bloqueado fuera de local/testing. Prepara 30 negocios públicos y dos privados (borrador/pausado), conservando los seis iniciales y sus medios. Los 26 públicos adicionales usan slugs estables, categoría principal/secundaria y ambas zonas DEMO; los registros y archivos permanecen para fases posteriores. Slugs y marcas DEMO permiten inserción única sin sobrescribir cambios ni recrear archivos. Identidad estructural vacía se completa una sola vez mediante marca persistente. is_demo identifica fichas/zonas; lecturas públicas excluyen DEMO fuera de local/testing. No crea ni modifica cuentas.

### Implementación incremental VS03 (22-10-2026)

Dos migraciones nuevas crean `home_selections` y amplían `interaction_events`, sin modificar las migraciones históricas ni reiniciar la base. La segunda vuelve nullable `listing_id`, añade las referencias de descubrimiento con claves foráneas restrictivas y un índice ciudad/tipo/fecha. Conserva los eventos anteriores. Su `down()` rechaza la reversión cuando existen eventos sin ficha, para evitar perderlos; una corrección posterior debe preservar esos datos.

- **`PublicListingQuery`:** `availableIds(City)` obtiene candidatos en lotes de 100 con logos precargados, comprueba autorización y archivo físico y reutiliza los identificadores durante la petición. `inCity()` limita el conjunto antes de contar o paginar. Detalle, tarjetas, conteos y selección comparten condiciones de publicación, tipo, ciudad, categoría principal y logo. Las tarjetas precargan relaciones; si falta el derivado, utilizan el original autorizado disponible.
- **`SaveHomeSelection`:** exige la política de edición de ciudad y super admin con 2FA. Valida hasta seis identificadores únicos, misma ciudad y negocios elegibles que no estén cerrados permanentemente. Una transacción bloquea ciudad y negocios, guarda posiciones contiguas y registra `home_selection.changed` con el campo `home_selections`, sin copiar valores privados. Permite conservar, reordenar o retirar seleccionados que hayan perdido elegibilidad; el panel los identifica como no visibles.
- **`DiscoveryController`:** compone Inicio y los tres listados. Inicio publica hasta seis seleccionados y seis recientes, sin repetir fichas y sin cerrados permanentemente en ambos bloques. Promociona solo categorías y zonas con resultados. Los listados mantienen negocios cerrados con su estado, combinan filtros por intersección y usan `whereHas` para no duplicar negocios con varias categorías.
- **`PublicTaxonomy` / `DiscoveryPageData`:** resuelven únicamente la ciudad pública inicial, categorías activas de negocio y zonas activas de esa ciudad. Preparan identidad sanitizada, respaldos de color, icono y OG autorizados. Fuera de local/testing excluyen zonas y medios marcados DEMO.
- **`RecordDiscoveryInteraction`:** registra `city.view`, `businesses.view`, `category.view` y `area.view` solo para GET válidos. Deduplica por sesión, superficie, ciudad, categoría, zona y página durante 60 segundos. Guarda referencias normalizadas; ignora parámetros arbitrarios y no modifica `RecordInteraction`, que conserva vistas de ficha y contactos.

### Contratos de dominio por secuencia

- **`PublishListing` / `PublicListingQuery`:** comprobar datos mínimos, aprobación, fecha y estado; devolver solo fichas elegibles. El orden orgánico es `published_at DESC, id DESC`. El listado de Lugar añade negocio hijo publicado y operativo activo. La ficha permanentemente cerrada puede seguir accesible, pero no ser patrocinada.
- **`LinkBusinessToPlace`:** validar tipos, misma ciudad, ausencia de otro Lugar, fuente/comprobación, fecha y responsable. Rechazar vínculos inferidos por texto.
- **`ModerateSubmission`:** estados pendiente → aprobada/rechazada/duplicada/spam. Aprobar crea o vincula **borrador**, nunca una publicación automática. El consentimiento controla el crédito visible.
- **`PublicCatalogQuery`:** producto aprobado, hasta diez visibles por negocio, y negocio publicado con plan activo; ofertas también requieren intervalo vigente. Filtro `tipo=todos|productos|ofertas` y `categoria={slug}`. Oferta por `ends_at ASC, id ASC`; producto global por `published_at DESC, id DESC`; en ficha por orden manual.
- **`ActivatePlan` / `EndPlan`:** autorización, historial y asignación única activa por negocio; pagada exige acuerdo y recibo confirmado, prueba exige fecha de fin, cortesía puede ser indefinida y siempre ingresa cero. Al terminar, beneficio efectivo Gratis y contenido exclusivo oculto. La renovación crea nueva asignación.
- **`ScheduleSponsorship`:** transacción que comprueba plan Super Premium activo durante toda la campaña, negocio apto, máximo tres campañas de ciudad simultáneas y posición de categoría no superpuesta. Cada Super Premium aporta portada y una posición en su categoría principal. Bloque contextual admite solo uno activo por lista o Lugar y se contrata aparte. En lectura, publicar únicamente intervalos y materiales aprobados.
- **`PublishEditorialList` / `PublishBlogPost`:** validar destinos públicos; preservar orden manual de lista y aislamiento de ciudad. Una entrada programada se vuelve legible al llegar `published_at` sin depender del éxito de un cron.
- **`CommercialPageAvailability`:** /premium visible solo con ambos precios, tratamiento de IVA, número y responsable de WhatsApp aprobados. El plazo de respuesta se muestra únicamente si el equipo lo ha fijado. No se infiere un plazo ni se promete activación automática.
- **`RecordInteraction`:** permitir solo tipos conocidos, evitar duplicaciones obvias y nunca recibir PII del navegador. Registrar fuente y destino de forma agregable.

Los nombres concretos de clases pueden seguir las convenciones del Laravel nuevo, pero estas operaciones y límites deben existir como contratos independientes de Filament y del controlador.

## 4. Rutas y superficies públicas

| Nombre conceptual | Método/ruta | Respuesta y restricciones |
| --- | --- | --- |
| Inicio | GET `/` | 302 a `/playa-del-carmen`. |
| Ciudad | GET `/{city}` | Solo ciudad pública; 404 para otra ciudad. |
| Negocios | GET `/{city}/negocios` | `business` publicados; filtros válidos categoría/zona, paginación y orden estable. |
| Explorar | GET `/{city}/explorar` | Catálogo elegible; filtros `tipo`/`categoria`; no página /ofertas. |
| Categoría y zona | GET `/{city}/{category}`, `/{city}/zonas/{area}` | Solo slugs vigentes y recursos publicados. |
| Ficha | GET `/@{listing}` | Slug global, solo ficha pública; canonical absoluto. |
| Medio de taxonomía | GET `/identidad/{type}/{record}/medios/{media}` | Solo ciudad/categoría/zona visible y archivo autorizado de `icon` u `og_image`; se conserva la ruta administrativa separada. |
| Lista / blog | GET `/{city}/listas/{slug}`, `/{city}/blog[/{slug}]` | Ciudad y estado publicados. |
| Recomendar | GET/POST `/recomendar` | CSRF, honeypot, validación, 3/h y 10/d por IP; confirmación neutra. |
| Planes | GET `/premium` | 404 y sin enlaces/SEO hasta aprobar configuración. |
| Privacidad posterior | Sin ruta en VS05 | Aviso, conservación y eliminación después de la primera publicación; no condicionan formularios. |
| Utilidad | GET `/sitemap.xml`, 404 | Sitemap solo indexables; error en español con vuelta útil. |

VS03 implementa raíz, ciudad, Negocios, categoría, zona y medios de taxonomías; conserva las rutas de ficha de VS02. Las demás superficies de la tabla siguen las fases previstas. Lugares públicos devuelve 404 hasta VS12. `ofertas` se añade a los slugs reservados y no crea una página pública.

Negocios acepta `categoria`, `zona` y `page`; categoría acepta zona y página; zona acepta categoría y página. Formularios GET con «Aplicar», 12 resultados por página, orden `published_at DESC, id DESC`. Aplicar elimina la página anterior y limpiar conserva el contexto de ruta. Los parámetros desconocidos no pasan a los enlaces de paginación. Filtros inválidos, zonas ajenas y páginas fuera de rango devuelven 404 en español; filtros válidos sin coincidencias producen un estado vacío útil.

Usar rutas nombradas y declarar los destinos específicos antes de comodines cuando se implemente su fase. Las categorías reservadas no pueden colisionar con palabras de rutas. Ninguna URL de la aplicación antigua tiene tratamiento especial. VS04 implementa las redirecciones 301 solo para cambios de slug de fichas ocurridos dentro de este proyecto. Guardar/publicar ficha toma el bloqueo de la ciudad inicial antes del bloqueo de ficha, con reintentos de transacción, para serializar cambios de slug y nuevas reclamaciones del mismo origen; la unicidad en MySQL respalda la reserva.

## 5. Estados, transacciones y autorización

### Visibilidad

- **Ficha:** borrador/pausada/archivada → privada; publicada y fecha alcanzada → ficha pública. Estado operativo cerrado permanente permite detalle informativo, pero la excluye de vitrinas y campañas.
- **Producto/oferta:** requiere aprobación propia, negocio publicado y plan vigente. Oferta añade `starts_at <= ahora < ends_at`. El contenido histórico queda visible al admin.
- **Lista:** requiere publicación y al menos una ficha pública válida para indexarse; entradas ocultas no cambian el orden relativo restante.
- **Lugar:** requiere publicación propia y al menos un negocio hijo público, activo y verificado; de lo contrario no se indexa ni aparece en navegación.
- **Post:** borrador/programado antes de hora/pausado/archivado → privado; publicado y hora alcanzada → público.
- **Patrocinio:** requiere contrato, material aprobado, campaña y plan elegibles; el intervalo es semiabierto `inicio <= ahora < fin`. Si el negocio deja de ser apto, se oculta al instante.

Filament usa policies para ver, crear, editar, publicar, borrar/restaurar y acciones masivas habilitadas. El guard administrativo no admite autorregistro. La primera cuenta se crea mediante proceso seguro local, con `is_super_admin=false` por defecto y promoción explícita; 2FA obligatorio antes de operación real. Pruebas de acceso directo y de acciones masivas verifican el límite. Cualquier archivo enviado se valida por tipo real, extensión, tamaño y autorización; PDFs no se ejecutan en el servidor.

VS01 implementó `app:bootstrap-super-admin`: entrada interactiva con contraseña oculta y confirmación, mínimo 12 caracteres con mayúsculas/minúsculas/números, sin contraseña en argumentos, bloqueo de creación compartido en MySQL y transacción con auditoría. No modifica cuentas existentes ni crea otra si ya existe super admin. La base principal local se configura mediante `DB_DATABASE` en `.env`, actualmente `tugurulocal_2026` por decisión del propietario. `config/database.php` mantiene `env('DB_DATABASE', 'laravel')` para MySQL: `'laravel'` es solo el respaldo si falta la variable. PHPUnit fuerza `tugurulocal_2026_test`.

El panel usa autorización estricta y middleware persistente en Livewire. TOTP es obligatorio antes de operar: secreto cifrado, ocho códigos de recuperación almacenados como hashes dentro de un atributo cifrado, consumo único y protección contra reutilización de TOTP. Filament limita login a cinco intentos por minuto/IP y el desafío de segundo factor por cuenta; la ventana TOTP permite un intervalo de 30 segundos a cada lado. Las sesiones usan datos cifrados, 30 minutos de inactividad y cookie de sesión HttpOnly/SameSite=Lax, con Secure por defecto en producción. El perfil solo permite modificar la propia cuenta con permisos; cambiar correo o contraseña exige la contraseña actual.

`audit_events` conserva actor, acción, resultado, recurso afectado, origen y fecha UTC; solo guarda nombres de campos modificados. Registra creación inicial, accesos correctos/rechazados, límites, cierre de sesión, denegaciones y cambios de cuenta/2FA. La consulta en `/dashboard/auditoria` es de solo lectura; las policies niegan mutaciones y acciones masivas, y el modelo rechaza editar o borrar eventos. Esto protege los flujos de aplicación; no sustituye los permisos y respaldos de MySQL pendientes de VS17. Cada nuevo módulo incorporará sus acciones auditables y policies. El [registro VS01](dev_changelog/VS01.md) detalla las pruebas y la revisión visual local completada el 22 de octubre de 2026.

El acceso a `/dashboard/login` descarta los destinos de retorno guardados en sesión que todavía apunten a `/admin` o sus subrutas, tanto al abrir el formulario como al autenticarlo. Filament vuelve entonces al panel vigente. Los destinos actuales bajo `/dashboard` se conservan y siguen sujetos a autorización y 2FA.

## 6. SEO, medios, privacidad y operación

Metadatos generados desde contenido real, editables por el super admin. Fichas usan `/@{slug}`; posts usarán slug por ciudad. VS03 incorpora título, descripción, canonical, metadatos sociales y breadcrumbs a las superficies de descubrimiento. Filtros, resultados vacíos y páginas con solo DEMO llevan `noindex`; los filtros apuntan al canonical base del contexto. La paginación sin filtros tiene canonical propio. No se incluyen valores privados en los metadatos.

VS04 comparte el resolvedor de URL entre canonical, enlaces y sitemap. Por decisión del propietario del 23-10-2026, los cambios de slug se habilitan solo en fichas; ciudad, categorías y zonas conservan sus restricciones. Cada origen se reserva definitivamente y referencia la identidad de la ficha, para resolver directamente su slug vigente solo mientras sea pública. El sitemap excluye DEMO y variantes paginadas y se genera sin caché persistente. `robots.txt` conserva el bloqueo hasta VS17. Explorar se desarrollará en su fase: su base se indexará solo con contenido real, y filtros `tipo`/`categoria` llevarán `noindex` con canonical al catálogo base. Borradores y /premium inactiva permanecen fuera del acceso público y de la indexación.

`BuildPublicSitemap` reúne las URLs mediante `PublicListingQuery`, `PublicTaxonomy`, `PublicIndexability` y `PublicUrl`, y utiliza la API manual de Spatie Laravel Sitemap 8.2.0 para producir el XML. `SitemapController` responde a GET/HEAD `/sitemap.xml` con `application/xml; charset=UTF-8` y la directiva `no-store` (Symfony añade `private`). Cada petición vuelve a evaluar la elegibilidad; no hay rastreo HTTP, archivo estático, tarea programada ni caché persistente del sitemap. Solo incluye ciudad, Negocios, categorías y zonas útiles y fichas reales elegibles. Se omiten `lastmod`, prioridades y frecuencias hasta definir criterios fiables; el paquete no decide canonical, `noindex` ni redirecciones. La retirada inmediata se refiere al XML servido, sin prometer retirada inmediata de resultados de buscadores.

Media Library organiza imagen principal, galería, PDF de menú/flyer, material de campaña e imágenes del blog en colecciones con permiso y crédito. Guardar alt, pie, autorización y fecha manual de verificación cuando aplique. Fuente/licencia multimedia anteriores se conservan como histórico privado, fuera del formulario. Generar tamaños optimizados; una ausencia se resuelve con sustituto visual, nunca con foto de otro negocio. Para videos, aceptar URL validada de YouTube/Vimeo, sin descargar archivo ni insertar HTML arbitrario.

La ruta pública de taxonomías comprueba visibilidad, pertenencia del archivo, colección `icon`/`og_image`, autorización, alt, fecha manual no futura y existencia en disco privado. Solo acepta el original o el derivado `card` permitido y existente. Retirar autorización o desactivar la taxonomía cierra también la URL directa. Las respuestas usan `private, no-store`, `nosniff` y `noindex`; la ruta administrativa mantiene su autorización con 2FA.

Las recomendaciones conservan contacto/evidencia en acceso restringido y oculta en serialización. No descargan ni incrustan enlaces y rechazan todos los archivos. CSRF, honeypot y token cifrado ligado a sesión (3 segundos a 2 horas) protegen el envío. Los límites independientes usan caché por IP (3/hora y 10/24 horas), sin guardarla en submissions. GET/POST y confirmación llevan `noindex` y `private, no-store`. Recomendaciones y reportes funcionan también en producción sin aviso aprobado ni ruta de privacidad. Aviso, conservación y eliminación se prepararán después de la primera publicación, sin inventar plazos ni bloquear los formularios. VS05 no incluye despliegue. Copias de seguridad y restauración se prueban antes del despliegue. El reloj de la app y los tests cubre los límites de vigencia de America/Cancun.

## 7. Matriz mínima de pruebas

| Contrato | Prueba de comportamiento |
| --- | --- |
| R01/AC01 | Rutas, slugs reservados, filtros, conteos y orden orgánico; ninguna /ofertas. |
| R02/AC02 | Publicación y cierre, slug global, datos vacíos, acceso directo privado y cambio de slug interno. |
| R03/AC03 | Cupo de diez, productos sin plan, ofertas antes/durante/después de vigencia y filtros de Explorar. |
| R04/AC04 | Lugar único por negocio, prueba de vínculo, ciudad correcta, lista manual y retiro de fichas. |
| R05/AC05 | Estado/fecha/ciudad de posts, autor, sanitización y permiso del editor. |
| R06/AC06 | Envío pendiente, límites, duplicados, moderación y consentimiento de atribución. |
| R07/AC07 | Solapamiento y cupo de campañas, cortesía con ingreso cero, pago confirmado, caducidad y etiqueta visible. |
| R08/AC08 | Policies, acciones masivas, 2FA y ocultamiento de /premium sin configuración. |
| R09/AC09 | Sitemap, canonical, noindex, metadatos, eventos sin PII y validación de medios. |
| R10/AC10 | Lista de comprobación editorial y recorrido responsive/teclado con contenido aprobado. |

Probar primero la ruta o servicio más estrecho, luego la suite en MySQL, Pint y build. La guía de pruebas completa y puertas de fase están en [PLAN_IMPLEMENTACION.md](PLAN_IMPLEMENTACION.md).


### Simplificación multimedia VS02 (22-10-2026)

Fuente y permiso/licencia multimedia se retiran de formularios, validación de entrada, campos asignables y nuevas revisiones/factories/DEMO. No se borran columnas ni valores anteriores y se ocultan de serialización. Se conserva la fecha manual, requerida para autorizar y nunca futura en America/Cancun. Publicación y lectura pública exigen logo autorizado, alt, fecha y archivo existente; no exigen fuente ni licencia escrita. Otros medios requieren autorización y fecha válida para mostrarse. Fuentes/fechas editoriales, Google y vínculos de Lugares permanecen. No cambia el esquema ni se reinicia la base.
