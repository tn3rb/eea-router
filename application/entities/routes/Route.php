<?php

namespace EspressoRouter\application\entities\routes;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class Route
 * Primarily a DTO (Data Transfer Object) for configuring routes used by the Router
 *
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Route
{
    // URL parameter used to designate a route
    const URL_PARAMETER = 'ee_route';

    // the following will get moved to EE_Request (or it's equivalent)
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    // default constructor parameters
    const DEFAULT_HOOK = 'loop_start';
    const DEFAULT_METHOD = 'execute';
    const DEFAULT_BASE_PATH = '/';

    /**
     * Name of the WP action hook to use for loading the BaseController
     * defaults to "AHEE__EE_System__load_controllers__start"
     * which runs on the WP "init" do_action at priority 7
     *
     * @var string $action
     */
    private $action;

    /**
     * The portion of the request URI between the domain and URL parameters
     * ex: "/registration-checkout/"
     * defaults to "/"
     *
     * @var string $base_path
     */
    private $base_path;

    /**
     * FQCN of the BaseController class to load when this route is dispatched,
     * ex: "EventEspresso\domain\services\modules\ticket_selector\ProcessTicketSelections"
     *
     * @var string $controller
     */
    private $controller;


    /**
     * A unique identifier for this route, ex: process_ticket_selections
     *
     * @var string $identifier
     */
    private $identifier;

    /**
     * Name of the method on the BaseController class to call when the Route is executed
     * defaults to "execute"
     *
     * @var string $method
     */
    private $method;

    /**
     * array of parameters to be passed to the BaseController method called when the Route is executed,
     * ex: array( $parameter1, $parameter2, $parameter3 )
     * type hinted class dependencies can be omitted as they will be injected by the DI container
     * defaults to an empty array.
     * These are intended for configuration purposes as opposed to request data
     * which will automatically get added to the ViewModel
     *
     * @var array $parameters
     */
    private $parameters;

    /**
     * One of the EE_Request::METHOD_* class constants (temporarily added to this class)
     * defaults to EE_Request::METHOD_GET
     *
     * @var string $type
     */
    private $type;

    /**
     * A reverse engineered URL built using the list of parameters passed to the generateUrl() method
     *
     * @var string $url
     */
    private $url = '';

    /**
     * FQCN of the \EspressoRouter\presentation\views\View class to load when this route is dispatched,
     * ex: "EventEspresso\domain\entities\views\ticket_selector\TicketDetails"
     *
     * @var string $view
     */
    private $view;



    /**
     * Route constructor.
     *
     * @param string $identifier
     * @param string $controller
     * @param string $view
     * @param string $method
     * @param string $action
     * @param string $base_path
     * @param array  $parameters
     * @param string $type
     */
    private function __construct(
        $identifier,
        $controller,
        $view,
        $method = Route::DEFAULT_METHOD,
        $action = Route::DEFAULT_HOOK,
        $base_path = Route::DEFAULT_BASE_PATH,
        $parameters = array(),
        $type = Route::METHOD_GET
    ) {
        $this->action = $action;
        $this->base_path = $base_path;
        $this->controller = $controller;
        $this->identifier = $identifier;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->type = $type;
        $this->view = $view;
    }



    /**
     * @param string $identifier
     * @param string $controller
     * @param string $view
     * @param string $method
     * @param string $action
     * @param string $base_path
     * @param array  $parameters
     * @return Route
     */
    public static function forGetRequest(
        $identifier,
        $controller,
        $view,
        $method = Route::DEFAULT_METHOD,
        $action = Route::DEFAULT_HOOK,
        $base_path = Route::DEFAULT_BASE_PATH,
        $parameters = array()
    ) {
        return new Route(
            $identifier,
            $controller,
            $view,
            $method,
            $action,
            $base_path,
            $parameters,
            Route::METHOD_GET
        );
    }



    /**
     * @param string $identifier
     * @param string $controller
     * @param string $view
     * @param string $method
     * @param string $action
     * @param string $base_path
     * @param array  $parameters
     * @return Route
     */
    public static function forPostRequest(
        $identifier,
        $controller,
        $view,
        $method = Route::DEFAULT_METHOD,
        $action = Route::DEFAULT_HOOK,
        $base_path = Route::DEFAULT_BASE_PATH,
        $parameters = array()
    ) {
        return new Route(
            $identifier,
            $controller,
            $view,
            $method,
            $action,
            $base_path,
            $parameters,
            Route::METHOD_POST
        );
    }



    /**
     * @return string
     */
    public function identifier()
    {
        return $this->identifier;
    }



    /**
     * @return string
     */
    public function basePath()
    {
        return $this->base_path;
    }



    /**
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }



    /**
     * @return string
     */
    public function method()
    {
        return $this->method;
    }



    /**
     * @return array
     */
    public function parameters()
    {
        return $this->parameters;
    }



    /**
     * @return string
     */
    public function action()
    {
        return $this->action;
    }



    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }



    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }



    /**
     * @param array $parameters
     * @param bool  $for_admin
     */
    public function generateUrl($parameters = array(), $for_admin = true)
    {
        $parameters[Route::URL_PARAMETER] = $this->identifier();
        $this->url = add_query_arg(
            $parameters,
            $for_admin ? admin_url($this->base_path) : site_url($this->base_path)
        );
    }



    /**
     * @return string
     */
    public function view()
    {
        return $this->view;
    }



}
// End of file Route.php
// Location: /application/entities/routes/Route.php