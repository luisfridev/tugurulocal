# Tu Gurú Local — proyecto nuevo

Aplicación Laravel nueva para construir Tu Gurú Local desde cero. La única ciudad pública inicial será Playa del Carmen y habrá un solo lanzamiento cuando los módulos y el contenido de salida estén aprobados. El [tablero](docs/MEMORY.md#secuencia-y-dependencias) resume el avance y las dependencias; GitHub acredita las integraciones. El sitio no está publicado. La raíz redirige temporalmente al Inicio de Playa del Carmen. Las superficies vacías, filtradas o con solo DEMO llevan `noindex`.

## Contratos y secuencia

1. [Especificación maestra](docs/ESPECIFICACION_MAESTRA.md): producto y criterios de aceptación; prevalece ante discrepancias.
2. [Diseño técnico](docs/DISENO_TECNICO.md) y [stack](docs/TECH-STACK.md): arquitectura y versiones objetivo.
3. [Plan](docs/PLAN_IMPLEMENTACION.md) y [tablero VS00–VS17](docs/MEMORY.md): dependencias, puertas de fase y evidencia.
4. [Sistema de diseño](docs/design.md) y [contenido y operación](docs/CONTENIDO_Y_OPERACION.md): autoridad visual pública y verificación editorial/comercial. La [guía visual](docs/GUIA_VISUAL.md) remite al manual; Filament conserva su sistema administrativo.

El manual extrae el diseño de [la referencia Base44](https://preview--turu-guia-local.base44.app/), con [tokens](docs/design-ref/design-tokens.json), [inventario](docs/design-ref/component-inventory.md), [capturas y procedencia](docs/design-ref/screenshots/manifest.json) y [procedimiento de adopción/comparación](docs/design-ref/implementation-guide.md). Distingue valores medidos, observados, inferidos y no verificados. Los dos hitos previos a VS04 —manual y alineación de Inicio, listados, fichas y 404— están integrados mediante las PR #6 y #8. [Estado y evidencia de la puerta transversal](docs/MEMORY.md#puerta-transversal-de-diseno-antes-de-vs04). Cuatro comprobaciones asistidas de accesibilidad se reservan para VS15; no se consideran aprobadas. La integración no acredita producción.

Los cambios ya realizados se explican por tarea en [docs/dev_changelog](docs/dev_changelog/VS00.md); el primer registro corresponde a VS00. Esa carpeta contiene únicamente registros Markdown, sin imágenes ni capturas; la revisión visual se documenta con URLs, tamaños de pantalla, interacciones y resultados.

Cada entrega, normalmente una `VSXX`, mantiene una rama y una PR hasta su fusión. Las continuaciones y correcciones reutilizan ambas. La PR reúne implementación, pruebas y documentación; el propietario la revisa y fusiona. El agente sube únicamente la rama de trabajo y no fusiona ni hace push a `master`. Tras comprobar la integración en GitHub y sincronizar `master` por avance rápido, puede continuar la tarea dependiente. El tablero y el registro anterior se actualizan dentro de la siguiente PR con trabajo sustantivo; nunca se abre otra solo para registrar un merge o cambiar un estado. GitHub acredita la integración, el tablero resume el avance y el changelog conserva las comprobaciones. El [flujo de cierre](docs/PLAN_IMPLEMENTACION.md#cierre-mediante-pull-requests) y [AGENTS.md](AGENTS.md) detallan las reglas.

Las [capturas](docs/referencias/capturas/26-mapa-de-pantallas.jpg) orientan la composición. No aportan datos verificables ni autorizan funciones pospuestas. No se importó código, base de datos, contenido, slugs ni redirecciones del proyecto anterior.

Cada tarea aplica [Antislop según el trabajo](AGENTS.md#antislop-por-tarea), en modo `during` por defecto para este proyecto: base más los complementos de texto, UI, accesibilidad, responsive o comentarios que correspondan, junto con las skills técnicas. El [procedimiento de inicio y revisión final](docs/PLAN_IMPLEMENTACION.md#antislop-en-la-ejecución) exige evidencia de los criterios aplicables y conserva design.md como dirección de diseño público.

## Desarrollo local

Requisitos verificados para VS00: PHP 8.4, Composer 2, Node 22, npm 10 y MySQL 8.4. La base principal local se configura con `DB_DATABASE` en `.env`; actualmente es `tugurulocal_2026`, según la decisión del propietario. `config/database.php` conserva `'laravel'` como respaldo original de MySQL, usado solo si no existe esa variable de entorno. PHPUnit usa `tugurulocal_2026_test`, separada porque las pruebas pueden vaciar sus tablas. Crea un usuario MySQL local con acceso a ambas. Configura nombre de base, host, puerto, usuario y contraseña en `.env` (o `DB_SOCKET` para un servidor por socket); nunca confirmes el archivo local ni sus credenciales en Git.

Herd ya sirve esta carpeta en [http://tugurulocal-nuevo.test/](http://tugurulocal-nuevo.test/). Configura `APP_URL=http://tugurulocal-nuevo.test` en tu `.env` local. Abre esa dirección en el navegador para revisar los cambios; Herd sirve PHP sin ejecutar `php artisan serve`. Durante trabajos de interfaz, deja `npm run dev` activo en una terminal para que Vite actualice estilos y recargue las vistas al guardar.

```sh
cp .env.example .env
# Configura APP_URL y DB_* en .env y crea ambas bases MySQL antes de migrar.
composer install
npm ci
php artisan key:generate
php artisan migrate
php artisan test
vendor/bin/pint --test
npm run build
# Durante trabajos de interfaz, en una terminal que permanezca abierta:
npm run dev
```

`phpunit.xml` fuerza MySQL y la base `tugurulocal_2026_test`, incluso si el proceso recibe otro `DB_DATABASE`; toma los demás datos de conexión local de `.env` o `.env.testing`. No apuntes esa base de pruebas a datos reales. La aplicación guarda instantes en UTC y `config/business.php` fija `America/Cancun` para su presentación. Las fuentes web están incluidas como paquetes npm, así que `npm run build` no necesita descargar fuentes en tiempo de compilación.

En cada VSXX con cambios visuales, revisa la URL afectada en Herd y enlázala en el registro de la tarea para poder abrirla en el navegador. Herd sirve la aplicación; la conexión MySQL se configura por separado. La instancia temporal usada al comprobar VS00 no es una base permanente para tareas posteriores.

La raíz devuelve 302 a `/playa-del-carmen`. Filament está en `/dashboard`: un usuario nuevo no tiene privilegios y no puede entrar al panel. VS01 exige segundo factor TOTP, comprueba permisos en peticiones directas y Livewire y ofrece auditoría de solo lectura. Debugbar queda apagada por defecto y se puede habilitar solo para desarrollo local mediante `DEBUGBAR_ENABLED=true`.

### Primera cuenta administrativa

Tras migrar, ejecuta en una terminal interactiva:

```sh
php artisan app:bootstrap-super-admin
```

El comando pide nombre, correo y una contraseña oculta, con confirmación, de al menos 12 caracteres con mayúsculas, minúsculas y números. No acepta contraseña como argumento ni funciona en modo no interactivo. Rechaza un correo ya registrado y la creación si ya existe un super admin. La operación usa un bloqueo compartido en MySQL y una transacción; no promueve cuentas existentes de forma implícita.

Abre [el acceso local](http://tugurulocal-nuevo.test/dashboard/login), configura una aplicación autenticadora y guarda fuera del sitio los ocho códigos de recuperación. Antes de completar ese paso no se permite operar el panel. Cada código de recuperación se consume una sola vez; la misma clave TOTP no puede reutilizarse en otro inicio de sesión. El perfil exige la contraseña actual para cambiar correo o contraseña; la auditoría registra identificadores, acciones y campos modificados, sin copiar sus valores privados.

Las sesiones usan cookies de sesión HttpOnly y SameSite=Lax, datos de sesión cifrados y 30 minutos de inactividad por defecto. La opción Recordarme no está disponible. En `APP_ENV=production` la cookie exige HTTPS por defecto; el despliegue y su comprobación siguen pendientes. El propietario ya creó su cuenta administrativa y el 22 de octubre de 2026 se verificó localmente que tiene 2FA habilitado. La cuenta temporal autorizada para revisar el alta y el desafío se eliminó al terminar. No se guardan credenciales de acceso en Git.

### Fichas Gratis y taxonomía — VS02

El panel administra ciudades, zonas, categorías, fichas y reportes. `php artisan db:seed` crea solo Playa del Carmen y las cinco categorías iniciales; no crea cuentas ni negocios de ejemplo. Solo Playa del Carmen puede ser pública inicialmente.

Las fichas se publican manualmente, sin programación. Se pueden previsualizar, despublicar, archivar y recuperar; el historial conserva el contenido y exige nueva aprobación al recuperar una versión. VS04 permite cambiar el slug de una ficha publicada y reserva cada URL anterior: devuelve 301 directamente al slug vigente mientras la ficha sea pública, o 404 cuando pierde elegibilidad. Los Lugares permanecen privados hasta implementar sus negocios hijos verificados en VS12.

Imágenes y PDF se gestionan con `filament/spatie-laravel-media-library-plugin` y permanecen en almacenamiento privado. El botón PDF tiene texto editable por ficha. `/@{listing}` muestra solo contenido publicado y aprobado; los medios pierden acceso público al despublicar. Los reportes se revisan desde el panel y no corrigen datos automáticamente. Su recepción está disponible para comprobaciones locales; en producción queda cerrada hasta aprobar la privacidad y conectar su ruta en VS05.

[El registro VS02](docs/dev_changelog/VS02.md) explica las comprobaciones, las URLs locales y los pendientes. La primera ficha temporal ya se eliminó. La ampliación de VS03 conserva las seis fichas DEMO originales y añade 26 públicas: quedan **30 negocios DEMO públicos, un borrador y un pausado**, con sus medios persistentes para etapas futuras, separados del contenido real de salida.

Preparar DEMO explícitamente con `php artisan db:seed --class=DemoListingSeeder --no-interaction`. Solo local/testing; sin reiniciar la base. Es repetible, conserva archivos y cambios manuales y no modifica cuentas. No se retiran al terminar las pruebas. Los nuevos negocios incluyen categorías secundarias y se distribuyen entre ambas zonas para revisar filtros y paginación. DEMO se excluye de producción y del inventario real.

- [Café Menta DEMO](http://tugurulocal-nuevo.test/@demo-cafe-menta): portada, galería, FAQs y menú.
- [Taller Creativo DEMO](http://tugurulocal-nuevo.test/@demo-taller-creativo): sin portada; OG usa logo, con guía PDF.
- [Estudio Local DEMO](http://tugurulocal-nuevo.test/@demo-estudio-local): portada, FAQs y folleto PDF.
- [Experiencia Coral DEMO](http://tugurulocal-nuevo.test/@demo-experiencia-coral): sin portada ni PDF, con galería.
- [Borrador privado](http://tugurulocal-nuevo.test/dashboard/fichas/7/edit) y [pausado privado](http://tugurulocal-nuevo.test/dashboard/fichas/8/edit): sus URLs públicas devuelven 404.

Publicar requiere logo propio autorizado, con alt y fecha manual de verificación independientes de otros medios. La autorización confirma el permiso de uso; no se solicita fuente ni licencia escrita. Despublicar antes de retirarlo o reemplazarlo. Ciudades, categorías y zonas administran emojis, color HEX, tagline, descripción enriquecida, OG e icono independientes; VS03 utiliza esa identidad en sus páginas públicas.

### Descubrir negocios: VS03 completada

La implementación local comenzó en `codex/vs03-descubrir-negocios`, creada desde la base integrada `b59d920`; la ampliación DEMO continuó en `codex/vs03-descubrir-negocios-cont-20261022-1` y la confirmación sin JavaScript en la continuación `-2`. La entrega se realizó desde `codex/vs03-descubrir-negocios-cont-20261022-3`. [El registro VS03](docs/dev_changelog/VS03.md) conserva las comprobaciones y la integración verificada.

[PR #4: VS03, Descubrir negocios](https://github.com/luisfridev/tugurulocal/pull/4) fusionada hacia `master` el 22-10-2026 a las 16:17:35 (America/Cancun), commit `78e67bd3cee9e7b5389161ddb106ba354b306fef`. La continuación `codex/vs03-descubrir-negocios-cont-20261022-4` registra la integración comprobada. La puerta transversal de diseño también está integrada; VS04, URLs e indexación, se completó con la integración de la PR #11.

- [Inicio de Playa del Carmen](http://tugurulocal-nuevo.test/playa-del-carmen) ofrece accesos a categorías y zonas con resultados, hasta seis seleccionados manualmente y hasta seis recientes sin repetir los seleccionados.
- [Negocios](http://tugurulocal-nuevo.test/playa-del-carmen/negocios) admite `categoria`, `zona` y `page`. Las páginas de categoría y zona permiten filtrar el contexto restante. El botón «Aplicar» envía un formulario GET y vuelve a la primera página; «Limpiar filtros» conserva la página de origen.
- Los listados muestran 12 resultados por página, ordenados por publicación e identificador descendentes. Los filtros o páginas inválidos devuelven 404; una combinación válida sin resultados ofrece caminos para continuar. Los negocios cerrados conservan su estado visible; los cerrados permanentemente quedan fuera de los destacados de Inicio.
- En [Ciudades](http://tugurulocal-nuevo.test/dashboard/ciudades), «Selección de Inicio» permite elegir, subir, bajar y retirar negocios. La selección es independiente de los planes y de las listas futuras. Si un negocio pierde elegibilidad, se conserva identificado en el panel y se oculta de Inicio.

La comprobación física del logo se realiza antes de contar y paginar. Los iconos e imágenes OG autorizados de taxonomías tienen una ruta pública protegida por visibilidad, permiso, alt, fecha y existencia del archivo. Las vistas de descubrimiento registran referencias normalizadas de ciudad, categoría, zona y página, con deduplicación por sesión durante 60 segundos y sin IP ni URL completa.

VS03 incorpora dos migraciones incrementales: crea `home_selections` y amplía `interaction_events`, haciendo opcional `listing_id` para conservar los eventos de ficha y admitir los de descubrimiento. Se aplican con `php artisan migrate --no-interaction`; no requieren reiniciar la base ni recrear datos o medios DEMO. La reversión de la ampliación de eventos se rechaza si ya existen eventos de descubrimiento sin ficha; en ese caso corresponde una migración correctiva.

VS04 integra Spatie Laravel Sitemap 8.2.0 en `/sitemap.xml`, generado desde las consultas públicas en cada petición y sin rastreo, archivo estático ni cron. Solo incorpora URLs reales elegibles; un inventario compuesto únicamente por DEMO devuelve un XML sin URLs. Canonical, `noindex` y redirecciones continúan bajo las reglas propias del proyecto. GitHub confirmó la fusión de la PR #11 hacia `master` el 23-10-2026 a las 03:28:56 (America/Cancun), commit `7524bbc20e7cda895eb7795919bd0f90806c92bc`; [registro de integración y comprobaciones locales](docs/dev_changelog/VS04.md). No acredita producción.

Lugares públicos, catálogo, recomendaciones, listas y comercio siguen sus fases previstas. La navegación solo enlaza superficies disponibles y `/premium` permanece oculta.

### Laravel Boost para Codex

Boost 2.10.3 ya está en `require-dev`. VS00 configuró Codex con guías en `AGENTS.md`, skills en `.agents/skills/` y servidor MCP en `.codex/config.toml`; `boost.json` guarda las opciones para regenerarlos. No se seleccionó Laravel Cloud porque el alojamiento sigue pendiente. Después de `composer install`, una sesión nueva de Codex puede cargar el servidor MCP del proyecto. Para regenerar la configuración con la versión instalada:

```sh
php artisan boost:install --no-interaction
```

El comando actualiza el bloque delimitado de Boost en `AGENTS.md` y los archivos generados. Revisa su salida: Boost puede terminar con código cero aunque informe un fallo al escribir una integración. Las instrucciones propias de este proyecto, situadas antes del bloque generado, prevalecen ante discrepancias.

## Pendientes para el lanzamiento

Implementar VS05–VS17 y aprobar contenido verificado; importes mensuales Premium/Super Premium e IVA, WhatsApp Business y responsable, alojamiento, logotipo oficial y textos legales aprobados. `/premium` y sus CTA no existen públicamente hasta completar su puerta comercial. Ninguna prueba local equivale a verificación en producción.
