# Tu Gurú Local — proyecto nuevo

Aplicación Laravel nueva para construir Tu Gurú Local desde cero. La única ciudad pública inicial será Playa del Carmen y habrá un solo lanzamiento cuando los módulos y el contenido de salida estén aprobados. **VS00–VS03 completadas localmente; Inicio, Negocios, categorías y zonas están integrados en `master`. El sitio no está publicado.** La raíz redirige temporalmente al Inicio de Playa del Carmen. Las superficies vacías, filtradas o con solo DEMO llevan `noindex`.

## Contratos y secuencia

1. [Especificación maestra](docs/ESPECIFICACION_MAESTRA.md): producto y criterios de aceptación; prevalece ante discrepancias.
2. [Diseño técnico](docs/DISENO_TECNICO.md) y [stack](docs/TECH-STACK.md): arquitectura y versiones objetivo.
3. [Plan](docs/PLAN_IMPLEMENTACION.md) y [tablero VS00–VS17](docs/MEMORY.md): dependencias, puertas de fase y evidencia.
4. [Guía visual](docs/GUIA_VISUAL.md) y [contenido y operación](docs/CONTENIDO_Y_OPERACION.md): presentación y verificación editorial/comercial.

Los cambios ya realizados se explican por tarea en [docs/dev_changelog](docs/dev_changelog/VS00.md); el primer registro corresponde a VS00. Esa carpeta contiene únicamente registros Markdown, sin imágenes ni capturas; la revisión visual se documenta con URLs, tamaños de pantalla, interacciones y resultados.

Cada intervención en una tarea `VSXX` se desarrolla en una rama Git nueva: `codex/vsXX-descripcion` al comenzar y una rama con sufijo de continuación al retomarla. El cierre se entrega mediante una PR abierta hacia `master`, con cambios, pruebas y pendientes, para que el propietario la revise y fusione. El agente sube solo la rama de trabajo: no fusiona la PR, no integra directamente ni hace push a `master`. Mientras la PR no esté fusionada, la tarea sigue `en curso`, con «implementación verificada; integración pendiente en PR» y su número, URL y rama registrados en el tablero y el registro de la tarea. El [flujo de cierre](docs/PLAN_IMPLEMENTACION.md#cierre-mediante-pull-requests) y [AGENTS.md](AGENTS.md) detallan las reglas y la comprobación posterior de la integración. Los cierres anteriores, incluido VS02, conservan su evidencia histórica.

Las [capturas](docs/referencias/capturas/26-mapa-de-pantallas.jpg) orientan la composición. No aportan datos verificables ni autorizan funciones pospuestas. No se importó código, base de datos, contenido, slugs ni redirecciones del proyecto anterior.

Cada tarea aplica [Antislop según el trabajo](AGENTS.md#antislop-por-tarea), en modo `during` por defecto para este proyecto: base más los complementos de texto, UI, accesibilidad, responsive o comentarios que correspondan, junto con las skills técnicas. El [procedimiento de inicio y revisión final](docs/PLAN_IMPLEMENTACION.md#antislop-en-la-ejecución) exige evidencia de los criterios aplicables y conserva la guía visual como dirección de diseño.

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

Las fichas se publican manualmente, sin programación. Se pueden previsualizar, despublicar, archivar y recuperar; el historial conserva el contenido y exige nueva aprobación al recuperar una versión. El slug de una ficha ya publicada queda protegido hasta VS04. Los Lugares permanecen privados hasta implementar sus negocios hijos verificados en VS12.

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

[PR #4 — VS03: Descubrir negocios](https://github.com/luisfridev/tugurulocal/pull/4) fusionada hacia `master` el 22-10-2026 a las 16:17:35 (America/Cancun), commit `78e67bd3cee9e7b5389161ddb106ba354b306fef`. La continuación `codex/vs03-descubrir-negocios-cont-20261022-4` registra la integración comprobada. La siguiente tarea es VS04, URLs e indexación.

- [Inicio de Playa del Carmen](http://tugurulocal-nuevo.test/playa-del-carmen) ofrece accesos a categorías y zonas con resultados, hasta seis seleccionados manualmente y hasta seis recientes sin repetir los seleccionados.
- [Negocios](http://tugurulocal-nuevo.test/playa-del-carmen/negocios) admite `categoria`, `zona` y `page`. Las páginas de categoría y zona permiten filtrar el contexto restante. El botón «Aplicar» envía un formulario GET y vuelve a la primera página; «Limpiar filtros» conserva la página de origen.
- Los listados muestran 12 resultados por página, ordenados por publicación e identificador descendentes. Los filtros o páginas inválidos devuelven 404; una combinación válida sin resultados ofrece caminos para continuar. Los negocios cerrados conservan su estado visible; los cerrados permanentemente quedan fuera de los destacados de Inicio.
- En [Ciudades](http://tugurulocal-nuevo.test/dashboard/ciudades), «Selección de Inicio» permite elegir, subir, bajar y retirar negocios. La selección es independiente de los planes y de las listas futuras. Si un negocio pierde elegibilidad, se conserva identificado en el panel y se oculta de Inicio.

La comprobación física del logo se realiza antes de contar y paginar. Los iconos e imágenes OG autorizados de taxonomías tienen una ruta pública protegida por visibilidad, permiso, alt, fecha y existencia del archivo. Las vistas de descubrimiento registran referencias normalizadas de ciudad, categoría, zona y página, con deduplicación por sesión durante 60 segundos y sin IP ni URL completa.

VS03 incorpora dos migraciones incrementales: crea `home_selections` y amplía `interaction_events`, haciendo opcional `listing_id` para conservar los eventos de ficha y admitir los de descubrimiento. Se aplican con `php artisan migrate --no-interaction`; no requieren reiniciar la base ni recrear datos o medios DEMO. La reversión de la ampliación de eventos se rechaza si ya existen eventos de descubrimiento sin ficha; en ese caso corresponde una migración correctiva.

Lugares públicos, sitemap, redirecciones de slugs, catálogo, recomendaciones, listas y comercio siguen sus fases previstas. La navegación solo enlaza superficies disponibles y `/premium` permanece oculta.

### Laravel Boost para Codex

Boost 2.10.3 ya está en `require-dev`. VS00 configuró Codex con guías en `AGENTS.md`, skills en `.agents/skills/` y servidor MCP en `.codex/config.toml`; `boost.json` guarda las opciones para regenerarlos. No se seleccionó Laravel Cloud porque el alojamiento sigue pendiente. Después de `composer install`, una sesión nueva de Codex puede cargar el servidor MCP del proyecto. Para regenerar la configuración con la versión instalada:

```sh
php artisan boost:install --no-interaction
```

El comando actualiza el bloque delimitado de Boost en `AGENTS.md` y los archivos generados. Revisa su salida: Boost puede terminar con código cero aunque informe un fallo al escribir una integración. Las instrucciones propias de este proyecto, situadas antes del bloque generado, prevalecen ante discrepancias.

## Pendientes para el lanzamiento

Implementar VS04–VS17 y aprobar contenido verificado; importes mensuales Premium/Super Premium e IVA, WhatsApp Business y responsable, alojamiento, logotipo oficial y textos legales aprobados. `/premium` y sus CTA no existen públicamente hasta completar su puerta comercial. Ninguna prueba local equivale a verificación en producción.
