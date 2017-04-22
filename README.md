# Espresso Router
Simple configuration based router for the Event Espresso WordPress plugin that connects Controllers with composite Views in a completely decoupled way that promotes reusability.

##### Requirements

 * Event Espresso 4.9.30+
 * PHP 5.3+

## The Router

#### Setup and Usage

Although this will be handled by Event Espresso core, for illustrative purposes,
here's how the router can be setup by direct instantiation, which only requires it's two dependencies: 
```php
$router = new EspressoRouter\application\services\routers\Router(
    $coffee_shop_di_container,
    $ee_request_object,
);
```
or within a class that has access to the CoffeeShop DI container (since shared access is best)
```php
$router = $this->coffee_shop->brew(
    'EspressoRouter\application\services\routers\Router',
    array(
        $this->coffee_shop,
        $this->coffee_shop->brew('EE_Request'),
    ),
    CoffeeMaker::BREW_SHARED
);
```
once properly configured within the DI container (added as a service, aliases set, etc), loading would be as easy as:
```php
$router = $this->coffee_shop->get('Router');
```
and dispatching the routes (after they have been added)
```php
$router->dispatch();
```
although it's best to wrap that in a try catch block
```php
try {
    $router = $this->coffee_shop->get('Router');
    $router->dispatch();
} catch (Exception $exception) {
    new ExceptionStackTraceDisplay($exception);
}
```
It's on the todo list to change the router to use the new abstracted Loader class instead of the CoffeeShop DI container directly in order to reduce coupling.

#### Adding Routes

After getting access to a shared instance of the router:
```php
$router->addRoute(
    new EspressoRouter\application\entities\routes\Route(
        'event_thumbnail', // identifier
        'EspressoRouter\presentation\controllers\events\DisplayEvent', // controller
        'EspressoRouter\presentation\views\events\EventThumbnail' // view
    )
);
```
If you have a lot of Routes to add, then it can also be done by creating a child of the RouteConfig class that has a shared instance of the Router already injected, and then adding your routes within the AddRoutes() method:
```php
class MyRouteConfig extends RouteConfig
{
    public function addRoutes()
    {
        $this->router->addRoute(
            new EspressoRouter\application\entities\routes\Route(
                'event_thumbnail', // identifier
                'EspressoRouter\presentation\controllers\events\DisplayEvent', // controller
                'EspressoRouter\presentation\views\events\EventThumbnail' // view
            )
        );
    }
}
```
Routes would then be registered by core by calling the addRoutes() method.

## Routes

Routes can be instantiated directly as seen above, or by one of the factory methods:

```php
$route = Route::forGetRequest($identifier, $controller, $view);
$route = Route::forPostRequest($identifier, $controller, $view);
```
Routes require a minimum of three parameters: 

 * $identifier - a unique string identifier which can be used as a URL parameter
 * $controller - the FQCN for the Controller class to be loaded when the route is executed
 * $view - the FQCN for the View class to be loaded when the route is executed (for GET requests)

The additional parameters in order are:

 * $method - name of the Controller method to call when the Route is executed. Defaults to 'execute'
 * $action - name of the WordPress action hook that determines when the Route runs. Defaults to 'loop_start'
 * $base_path - URL base path used to help resolve when Routes run. Defaults to '/'
 * $parameters - array of parameters passed to the Controller upon instantiation for configuration purposes. Dependencies that can be injected by the Loader / DI container can be omitted, as can any request parameters, which will automatically get passed to the ModelView.
 * $type - whether Route is for a GET or POST request. GET requests will call display() on the View class specified by the Route, whereas POST requests are expected to be redirected afterwards by the Controller.
 
 ## Controllers
 
 All Containers must extends the BaseController class and implement the execute() method. Controllers have access to the following injected dependencies:
 
 * A ViewModel class that maintains state for the View and can be updated by the controller
 * the CoffeeShop DI container (will later be decoupled and changed to the new Loader class)
 * the EE Request object (for access to request and server parameters)
 * the EE Response object (eventually for storing the controller/view output and/or managing redirects. This will allow the request type to control whether to print content to the screen directly or convert to another format like JSON, CSV, XML, etc, to be sent somewhere else)

A simple controller example:

```php
class SimpleController extends BaseController
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->request->get('id');
        $object = $this->view_model->getModel()->get_one_by_ID($id);
        if(!$object instanceof SomeObject) {
            return;
        }
        $this->view_model->setModelObject($object);
    }
}
```



