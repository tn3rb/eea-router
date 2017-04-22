<?php

namespace EspressoRouter\application\services\routers;


defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class RouteConfig
 * abstract parent class for configuring routes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class RouteConfig
{

    /**
     * @var Router $router
     */
    protected $router;



    /**
     * RouteConfig constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }



    abstract public function addRoutes();

}
// End of file RouteConfig.php
// Location: /application/services/routers/RouteConfig.php