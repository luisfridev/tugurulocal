# Tu Gurú Local — proyecto nuevo

Este directorio contiene el contrato para construir desde cero Tu Gurú Local. **Todavía no contiene una aplicación Laravel, base de datos ni contenido publicable.** El primer lanzamiento será único, para Playa del Carmen.

## Orden de lectura y autoridad

1. [Especificación maestra](docs/ESPECIFICACION_MAESTRA.md): alcance, reglas de producto y criterios de aceptación. Es la autoridad funcional.
2. [Diseño técnico](docs/DISENO_TECNICO.md): propuesta de implementación que debe satisfacer la maestra.
3. [Plan de implementación](docs/PLAN_IMPLEMENTACION.md): fases, trazabilidad y puertas de salida; [tablero de entregas verticales](docs/MEMORY.md): secuencia, dependencias, aceptación y seguimiento de cada tarea.
4. [Guía visual](docs/GUIA_VISUAL.md) y [contenido y operación](docs/CONTENIDO_Y_OPERACION.md): presentación y trabajo editorial/comercial.
5. [Stack](docs/TECH-STACK.md): tecnologías elegidas y condiciones de instalación.

Si hay contradicción, se corrige el documento subordinado antes de desarrollar. Los acuerdos de esta carpeta sustituyen a las especificaciones históricas del proyecto anterior. Las [capturas](docs/referencias/capturas/26-mapa-de-pantallas.jpg) ilustran composición; **no** autorizan sus datos de ejemplo ni funciones pospuestas.

## Qué se construirá después

Laravel 13 con PHP 8.4, MySQL, Livewire 4 y Filament 5. Se usarán convenciones Laravel (app/, routes/, database/, resources/, tests/) y se mantendrán las reglas de negocio fuera de paneles y controladores. El trabajo empieza por el [plan](docs/PLAN_IMPLEMENTACION.md) y sigue VS00–VS17 del [tablero](docs/MEMORY.md); para añadir Laravel a esta carpeta ya poblada, generar primero el esqueleto en una carpeta temporal vacía y fusionarlo sin sobrescribir estos documentos. Cada fase tiene pruebas y una condición de cierre. No se copiará código, datos, migraciones ni redirecciones de la aplicación anterior.

## Insumos todavía pendientes

Importes mensuales de Premium y Super Premium y si incluyen IVA; número y responsable de WhatsApp Business y plazo de respuesta; alojamiento; archivo oficial del logotipo. Se registran en [contenido y operación](docs/CONTENIDO_Y_OPERACION.md). No impiden desarrollar localmente. La página /premium y sus CTA seguirán ocultos hasta tener datos comerciales reales; el alojamiento se decide antes del despliegue.

## Estado de este paquete

Documentación de arranque, no software probado. Los comandos de instalación, migración y pruebas se ejecutarán cuando se cree la aplicación. No se deben presentar las capturas, nombres de ejemplo o cifras de votos como contenido aprobado.

