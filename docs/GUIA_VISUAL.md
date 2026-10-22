# Tu Gurú Local — Guía visual del proyecto nuevo

**Estado:** dirección visual para construir el alcance de la [especificación maestra](ESPECIFICACION_MAESTRA.md). Las capturas son referencias de composición, componentes y tono; no son datos, copys ni contratos funcionales aprobados. [Mapa completo de pantallas](referencias/capturas/26-mapa-de-pantallas.jpg).

## 1. Principios

- Apariencia cercana y editorial: superficies lavanda, tarjetas blancas, hero índigo y acción menta. El contenido verificado tiene prioridad sobre cifras o decoraciones.
- Mantener tres señales distintas: selección editorial por texto/ubicación, Premium por **borde** en tarjetas orgánicas y espacio pagado por la palabra visible **Patrocinado**. La tarjeta orgánica no lleva insignia del plan ni adelanto del catálogo.
- Omitir por completo votos, posiciones de ranking, favoritos, cuentas y barras de navegación hacia funciones pospuestas. Reescribir el pie y CTAs de las capturas para que reflejen rutas reales.
- El logotipo final usará archivo oficial aportado por el propietario. El favicon o el logotipo visible en un JPG no se considera archivo maestro.
- Imágenes y nombres de negocios en JPG son ejemplos visuales. Usar solo medios y datos autorizados para producción.

## 2. Tokens de diseño iniciales

Los anclajes proceden de las capturas. Texto y bordes son normalizaciones propuestas; validar el contraste en componentes terminados. Implementar como tokens semánticos en Tailwind/DaisyUI, no dispersar HEX en vistas.

| Función | HEX | Aplicación |
| --- | --- | --- |
| Marca profunda | `#2C254E` | Hero, pie, secciones de marca. |
| Cabecera | `#4A4468` | Navegación global. |
| Detalle oscuro | `#222A4E` | Cabecera de fichas. |
| Acción primaria | `#74DDCF` | Botón y selección con texto `#2B283D`. |
| Acento Premium | `#9E5CA4` | Borde y énfasis comercial; texto pequeño usa `#8D4C94`. |
| Página | `#F6F3FA` | Fondo público. |
| Tarjeta | `#FFFFFF` | Tarjetas, superficies, formularios. |
| Texto principal | `#2B283D` | Títulos y cuerpo sobre claro. |
| Texto secundario | `#5F5A6E` | Metadatos; comprobar contraste final. |
| Borde común | `#ECEBF3` | Separadores; nunca único indicador de foco. |

Tipografía inicial: **Nunito Sans** para títulos (700–900), **Instrument Sans** para lectura e interfaz (400–700), con fallback de sistema. Escala guía: cuerpo 16/26, cuerpo destacado 18/30, título de tarjeta 20/26, H3 24/30, H2 móvil 30/36 y escritorio 36/42, H1 móvil 36/40 y escritorio 48/52. Texto corrido hasta unos 70 caracteres por línea. Retícula de 4 px; espaciados 4, 8, 12, 16, 24, 32, 48, 64 y 96 px. Botones/campos con radio aproximado de 12 px, tarjetas de 24 px y hero de 32 px. Ajustar solo tras revisar las capturas en tamaños reales.

En las combinaciones ancla, texto oscuro sobre menta y blanco sobre índigo tienen contraste amplio; blanco sobre menta no sirve para botón primario. Verificar al menos 4.5:1 para texto normal y 3:1 para texto grande, además de estados hover, focus, disabled y texto sobre fotos. El foco debe ser visible, con anillo separado y sin depender del color de borde Premium.

## 3. Pantallas y adaptación

