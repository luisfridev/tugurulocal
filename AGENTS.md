# Instrucciones para agentes — Tu Gurú Local nuevo

## Fuente de verdad

Lee primero [README.md](README.md), [docs/ESPECIFICACION_MAESTRA.md](docs/ESPECIFICACION_MAESTRA.md), [docs/DISENO_TECNICO.md](docs/DISENO_TECNICO.md) y [docs/TECH-STACK.md](docs/TECH-STACK.md). Sigue [docs/PLAN_IMPLEMENTACION.md](docs/PLAN_IMPLEMENTACION.md) por fase. La maestra manda en producto; si otro documento discrepa, corrígelo antes de implementar. La carpeta docs/referencias/capturas contiene referencias visuales, no requisitos funcionales ni contenido verificable.

Para diseño de la web pública, lee [docs/design.md](docs/design.md): es la autoridad visual y la base del futuro panel de usuario, sin habilitar sus funciones. Sus [tokens](docs/design-ref/design-tokens.json), [componentes](docs/design-ref/component-inventory.md) y [guía de implementación](docs/design-ref/implementation-guide.md) desarrollan ese contrato. La referencia principal es el sitio Base44 identificado en el manual; las capturas históricas son apoyo. [GUIA_VISUAL.md](docs/GUIA_VISUAL.md) remite al manual y no mantiene reglas propias. Filament conserva su sistema administrativo.

