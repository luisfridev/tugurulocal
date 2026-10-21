# Stack técnico objetivo

**Estado:** decisión de arquitectura para el proyecto nuevo. Las versiones exactas de paquetes se fijarán al generar los manifiestos; primero se comprobará su compatibilidad real.

| Capa | Elección | Uso y límite |
| --- | --- | --- |
| Runtime | PHP 8.4 | Desarrollo y despliegue compatibles con Laravel 13. |
| Aplicación | Laravel 13 | Rutas, validación, autorización, colas, correo, caché y pruebas. |
| Base de datos | MySQL | Fuente de verdad de datos y pruebas de integración; no sustituir por SQLite para declarar la salida verificada. |
| Interfaz dinámica | Livewire 4 | Interacciones que necesitan estado servidor; no crear una SPA adicional. |
| Administración | Filament 5 | Panel exclusivo del super admin; las políticas y reglas viven en la aplicación. |
| UI | Tailwind CSS 4 y DaisyUI 5 | Tokens y componentes según [guía visual](GUIA_VISUAL.md). |
| Medios | Spatie Laravel Media Library y su integración compatible con Filament 5 | Imágenes, PDFs y derivados autorizados; verificar compatibilidad antes de instalar. |
| Correo | Resend | Correo transaccional cuando lo requiera un flujo real; claves solo en entorno. |
| Desarrollo | Laravel Boost y Laravel Debugbar | Ayuda local; Debugbar nunca habilitada en producción. |
| Vídeo | URL validada de YouTube o Vimeo | Guardar enlace, no descargar ni alojar vídeos de terceros. |
| Pruebas | PHPUnit | Pruebas de comportamiento y regresión sobre MySQL; revisión visual manual para responsive y accesibilidad. |

## Instalación futura

La fase 0 del [plan](PLAN_IMPLEMENTACION.md) crea Laravel limpio, manifiestos y entorno local. Confirmar las versiones mayores indicadas y las APIs de paquetes antes de escribir integración. Añadir solo dependencias con uso definido en la maestra o diseño técnico. Configurar zona horaria de negocio **America/Cancun** para mostrar vigencias de Playa del Carmen; almacenar instantes de forma consistente y probar los límites de inicio/fin.

No se instalará ningún paquete en esta entrega documental. Las claves de Resend, MySQL y cualquier servicio se proporcionarán por variables de entorno y nunca se copiarán al repositorio.

