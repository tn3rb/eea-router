<?php

namespace EspressoRouter\application\entities\routes;

use EspressoRouter\application\services\routers\RouteConfig;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class ExampleRouteConfig
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class ExampleRouteConfig extends RouteConfig
{

    public function addRoutes()
    {
        // try  ?ee_route=event_thumbnail&event_id=59
        // or   /events/*/?ee_route=event_thumbnail
        $this->router->addRoute(
            Route::forGetRequest(
                'event_thumbnail',
                '\EspressoRouter\presentation\controllers\events\DisplayEvent',
                '\EspressoRouter\presentation\views\events\EventThumbnail'//,
            // 'execute',
            // array(),
            // 'AHEE__Router__before_the_content',
            // '/events/' // '/events/'
            )
        );
        // try  ?ee_route=venue_details&venue_id=61
        // or   /venues/*/?ee_route=venue_details
        $this->router->addRoute(
            Route::forGetRequest(
                'venue_details',
                '\EspressoRouter\presentation\controllers\venues\DisplayVenue',
                '\EspressoRouter\presentation\views\venues\VenueView'//,
            // 'execute',
            // array(),
            // 'AHEE__Router__before_the_content',
            // '/'  // '/venues/'
            )
        );
    }

}
// End of file ExampleRouteConfig.php
// Location: EspressoRouter\application\entities\routes/ExampleRouteConfig.php