Antes de iniciar VS04, completar e integrar los dos hitos de la [puerta transversal de diseño](docs/MEMORY.md#puerta-transversal-de-diseno-antes-de-vs04): manual/referencias y alineación de las superficies públicas existentes. Son entregas separadas por PR; no renumeran ni reabren las VSXX. En cada intervención con UI pública registrar la revisión/base del manual, componentes afectados, capturas locales comparables y diferencias intencionales. Actualizar manual y tokens juntos si cambia una decisión visual; no declarar conformidad por parecido o por crear documentación.

## Límites de producto

- Una ciudad pública inicial: Playa del Carmen. Un solo lanzamiento cuando todos los módulos incluidos y el contenido de salida estén listos.
- Construye desde cero. No copies código, datos, migraciones, slugs históricos ni redirecciones del proyecto anterior.
- No añadas cuentas públicas, favoritos, votos, ranking, reseñas propias, panel de propietarios, cobros en línea ni búsqueda por texto.
- Solo el super admin publica y modera. Las recomendaciones públicas quedan pendientes hasta revisión.
- La selección editorial, el borde Premium y el espacio pagado son conceptos distintos. Todo espacio contratado visible dice «Patrocinado».
- Usa hechos públicos verificados y medios autorizados. Si falta un dato de Google Maps, déjalo vacío; nunca inventes cifras.
- Precios, IVA, WhatsApp, alojamiento y logotipo oficial están pendientes. Mantén /premium y sus CTA ocultos hasta completar su puerta de activación.

## Implementación

- Usa una rama y una PR por entrega, normalmente una `VSXX`. Antes de empezar, confirma sus dependencias y comprueba si ya tiene rama o PR: reutilízalas entre sesiones hasta la fusión, incluidas las correcciones, pruebas y documentación de esa entrega. Para una entrega nueva, crea `codex/vsXX-descripcion` desde `master` sincronizado con las dependencias integradas. Una nueva sesión o continuación no justifica otra rama ni un sufijo de continuación; conserva el nombre de las ramas existentes mientras sigan activas. Crea otra rama y PR solo para una entrega independiente o una corrección posterior a la fusión, con un nombre que describa ese trabajo. No desarrolles tareas VSXX directamente en `master` ni mezcles implementaciones de tareas distintas en una rama.
- Comprueba el estado de Git antes de crear o cambiar de rama y conserva los cambios sin confirmar; registra cada rama usada en `docs/dev_changelog/VSXX.md` y en la evidencia de `docs/MEMORY.md`.
- Al pedir el cierre de una `VSXX`, completa sus comprobaciones y documentación, crea los commits y sube únicamente la rama de trabajo. Actualiza su PR si ya existe; en caso contrario, abre una hacia `master` con título `VSXX: descripción`. Incluye implementación, pruebas, documentación y correcciones de revisión en esa misma PR. Explica los cambios, las pruebas ejecutadas y los pendientes. Deja la PR abierta para revisión y fusión del propietario; no la fusiones, no integres la rama directamente en `master` y no hagas push a `master`.
- Mientras la PR siga sin fusionar, conserva la tarea `en curso` con la indicación «implementación verificada; integración pendiente en PR». Registra número, URL y rama de la PR en `docs/MEMORY.md` y `docs/dev_changelog/VSXX.md`, actualizando la misma PR con ese registro; adjúntala al chat y entrega su enlace.
- Al retomar el trabajo después de la fusión del propietario, comprueba en GitHub que la PR esté fusionada hacia `master` y sincroniza `master` local únicamente por avance rápido desde `origin/master`. Con la aceptación satisfecha y esa integración comprobada, la dependencia queda cerrada y se puede iniciar la siguiente tarea aunque el tablero aún refleje el estado anterior. No abras una PR solo para registrar un merge, cambiar un estado o copiar metadatos, aunque cierre una fase o desbloquee trabajo. Incorpora la actualización del tablero y la evidencia del cierre anterior en la siguiente PR con trabajo sustantivo; esto no reabre la tarea anterior ni mezcla implementaciones de VSXX. Si no hay otra entrega, conserva la evidencia en GitHub e informa del cierre en el chat, sin crear una PR de registro.
- GitHub acredita el estado, la fecha y el commit de integración; `docs/MEMORY.md` resume el avance y `docs/dev_changelog/VSXX.md` conserva cambios y comprobaciones. README, plan y documentos de contrato enlazan esas fuentes sin exigir repetir el estado ni los metadatos de cada merge. Una discrepancia documental no revoca una integración comprobada: corrígela en la siguiente entrega. Conserva la evidencia histórica; no crees PR retrospectivas ni reescribas registros para ajustarlos al flujo nuevo.
- Estructura Laravel convencional: reglas de dominio en app/, rutas en routes/, esquema y datos iniciales en database/, interfaz en resources/, pruebas en tests/ y recursos Filament en app/Filament/.
- Usa PHP con tipos explícitos, PSR-12 y Pint. Mantén servicios/acciones de negocio fuera de controladores, vistas y recursos Filament. Reutiliza componentes existentes una vez creado el proyecto.
- Confirma las versiones instaladas antes de usar APIs de paquetes. Usa MySQL como referencia de integración y PHPUnit para pruebas observables.
- Crea pruebas de comportamiento para cada decisión y fallo importante: publicación, autorización, moderación, relaciones, cupos, vigencias, patrocinios, rutas y SEO. Corre pruebas enfocadas y luego la suite completa, Pint, build y revisión visual antes de cerrar cada fase.
- No almacenes secretos, conversaciones privadas, credenciales ni datos personales en Git. La operación comercial asistida se registra sin confundir cortesías con ingreso.

## Vista previa local

Herd sirve este checkout en [http://tugurulocal-nuevo.test/](http://tugurulocal-nuevo.test/). Usa ese dominio para revisar cambios en el navegador y configura `APP_URL` solo en el `.env` local. Herd sirve PHP; durante cambios de interfaz mantén `npm run dev` activo para la actualización de Vite. No se necesita `php artisan serve` para este checkout.

Por cada VSXX con UI, abre en Herd las rutas afectadas, comprueba que cargan los recursos y revisa móvil, tableta, escritorio y teclado. Enlaza las URLs locales revisadas desde `docs/dev_changelog/VSXX.md` para que el usuario pueda abrirlas. Si aún no existe una ruta pública, registra la pantalla administrativa o temporal comprobada y el límite de esa revisión. No presentes la vista local como verificación de producción.

## Entrega

Actualiza documentación, enlaces, implementación y pruebas cuando cambie un contrato. En cada entrega informa qué cambió, qué verificaste y qué permanece pendiente, sin atribuir resultados de producción a comprobaciones locales.

Cada vez que trabajes una tarea `VSXX` de [docs/MEMORY.md](docs/MEMORY.md), crea al comenzar `docs/dev_changelog/VSXX.md` si no existe y actualízalo al terminar cada intervención. Usa exactamente el ID de la tarea como nombre, por ejemplo `VS01.md`; si retomas la misma tarea, continúa en su archivo y fecha las nuevas entradas. Explica en español claro el objetivo, los cambios realizados y su motivo, los resultados observables, las comprobaciones con entorno y resultado, y lo pendiente. Distingue lo planeado de lo terminado y las pruebas locales de producción. No incluyas secretos, datos personales ni evidencia privada. Enlaza ese archivo desde la tarea correspondiente en `docs/MEMORY.md` y mantén su estado y evidencia sincronizados. No crees registros vacíos para tareas aún no trabajadas.

`docs/dev_changelog/` contiene únicamente registros Markdown por tarea. No guardes imágenes ni capturas en esa carpeta. Documenta la revisión visual en texto, con las URLs locales, tamaños de pantalla, interacciones comprobadas y resultados observables.

El bloque siguiente lo genera Laravel Boost. Si una regla generada discrepa de las instrucciones anteriores o de la especificación maestra, prevalecen las reglas propias del proyecto. `boost:install` conserva este texto fuera de su bloque.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>

<!-- antislop:start -->
## Antislop por tarea

Usa `antislop` como base al planificar y trabajar. El propietario eligió `during` como modo predeterminado de este proyecto: aplica las reglas durante el trabajo y comprueba el resultado antes de entregarlo. Una elección explícita del usuario para la sesión prevalece sobre este valor. Esta regla de proyecto prevalece sobre la preferencia global de Antislop; no consultes ni modifiques esa configuración para resolver el modo aquí, ni vuelvas a preguntarlo si ya está definido.

Selecciona los complementos por el contenido que se va a crear, modificar o revisar, no solo por el ID de la VSXX. Combina las filas que correspondan:

| Trabajo | Skills adicionales a `antislop` |
| --- | --- |
| Documentación, textos, mensajes y CTA | `antislop-copywriting`. |
| Interfaces públicas o administrativas: diseño, componentes, estilos e interacción | `antislop-ui` y `antislop-human`. |
| Distribución responsive, navegación y adaptación entre móvil, tableta y escritorio | `antislop-layoutmobile`, además de `antislop-ui` y `antislop-human`. |
| Redacción o limpieza de comentarios de código | `antislop-code`, limitada a comentarios; no autoriza cambios de lógica ni formato del código. |
| Backend, consultas, migraciones y pruebas | Skills técnicas correspondientes; añade complementos Antislop solo si también se trabajan textos, comentarios o interfaces. |

Antes de empezar:

1. Resuelve las ubicaciones de las skills instaladas mediante el catálogo de la sesión y lee sus `SKILL.md`: primero la base y después los complementos elegidos. Reutiliza las lecturas vigentes de la misma sesión. No copies las skills al repositorio, no fijes rutas personales en Git ni descargues instrucciones para suplir una ausencia. Si falta una skill necesaria, informa qué revisión queda pendiente y continúa el trabajo independiente; no declares cubierta esa revisión.
2. Indica una vez las skills aplicadas y su motivo. Anuncia el modo con su origen: `Antislop activo: during (regla del proyecto)`, o el modo elegido con `instrucción explícita de la sesión`. No repitas el aviso si la elección ya se confirmó en esta sesión. Si el usuario pide `after`, sigue el procedimiento de auditoría de la skill para ese alcance.
3. Para UI pública, lee [design.md](docs/design.md) como dirección de diseño y declara la interpretación y los valores ENERGY/RHYTHM/MOTION derivados de él. Filament conserva sus patrones administrativos. Conserva la autoridad de la [especificación maestra](docs/ESPECIFICACION_MAESTRA.md), las decisiones del propietario y los componentes existentes. Antislop complementa las skills de Laravel, Livewire, Tailwind y pruebas; no amplía el producto, cambia su identidad ni añade temas, animaciones o recursos sin una necesidad autorizada. No crees otro `DESIGN.md` paralelo en la raíz.

Antes de entregar, ejecuta la revisión final (`Delivery Gate`) de la base y las listas de los complementos usados sobre el alcance trabajado. Registra un resultado por criterio: `PASS` con evidencia concreta, `FAIL` con el defecto, o `no aplica` con motivo. Corrige los fallos antes de declarar la revisión superada; una comprobación sin ejecutar queda pendiente, nunca como `PASS`. Justifica las decisiones visuales relevantes y registra las interacciones comprobadas. Para UI conserva la revisión en Herd de móvil, tableta, escritorio, teclado, foco, contraste, estados e interacciones, con las pruebas técnicas que correspondan. En backend sin UI no exijas una revisión visual; para cambios exclusivamente documentales comprueba redacción, coherencia, enlaces y `git diff --check`, sin repetir PHPUnit, Pint ni build.

En cada intervención VSXX anota modo, skills y motivo de selección, resultados por criterio aplicable y pendientes en su `docs/dev_changelog/VSXX.md` existente, con evidencia resumida en [MEMORY.md](docs/MEMORY.md). Las actualizaciones generales del flujo se registran en el tablero y su PR; no abren una VSXX ni crean registros vacíos. Conserva la distinción entre comprobaciones locales e integración o producción. El [plan de implementación](docs/PLAN_IMPLEMENTACION.md#antislop-en-la-ejecución) incorpora estos pasos al inicio y al cierre.
<!-- antislop:end -->
