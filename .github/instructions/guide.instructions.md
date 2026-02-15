---
description: Reglas del proyecto
# applyTo: 'Describe when these instructions should be loaded' # when provided, instructions will automatically be added to the request context when the pattern matches an attached file
---

## Reglas del proyecto

- Siempre resuelve e importa `bootstrap.php` al inicio de cada archivo de entrada en `public/`.
- Para cualquier operación con base de datos, consulta primero el esquema en `database.sql` y usa **dbhub** (MCP).
- Para documentación de librerías o lenguajes, usa **context7** (MCP).

### UI/UX

- Usa exclusivamente clases de Bootstrap 5.
- Prohibido usar estilos inline.
- Prohibido crear clases o estilos personalizados, salvo que Bootstrap no cubra el caso. En ese supuesto, añade un comentario documentando el motivo.
- Para iconos, usa exclusivamente Bootstrap Icons. Si el icono que necesitas no existe, consulta con el equipo antes de usar una librería externa o crear un SVG personalizado.
- Para animaciones, usa exclusivamente Animate.css.
- Mobile first

### Arquitectura

El proyecto sigue una arquitectura de capas con la siguiente estructura:

- `public/` contiene únicamente los puntos de entrada. Su única responsabilidad es resolver el contenedor, iniciar sesión si aplica, y delegar al controlador correspondiente. No debe contener lógica de negocio ni conocer la estructura interna de la aplicación.
- `App\Http\Controller` contiene los controladores. Orquestan el flujo HTTP: leen input, invocan servicios y renderizan o redirigen. No contienen lógica de negocio.
- `App\Module\*\Service` contiene la lógica de negocio. No conoce HTTP, sesiones ni templates.
- `App\Module\*\Repository` es la única capa que interactúa con la base de datos.
- `App\Infrastructure` contiene implementaciones técnicas como el renderer, sesión y configuración.

Antes de implementar cualquier funcionalidad nueva, pregunta si no tienes claro en qué capa corresponde o si existe ya un servicio o repositorio que resuelva la necesidad.

Si tienes duda sobre de que es la aplicacion consulta `context.md`

### Ejecución

El proyecto corre con **Podman**. Antes de asumir configuración de servicios, puertos o variables de entorno, consulta `compose.yaml`.
