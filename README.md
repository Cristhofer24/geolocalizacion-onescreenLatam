# Geolocalización por página: `page-specific-geolocation-snippet.php`

Además del script global, puedes usar el snippet PHP `page-specific-geolocation-snippet.php` para definir **un enlace diferente por página y por país** (Colombia / Ecuador).

### 🧩 Dónde se configura

Dentro de `page-specific-geolocation-snippet.php` todo se controla en **una sola función**:

```php
function get_page_specific_links() {
    // ...
    $endpoint_map = array(
        'aulas-clase' => array('aulas-clase'),
        'empresas'    => array('empresas'),
        'gobierno'    => array('gobierno'),
        'retail'      => array('retail'),
        // ...
    );

    $page_links_config = array(
        '/' => array(
            'CO' => 'https://...',
            'EC' => 'https://...'
        ),
        'aulas-clase' => array(
            'CO' => 'https://...',
            'EC' => 'https://...'
        ),
        // ...
    );
    // ...
}
```

### ➕ Cómo agregar un nuevo endpoint (nueva página)

1. **Identifica el slug (endpoint) de la URL** de la página en WordPress  
   - Ejemplo URL: `https://tusitio.com/software/` → slug: `software`  
   - Ejemplo URL: `https://tusitio.com/pantalla-led/` → slug: `pantalla-led`

2. **Agrega el slug al mapa de endpoints** (`$endpoint_map`):  

```php
$endpoint_map = array(
    // existentes...
    'software' => array('software'),
    'pantalla-led' => array('pantalla-led'),
);
```

- **Clave del array** (`'software'`, `'pantalla-led'`, etc.) es el **identificador interno** que se usará luego en `$page_links_config`.  
- El **valor** es un array de slugs que deben aparecer en la URL. Normalmente solo uno, por ejemplo `array('software')`.

3. **Agrega la configuración de enlaces por país** en `$page_links_config` usando el mismo identificador:

```php
$page_links_config = array(
    // ...
    'software' => array(
        'CO' => 'https://erp.onescreenlatam.com/r/qQe', // enlace para Colombia
        'EC' => 'https://claryicon.odoo.com/r/Hxe',     // enlace para Ecuador
    ),
    'pantalla-led' => array(
        'CO' => 'https://claryicon.odoo.com/r/Waw',
        'EC' => 'https://claryicon.odoo.com/r/ozB',
    ),
);
```

- **Usa siempre las claves `'CO'` y `'EC'`** para que el script de JavaScript pueda leerlas correctamente.
- Si falta alguna clave (`CO` o `EC`), el código usará el enlace por defecto (actualmente, el de Colombia).

4. **Guarda el archivo** y limpia caché (si usas caché en WordPress).

> **Importante para WordPress:**  
> Si vas a copiar este código dentro de `functions.php` o de un plugin como **Code Snippets**, **no pegues la etiqueta de apertura `<?php`** de `page-specific-geolocation-snippet.php`. Solo copia el contenido de las funciones, porque esos archivos/snippets ya incluyen su propia apertura PHP.

### 🌎 Cómo agregar nuevos países (MX, PE, etc.)

Actualmente el snippet está preparado para trabajar con **dos países**: `CO` (Colombia) y `EC` (Ecuador).  
Si quieres agregar más países (por ejemplo `MX`, `PE`), tienes que actualizar **dos partes del código**:

1. **Agregar el país en la configuración PHP de la página (`$page_links_config`)**  
   - En `page-specific-geolocation-snippet.php`, dentro de `get_page_specific_links()`, busca el array `$page_links_config`.  
   - En cada página donde quieras soportar un nuevo país, agrega una entrada extra con el código ISO-2 del país:

```php
$page_links_config = array(
    'software' => array(
        'CO' => 'https://erp.onescreenlatam.com/r/qQe', // Colombia
        'EC' => 'https://claryicon.odoo.com/r/Hxe',     // Ecuador
        'MX' => 'https://tusitio.com/mx/software',      // ejemplo México
        'PE' => 'https://tusitio.com/pe/software',      // ejemplo Perú
    ),
    // ...
);
```

2. **Agregar el país en el JavaScript embebido (objeto `countryLinks`)**  
   - En la misma función `enqueue_page_specific_geolocation_script()`, dentro del string grande de JavaScript, busca:

```javascript
const countryLinks = {
    'CO': '...', // Colombia
    'EC': '...', // Ecuador
};
```

   - Añade el mismo código de país con su enlace correspondiente:

```javascript
const countryLinks = {
    'CO': '...',                    // Colombia
    'EC': '...',                    // Ecuador
    'MX': 'https://...',            // México
    'PE': 'https://...',            // Perú
};
```

3. **Qué hace el código con esos países**

- La función `getUserCountry()` devuelve un código de país ISO-2 (por ejemplo `CO`, `EC`, `MX`, `PE`).  
- La función `getCountryLink(countryCode)` usa ese código para buscar dentro de `countryLinks[countryCode]`.  
- Si el país **no existe** en `countryLinks`, usará el **enlace por defecto** configurado en PHP (actualmente el de Colombia).

### 📌 Plantilla rápida para copiar y pegar (nuevos países)

En el código real, cerca de las líneas:

```php
$co_link = isset($country_links['CO']) ? $country_links['CO'] : $default_link;
$ec_link = isset($country_links['EC']) ? $country_links['EC'] : $default_link;
```

puedes usar esta **plantilla** para agregar más países:

```php
// Plantilla para nuevo país (copia y cambia el código XX)
$xx_link = isset($country_links['XX']) ? $country_links['XX'] : $default_link;

// Ejemplos concretos:
$mx_link = isset($country_links['MX']) ? $country_links['MX'] : $default_link; // México
$pe_link = isset($country_links['PE']) ? $country_links['PE'] : $default_link; // Perú
```

Luego, en el objeto `countryLinks` del JavaScript embebido:

```javascript
const countryLinks = {
    'CO': '" . safe_esc_js($co_link) . "',
    'EC': '" . safe_esc_js($ec_link) . "',
    // Plantilla: copia una línea y cambia el país y la variable
    'MX': '" . safe_esc_js($mx_link) . "', // México
    'PE': '" . safe_esc_js($pe_link) . "'  // Perú
};
```

Solo debes:
- Crear la variable PHP (`$mx_link`, `$pe_link`, etc.) usando la plantilla.  
- Añadir la entrada correspondiente en `countryLinks` usando esa misma variable.

### 🔁 Flujo resumido

- El snippet detecta el **path de la URL** (ej. `/software/`).
- Busca un match en `$endpoint_map` (por ejemplo `software`).
- Con ese identificador (`software`) obtiene los enlaces para `CO` y `EC` desde `$page_links_config`.
- El JavaScript intercepta los clics en botones con enlaces de Odoo/WhatsApp y redirige según el país detectado.

## 🐛 Solución de problemas

### El botón no funciona
- Verifica que el script esté cargando correctamente (revisa la consola del navegador)
- Asegúrate de que el selector del botón sea correcto
- Verifica que el botón tenga un enlace de WhatsApp

### Siempre redirige al mismo país
- Revisa la consola del navegador para ver errores
- Verifica que los servicios de geolocalización estén disponibles
- Prueba con una VPN para simular otro país

## 📞 Soporte

Si tienes problemas, revisa:
1. La consola del navegador (F12) para ver errores
2. Que los números de WhatsApp estén correctamente formateados
3. Que el script esté cargando después de que Elementor haya renderizado la página

