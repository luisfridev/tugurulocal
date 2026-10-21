# Tu Gurú Local — Contenido y operación

**Estado:** instrucciones de carga y salida para el proyecto nuevo. Son parte de los criterios R10/AC10 de la [especificación maestra](ESPECIFICACION_MAESTRA.md). No existe aquí un catálogo de negocios aprobado; las capturas no lo sustituyen.

## 1. Inventario mínimo antes de publicar

| Entregable | Mínimo | Evidencia de cierre |
| --- | ---: | --- |
| Negocios publicados | 40 | Datos, contacto, ubicación y contenido revisados; al menos cinco con categoría principal en cada una de las cuatro categorías de negocio. |
| Lugares públicos | 3 | Cada uno con página propia y negocios hijos ubicados allí, comprobados y publicados. |
| Lista «Dónde aprender a bailar en Playa del Carmen» | 5 fichas | Orden manual, criterio editorial visible y destinos públicos válidos. |
| Guías del blog | 5 | Artículos duraderos en Guías locales, con firma, fuentes, medios y SEO revisados. |
| Super Premium | 3 campañas reales o de cortesía | Material aprobado, cupo, posición, vigencia y etiqueta Patrocinado comprobados. |

No rellenar cupos con datos ficticios. Si una ficha no cumple verificación, permanecerá en borrador y el contador de salida no la incluirá. Las cortesías consumen los mismos espacios publicitarios y registran ingreso cero.

## 2. Plantilla de verificación por registro

Para cada ficha, Lugar, oferta, producto, guía o campaña guardar en administración o en el inventario editorial:

- Identificador interno; tipo y ciudad; nombre/título; responsable de carga y de aprobación; fecha de última revisión.
- Fuente específica de cada afirmación importante: web oficial, visita/comprobación directa, documento autorizado o contacto con el negocio. Anotar URL o referencia y fecha de consulta; no usar una captura de diseño como fuente.
- Permiso de uso del material: propietario/licencia, alcance, fecha y crédito requerido. Guardar evidencia fuera del contenido público. Si no existe permiso, no publicar ese medio.
- Estado de dirección, horario, teléfono, WhatsApp, web, precios, condiciones, menú/flyer, datos de Google Maps y vínculos con Lugares. Dejar vacíos los campos no confirmados.
- Motivo editorial de la inclusión y categoría principal. En lugares, nombre de cada negocio hijo, fuente o comprobación directa, fecha y responsable de la relación.
- Estado editorial, fecha prevista, URL canónica, título/descripcion SEO, imagen social y resultado de la revisión en móvil.
- Para ofertas: tipo, condiciones completas, inicio/fin en America/Cancun y modo de contactar al negocio. Para productos: precio y moneda solo si fueron confirmados.
- Para envíos públicos: consentimiento de atribución separado, decisión de moderación y cualquier contacto/evidencia privada con acceso restringido.

Esta plantilla es un control de procedencia; no autoriza publicar datos personales ni descargar imágenes sin permiso.

## 3. Flujo editorial

1. **Captura:** crear borrador con fuente, medios autorizados y campos verificados. La recomendación pública inicia pendiente y exige moderación.
2. **Revisión:** comprobar taxonomía, veracidad, ortografía, enlaces, condición comercial, permisos y que no haya duplicado. Las cifras de Google se introducen manualmente con fecha o se dejan vacías.
3. **Aprobación:** super admin registra responsable y fecha. Previsualizar antes de publicar; el contenido programado espera su hora.
4. **Publicación:** confirmar URL, canonical, sitemap, acciones de contacto, estado móvil y ausencia de datos privados. Un cambio posterior conserva revisión e historial.
5. **Mantenimiento:** revisar mensualmente datos cambiantes (horarios, teléfonos, precios, ofertas y Maps) y cada ficha completa al menos trimestralmente. Revalidar antes cualquier reporte de cierre o dato erróneo. Retirar inmediatamente ofertas vencidas por regla de visibilidad, incluso si no se ejecuta una tarea programada.

La lista de baile explica por qué cada opción entra; no es un filtro de categoría con otro nombre. Las cinco guías deben servir una consulta local duradera y enlazar solo a contenido público real. Ninguna selección editorial se vende como patrocinio.

## 4. Operación comercial asistida

- **Gratis:** el super admin publica una ficha completa sin compromiso comercial.
- **Pagada:** registrar acuerdo, período, referencia comercial, pago confirmado, importe recibido y responsable antes de activar Premium o Super Premium. La factura y el cobro se gestionan fuera del sitio.
- **Prueba:** registrar objetivo, inicio y fin. No cuenta como ingreso; al vencer regresa a Gratis.
- **Cortesía:** registrar motivo, aprobador, inicio y fin si se pacta; puede ser indefinida, pero se revisa y puede pausarse o revocarse. Ingreso cero. Si es Super Premium, consume uno de los tres cupos y dice «Patrocinado» en cada espacio.
- **Renovación:** decisión manual con nuevo período y, si es pagada, nuevo pago confirmado. Ninguna renovación automática.
- **Campaña:** comprobar elegibilidad de ficha y plan, material, categoría principal, posición, ciudad, inicio/fin y ausencia de conflicto. Un bloque en lista o Lugar es contrato adicional, con un máximo visible por página. El patrocinio nunca reordena listas editoriales.
- **Reporte:** separar planes activos, pruebas y cortesías de recibos confirmados. Impresiones/clics describen interacción, no ventas. Revisar próxima expiración y responsable de cada contrato.

## 5. Insumos del propietario y puertas

| Insumo pendiente | Dónde se configura o usa | Qué bloquea |
| --- | --- | --- |
| Precio mensual Premium y Super Premium en MXN | Panel de ajustes comerciales | Mostrar /premium y sus CTA. |
| Indicación de IVA incluido o adicional | Panel y copy de planes | Mostrar precio público. |
| WhatsApp Business oficial y responsable | Panel y CTA comercial | Mostrar /premium y sus CTA. |
| Plazo de respuesta | Panel si se desea prometer uno | Solo la promesa de plazo; no inventarlo. |
| Alojamiento, dominio y credenciales de despliegue | Operación | Despliegue, no desarrollo local. |
| Logotipo oficial | Sistema visual | Publicación de identidad final. |
| Aviso de privacidad y textos legales aprobados | Páginas legales | Publicación del formulario y analítica. |
| Permisos y contenido real del inventario | Panel/editorial | Lanzamiento público. |

Mientras no se completen precios, IVA, WhatsApp y responsable, /premium responde como página no disponible, no está en navegación ni sitemap y los CTA comerciales se ocultan. El plazo de respuesta solo se muestra si se aprueba uno. La implementación puede completarse y probarse localmente con estados de configuración ausente sin exponer valores de ejemplo al público.

## 6. Comprobación de salida

Antes de publicar, el super admin y quien implementa revisan el inventario y dejan constancia de fecha y resultado: fuentes y permisos; 40/3/5/5/3; vínculos de Lugares; oferta vigente/expirada; configuración comercial; etiquetas Patrocinado; rutas y SEO; privacidad y copias de seguridad; móvil, tableta, escritorio y teclado. El [plan de implementación](PLAN_IMPLEMENTACION.md) exige esta revisión como puerta del único lanzamiento. Si falta un bloque obligatorio, no se sustituye por contenido de las capturas.

