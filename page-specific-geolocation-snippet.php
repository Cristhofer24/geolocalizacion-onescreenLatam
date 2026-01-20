<?php
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
        // ---- SOLO EDITA ESTO: slugs/endpoint que quieres detectar ----
        // Key = identificador; Value = lista de slugs que deben matchear en la URL
        $endpoint_map = array(
            'aulas-clase' => array('aulas-clase'),
            'empresas'    => array('empresas'),
            'gobierno'    => array('gobierno'),
            'retail'      => array('retail'),
            'software'    => array('software'),
            'pantalla-led'   => array('pantalla-led'),
            '32-pulgadas'    => array('32-pulgadas'),
            '65-pulgadas'    => array('65-pulgadas'),
            '75-pulgadas'    => array('75-pulgadas'),
            '86-pulgadas'    => array('86-pulgadas'),
            '98-pulgadas'    => array('98-pulgadas'),
            'core'    => array('core'),
            't7'    => array('t7'),
            'titan'    => array('titan'),
            'wandr'    => array('wandr'),
            'clientes' => array('clientes'),
            'distribuidores' => array('distribuidores'),
            'rental' => array('rental'),
            'recursos' => array('recursos'),
        );

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

    // Configuración de enlaces por página y país
    $page_links_config = array(
        '/' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/lWN',
            'EC' => 'https://claryicon.odoo.com/r/kGO'
        ),
        'aulas-clase' => array(
            'CO' => 'https://claryicon.odoo.com/r/6qo',
            'EC' => 'https://claryicon.odoo.com/r/l7s'
        ),
        'empresas' => array(
            'CO' => 'https://claryicon.odoo.com/r/Xtv',
            'EC' => 'https://claryicon.odoo.com/r/gSf'
        ),
        'gobierno' => array(
            'CO' => 'https://claryicon.odoo.com/r/SmC',
            'EC' => 'https://claryicon.odoo.com/r/Ua7'
        ),
        'retail' => array(
            'CO' => 'https://claryicon.odoo.com/r/Z4o',
            'EC' => 'https://claryicon.odoo.com/r/5ux'
        ),
        'software' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/qQe',
            'EC' => 'https://claryicon.odoo.com/r/Hxe'
        ),
        'pantalla-led' => array(
            'CO' => 'https://claryicon.odoo.com/r/Waw',
            'EC' => 'https://claryicon.odoo.com/r/ozB'
        ),
        '32-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/uLb',
            'EC' => 'https://claryicon.odoo.com/r/XtX'
        ),
        '65-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/CHj',
            'EC' => 'https://claryicon.odoo.com/r/uVB'
        ),
        '75-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/k5B',
            'EC' => 'https://claryicon.odoo.com/r/tgX'
        ),
        '86-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/c8b',
            'EC' => 'https://claryicon.odoo.com/r/qxC'
        ),
        '98-pulgadas' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/CZN',
            'EC' => 'https://claryicon.odoo.com/r/cro'
        ),
        'core' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/YpV',
            'EC' => 'https://claryicon.odoo.com/r/kKt'
        ),
        't7' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/7zV',
            'EC' => 'https://claryicon.odoo.com/r/qP9'
        ),
        'titan' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/YZ2',
            'EC' => 'https://claryicon.odoo.com/r/jbs'
        ),
        'wandr' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/xA3',
            'EC' => 'https://claryicon.odoo.com/r/fd4'
        ),
        'clientes' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/ZmR',
            'EC' => 'https://claryicon.odoo.com/r/vHG'
        ),
        'distribuidores' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/SZJ',
            'EC' => 'https://claryicon.odoo.com/r/Qjy'
        ),
        'rental' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/l2Z',
            'EC' => 'https://claryicon.odoo.com/r/ywT'
        ),
        'recursos' => array(
            'CO' => 'https://erp.onescreenlatam.com/r/Lkg',
            'EC' => 'https://claryicon.odoo.com/r/4Pr'
        )
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
    // Obtener enlaces según la página actual
    $country_links = get_page_specific_links();

    // Enlace por defecto (Colombia)
    $default_link = isset($country_links['CO']) ? $country_links['CO'] : 'https://erp.onescreenlatam.com/r/lWN';

    // Evitar "Undefined index" si falta un país en la configuración
    $co_link = isset($country_links['CO']) ? $country_links['CO'] : $default_link;
    $ec_link = isset($country_links['EC']) ? $country_links['EC'] : $default_link;

    // Guardar configuración para usar en wp_footer
    global $page_specific_geo_script;
    $page_specific_geo_script = "
    (function() {
        'use strict';

        // Configuración de enlaces por país (desde PHP - específico por página)
        const countryLinks = {
            'CO': '" . safe_esc_js($co_link) . "',
            'EC': '" . safe_esc_js($ec_link) . "'
        };

        // Enlace por defecto (Colombia)
        const defaultLink = '" . safe_esc_js($default_link) . "';
        
        // Debug: Mostrar configuración detectada
        console.log('🔍 Geolocalización por Página - Configuración detectada:');
        console.log('📄 URL actual:', window.location.href);
        console.log('📄 Path:', window.location.pathname);
        console.log('🔗 Enlaces configurados para esta página:');
        console.log('   CO:', countryLinks['CO']);
        console.log('   EC:', countryLinks['EC']);
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
                    headers: {
                        'Accept': 'application/json'
                    }
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
            return countryLinks[countryCode] || defaultLink;
        }

        /**
         * Maneja el clic en el botón
         */
        async function handleButtonClick(event) {
            event.preventDefault();
            
            const button = event.currentTarget;
            const originalText = button.innerHTML;
            button.innerHTML = 'Cargando...';
            button.disabled = true;

            try {
                console.log('🌍 Detectando país del usuario...');
                const countryCode = await getUserCountry();
                console.log(`✅ País detectado: ` + countryCode);
                
                const redirectUrl = getCountryLink(countryCode);
                console.log(`🔗 Redirigiendo a: ` + redirectUrl);
                console.log(`📄 Página actual: ` + window.location.pathname);
                
                window.open(redirectUrl, '_blank');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 1000);
                
            } catch (error) {
                console.error('Error al obtener geolocalización:', error);
                const defaultUrl = getCountryLink('CO');
                window.open(defaultUrl, '_blank');
                
                button.innerHTML = originalText;
                button.disabled = false;
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
                console.log(`🔍 Buscando botones de Elementor... Encontrados: ` + allElementorButtons.length);
                
                allElementorButtons.forEach((button, index) => {
                    const href = button.getAttribute('href') || '';
                    const buttonText = button.textContent.trim() || button.innerText.trim();
                    
                    console.log(`  Botón ` + (index + 1) + `:`, {
                        href: href || '(sin enlace)',
                        texto: buttonText || '(sin texto)',
                        clases: button.className
                    });
                    
                    if (isTrackableLink(href)) {
                        button.addEventListener('click', handleButtonClick);
                        console.log(`  ✅ Botón ` + (index + 1) + ` configurado - Enlace detectado: ` + href);
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
                            console.log(`✅ Botón configurado con selector: ` + selector + ` - Enlace: ` + href);
                            if (!Array.from(allElementorButtons).includes(button)) {
                                buttonsFound++;
                            }
                        }
                    });
                });
                
                if (buttonsFound > 0) {
                    console.log(`\\n🎉 Total de botones configurados: ` + buttonsFound);
                    console.log('💡 El script interceptará los clics y redirigirá según el país del usuario');
                    console.log('📄 Enlaces configurados para esta página:\\n');
                    console.log('   CO:', countryLinks['CO']);
                    console.log('   EC:', countryLinks['EC']);
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
