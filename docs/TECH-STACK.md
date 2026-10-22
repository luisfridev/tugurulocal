# Stack técnico objetivo

**Estado:** VS00 instaló la base y fijó versiones exactas en `composer.lock` y `package-lock.json` el 21 de octubre de 2026. Las integraciones de módulos posteriores se añaden al implementarlos.

| Capa | Elección | Uso y límite |
| --- | --- | --- |
| Runtime | PHP 8.4 | Desarrollo y despliegue compatibles con Laravel 13. |
| Aplicación | Laravel 13 | Rutas, validación, autorización, colas, correo, caché y pruebas. |
| Base de datos | MySQL | Fuente de verdad de datos y pruebas de integración; no sustituir por SQLite para declarar la salida verificada. |
| Interfaz dinámica | Livewire 4 | Interacciones que necesitan estado servidor; no crear una SPA adicional. |
| Administración | Filament 5 | Panel exclusivo del super admin; las políticas y reglas viven en la aplicación. |
| UI | Tailwind CSS 4 y DaisyUI 5 | Tokens y componentes públicos según [design.md](design.md); Filament conserva sus patrones administrativos. |
| Medios | Spatie Laravel Media Library y su integración compatible con Filament 5 | Imágenes, PDFs y derivados autorizados; VS02 integra el plugin instalado con disco privado y entrega autorizada. |
| Correo | Resend | Pendiente de instalar hasta que un flujo real requiera correo; claves solo en entorno. |
| Desarrollo | Laravel Boost y Laravel Debugbar | Ayuda local; Debugbar nunca habilitada en producción. |
| Vídeo | URL validada de YouTube o Vimeo | Guardar enlace, no descargar ni alojar vídeos de terceros. |
| Pruebas | PHPUnit | Pruebas de comportamiento y regresión sobre MySQL; revisión visual manual para responsive y accesibilidad. |

## Versiones verificadas en VS00

PHP 8.4.25; Laravel 13.38.0; Livewire 4.4.8; Filament 5.10.3; Media Library 11.23.10 y plugin Filament 5.10.3; PHPUnit 12.5.40; Tailwind 4.3.3; DaisyUI 5.7.50; Vite 8.3.5. Laravel Boost 2.10.3 y Debugbar 4.4.4 son dependencias de desarrollo. Las tipografías locales vienen de Fontsource. Los manifiestos fijan familias mayores y los lockfiles resuelven las versiones exactas.

## Configuración y siguientes integraciones

El [manual de diseño](design.md) y la [guía de adopción](design-ref/implementation-guide.md) documentan la referencia Base44 y su mapeo a Blade/Tailwind 4/DaisyUI 5. La adopción visual es una intervención posterior, previa a VS04. El código actual conserva Nunito Sans e Instrument Sans; Baloo 2 e Inter son familias objetivo declaradas por la referencia, aún no instaladas aquí. Revisar licencia y autorización de cualquier nueva dependencia al implementarlas. Esta extracción no modifica CSS, paquetes ni lockfiles.

VS02 ampliada usa RichEditor y ColorPicker nativos de Filament 5, el plugin Spatie instalado para logo e identidad de taxonomías y el sanitizador Symfony ya instalado con lista restringida. No añade dependencias.

VS00 creó Laravel limpio, manifiestos y entorno MySQL local. En su continuación del 21 de octubre de 2026 se configuró Boost para Codex: `boost.json`, guías en `AGENTS.md`, skills locales y servidor MCP del proyecto. Boost permanece como dependencia de desarrollo; Laravel Cloud no se seleccionó porque el alojamiento sigue pendiente. Confirmar las APIs efectivas de paquetes antes de escribir cada integración. Añadir solo dependencias con uso definido en la maestra o diseño técnico. Guardar instantes en UTC y convertir a **America/Cancun** para mostrar vigencias de Playa del Carmen; probar los límites de inicio/fin al implementar contenido temporal.

Las claves de Resend, MySQL y cualquier servicio se proporcionan por variables de entorno y nunca se copian al repositorio. La instalación y pruebas locales están en [README](../README.md).

VS01 usa la autenticación TOTP nativa de Filament 5.10.3 sin añadir paquetes. El nombre de la base principal local se configura con `DB_DATABASE` en `.env`, actualmente `tugurulocal_2026`; PHPUnit usa `tugurulocal_2026_test`, según la decisión del propietario del 21 de octubre de 2026. La configuración MySQL conserva `'laravel'` como respaldo original, usado solo si falta `DB_DATABASE`. El comando de primera cuenta, las sesiones y la auditoría están implementados y probados localmente. La revisión visual y de teclado se completó el 22 de octubre de 2026 y cerró la fase Base local; no acredita producción.

