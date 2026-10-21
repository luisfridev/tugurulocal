# Tu Gurú Local — proyecto nuevo

Aplicación Laravel nueva para construir Tu Gurú Local desde cero. La única ciudad pública inicial será Playa del Carmen y habrá un solo lanzamiento cuando los módulos y el contenido de salida estén aprobados. **VS00 deja una base local ejecutable; la guía pública todavía no está implementada ni publicada.** La pantalla raíz es temporal y lleva `noindex`.

## Contratos y secuencia

1. [Especificación maestra](docs/ESPECIFICACION_MAESTRA.md): producto y criterios de aceptación; prevalece ante discrepancias.
2. [Diseño técnico](docs/DISENO_TECNICO.md) y [stack](docs/TECH-STACK.md): arquitectura y versiones objetivo.
3. [Plan](docs/PLAN_IMPLEMENTACION.md) y [tablero VS00–VS17](docs/MEMORY.md): dependencias, puertas de fase y evidencia.
4. [Guía visual](docs/GUIA_VISUAL.md) y [contenido y operación](docs/CONTENIDO_Y_OPERACION.md): presentación y verificación editorial/comercial.

Los cambios ya realizados se explican por tarea en [docs/dev_changelog](docs/dev_changelog/VS00.md); el primer registro corresponde a VS00.

Las [capturas](docs/referencias/capturas/26-mapa-de-pantallas.jpg) orientan la composición. No aportan datos verificables ni autorizan funciones pospuestas. No se importó código, base de datos, contenido, slugs ni redirecciones del proyecto anterior.

## Desarrollo local

Requisitos verificados para VS00: PHP 8.4, Composer 2, Node 22, npm 10 y MySQL 8.4. Usa dos bases **nuevas y separadas**, por ejemplo `tugurulocal_nuevo_dev` y `tugurulocal_nuevo_test`; las pruebas pueden vaciar la segunda. Crea un usuario MySQL local con acceso a ambas. Configura host, puerto, usuario y contraseña en `.env` (o `DB_SOCKET` para un servidor por socket); nunca los confirmes en Git.

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

`phpunit.xml` fuerza MySQL y usa `tugurulocal_nuevo_test`; toma la conexión local de `.env` o `.env.testing`. No apuntes esa base de pruebas a datos reales. La aplicación guarda instantes en UTC y `config/business.php` fija `America/Cancun` para su presentación. Las fuentes web están incluidas como paquetes npm, así que `npm run build` no necesita descargar fuentes en tiempo de compilación.

En cada VSXX con cambios visuales, revisa la URL afectada en Herd y enlázala en el registro de la tarea para poder abrirla en el navegador. Herd sirve la aplicación; la conexión MySQL se configura por separado. La instancia temporal usada al comprobar VS00 no es una base permanente para tareas posteriores.

La raíz muestra únicamente un aviso temporal en español. Filament está instalado en `/admin`, pero un usuario nuevo no tiene privilegios y no puede entrar al panel; la creación segura del primer super admin, 2FA, policies y auditoría corresponden a VS01. Debugbar queda apagada por defecto y se puede habilitar solo para desarrollo local mediante `DEBUGBAR_ENABLED=true`.

## Pendientes para el lanzamiento

VS01–VS17 y contenido verificado; importes mensuales Premium/Super Premium e IVA, WhatsApp Business y responsable, alojamiento, logotipo oficial y textos legales aprobados. `/premium` y sus CTA no existen públicamente hasta completar su puerta comercial. Ninguna prueba local equivale a verificación en producción.