| Captura | Destino vigente | Adaptación obligatoria |
| --- | --- | --- |
| [01 Inicio](referencias/capturas/01-inicio.jpg) | `/{city}` | Hero, vitrinas y categorías; quitar votos, ranking y cifras de ejemplo; mostrar CTA comercial solo si /premium está activa. |
| [02 listado llamado «Explorar»](referencias/capturas/02-explorar.jpg) | `/{city}/negocios` | Reutilizar filtros y filas para todos los negocios; quitar votos, favoritos, puestos, “Más votados”, insignias y adelantos. |
| [03 Premium](referencias/capturas/03-premium.jpg) | `/premium` condicional | Comparar Gratis, Premium y Super Premium; retirar promesas de ranking/voto; precios editados en panel y contacto real. |
| [04 Comer y beber](referencias/capturas/04-categoria-comer-beber.jpg) | Categoría | Listado orgánico estable y vitrina Premium separada después de los primeros negocios. |
| [05 Qué hacer](referencias/capturas/05-categoria-que-hacer.jpg) | Categoría | Igual contrato; permite categoría secundaria editorial. |
| [06 Servicios](referencias/capturas/06-categoria-servicios.jpg) | Categoría | Igual contrato; sin cifras ni orden por votos. |
| [07 Experiencias](referencias/capturas/07-categoria-experiencias.jpg) | Categoría | Igual contrato; sin cifras ni orden por votos. |
| [08 Lugares públicos](referencias/capturas/08-categoria-lugares-publicos.jpg) | Categoría | Mostrar solo Lugares con hijos comprobados; contar Lugares, no “negocios”. |
| [09 Ranking](referencias/capturas/09-ranking.jpg) | Pospuesta | No implementar ni enlazar. |
| [10 Taquería](referencias/capturas/10-taqueria-el-fogon.jpg) | Ficha negocio | Cabecera, datos prácticos, contacto; retirar voto. |
| [11 Cenote](referencias/capturas/11-cenote-divers.jpg) | Ficha negocio | Igual; datos/medios solo verificados. |
| [12 Cafetería](referencias/capturas/12-cafeteria-xaman.jpg) | Ficha negocio | Igual; tarjeta orgánica no hereda insignia de plan. |
| [13 Spa](referencias/capturas/13-casa-jaguar-spa.jpg) | Ficha negocio | Ofertas/productos aprobados también visibles dentro de la ficha. |
| [14 Parque](referencias/capturas/14-parque-fundadores.jpg) | Ficha Lugar | Añadir sección de negocios hijos verificables; no publicar lugar aislado. |
| [15 Yoga](referencias/capturas/15-yoga-selva-studio.jpg) | Ficha negocio | Horarios/ubicación/contacto aprobados, sin voto. |
| [16 Mariscos](referencias/capturas/16-mariscos-la-cueva.jpg) | Ficha negocio | Oferta identificable y condiciones completas si existe. |
| [17 Barbería](referencias/capturas/17-barberia-norte.jpg) | Ficha negocio | Variante de ficha Gratis completa. |
| [18 Playa](referencias/capturas/18-playa-72.jpg) | Ficha negocio | Vigencia de promoción y camino claro a contacto. |
| [19 Cerámica](referencias/capturas/19-taller-ceramica-kaan.jpg) | Ficha negocio | Medios autorizados y alternativas cuando falten. |
| [20 Beneficios comunitarios](referencias/capturas/20-beneficios.jpg) | Pospuesta | No prometer cuentas ni “sin publicidad” mientras existan espacios identificados. |
| [21 Entrar](referencias/capturas/21-login.jpg) | Pospuesta | No crear acceso público. |
| [22 Registro](referencias/capturas/22-register.jpg) | Pospuesta | No crear registro público. |
| [23 Recuperación](referencias/capturas/23-forgot-password.jpg) | Pospuesta | No crear recuperación pública. |
| [24 Restablecimiento](referencias/capturas/24-reset-password.jpg) | Pospuesta | No crear restablecimiento público. |
| [25 Error](referencias/capturas/25-pagina-no-encontrada.jpg) | 404 | Mantener regreso útil, identidad de marca y texto español; no repetir URL sensible. |
| [26 Mapa](referencias/capturas/26-mapa-de-pantallas.jpg) | Índice visual | Solo mapa de referencias, no pantalla de producto. |

### Pantallas nuevas sin captura final

- **Explorar catálogo:** encabezado que explica que reúne contenido de negocios Premium/Super Premium; filtros por tipo y categoría; sección de ofertas vigentes con condiciones y fecha, ordenada por vencimiento cercano; sección de productos/servicios; tarjetas con enlace a ficha y ancla del elemento. Estado vacío honesto. “Ver todo” de ofertas activa `tipo=ofertas`.
- **Lugar con hijos:** después de información del Lugar, listado de negocios realmente ubicados dentro, con tarjetas orgánicas y su borde Premium cuando corresponda. Un bloque contratado, si existe, va aparte y dice «Patrocinado».
- **Listas y blog:** usar tarjetas y jerarquía de las capturas, pero diseñar páginas editoriales propias: criterio de inclusión visible, orden manual, firma, fecha, encabezados y navegación útil.
- **Carrusel Super Premium:** máximo tres campañas, etiqueta «Patrocinado» en cada diapositiva, pausa explícita, flechas/indicadores accesibles y avance automático desactivado con movimiento reducido.

## 4. Responsive y estados

La ampliación VS02 muestra logo de negocio junto al nombre, separado de portada. Taxonomías admiten emojis, tagline, descripción enriquecida, icono, imagen OG y color HEX opcionales. Sus componentes usan color propio con texto negro/blanco calculado (mínimo 4.5:1), conservando la marca global; sin color, marca profunda. Sin contenido se omite el bloque. PNG/WebP admiten transparencia. Las páginas completas quedan para VS03. Los medios sintéticos llevan identificación DEMO.

Las capturas muestran sobre todo móvil y algunas fichas de escritorio; diseñar tableta y escritorio mediante una retícula coherente, sin estirar tarjetas móviles indiscriminadamente. En móvil, navegación inferior y teclado virtual no pueden tapar contenido, botones ni formulario. En escritorio, limitar ancho de lectura y dar espacio a filtros sin romper el orden visual. Revisar estados de carga, vacíos, error, contenido largo, PDF, fotos ausentes, oferta vencida y /premium inactiva.

Comprobar navegación completa con teclado, orden de foco, lectura de etiquetas, zoom 200 %, objetivos táctiles, contraste y pausa del carrusel. El texto «Patrocinado» debe ser visible también en lectores de pantalla y no depender de una foto, color o borde.

