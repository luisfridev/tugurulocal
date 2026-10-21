# Instrucciones para agentes — Tu Gurú Local nuevo

## Fuente de verdad

Lee primero [README.md](README.md), [docs/ESPECIFICACION_MAESTRA.md](docs/ESPECIFICACION_MAESTRA.md), [docs/DISENO_TECNICO.md](docs/DISENO_TECNICO.md) y [docs/TECH-STACK.md](docs/TECH-STACK.md). Sigue [docs/PLAN_IMPLEMENTACION.md](docs/PLAN_IMPLEMENTACION.md) por fase. La maestra manda en producto; si otro documento discrepa, corrígelo antes de implementar. La carpeta docs/referencias/capturas contiene referencias visuales, no requisitos funcionales ni contenido verificable.

## Límites de producto

- Una ciudad pública inicial: Playa del Carmen. Un solo lanzamiento cuando todos los módulos incluidos y el contenido de salida estén listos.
- Construye desde cero. No copies código, datos, migraciones, slugs históricos ni redirecciones del proyecto anterior.
- No añadas cuentas públicas, favoritos, votos, ranking, reseñas propias, panel de propietarios, cobros en línea ni búsqueda por texto.
- Solo el super admin publica y modera. Las recomendaciones públicas quedan pendientes hasta revisión.
- La selección editorial, el borde Premium y el espacio pagado son conceptos distintos. Todo espacio contratado visible dice «Patrocinado».
- Usa hechos públicos verificados y medios autorizados. Si falta un dato de Google Maps, déjalo vacío; nunca inventes cifras.
- Precios, IVA, WhatsApp, alojamiento y logotipo oficial están pendientes. Mantén /premium y sus CTA ocultos hasta completar su puerta de activación.

## Implementación

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

