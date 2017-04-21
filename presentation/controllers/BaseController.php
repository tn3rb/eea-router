<?php

namespace EspressoRouter\presentation\controllers;

use EE_Request;
use EE_Response;
use EspressoRouter\presentation\views\ViewModelInterface;
use EventEspresso\core\services\container\CoffeeShop;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class BaseController
 * abstract parent class for all controllers
 * loaded by \EspressoRouter\application\services\routers\Router
 * as configured in Route classes.
 * Controller can communicate with the ViewModel and control it's state,
 * but have no access or knowledge of the View or Template being used.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class BaseController
{


    /**
     * @var CoffeeShop $coffee_shop
     */
    protected $coffee_shop;


    /**
     * @var EE_Request $request
     */
    protected $request;


    /**
     * @var EE_Response $response
     */
    protected $response;


    /**
     * @var ViewModelInterface $view_model
     */
    protected $view_model;



    /**
     * BaseController constructor.
     *
     * @param ViewModelInterface $view_model
     * @param CoffeeShop         $coffee_shop
     * @param EE_Request         $request
     * @param EE_Response        $response
     */
    public function __construct(
        ViewModelInterface $view_model,
        CoffeeShop $coffee_shop,
        EE_Request $request,
        EE_Response $response
    ) {
        $this->view_model = $view_model;
        $this->coffee_shop = $coffee_shop;
        $this->request = $request;
        $this->response = $response;
    }



    /**
     * @return void
     */
    abstract public function execute();

}
// End of file BaseController.php
// Location: presentation/controllers/BaseController.php