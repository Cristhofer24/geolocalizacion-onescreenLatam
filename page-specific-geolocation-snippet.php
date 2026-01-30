<?php
// Prevenir acceso directo

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper function para escapar strings JavaScript de forma segura
 */
function safe_esc_js($text)
{
    if (function_exists('esc_js')) {
        return call_user_func('esc_js', $text);
    }
    // Fallback: escapar caracteres especiales manualmente
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Devuelve el mapa de endpoints (slug => identificador de página).
 * Endpoints en esta lista excluidos del script no deben tener entrada en page_links_config.
 *
 * @return array
 */
function get_page_geo_endpoint_map()
{
    return array(
        'aulas-clase'     => array('aulas-clase'),
        'empresas'        => array('empresas'),
        'gobierno'        => array('gobierno'),
        'retail'          => array('retail'),
        'software'        => array('software'),
        'pantalla-led'    => array('pantalla-led'),
        '32-pulgadas'     => array('32-pulgadas'),
        '65-pulgadas'     => array('65-pulgadas'),
        '75-pulgadas'     => array('75-pulgadas'),
        '86-pulgadas'     => array('86-pulgadas'),
        '98-pulgadas'     => array('98-pulgadas'),
        'core'            => array('core'),
        't7'              => array('t7'),
        'titan'           => array('titan'),
        'wandr'           => array('wandr'),
        'clientes'        => array('clientes'),
        'distribuidores'  => array('distribuidores'),
        'rental'          => array('rental'),
        'recursos'        => array('recursos'),
        'registro-exitoso' => array('registro-exitoso'), // academy/registro-exitoso - sin geolocalización
    );
}

/**
 * Identificadores de página donde NO se debe cargar el script de geolocalización (conservan comportamiento WordPress).
 *
 * @return array
 */
function get_page_geo_excluded_identifiers()
{
    return array('registro-exitoso');
}

/**
 * Obtiene el identificador de página actual para geolocalización (path → clave usada en page_links_config).
 *
 * @return string
 */
function get_current_page_geo_identifier()
{
    $current_url = '';
    if (function_exists('home_url')) {
        $current_url = call_user_func('home_url', $_SERVER['REQUEST_URI']);
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    $path = (string) parse_url($current_url, PHP_URL_PATH);
    $path = '/' . ltrim($path, '/');
    $path = rtrim($path, '/') . '/';

    if ((function_exists('is_front_page') && call_user_func('is_front_page')) ||
        (function_exists('is_home') && call_user_func('is_home')) ||
        $path === '/'
    ) {
        return '/';
    }

    $page_identifier = '/';
    foreach (get_page_geo_endpoint_map() as $identifier => $slugs) {
        foreach ($slugs as $slug) {
            $slug = trim((string) $slug, " \t\n\r\0\x0B/");
            if ($slug === '') {
                continue;
            }
            if (strpos($path, '/' . $slug . '/') !== false) {
                return $identifier;
            }
        }
    }
    return $page_identifier;
}

/**
 * Obtiene la configuración de enlaces según la página actual
 *
 * @return array Configuración de enlaces por país para la página actual
 */
function get_page_specific_links()
{
    // Obtener la URL actual
    $current_url = '';
    if (function_exists('home_url')) {
        $current_url = call_user_func('home_url', $_SERVER['REQUEST_URI']);
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    // Obtener solo el path (sin querystring), normalizado
    $path = (string) parse_url($current_url, PHP_URL_PATH);
    $path = '/' . ltrim($path, '/');
    $path = rtrim($path, '/') . '/'; // siempre termina en /

    // Página Home (raíz del sitio)
    if ((function_exists('is_front_page') && call_user_func('is_front_page')) ||
        (function_exists('is_home') && call_user_func('is_home')) ||
        $path === '/'
    ) {
        $page_identifier = '/';
    } else {
        $endpoint_map = get_page_geo_endpoint_map();
        $page_identifier = '/';
        foreach ($endpoint_map as $identifier => $slugs) {
            foreach ($slugs as $slug) {
                $slug = trim((string) $slug, " \t\n\r\0\x0B/");
                if ($slug === '') {
                    continue;
                }
                // match robusto: /slug/ en el path
                if (strpos($path, '/' . $slug . '/') !== false) {
                    $page_identifier = $identifier;
                    break 2;
                }
            }
        }
    }

    // Configuración de enlaces por página y país (EXPLÍCITO por país, sin agrupar)
    $page_links_config = array(
        '/' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/lWN',
            'EC' => 'https://claryicon.odoo.com/r/kGO',
            'PE' => 'https://erp.onescreenlatam.com/r/qPe',
            'MX' => 'https://erp.onescreenlatam.com/r/3It',

            // Resto LATAM (explícito)
            'AR' => 'https://erp.onescreenlatam.com/r/qgL',
            'BO' => 'https://erp.onescreenlatam.com/r/qgL',
            'BR' => 'https://erp.onescreenlatam.com/r/qgL',
            'CL' => 'https://erp.onescreenlatam.com/r/qgL',
            'CR' => 'https://erp.onescreenlatam.com/r/qgL',
            'CU' => 'https://erp.onescreenlatam.com/r/qgL',
            'DO' => 'https://erp.onescreenlatam.com/r/qgL',
            'SV' => 'https://erp.onescreenlatam.com/r/qgL',
            'GT' => 'https://erp.onescreenlatam.com/r/qgL',
            'HN' => 'https://erp.onescreenlatam.com/r/qgL',
            'HT' => 'https://erp.onescreenlatam.com/r/qgL',
            'NI' => 'https://erp.onescreenlatam.com/r/qgL',
            'PA' => 'https://erp.onescreenlatam.com/r/qgL',
            'PY' => 'https://erp.onescreenlatam.com/r/qgL',
            'UY' => 'https://erp.onescreenlatam.com/r/qgL',
            'VE' => 'https://erp.onescreenlatam.com/r/qgL',
            'BZ' => 'https://erp.onescreenlatam.com/r/qgL',
            'GY' => 'https://erp.onescreenlatam.com/r/qgL',
            'SR' => 'https://erp.onescreenlatam.com/r/qgL',
            'JM' => 'https://erp.onescreenlatam.com/r/qgL',
            'TT' => 'https://erp.onescreenlatam.com/r/qgL',
            'BS' => 'https://erp.onescreenlatam.com/r/qgL',
            'BB' => 'https://erp.onescreenlatam.com/r/qgL',
            'GD' => 'https://erp.onescreenlatam.com/r/qgL',
            'DM' => 'https://erp.onescreenlatam.com/r/qgL',
            'LC' => 'https://erp.onescreenlatam.com/r/qgL',
            'VC' => 'https://erp.onescreenlatam.com/r/qgL',
            'KN' => 'https://erp.onescreenlatam.com/r/qgL',
            'AG' => 'https://erp.onescreenlatam.com/r/qgL',
            'PR' => 'https://erp.onescreenlatam.com/r/qgL'
        ),

        'aulas-clase' => array(
            'CO' => 'https://claryicon.odoo.com/r/6qo',
            'EC' => 'https://claryicon.odoo.com/r/l7s',
            'PE' => 'https://erp.onescreenlatam.com/r/nSm',
            'MX' => 'https://erp.onescreenlatam.com/r/Cwp',

            'AR' => 'https://erp.onescreenlatam.com/r/nJM',
            'BO' => 'https://erp.onescreenlatam.com/r/nJM',
            'BR' => 'https://erp.onescreenlatam.com/r/nJM',
            'CL' => 'https://erp.onescreenlatam.com/r/nJM',
            'CR' => 'https://erp.onescreenlatam.com/r/nJM',
            'CU' => 'https://erp.onescreenlatam.com/r/nJM',
            'DO' => 'https://erp.onescreenlatam.com/r/nJM',
            'SV' => 'https://erp.onescreenlatam.com/r/nJM',
            'GT' => 'https://erp.onescreenlatam.com/r/nJM',
            'HN' => 'https://erp.onescreenlatam.com/r/nJM',
            'HT' => 'https://erp.onescreenlatam.com/r/nJM',
            'NI' => 'https://erp.onescreenlatam.com/r/nJM',
            'PA' => 'https://erp.onescreenlatam.com/r/nJM',
            'PY' => 'https://erp.onescreenlatam.com/r/nJM',
            'UY' => 'https://erp.onescreenlatam.com/r/nJM',
            'VE' => 'https://erp.onescreenlatam.com/r/nJM',
            'BZ' => 'https://erp.onescreenlatam.com/r/nJM',
            'GY' => 'https://erp.onescreenlatam.com/r/nJM',
            'SR' => 'https://erp.onescreenlatam.com/r/nJM',
            'JM' => 'https://erp.onescreenlatam.com/r/nJM',
            'TT' => 'https://erp.onescreenlatam.com/r/nJM',
            'BS' => 'https://erp.onescreenlatam.com/r/nJM',
            'BB' => 'https://erp.onescreenlatam.com/r/nJM',
            'GD' => 'https://erp.onescreenlatam.com/r/nJM',
            'DM' => 'https://erp.onescreenlatam.com/r/nJM',
            'LC' => 'https://erp.onescreenlatam.com/r/nJM',
            'VC' => 'https://erp.onescreenlatam.com/r/nJM',
            'KN' => 'https://erp.onescreenlatam.com/r/nJM',
            'AG' => 'https://erp.onescreenlatam.com/r/nJM',
            'PR' => 'https://erp.onescreenlatam.com/r/nJM'
        ),

        'empresas' => array(
            'CO' => 'https://claryicon.odoo.com/r/Xtv',
            'EC' => 'https://claryicon.odoo.com/r/gSf',
            'PE' => 'https://erp.onescreenlatam.com/r/5nB',
            'MX' => 'https://erp.onescreenlatam.com/r/ta5',

            'AR' => 'https://erp.onescreenlatam.com/r/ZjP',
            'BO' => 'https://erp.onescreenlatam.com/r/ZjP',
            'BR' => 'https://erp.onescreenlatam.com/r/ZjP',
            'CL' => 'https://erp.onescreenlatam.com/r/ZjP',
            'CR' => 'https://erp.onescreenlatam.com/r/ZjP',
            'CU' => 'https://erp.onescreenlatam.com/r/ZjP',
            'DO' => 'https://erp.onescreenlatam.com/r/ZjP',
            'SV' => 'https://erp.onescreenlatam.com/r/ZjP',
            'GT' => 'https://erp.onescreenlatam.com/r/ZjP',
            'HN' => 'https://erp.onescreenlatam.com/r/ZjP',
            'HT' => 'https://erp.onescreenlatam.com/r/ZjP',
            'NI' => 'https://erp.onescreenlatam.com/r/ZjP',
            'PA' => 'https://erp.onescreenlatam.com/r/ZjP',
            'PY' => 'https://erp.onescreenlatam.com/r/ZjP',
            'UY' => 'https://erp.onescreenlatam.com/r/ZjP',
            'VE' => 'https://erp.onescreenlatam.com/r/ZjP',
            'BZ' => 'https://erp.onescreenlatam.com/r/ZjP',
            'GY' => 'https://erp.onescreenlatam.com/r/ZjP',
            'SR' => 'https://erp.onescreenlatam.com/r/ZjP',
            'JM' => 'https://erp.onescreenlatam.com/r/ZjP',
            'TT' => 'https://erp.onescreenlatam.com/r/ZjP',
            'BS' => 'https://erp.onescreenlatam.com/r/ZjP',
            'BB' => 'https://erp.onescreenlatam.com/r/ZjP',
            'GD' => 'https://erp.onescreenlatam.com/r/ZjP',
            'DM' => 'https://erp.onescreenlatam.com/r/ZjP',
            'LC' => 'https://erp.onescreenlatam.com/r/ZjP',
            'VC' => 'https://erp.onescreenlatam.com/r/ZjP',
            'KN' => 'https://erp.onescreenlatam.com/r/ZjP',
            'AG' => 'https://erp.onescreenlatam.com/r/ZjP',
            'PR' => 'https://erp.onescreenlatam.com/r/ZjP'
        ),

        'gobierno' => array(
            'CO' => 'https://claryicon.odoo.com/r/SmC',
            'EC' => 'https://claryicon.odoo.com/r/Ua7',
            'PE' => 'https://erp.onescreenlatam.com/r/cdP',
            'MX' => 'https://erp.onescreenlatam.com/r/LIU',

            'AR' => 'https://erp.onescreenlatam.com/r/she',
            'BO' => 'https://erp.onescreenlatam.com/r/she',
            'BR' => 'https://erp.onescreenlatam.com/r/she',
            'CL' => 'https://erp.onescreenlatam.com/r/she',
            'CR' => 'https://erp.onescreenlatam.com/r/she',
            'CU' => 'https://erp.onescreenlatam.com/r/she',
            'DO' => 'https://erp.onescreenlatam.com/r/she',
            'SV' => 'https://erp.onescreenlatam.com/r/she',
            'GT' => 'https://erp.onescreenlatam.com/r/she',
            'HN' => 'https://erp.onescreenlatam.com/r/she',
            'HT' => 'https://erp.onescreenlatam.com/r/she',
            'NI' => 'https://erp.onescreenlatam.com/r/she',
            'PA' => 'https://erp.onescreenlatam.com/r/she',
            'PY' => 'https://erp.onescreenlatam.com/r/she',
            'UY' => 'https://erp.onescreenlatam.com/r/she',
            'VE' => 'https://erp.onescreenlatam.com/r/she',
            'BZ' => 'https://erp.onescreenlatam.com/r/she',
            'GY' => 'https://erp.onescreenlatam.com/r/she',
            'SR' => 'https://erp.onescreenlatam.com/r/she',
            'JM' => 'https://erp.onescreenlatam.com/r/she',
            'TT' => 'https://erp.onescreenlatam.com/r/she',
            'BS' => 'https://erp.onescreenlatam.com/r/she',
            'BB' => 'https://erp.onescreenlatam.com/r/she',
            'GD' => 'https://erp.onescreenlatam.com/r/she',
            'DM' => 'https://erp.onescreenlatam.com/r/she',
            'LC' => 'https://erp.onescreenlatam.com/r/she',
            'VC' => 'https://erp.onescreenlatam.com/r/she',
            'KN' => 'https://erp.onescreenlatam.com/r/she',
            'AG' => 'https://erp.onescreenlatam.com/r/she',
            'PR' => 'https://erp.onescreenlatam.com/r/she'
        ),

        'retail' => array(
            'CO' => 'https://claryicon.odoo.com/r/Z4o',
            'EC' => 'https://claryicon.odoo.com/r/5ux',
            'PE' => 'https://erp.onescreenlatam.com/r/DKS',
            'MX' => 'https://erp.onescreenlatam.com/r/RkG',

            'AR' => 'https://erp.onescreenlatam.com/r/FN2',
            'BO' => 'https://erp.onescreenlatam.com/r/FN2',
            'BR' => 'https://erp.onescreenlatam.com/r/FN2',
            'CL' => 'https://erp.onescreenlatam.com/r/FN2',
            'CR' => 'https://erp.onescreenlatam.com/r/FN2',
            'CU' => 'https://erp.onescreenlatam.com/r/FN2',
            'DO' => 'https://erp.onescreenlatam.com/r/FN2',
            'SV' => 'https://erp.onescreenlatam.com/r/FN2',
            'GT' => 'https://erp.onescreenlatam.com/r/FN2',
            'HN' => 'https://erp.onescreenlatam.com/r/FN2',
            'HT' => 'https://erp.onescreenlatam.com/r/FN2',
            'NI' => 'https://erp.onescreenlatam.com/r/FN2',
            'PA' => 'https://erp.onescreenlatam.com/r/FN2',
            'PY' => 'https://erp.onescreenlatam.com/r/FN2',
            'UY' => 'https://erp.onescreenlatam.com/r/FN2',
            'VE' => 'https://erp.onescreenlatam.com/r/FN2',
            'BZ' => 'https://erp.onescreenlatam.com/r/FN2',
            'GY' => 'https://erp.onescreenlatam.com/r/FN2',
            'SR' => 'https://erp.onescreenlatam.com/r/FN2',
            'JM' => 'https://erp.onescreenlatam.com/r/FN2',
            'TT' => 'https://erp.onescreenlatam.com/r/FN2',
            'BS' => 'https://erp.onescreenlatam.com/r/FN2',
            'BB' => 'https://erp.onescreenlatam.com/r/FN2',
            'GD' => 'https://erp.onescreenlatam.com/r/FN2',
            'DM' => 'https://erp.onescreenlatam.com/r/FN2',
            'LC' => 'https://erp.onescreenlatam.com/r/FN2',
            'VC' => 'https://erp.onescreenlatam.com/r/FN2',
            'KN' => 'https://erp.onescreenlatam.com/r/FN2',
            'AG' => 'https://erp.onescreenlatam.com/r/FN2',
            'PR' => 'https://erp.onescreenlatam.com/r/FN2'
        ),

        'software' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/qQe',
            'EC' => 'https://claryicon.odoo.com/r/Hxe',
            'PE' => 'https://erp.onescreenlatam.com/r/s7E',
            'MX' => 'https://erp.onescreenlatam.com/r/pAJ',

            'AR' => 'https://erp.onescreenlatam.com/r/GPD',
            'BO' => 'https://erp.onescreenlatam.com/r/GPD',
            'BR' => 'https://erp.onescreenlatam.com/r/GPD',
            'CL' => 'https://erp.onescreenlatam.com/r/GPD',
            'CR' => 'https://erp.onescreenlatam.com/r/GPD',
            'CU' => 'https://erp.onescreenlatam.com/r/GPD',
            'DO' => 'https://erp.onescreenlatam.com/r/GPD',
            'SV' => 'https://erp.onescreenlatam.com/r/GPD',
            'GT' => 'https://erp.onescreenlatam.com/r/GPD',
            'HN' => 'https://erp.onescreenlatam.com/r/GPD',
            'HT' => 'https://erp.onescreenlatam.com/r/GPD',
            'NI' => 'https://erp.onescreenlatam.com/r/GPD',
            'PA' => 'https://erp.onescreenlatam.com/r/GPD',
            'PY' => 'https://erp.onescreenlatam.com/r/GPD',
            'UY' => 'https://erp.onescreenlatam.com/r/GPD',
            'VE' => 'https://erp.onescreenlatam.com/r/GPD',
            'BZ' => 'https://erp.onescreenlatam.com/r/GPD',
            'GY' => 'https://erp.onescreenlatam.com/r/GPD',
            'SR' => 'https://erp.onescreenlatam.com/r/GPD',
            'JM' => 'https://erp.onescreenlatam.com/r/GPD',
            'TT' => 'https://erp.onescreenlatam.com/r/GPD',
            'BS' => 'https://erp.onescreenlatam.com/r/GPD',
            'BB' => 'https://erp.onescreenlatam.com/r/GPD',
            'GD' => 'https://erp.onescreenlatam.com/r/GPD',
            'DM' => 'https://erp.onescreenlatam.com/r/GPD',
            'LC' => 'https://erp.onescreenlatam.com/r/GPD',
            'VC' => 'https://erp.onescreenlatam.com/r/GPD',
            'KN' => 'https://erp.onescreenlatam.com/r/GPD',
            'AG' => 'https://erp.onescreenlatam.com/r/GPD',
            'PR' => 'https://erp.onescreenlatam.com/r/GPD'
        ),

        'pantalla-led' => array(
            'CO' => 'https://claryicon.odoo.com/r/Waw',
            'EC' => 'https://claryicon.odoo.com/r/ozB',
            'PE' => 'https://erp.onescreenlatam.com/r/6eU',
            'MX' => 'https://erp.onescreenlatam.com/r/nVK',

            'AR' => 'https://erp.onescreenlatam.com/r/A5O',
            'BO' => 'https://erp.onescreenlatam.com/r/A5O',
            'BR' => 'https://erp.onescreenlatam.com/r/A5O',
            'CL' => 'https://erp.onescreenlatam.com/r/A5O',
            'CR' => 'https://erp.onescreenlatam.com/r/A5O',
            'CU' => 'https://erp.onescreenlatam.com/r/A5O',
            'DO' => 'https://erp.onescreenlatam.com/r/A5O',
            'SV' => 'https://erp.onescreenlatam.com/r/A5O',
            'GT' => 'https://erp.onescreenlatam.com/r/A5O',
            'HN' => 'https://erp.onescreenlatam.com/r/A5O',
            'HT' => 'https://erp.onescreenlatam.com/r/A5O',
            'NI' => 'https://erp.onescreenlatam.com/r/A5O',
            'PA' => 'https://erp.onescreenlatam.com/r/A5O',
            'PY' => 'https://erp.onescreenlatam.com/r/A5O',
            'UY' => 'https://erp.onescreenlatam.com/r/A5O',
            'VE' => 'https://erp.onescreenlatam.com/r/A5O',
            'BZ' => 'https://erp.onescreenlatam.com/r/A5O',
            'GY' => 'https://erp.onescreenlatam.com/r/A5O',
            'SR' => 'https://erp.onescreenlatam.com/r/A5O',
            'JM' => 'https://erp.onescreenlatam.com/r/A5O',
            'TT' => 'https://erp.onescreenlatam.com/r/A5O',
            'BS' => 'https://erp.onescreenlatam.com/r/A5O',
            'BB' => 'https://erp.onescreenlatam.com/r/A5O',
            'GD' => 'https://erp.onescreenlatam.com/r/A5O',
            'DM' => 'https://erp.onescreenlatam.com/r/A5O',
            'LC' => 'https://erp.onescreenlatam.com/r/A5O',
            'VC' => 'https://erp.onescreenlatam.com/r/A5O',
            'KN' => 'https://erp.onescreenlatam.com/r/A5O',
            'AG' => 'https://erp.onescreenlatam.com/r/A5O',
            'PR' => 'https://erp.onescreenlatam.com/r/A5O'
        ),

        '32-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/uLb',
            'EC' => 'https://claryicon.odoo.com/r/XtX',
            'PE' => 'https://erp.onescreenlatam.com/r/06N',
            'MX' => 'https://erp.onescreenlatam.com/r/28m',

            'AR' => 'https://erp.onescreenlatam.com/r/wLm',
            'BO' => 'https://erp.onescreenlatam.com/r/wLm',
            'BR' => 'https://erp.onescreenlatam.com/r/wLm',
            'CL' => 'https://erp.onescreenlatam.com/r/wLm',
            'CR' => 'https://erp.onescreenlatam.com/r/wLm',
            'CU' => 'https://erp.onescreenlatam.com/r/wLm',
            'DO' => 'https://erp.onescreenlatam.com/r/wLm',
            'SV' => 'https://erp.onescreenlatam.com/r/wLm',
            'GT' => 'https://erp.onescreenlatam.com/r/wLm',
            'HN' => 'https://erp.onescreenlatam.com/r/wLm',
            'HT' => 'https://erp.onescreenlatam.com/r/wLm',
            'NI' => 'https://erp.onescreenlatam.com/r/wLm',
            'PA' => 'https://erp.onescreenlatam.com/r/wLm',
            'PY' => 'https://erp.onescreenlatam.com/r/wLm',
            'UY' => 'https://erp.onescreenlatam.com/r/wLm',
            'VE' => 'https://erp.onescreenlatam.com/r/wLm',
            'BZ' => 'https://erp.onescreenlatam.com/r/wLm',
            'GY' => 'https://erp.onescreenlatam.com/r/wLm',
            'SR' => 'https://erp.onescreenlatam.com/r/wLm',
            'JM' => 'https://erp.onescreenlatam.com/r/wLm',
            'TT' => 'https://erp.onescreenlatam.com/r/wLm',
            'BS' => 'https://erp.onescreenlatam.com/r/wLm',
            'BB' => 'https://erp.onescreenlatam.com/r/wLm',
            'GD' => 'https://erp.onescreenlatam.com/r/wLm',
            'DM' => 'https://erp.onescreenlatam.com/r/wLm',
            'LC' => 'https://erp.onescreenlatam.com/r/wLm',
            'VC' => 'https://erp.onescreenlatam.com/r/wLm',
            'KN' => 'https://erp.onescreenlatam.com/r/wLm',
            'AG' => 'https://erp.onescreenlatam.com/r/wLm',
            'PR' => 'https://erp.onescreenlatam.com/r/wLm'
        ),

        '65-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/CHj',
            'EC' => 'https://claryicon.odoo.com/r/uVB',
            'PE' => 'https://erp.onescreenlatam.com/r/Vim',
            'MX' => 'https://erp.onescreenlatam.com/r/4gH',

            'AR' => 'https://erp.onescreenlatam.com/r/Wc9',
            'BO' => 'https://erp.onescreenlatam.com/r/Wc9',
            'BR' => 'https://erp.onescreenlatam.com/r/Wc9',
            'CL' => 'https://erp.onescreenlatam.com/r/Wc9',
            'CR' => 'https://erp.onescreenlatam.com/r/Wc9',
            'CU' => 'https://erp.onescreenlatam.com/r/Wc9',
            'DO' => 'https://erp.onescreenlatam.com/r/Wc9',
            'SV' => 'https://erp.onescreenlatam.com/r/Wc9',
            'GT' => 'https://erp.onescreenlatam.com/r/Wc9',
            'HN' => 'https://erp.onescreenlatam.com/r/Wc9',
            'HT' => 'https://erp.onescreenlatam.com/r/Wc9',
            'NI' => 'https://erp.onescreenlatam.com/r/Wc9',
            'PA' => 'https://erp.onescreenlatam.com/r/Wc9',
            'PY' => 'https://erp.onescreenlatam.com/r/Wc9',
            'UY' => 'https://erp.onescreenlatam.com/r/Wc9',
            'VE' => 'https://erp.onescreenlatam.com/r/Wc9',
            'BZ' => 'https://erp.onescreenlatam.com/r/Wc9',
            'GY' => 'https://erp.onescreenlatam.com/r/Wc9',
            'SR' => 'https://erp.onescreenlatam.com/r/Wc9',
            'JM' => 'https://erp.onescreenlatam.com/r/Wc9',
            'TT' => 'https://erp.onescreenlatam.com/r/Wc9',
            'BS' => 'https://erp.onescreenlatam.com/r/Wc9',
            'BB' => 'https://erp.onescreenlatam.com/r/Wc9',
            'GD' => 'https://erp.onescreenlatam.com/r/Wc9',
            'DM' => 'https://erp.onescreenlatam.com/r/Wc9',
            'LC' => 'https://erp.onescreenlatam.com/r/Wc9',
            'VC' => 'https://erp.onescreenlatam.com/r/Wc9',
            'KN' => 'https://erp.onescreenlatam.com/r/Wc9',
            'AG' => 'https://erp.onescreenlatam.com/r/Wc9',
            'PR' => 'https://erp.onescreenlatam.com/r/Wc9'
        ),

        '75-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/k5B',
            'EC' => 'https://claryicon.odoo.com/r/tgX',
            'PE' => 'https://erp.onescreenlatam.com/r/e0E',
            'MX' => 'https://erp.onescreenlatam.com/r/WtY',

            'AR' => 'https://erp.onescreenlatam.com/r/7jy',
            'BO' => 'https://erp.onescreenlatam.com/r/7jy',
            'BR' => 'https://erp.onescreenlatam.com/r/7jy',
            'CL' => 'https://erp.onescreenlatam.com/r/7jy',
            'CR' => 'https://erp.onescreenlatam.com/r/7jy',
            'CU' => 'https://erp.onescreenlatam.com/r/7jy',
            'DO' => 'https://erp.onescreenlatam.com/r/7jy',
            'SV' => 'https://erp.onescreenlatam.com/r/7jy',
            'GT' => 'https://erp.onescreenlatam.com/r/7jy',
            'HN' => 'https://erp.onescreenlatam.com/r/7jy',
            'HT' => 'https://erp.onescreenlatam.com/r/7jy',
            'NI' => 'https://erp.onescreenlatam.com/r/7jy',
            'PA' => 'https://erp.onescreenlatam.com/r/7jy',
            'PY' => 'https://erp.onescreenlatam.com/r/7jy',
            'UY' => 'https://erp.onescreenlatam.com/r/7jy',
            'VE' => 'https://erp.onescreenlatam.com/r/7jy',
            'BZ' => 'https://erp.onescreenlatam.com/r/7jy',
            'GY' => 'https://erp.onescreenlatam.com/r/7jy',
            'SR' => 'https://erp.onescreenlatam.com/r/7jy',
            'JM' => 'https://erp.onescreenlatam.com/r/7jy',
            'TT' => 'https://erp.onescreenlatam.com/r/7jy',
            'BS' => 'https://erp.onescreenlatam.com/r/7jy',
            'BB' => 'https://erp.onescreenlatam.com/r/7jy',
            'GD' => 'https://erp.onescreenlatam.com/r/7jy',
            'DM' => 'https://erp.onescreenlatam.com/r/7jy',
            'LC' => 'https://erp.onescreenlatam.com/r/7jy',
            'VC' => 'https://erp.onescreenlatam.com/r/7jy',
            'KN' => 'https://erp.onescreenlatam.com/r/7jy',
            'AG' => 'https://erp.onescreenlatam.com/r/7jy',
            'PR' => 'https://erp.onescreenlatam.com/r/7jy'
        ),

        '86-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/c8b',
            'EC' => 'https://claryicon.odoo.com/r/qxC',
            'PE' => 'https://erp.onescreenlatam.com/r/Rrx',
            'MX' => 'https://erp.onescreenlatam.com/r/pp3',

            'AR' => 'https://erp.onescreenlatam.com/r/B67',
            'BO' => 'https://erp.onescreenlatam.com/r/B67',
            'BR' => 'https://erp.onescreenlatam.com/r/B67',
            'CL' => 'https://erp.onescreenlatam.com/r/B67',
            'CR' => 'https://erp.onescreenlatam.com/r/B67',
            'CU' => 'https://erp.onescreenlatam.com/r/B67',
            'DO' => 'https://erp.onescreenlatam.com/r/B67',
            'SV' => 'https://erp.onescreenlatam.com/r/B67',
            'GT' => 'https://erp.onescreenlatam.com/r/B67',
            'HN' => 'https://erp.onescreenlatam.com/r/B67',
            'HT' => 'https://erp.onescreenlatam.com/r/B67',
            'NI' => 'https://erp.onescreenlatam.com/r/B67',
            'PA' => 'https://erp.onescreenlatam.com/r/B67',
            'PY' => 'https://erp.onescreenlatam.com/r/B67',
            'UY' => 'https://erp.onescreenlatam.com/r/B67',
            'VE' => 'https://erp.onescreenlatam.com/r/B67',
            'BZ' => 'https://erp.onescreenlatam.com/r/B67',
            'GY' => 'https://erp.onescreenlatam.com/r/B67',
            'SR' => 'https://erp.onescreenlatam.com/r/B67',
            'JM' => 'https://erp.onescreenlatam.com/r/B67',
            'TT' => 'https://erp.onescreenlatam.com/r/B67',
            'BS' => 'https://erp.onescreenlatam.com/r/B67',
            'BB' => 'https://erp.onescreenlatam.com/r/B67',
            'GD' => 'https://erp.onescreenlatam.com/r/B67',
            'DM' => 'https://erp.onescreenlatam.com/r/B67',
            'LC' => 'https://erp.onescreenlatam.com/r/B67',
            'VC' => 'https://erp.onescreenlatam.com/r/B67',
            'KN' => 'https://erp.onescreenlatam.com/r/B67',
            'AG' => 'https://erp.onescreenlatam.com/r/B67',
            'PR' => 'https://erp.onescreenlatam.com/r/B67'
        ),

        '98-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/CZN',
            'EC' => 'https://claryicon.odoo.com/r/cro',
            'PE' => 'https://erp.onescreenlatam.com/r/D6S',
            'MX' => 'https://erp.onescreenlatam.com/r/AL3',

            'AR' => 'https://erp.onescreenlatam.com/r/gqz',
            'BO' => 'https://erp.onescreenlatam.com/r/gqz',
            'BR' => 'https://erp.onescreenlatam.com/r/gqz',
            'CL' => 'https://erp.onescreenlatam.com/r/gqz',
            'CR' => 'https://erp.onescreenlatam.com/r/gqz',
            'CU' => 'https://erp.onescreenlatam.com/r/gqz',
            'DO' => 'https://erp.onescreenlatam.com/r/gqz',
            'SV' => 'https://erp.onescreenlatam.com/r/gqz',
            'GT' => 'https://erp.onescreenlatam.com/r/gqz',
            'HN' => 'https://erp.onescreenlatam.com/r/gqz',
            'HT' => 'https://erp.onescreenlatam.com/r/gqz',
            'NI' => 'https://erp.onescreenlatam.com/r/gqz',
            'PA' => 'https://erp.onescreenlatam.com/r/gqz',
            'PY' => 'https://erp.onescreenlatam.com/r/gqz',
            'UY' => 'https://erp.onescreenlatam.com/r/gqz',
            'VE' => 'https://erp.onescreenlatam.com/r/gqz',
            'BZ' => 'https://erp.onescreenlatam.com/r/gqz',
            'GY' => 'https://erp.onescreenlatam.com/r/gqz',
            'SR' => 'https://erp.onescreenlatam.com/r/gqz',
            'JM' => 'https://erp.onescreenlatam.com/r/gqz',
            'TT' => 'https://erp.onescreenlatam.com/r/gqz',
            'BS' => 'https://erp.onescreenlatam.com/r/gqz',
            'BB' => 'https://erp.onescreenlatam.com/r/gqz',
            'GD' => 'https://erp.onescreenlatam.com/r/gqz',
            'DM' => 'https://erp.onescreenlatam.com/r/gqz',
            'LC' => 'https://erp.onescreenlatam.com/r/gqz',
            'VC' => 'https://erp.onescreenlatam.com/r/gqz',
            'KN' => 'https://erp.onescreenlatam.com/r/gqz',
            'AG' => 'https://erp.onescreenlatam.com/r/gqz',
            'PR' => 'https://erp.onescreenlatam.com/r/gqz'
        ),

        'core' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/YpV',
            'EC' => 'https://claryicon.odoo.com/r/kKt',
            'PE' => 'https://erp.onescreenlatam.com/r/7nE',
            'MX' => 'https://erp.onescreenlatam.com/r/0Rx',

            'AR' => 'https://erp.onescreenlatam.com/r/UMf',
            'BO' => 'https://erp.onescreenlatam.com/r/UMf',
            'BR' => 'https://erp.onescreenlatam.com/r/UMf',
            'CL' => 'https://erp.onescreenlatam.com/r/UMf',
            'CR' => 'https://erp.onescreenlatam.com/r/UMf',
            'CU' => 'https://erp.onescreenlatam.com/r/UMf',
            'DO' => 'https://erp.onescreenlatam.com/r/UMf',
            'SV' => 'https://erp.onescreenlatam.com/r/UMf',
            'GT' => 'https://erp.onescreenlatam.com/r/UMf',
            'HN' => 'https://erp.onescreenlatam.com/r/UMf',
            'HT' => 'https://erp.onescreenlatam.com/r/UMf',
            'NI' => 'https://erp.onescreenlatam.com/r/UMf',
            'PA' => 'https://erp.onescreenlatam.com/r/UMf',
            'PY' => 'https://erp.onescreenlatam.com/r/UMf',
            'UY' => 'https://erp.onescreenlatam.com/r/UMf',
            'VE' => 'https://erp.onescreenlatam.com/r/UMf',
            'BZ' => 'https://erp.onescreenlatam.com/r/UMf',
            'GY' => 'https://erp.onescreenlatam.com/r/UMf',
            'SR' => 'https://erp.onescreenlatam.com/r/UMf',
            'JM' => 'https://erp.onescreenlatam.com/r/UMf',
            'TT' => 'https://erp.onescreenlatam.com/r/UMf',
            'BS' => 'https://erp.onescreenlatam.com/r/UMf',
            'BB' => 'https://erp.onescreenlatam.com/r/UMf',
            'GD' => 'https://erp.onescreenlatam.com/r/UMf',
            'DM' => 'https://erp.onescreenlatam.com/r/UMf',
            'LC' => 'https://erp.onescreenlatam.com/r/UMf',
            'VC' => 'https://erp.onescreenlatam.com/r/UMf',
            'KN' => 'https://erp.onescreenlatam.com/r/UMf',
            'AG' => 'https://erp.onescreenlatam.com/r/UMf',
            'PR' => 'https://erp.onescreenlatam.com/r/UMf'
        ),

        't7' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/7zV',
            'EC' => 'https://claryicon.odoo.com/r/qP9',
            'PE' => 'https://erp.onescreenlatam.com/r/av1',
            'MX' => 'https://erp.onescreenlatam.com/r/tgS',

            'AR' => 'https://erp.onescreenlatam.com/r/0hJ',
            'BO' => 'https://erp.onescreenlatam.com/r/0hJ',
            'BR' => 'https://erp.onescreenlatam.com/r/0hJ',
            'CL' => 'https://erp.onescreenlatam.com/r/0hJ',
            'CR' => 'https://erp.onescreenlatam.com/r/0hJ',
            'CU' => 'https://erp.onescreenlatam.com/r/0hJ',
            'DO' => 'https://erp.onescreenlatam.com/r/0hJ',
            'SV' => 'https://erp.onescreenlatam.com/r/0hJ',
            'GT' => 'https://erp.onescreenlatam.com/r/0hJ',
            'HN' => 'https://erp.onescreenlatam.com/r/0hJ',
            'HT' => 'https://erp.onescreenlatam.com/r/0hJ',
            'NI' => 'https://erp.onescreenlatam.com/r/0hJ',
            'PA' => 'https://erp.onescreenlatam.com/r/0hJ',
            'PY' => 'https://erp.onescreenlatam.com/r/0hJ',
            'UY' => 'https://erp.onescreenlatam.com/r/0hJ',
            'VE' => 'https://erp.onescreenlatam.com/r/0hJ',
            'BZ' => 'https://erp.onescreenlatam.com/r/0hJ',
            'GY' => 'https://erp.onescreenlatam.com/r/0hJ',
            'SR' => 'https://erp.onescreenlatam.com/r/0hJ',
            'JM' => 'https://erp.onescreenlatam.com/r/0hJ',
            'TT' => 'https://erp.onescreenlatam.com/r/0hJ',
            'BS' => 'https://erp.onescreenlatam.com/r/0hJ',
            'BB' => 'https://erp.onescreenlatam.com/r/0hJ',
            'GD' => 'https://erp.onescreenlatam.com/r/0hJ',
            'DM' => 'https://erp.onescreenlatam.com/r/0hJ',
            'LC' => 'https://erp.onescreenlatam.com/r/0hJ',
            'VC' => 'https://erp.onescreenlatam.com/r/0hJ',
            'KN' => 'https://erp.onescreenlatam.com/r/0hJ',
            'AG' => 'https://erp.onescreenlatam.com/r/0hJ',
            'PR' => 'https://erp.onescreenlatam.com/r/0hJ'
        ),

        'titan' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/YZ2',
            'EC' => 'https://claryicon.odoo.com/r/jbs',
            'PE' => 'https://erp.onescreenlatam.com/r/Tza',
            'MX' => 'https://erp.onescreenlatam.com/r/iDg',

            'AR' => 'https://erp.onescreenlatam.com/r/eLc',
            'BO' => 'https://erp.onescreenlatam.com/r/eLc',
            'BR' => 'https://erp.onescreenlatam.com/r/eLc',
            'CL' => 'https://erp.onescreenlatam.com/r/eLc',
            'CR' => 'https://erp.onescreenlatam.com/r/eLc',
            'CU' => 'https://erp.onescreenlatam.com/r/eLc',
            'DO' => 'https://erp.onescreenlatam.com/r/eLc',
            'SV' => 'https://erp.onescreenlatam.com/r/eLc',
            'GT' => 'https://erp.onescreenlatam.com/r/eLc',
            'HN' => 'https://erp.onescreenlatam.com/r/eLc',
            'HT' => 'https://erp.onescreenlatam.com/r/eLc',
            'NI' => 'https://erp.onescreenlatam.com/r/eLc',
            'PA' => 'https://erp.onescreenlatam.com/r/eLc',
            'PY' => 'https://erp.onescreenlatam.com/r/eLc',
            'UY' => 'https://erp.onescreenlatam.com/r/eLc',
            'VE' => 'https://erp.onescreenlatam.com/r/eLc',
            'BZ' => 'https://erp.onescreenlatam.com/r/eLc',
            'GY' => 'https://erp.onescreenlatam.com/r/eLc',
            'SR' => 'https://erp.onescreenlatam.com/r/eLc',
            'JM' => 'https://erp.onescreenlatam.com/r/eLc',
            'TT' => 'https://erp.onescreenlatam.com/r/eLc',
            'BS' => 'https://erp.onescreenlatam.com/r/eLc',
            'BB' => 'https://erp.onescreenlatam.com/r/eLc',
            'GD' => 'https://erp.onescreenlatam.com/r/eLc',
            'DM' => 'https://erp.onescreenlatam.com/r/eLc',
            'LC' => 'https://erp.onescreenlatam.com/r/eLc',
            'VC' => 'https://erp.onescreenlatam.com/r/eLc',
            'KN' => 'https://erp.onescreenlatam.com/r/eLc',
            'AG' => 'https://erp.onescreenlatam.com/r/eLc',
            'PR' => 'https://erp.onescreenlatam.com/r/eLc'
        ),

        'wandr' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/xA3',
            'EC' => 'https://claryicon.odoo.com/r/fd4',
            'PE' => 'https://erp.onescreenlatam.com/r/2kr',
            'MX' => 'https://erp.onescreenlatam.com/r/YRA',

            'AR' => 'https://erp.onescreenlatam.com/r/bSU',
            'BO' => 'https://erp.onescreenlatam.com/r/bSU',
            'BR' => 'https://erp.onescreenlatam.com/r/bSU',
            'CL' => 'https://erp.onescreenlatam.com/r/bSU',
            'CR' => 'https://erp.onescreenlatam.com/r/bSU',
            'CU' => 'https://erp.onescreenlatam.com/r/bSU',
            'DO' => 'https://erp.onescreenlatam.com/r/bSU',
            'SV' => 'https://erp.onescreenlatam.com/r/bSU',
            'GT' => 'https://erp.onescreenlatam.com/r/bSU',
            'HN' => 'https://erp.onescreenlatam.com/r/bSU',
            'HT' => 'https://erp.onescreenlatam.com/r/bSU',
            'NI' => 'https://erp.onescreenlatam.com/r/bSU',
            'PA' => 'https://erp.onescreenlatam.com/r/bSU',
            'PY' => 'https://erp.onescreenlatam.com/r/bSU',
            'UY' => 'https://erp.onescreenlatam.com/r/bSU',
            'VE' => 'https://erp.onescreenlatam.com/r/bSU',
            'BZ' => 'https://erp.onescreenlatam.com/r/bSU',
            'GY' => 'https://erp.onescreenlatam.com/r/bSU',
            'SR' => 'https://erp.onescreenlatam.com/r/bSU',
            'JM' => 'https://erp.onescreenlatam.com/r/bSU',
            'TT' => 'https://erp.onescreenlatam.com/r/bSU',
            'BS' => 'https://erp.onescreenlatam.com/r/bSU',
            'BB' => 'https://erp.onescreenlatam.com/r/bSU',
            'GD' => 'https://erp.onescreenlatam.com/r/bSU',
            'DM' => 'https://erp.onescreenlatam.com/r/bSU',
            'LC' => 'https://erp.onescreenlatam.com/r/bSU',
            'VC' => 'https://erp.onescreenlatam.com/r/bSU',
            'KN' => 'https://erp.onescreenlatam.com/r/bSU',
            'AG' => 'https://erp.onescreenlatam.com/r/bSU',
            'PR' => 'https://erp.onescreenlatam.com/r/bSU'
        ),

        'clientes' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/ZmR',
            'EC' => 'https://claryicon.odoo.com/r/vHG',
            'PE' => 'https://erp.onescreenlatam.com/r/QhK',
            'MX' => 'https://erp.onescreenlatam.com/r/VSN',

            'AR' => 'https://erp.onescreenlatam.com/r/KqX',
            'BO' => 'https://erp.onescreenlatam.com/r/KqX',
            'BR' => 'https://erp.onescreenlatam.com/r/KqX',
            'CL' => 'https://erp.onescreenlatam.com/r/KqX',
            'CR' => 'https://erp.onescreenlatam.com/r/KqX',
            'CU' => 'https://erp.onescreenlatam.com/r/KqX',
            'DO' => 'https://erp.onescreenlatam.com/r/KqX',
            'SV' => 'https://erp.onescreenlatam.com/r/KqX',
            'GT' => 'https://erp.onescreenlatam.com/r/KqX',
            'HN' => 'https://erp.onescreenlatam.com/r/KqX',
            'HT' => 'https://erp.onescreenlatam.com/r/KqX',
            'NI' => 'https://erp.onescreenlatam.com/r/KqX',
            'PA' => 'https://erp.onescreenlatam.com/r/KqX',
            'PY' => 'https://erp.onescreenlatam.com/r/KqX',
            'UY' => 'https://erp.onescreenlatam.com/r/KqX',
            'VE' => 'https://erp.onescreenlatam.com/r/KqX',
            'BZ' => 'https://erp.onescreenlatam.com/r/KqX',
            'GY' => 'https://erp.onescreenlatam.com/r/KqX',
            'SR' => 'https://erp.onescreenlatam.com/r/KqX',
            'JM' => 'https://erp.onescreenlatam.com/r/KqX',
            'TT' => 'https://erp.onescreenlatam.com/r/KqX',
            'BS' => 'https://erp.onescreenlatam.com/r/KqX',
            'BB' => 'https://erp.onescreenlatam.com/r/KqX',
            'GD' => 'https://erp.onescreenlatam.com/r/KqX',
            'DM' => 'https://erp.onescreenlatam.com/r/KqX',
            'LC' => 'https://erp.onescreenlatam.com/r/KqX',
            'VC' => 'https://erp.onescreenlatam.com/r/KqX',
            'KN' => 'https://erp.onescreenlatam.com/r/KqX',
            'AG' => 'https://erp.onescreenlatam.com/r/KqX',
            'PR' => 'https://erp.onescreenlatam.com/r/KqX'
        ),

        'distribuidores' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/SZJ',
            'EC' => 'https://claryicon.odoo.com/r/Qjy',
            'PE' => 'https://erp.onescreenlatam.com/r/Ip7',
            'MX' => 'https://erp.onescreenlatam.com/r/GtP',

            'AR' => 'https://erp.onescreenlatam.com/r/riJ',
            'BO' => 'https://erp.onescreenlatam.com/r/riJ',
            'BR' => 'https://erp.onescreenlatam.com/r/riJ',
            'CL' => 'https://erp.onescreenlatam.com/r/riJ',
            'CR' => 'https://erp.onescreenlatam.com/r/riJ',
            'CU' => 'https://erp.onescreenlatam.com/r/riJ',
            'DO' => 'https://erp.onescreenlatam.com/r/riJ',
            'SV' => 'https://erp.onescreenlatam.com/r/riJ',
            'GT' => 'https://erp.onescreenlatam.com/r/riJ',
            'HN' => 'https://erp.onescreenlatam.com/r/riJ',
            'HT' => 'https://erp.onescreenlatam.com/r/riJ',
            'NI' => 'https://erp.onescreenlatam.com/r/riJ',
            'PA' => 'https://erp.onescreenlatam.com/r/riJ',
            'PY' => 'https://erp.onescreenlatam.com/r/riJ',
            'UY' => 'https://erp.onescreenlatam.com/r/riJ',
            'VE' => 'https://erp.onescreenlatam.com/r/riJ',
            'BZ' => 'https://erp.onescreenlatam.com/r/riJ',
            'GY' => 'https://erp.onescreenlatam.com/r/riJ',
            'SR' => 'https://erp.onescreenlatam.com/r/riJ',
            'JM' => 'https://erp.onescreenlatam.com/r/riJ',
            'TT' => 'https://erp.onescreenlatam.com/r/riJ',
            'BS' => 'https://erp.onescreenlatam.com/r/riJ',
            'BB' => 'https://erp.onescreenlatam.com/r/riJ',
            'GD' => 'https://erp.onescreenlatam.com/r/riJ',
            'DM' => 'https://erp.onescreenlatam.com/r/riJ',
            'LC' => 'https://erp.onescreenlatam.com/r/riJ',
            'VC' => 'https://erp.onescreenlatam.com/r/riJ',
            'KN' => 'https://erp.onescreenlatam.com/r/riJ',
            'AG' => 'https://erp.onescreenlatam.com/r/riJ',
            'PR' => 'https://erp.onescreenlatam.com/r/riJ'
        ),

        'rental' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/l2Z',
            'EC' => 'https://claryicon.odoo.com/r/ywT',
            'PE' => 'https://erp.onescreenlatam.com/r/EIL',
            'MX' => 'https://erp.onescreenlatam.com/r/pws',

            'AR' => 'https://erp.onescreenlatam.com/r/2RK',
            'BO' => 'https://erp.onescreenlatam.com/r/2RK',
            'BR' => 'https://erp.onescreenlatam.com/r/2RK',
            'CL' => 'https://erp.onescreenlatam.com/r/2RK',
            'CR' => 'https://erp.onescreenlatam.com/r/2RK',
            'CU' => 'https://erp.onescreenlatam.com/r/2RK',
            'DO' => 'https://erp.onescreenlatam.com/r/2RK',
            'SV' => 'https://erp.onescreenlatam.com/r/2RK',
            'GT' => 'https://erp.onescreenlatam.com/r/2RK',
            'HN' => 'https://erp.onescreenlatam.com/r/2RK',
            'HT' => 'https://erp.onescreenlatam.com/r/2RK',
            'NI' => 'https://erp.onescreenlatam.com/r/2RK',
            'PA' => 'https://erp.onescreenlatam.com/r/2RK',
            'PY' => 'https://erp.onescreenlatam.com/r/2RK',
            'UY' => 'https://erp.onescreenlatam.com/r/2RK',
            'VE' => 'https://erp.onescreenlatam.com/r/2RK',
            'BZ' => 'https://erp.onescreenlatam.com/r/2RK',
            'GY' => 'https://erp.onescreenlatam.com/r/2RK',
            'SR' => 'https://erp.onescreenlatam.com/r/2RK',
            'JM' => 'https://erp.onescreenlatam.com/r/2RK',
            'TT' => 'https://erp.onescreenlatam.com/r/2RK',
            'BS' => 'https://erp.onescreenlatam.com/r/2RK',
            'BB' => 'https://erp.onescreenlatam.com/r/2RK',
            'GD' => 'https://erp.onescreenlatam.com/r/2RK',
            'DM' => 'https://erp.onescreenlatam.com/r/2RK',
            'LC' => 'https://erp.onescreenlatam.com/r/2RK',
            'VC' => 'https://erp.onescreenlatam.com/r/2RK',
            'KN' => 'https://erp.onescreenlatam.com/r/2RK',
            'AG' => 'https://erp.onescreenlatam.com/r/2RK',
            'PR' => 'https://erp.onescreenlatam.com/r/2RK'
        ),

        'recursos' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/Lkg',
            'EC' => 'https://claryicon.odoo.com/r/4Pr',
            'PE' => 'https://erp.onescreenlatam.com/r/Csh',
            'MX' => 'https://erp.onescreenlatam.com/r/2py',

            'AR' => 'https://erp.onescreenlatam.com/r/Fpg',
            'BO' => 'https://erp.onescreenlatam.com/r/Fpg',
            'BR' => 'https://erp.onescreenlatam.com/r/Fpg',
            'CL' => 'https://erp.onescreenlatam.com/r/Fpg',
            'CR' => 'https://erp.onescreenlatam.com/r/Fpg',
            'CU' => 'https://erp.onescreenlatam.com/r/Fpg',
            'DO' => 'https://erp.onescreenlatam.com/r/Fpg',
            'SV' => 'https://erp.onescreenlatam.com/r/Fpg',
            'GT' => 'https://erp.onescreenlatam.com/r/Fpg',
            'HN' => 'https://erp.onescreenlatam.com/r/Fpg',
            'HT' => 'https://erp.onescreenlatam.com/r/Fpg',
            'NI' => 'https://erp.onescreenlatam.com/r/Fpg',
            'PA' => 'https://erp.onescreenlatam.com/r/Fpg',
            'PY' => 'https://erp.onescreenlatam.com/r/Fpg',
            'UY' => 'https://erp.onescreenlatam.com/r/Fpg',
            'VE' => 'https://erp.onescreenlatam.com/r/Fpg',
            'BZ' => 'https://erp.onescreenlatam.com/r/Fpg',
            'GY' => 'https://erp.onescreenlatam.com/r/Fpg',
            'SR' => 'https://erp.onescreenlatam.com/r/Fpg',
            'JM' => 'https://erp.onescreenlatam.com/r/Fpg',
            'TT' => 'https://erp.onescreenlatam.com/r/Fpg',
            'BS' => 'https://erp.onescreenlatam.com/r/Fpg',
            'BB' => 'https://erp.onescreenlatam.com/r/Fpg',
            'GD' => 'https://erp.onescreenlatam.com/r/Fpg',
            'DM' => 'https://erp.onescreenlatam.com/r/Fpg',
            'LC' => 'https://erp.onescreenlatam.com/r/Fpg',
            'VC' => 'https://erp.onescreenlatam.com/r/Fpg',
            'KN' => 'https://erp.onescreenlatam.com/r/Fpg',
            'AG' => 'https://erp.onescreenlatam.com/r/Fpg',
            'PR' => 'https://erp.onescreenlatam.com/r/Fpg'
        ),
    );

    // Obtener los enlaces para la página actual
    if (isset($page_links_config[$page_identifier])) {
        return $page_links_config[$page_identifier];
    }

    // Fallback: usar enlaces de home (/) si la página no está configurada
    return isset($page_links_config['/']) ? $page_links_config['/'] : (isset($page_links_config['aulas-clase']) ? $page_links_config['aulas-clase'] : reset($page_links_config));
}

