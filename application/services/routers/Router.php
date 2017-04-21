<?php

namespace EspressoRouter\application\services\routers;

use DomainException;
use EE_Request;
use EspressoRouter\application\entities\routes\Route;
use EspressoRouter\presentation\controllers\BaseController;
use EspressoRouter\presentation\views\ViewInterface;
use EventEspresso\core\exceptions\ExceptionStackTraceDisplay;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\container\CoffeeShop;
use EventEspresso\core\services\container\CoffeeMaker;
use EventEspresso\core\services\container\exceptions\ServiceNotFoundException;
use Exception;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class Router
 * Configuration based router for connecting Controllers with Views based on Routes.
 * Maintains a collection of Route objects added by client code,
 * and attempts to match the current incoming request to one of those Routes.
 * If a match is found, will proceed to load the Controller and View for that Route
 * and display the results within a callback closure for a hook specified by the Route.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Router
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
     * @var Collection $routes
     */
    protected $routes;



    /**
     * Router constructor.
     *
     * @param CoffeeShop $coffee_shop
     * @param EE_Request $request
     * @param Collection $routes
     * @throws InvalidInterfaceException
     */
    public function __construct(CoffeeShop $coffee_shop, EE_Request $request, Collection $routes = null)
    {
        $this->coffee_shop = $coffee_shop;
        $this->request = $request;
        $this->routes = $routes instanceof Collection
            ? $routes
            : new Collection('\EspressoRouter\application\entities\routes\Route');
    }



    /**
     * @param Route $route
     * @throws InvalidEntityException
     */
    public function addRoute(Route $route)
    {
        $this->routes->add($route);
    }



    /**
     * @param Route $route
     */
    public function removeRoute(Route $route)
    {
        $this->routes->remove($route);
    }



    /**
     * @throws ServiceNotFoundException
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function dispatch()
    {
        $route = $this->resolveRoute();
        if(!$route instanceof Route) {
            return;
        }
        $router = $this;
        add_action(
            $route->action(),
            function () use ($route, $router) {
                try {
                    $view = $router->getView($route);
                    $controller = $router->getController($route, $view);
                    $controller->{$route->method()}();
                    $router->assignTemplateData($route, $view);
                    echo $view->display();
                } catch (Exception $exception) {
                    new ExceptionStackTraceDisplay($exception);
                }
            }
        );
    }



    /**
     * @return Route
     * @throws DomainException
     */
    private function resolveRoute()
    {
        $route_id = $this->request->get(Route::URL_PARAMETER);
        if (! $route_id) {
            return null;
        }
        $request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        foreach ($this->routes as $route) {
            /** @var Route $route */
            if (strpos($request_uri, $route->basePath()) !== 0) {
                continue;
            }
            if ($route->identifier() === $route_id) {
                return $route;
            }
        }
        return null;
    }



    /**
     * @param Route $route
     * @return ViewInterface
     * @throws DomainException
     * @throws ServiceNotFoundException
     */
    private function getView(Route $route)
    {
        $view = $this->coffee_shop->brew(
            $route->view(),
            array(),
            CoffeeMaker::BREW_NEW
        );
        if ($view instanceof ViewInterface) {
            return $view;
        }
        throw new DomainException();
    }



    /**
     * @param Route         $route
     * @param ViewInterface $view
     * @return BaseController
     * @throws DomainException
     * @throws ServiceNotFoundException
     */
    private function getController(Route $route, ViewInterface $view)
    {
        $parameters = $route->parameters();
        $parameters[] = $view->getViewModel();
        $controller = $this->coffee_shop->brew(
            $route->controller(),
            $parameters,
            CoffeeMaker::BREW_NEW
        );
        if ($controller instanceof BaseController) {
            return $controller;
        }
        throw new DomainException();
    }



    /**
     * @param Route         $route
     * @param ViewInterface $view
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    private function assignTemplateData(Route $route, ViewInterface $view)
    {
        if ($route->type() === Route::METHOD_GET) {
            $view->getViewModel()->assignTemplateData($this->request->get_params());
        }
        if ($route->type() === Route::METHOD_POST) {
            $view->getViewModel()->assignTemplateData($this->request->post_params());
        }
    }


}
// End of file Router.php
// Location: /application/services/routers/Router.php