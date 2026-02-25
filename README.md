# diff --git a/README.md b/README.md

# index 1dd0d8b6a70ea2315fd398661326dd6827c015bc..76bd2ab0729becbb7e3419394ce0135345d3b8a8 100644

# --- a/README.md

# +++ b/README.md

# @@ -1 +1,56 @@

# &nbsp;# Baramovil

# \+

# +Aplicación web con menú hamburguesa y listado de productos en tarjetas, conectada a MySQL/MariaDB.

# \+

# +## Configuración solicitada (ya incluida en el repositorio)

# \+

# +- \*\*Servidor DB:\*\* `localhost`

# +- \*\*Puerto:\*\* `3329`

# +- \*\*Base de datos:\*\* `ADN\_SEBAS`

# +- \*\*Usuario:\*\* `sistemas`

# +- \*\*Clave:\*\* `adn`

# \+

# +Estos valores están cargados en `.env` y `.env.example`.

# \+

# +## Requisitos

# \+

# +- Node.js 18+

# +- Base de datos con las columnas:

# \+  - Productos: `PDT\_CODIGO`, `PDT\_DESCRIPCION`, `DEP\_CODIGO`, `CAT\_CODIGO`

# \+  - Precios: `PRE\_PRECIO`

# \+  - Departamentos: `DEP\_CODIGO`, `DEP\_DESCRIPCION`

# \+  - Categorías: `CAT\_CODIGO`, `CAT\_DESCRIPCION`

# +- Función SQL: `PERSONALIZAR\_GET\_TASA(moneda, fecha)`

# \+

# +## Ejecutar

# \+

# +1. Instala dependencias:

# \+

# +```bash

# +npm install

# +```

# \+

# +2. Inicia servidor:

# \+

# +```bash

# +npm start

# +```

# \+

# +3. Abre en el navegador:

# \+

# +`http://localhost:3329`

# \+

# +## Funcionalidad implementada

# \+

# +- Menú hamburguesa en la esquina superior izquierda.

# +- Submenús:

# \+  - Productos (todos)

# \+  - Departamento (`DEP\_CODIGO`, `DEP\_DESCRIPCION`)

# \+  - Categoria (`CAT\_CODIGO`, `CAT\_DESCRIPCION`)

# +- Tarjetas de producto con:

# \+  - Imagen `${PDT\_CODIGO}.jpg`

# \+  - Descripción (`PDT\_DESCRIPCION`)

# \+  - Código (`PDT\_CODIGO`)

# \+  - Precios en línea: `BS - COP - USD`

# +- Filtrado por departamento/categoría desde el menú lateral.

# +- Búsqueda por código o descripción.

# 

