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

## Implementación futura

- Estructura Laravel convencional: reglas de dominio en app/, rutas en routes/, esquema y datos iniciales en database/, interfaz en resources/, pruebas en tests/ y recursos Filament en app/Filament/.
- Usa PHP con tipos explícitos, PSR-12 y Pint. Mantén servicios/acciones de negocio fuera de controladores, vistas y recursos Filament. Reutiliza componentes existentes una vez creado el proyecto.
- Confirma las versiones instaladas antes de usar APIs de paquetes. Usa MySQL como referencia de integración y PHPUnit para pruebas observables.
- Crea pruebas de comportamiento para cada decisión y fallo importante: publicación, autorización, moderación, relaciones, cupos, vigencias, patrocinios, rutas y SEO. Corre pruebas enfocadas y luego la suite completa, Pint, build y revisión visual antes de cerrar cada fase.
- No almacenes secretos, conversaciones privadas, credenciales ni datos personales en Git. La operación comercial asistida se registra sin confundir cortesías con ingreso.

## Entrega

Actualiza documentación, enlaces, implementación y pruebas cuando cambie un contrato. En cada entrega informa qué cambió, qué verificaste y qué permanece pendiente, sin atribuir resultados de producción a comprobaciones locales.