/**
 * Agregar el script de geolocalización específico por página
 */
function enqueue_page_specific_geolocation_script()
{
    // En estos endpoints NO se carga el script: conservan el comportamiento original de WordPress
    $excluded = get_page_geo_excluded_identifiers();
    $current_identifier = get_current_page_geo_identifier();
    if (in_array($current_identifier, $excluded, true)) {
        global $page_specific_geo_script;
        $page_specific_geo_script = '';
        return;
    }

    // Obtener enlaces según la página actual
    $country_links = get_page_specific_links();

    // Enlace por defecto (Colombia)
    $default_link = isset($country_links['CO']) ? $country_links['CO'] : 'https://erp.onescreenlatam.com/r/lWN';

    // JSON de enlaces por país (para que JS use exactamente los países configurados en PHP)
    $country_links_json = json_encode($country_links, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Guardar configuración para usar en wp_footer
    global $page_specific_geo_script;
    $page_specific_geo_script = "
    (function() {
        'use strict';

        // Configuración de enlaces por país (desde PHP - específico por página)
        const countryLinks = " . $country_links_json . ";

        // Enlace por defecto (Colombia)
        const defaultLink = '" . safe_esc_js($default_link) . "';
        
        // Debug: Mostrar configuración detectada
        console.log('🔍 Geolocalización por Página - Configuración detectada:');
        console.log('📄 URL actual:', window.location.href);
        console.log('📄 Path:', window.location.pathname);
        console.log('🔗 Enlace por defecto:', defaultLink);

        /**
         * Detecta si un enlace es de Odoo, WhatsApp o debe ser interceptado
         */
        function isTrackableLink(href) {
            if (!href) return false;
            const lowerHref = href.toLowerCase();
            // Detectar enlaces de Odoo
            if (lowerHref.includes('erp.onescreenlatam.com') || 
                lowerHref.includes('claryicon.odoo.com')) {
                return true;
            }
            // Detectar enlaces de WhatsApp
            if (lowerHref.includes('wa.me') || 
                lowerHref.includes('whatsapp.com') || 
                lowerHref.includes('api.whatsapp.com') ||
                lowerHref.includes('web.whatsapp.com') ||
                lowerHref.includes('chat.whatsapp.com')) {
                return true;
            }
            return false;
        }

        /**
         * Obtiene el país del usuario usando geolocalización por IP
         */
        async function getUserCountry() {
            // Intento 1: Usar ipapi.co
            try {
                const response1 = await fetch('https://ipapi.co/json/', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                if (response1.ok) {
                    const data = await response1.json();
                    if (data.country_code && !data.error) {
                        return data.country_code;
                    }
                }
            } catch (error) {
                console.log('ipapi.co no disponible, intentando alternativa...');
            }

            // Intento 2: Usar geojs.io
            try {
                const response2 = await fetch('https://get.geojs.io/v1/ip/country.json');
                if (response2.ok) {
                    const data = await response2.json();
                    if (data.country) {
                        return data.country;
                    }
                }
            } catch (error) {
                console.log('geojs.io no disponible, intentando alternativa...');
            }

            // Intento 3: Usar ipwho.is
            try {
                const response3 = await fetch('https://ipwho.is/');
                if (response3.ok) {
                    const data = await response3.json();
                    if (data.country_code && data.success) {
                        return data.country_code;
                    }
                }
            } catch (error) {
                console.log('ipwho.is no disponible, intentando alternativa...');
            }

            // Intento 4: Usar ip-api.io
            try {
                const response4 = await fetch('https://ip-api.io/json/');
                if (response4.ok) {
                    const data = await response4.json();
                    if (data.country_code) {
                        return data.country_code;
                    }
                }
            } catch (error) {
                console.log('ip-api.io no disponible, usando fallback...');
            }

            // Fallback: retornar código por defecto
            return 'CO';
        }

        /**
         * Genera el enlace según el país (Odoo o WhatsApp)
         */
        function getCountryLink(countryCode) {
            const cc = String(countryCode || '').toUpperCase();
            return countryLinks[cc] || defaultLink;
        }

        /**
         * Maneja el clic en el botón
         * (incluyendo compatibilidad con Safari en iPhone y Mac)
         */
        async function handleButtonClick(event) {
            event.preventDefault();

            const button = event.currentTarget;
            const originalText = button.innerHTML;

            // Feedback visual y bloqueo de clics múltiples
            button.innerHTML = 'Cargando...';
            button.disabled = true;

            // ✅ Abrir la pestaña/ventana inmediatamente dentro del gesto de usuario
            // Esto es CRÍTICO para Safari (iOS/macOS) para que no bloquee el popup
            let popup = null;
            try {
                popup = window.open('about:blank', '_blank');
            } catch (e) {
                console.warn('No se pudo abrir popup inmediatamente, se usará la misma pestaña.', e);
            }

            try {
                console.log('🌍 Detectando país del usuario...');
                const countryCode = await getUserCountry();
                console.log('✅ País detectado:', countryCode);

                const redirectUrl = getCountryLink(countryCode);
                console.log('🔗 Redirigiendo a:', redirectUrl);
                console.log('📄 Página actual:', window.location.pathname);

                if (popup && !popup.closed) {
                    // Redirigir en la pestaña que se abrió dentro del gesto de usuario
                    popup.location.href = redirectUrl;
                } else {
                    // Fallback: redirigir en la misma pestaña (por si Safari bloqueó el popup)
                    window.location.href = redirectUrl;
                }
            } catch (error) {
                console.error('Error al obtener geolocalización:', error);
                const fallbackUrl = getCountryLink('CO');

                if (popup && !popup.closed) {
                    popup.location.href = fallbackUrl;
                } else {
                    window.location.href = fallbackUrl;
                }
            } finally {
                // Restaurar el botón
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 800);
            }
        }

        /**
         * Inicializa el script cuando el DOM está listo
         */
        function init() {
            function setupButtons() {
                let buttonsFound = 0;
                
                // Buscar por clase específica
                const buttonByClass = document.querySelector('.whatsapp-geo-btn');
                if (buttonByClass) {
                    buttonByClass.addEventListener('click', handleButtonClick);
                    console.log('✅ Botón encontrado por clase: .whatsapp-geo-btn');
                    buttonsFound++;
                }
                
                // Buscar botón PHP específico por ID
                const phpButton = document.getElementById('php-button');
                if (phpButton) {
                    phpButton.addEventListener('click', handleButtonClick);
                    console.log('✅ Botón PHP configurado - Usa código de WordPress/PHP');
                    buttonsFound++;
                }
                
                // Buscar TODOS los botones de Elementor
                const allElementorButtons = document.querySelectorAll('.elementor-button-link, a.elementor-button, .elementor-button');
                console.log('🔍 Buscando botones de Elementor... Encontrados:', allElementorButtons.length);
                
                allElementorButtons.forEach((button, index) => {
                    const href = button.getAttribute('href') || '';
                    const buttonText = (button.textContent || button.innerText || '').trim();
                    
                    console.log('  Botón ' + (index + 1) + ':', {
                        href: href || '(sin enlace)',
                        texto: buttonText || '(sin texto)',
                        clases: button.className
                    });
                    
                    if (isTrackableLink(href)) {
                        button.addEventListener('click', handleButtonClick);
                        console.log('  ✅ Botón ' + (index + 1) + ' configurado - Enlace detectado:', href);
                        buttonsFound++;
                    }
                });
                
                // Buscar por selectores específicos (Odoo y WhatsApp)
                const trackableSelectors = [
                    '.elementor-button-link[href*=\"erp.onescreenlatam.com\"]',
                    '.elementor-button-link[href*=\"claryicon.odoo.com\"]',
                    '.elementor-button-link[href*=\"wa.me\"]',
                    '.elementor-button-link[href*=\"whatsapp\"]',
                    'a.elementor-button[href*=\"erp.onescreenlatam.com\"]',
                    'a.elementor-button[href*=\"claryicon.odoo.com\"]',
                    'a.elementor-button[href*=\"wa.me\"]',
                    'a.elementor-button[href*=\"whatsapp\"]',
                    '.wp-whatsapp-button'
                ];
                
                trackableSelectors.forEach(selector => {
                    const buttons = document.querySelectorAll(selector);
                    buttons.forEach(button => {
                        const href = button.getAttribute('href') || '';
                        if (isTrackableLink(href) && !button.hasAttribute('data-whatsapp-configured')) {
                            button.addEventListener('click', handleButtonClick);
                            button.setAttribute('data-whatsapp-configured', 'true');
                            console.log('✅ Botón configurado con selector:', selector, '- Enlace:', href);
                            if (!Array.from(allElementorButtons).includes(button)) {
                                buttonsFound++;
                            }
                        }
                    });
                });
                
                if (buttonsFound > 0) {
                    console.log('\\n🎉 Total de botones configurados:', buttonsFound);
                    console.log('💡 El script interceptará los clics y redirigirá según el país del usuario');
                    console.log('📄 Enlaces configurados para esta página:', countryLinks);
                    console.log('\\n');
                } else {
                    console.warn('\\n⚠️ No se encontró ningún botón con enlace de Odoo o WhatsApp.');
                    console.log('💡 Asegúrate de que tu botón tenga un enlace que contenga:');
                    console.log('   - erp.onescreenlatam.com (Odoo)');
                    console.log('   - claryicon.odoo.com (Odoo)');
                    console.log('   - wa.me o whatsapp.com (WhatsApp)\\n');
                }
            }
            
            // Esperar a que Elementor cargue completamente
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    if (typeof elementorFrontend !== 'undefined') {
                        setTimeout(setupButtons, 500);
                    } else {
                        setupButtons();
                    }
                });
            } else {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupButtons);
                } else {
                    setupButtons();
                }
            }
        }

        // Inicializar cuando el script se carga
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    ";
}

/**
 * Output el script en el footer
 */
function output_page_specific_geolocation_script()
{
    global $page_specific_geo_script;
    if (!empty($page_specific_geo_script)) {
        echo '<script type="text/javascript">' . $page_specific_geo_script . '</script>';
    }
}

// Registrar hooks - WordPress debe estar cargado (verificado por ABSPATH)
if (defined('ABSPATH') && function_exists('add_action')) {
    call_user_func('add_action', 'wp_enqueue_scripts', 'enqueue_page_specific_geolocation_script');
    call_user_func('add_action', 'wp_footer', 'output_page_specific_geolocation_script');
}
