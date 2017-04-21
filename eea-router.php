<?php
/*
  Plugin Name: Espresso Router
  Plugin URI: https://eventespresso.com
  Description: Espresso Router - is a configuration based router for WordPress plugins, that connects
Controllers with Views, using a completely decoupled system that promotes reusability.
  Version: 1.0.1.rc.002
  Author: Event Espresso
  Author URI: https://eventespresso.com
  Copyright 2015 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
 *
 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Event Espresso
 * @ copyright	(c) 2008-2015 Event Espresso  All Rights Reserved.
 * @ license		https://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link 			https://eventespresso.com
 * @ version	 	EE4
 *
 * ------------------------------------------------------------------------
 */
use EspressoRouter\application\entities\routes\Route;
use EventEspresso\core\exceptions\ExceptionStackTraceDisplay;

define('EE_ROUTER_BASE_PATH', plugin_dir_path(__FILE__));
add_action(
    'AHEE__EE_System__construct__begin',
    function() {
        EE_Psr4AutoloaderInit::psr4_loader()->addNamespace('EspressoRouter', EE_ROUTER_BASE_PATH);
    }
);
add_action(
    'AHEE__EE_System__load_controllers__start',
    function() {
        global $CoffeeShop;
        $router = new EspressoRouter\application\services\routers\Router(
            $CoffeeShop,
            EE_Registry::instance()->load_core('EE_Request')
        );
        // try  ?ee_route=event_details&event_id=59
        // or   /events/*/?ee_route=event_details
        $router->addRoute(
            Route::getRoute(
                'event_details',
                '\EspressoRouter\presentation\controllers\events\DisplayEvent',
                '\EspressoRouter\presentation\views\events\EventThumbnail',
                'execute',
                array(),
                'AHEE__Router__before_the_content',
                '/events/' // '/events/'
            )
        );
        // try  ?ee_route=venue_details&venue_id=61
        // or   /venues/*/?ee_route=venue_details
        $router->addRoute(
            Route::getRoute(
                'venue_details',
                '\EspressoRouter\presentation\controllers\venues\DisplayVenue',
                '\EspressoRouter\presentation\views\venues\VenueView',
                'execute',
                array(),
                'AHEE__Router__before_the_content',
                '/'  // '/venues/'
            )
        );
        try {
            $router->dispatch();
        } catch (Exception $exception) {
            new ExceptionStackTraceDisplay($exception);
        }
    }
);

add_filter(
    'the_content',
    function($the_content){
        if (has_action('AHEE__Router__before_the_content')){
            ob_start();
            do_action('AHEE__Router__before_the_content');
            return ob_get_clean() . $the_content;
        }
        return $the_content;
    },
    999
);

add_filter(
    'the_content',
    function($the_content){
        if (has_action('AHEE__Router__replace_the_content')) {
            ob_start();
            do_action('AHEE__Router__replace_the_content');
            return ob_get_clean();
        }
        return $the_content;
    },
    999
);

add_filter(
    'the_content',
    function($the_content){
        if (has_action('AHEE__Router__after_the_content')) {
            ob_start();
            do_action('AHEE__Router__after_the_content');
            return $the_content . ob_get_clean();
        }
        return $the_content;
    },
    999
);


// End of file: eea-router.php
// Location: eea-router.php