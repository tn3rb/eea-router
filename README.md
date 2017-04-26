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
It's on the todo list to change the router to use the new abstracted Loader class instead of the CoffeeShop DI container directly in order to reduce coupling. Again, this will all be handled by core and is only being shown here for illustrative purposes. 

Of course, to actually use the Espresso Router, you will need to define some Routes for it to use.

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
Routes would then be registered with core by calling the addRoutes() method.

```php
$routes_config = $this->coffee_shop->get('MyRouteConfig');
$routes_config->addRoutes();
```

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

## ViewModels

There is a common problem in MVC architecture where logic that is associated with a particular view or template doesn't have a satisfactory location to reside. It shouldn't be part of the template itself, certainly doesn't belong in the controller, but also doesn't belong in the domain model because it should represent the current state of the domain only, and not be tied to any particular UI element, which would be part of the presentation layer. ModelViews are an intermediary class that are used for holding state that is specific to a particular View or template. It is a natural extension of MVC and the defining element of the MVVM pattern.

Typically a ViewModel doesn't have to consist of more than a constructor that type hints for the domain model that it pull data from:

```php
class EventViewModel extends ViewModel
{
    public function __construct(EEM_Event $model)
    {
        parent::__construct($model);
    }
}
```

The parent class constructor also has a second parameter called ` $data_overwrite ` that sets a boolean flag for controlling whether or not template data can be overwritten. If this is set to true (default is false), then any attempt to overwrite an existing template variable will result in a DomainException.

## Views

Views are a wrapper class used for encapsulating a Template and ModelView class. They can be a place for common abstracted display related logic (like pagination), but should NOT hold any model state, or template data, which is instead contained in the corresponding ModelView. View classes follow the Composite pattern, so Views can be added to Views (which can be added to Views, which can be added to Views, etc, etc...) and calling display() on one View will call display() on all of its "subviews".

During instantiation, a View needs to set the corresponding ViewModel and Template classes that it requires by passing those to the parent View class constructor. Because they need to instantiate a ModelView, Views will therefore need to type hint for a domain model in their constructor that corresponds with the ModelView they utilize:

```php
    public function __construct(EEM_Event $model)
    {
        parent::__construct(
            new EventViewModel($model),
            new Template(
                EE_ROUTER_BASE_PATH . 'presentation/templates/custom_post_types/post_thumbnail.php'
            )
        );
    }
```

The reason for this abstraction is to allow reusability by decoupling the domain model from both the ViewModel and the Template. As you can see from the above example, a Template class is loading an actual template file named ` post_thumbnail.php ` and coupling it with a ViewModel called EventViewModel that utilizes the EEM_Event model. The ViewModel pulls and stores data from the Event domain model to be used in the View, in this case, it's data for displaying the post thumbnail for an Event. 

If however, we coupled that same Template setup with a different ModelView (and therefore a different Model) then the same template could be used for displaying a completely different set of data, for example:

```php
    public function __construct(EEM_Venue $model)
    {
        parent::__construct(
            new VenueViewModel($model),
            new Template(
                EE_ROUTER_BASE_PATH . 'presentation/templates/custom_post_types/post_thumbnail.php'
            )
        );
    }
```

This uses the VenueViewModel and corresponding EEM_Venue domain model, and would populate that same template with data for displaying the post thumbnail for a Venue instead of an Event. So the ` post_thumbnail.php ` template is now reusable with any ViewModel that can populate its required variables (more on this in the Template section). 

But this reusability goes both ways as ModelViews can be reused with different Templates:

```php
    public function __construct(EEM_Venue $model)
    {
        parent::__construct(
            new VenueViewModel($model),
            new Template(
                EE_ROUTER_BASE_PATH . 'presentation/templates/addresses/inline_address.php'
            )
        );
    }
```

Now the VenueViewModel and corresponding EEM_Venue domain model are being used to populate a template for displaying the address for the Venue. The benefits of this decoupling becomes staggeringly obvious when the number of ModelViews and corresponding templates they can utilize grows in number. For example, imagine that you decide to create hardcoded templates instead of using an abstracted system like above, but you need to display addresses on a single line (inline) or with each part of the address on a separate line (multiline). But you need to do this for both Venues and the people in your list of Contacts. 

No problem, you can just create the following four templates:
 
   * contact-address-inline.php
   * contact-address-multiline.php
   * venue-address-inline.php
   * venue-address-multiline.php
   
But what happens when management decides they want to add another type of contact called presenter whose data is stored somewhere different than in the Contacts. Well you can just add another couple of templates right? Then what if management then wants to add corporate sponsors that require an address, as well as data for organizations that host events, who of course, also have an address that needs displaying. Here's are list of required templates now:

   * contact-address-inline.php
   * contact-address-multiline.php
   * organization-address-inline.php
   * organization-address-multiline.php
   * presenter-address-inline.php
   * presenter-address-multiline.php
   * sponsor-address-inline.php
   * sponsor-address-multiline.php
   * venue-address-inline.php
   * venue-address-multiline.php
 
Wow 10 templates! That's a lot... but oh-uh... management now needs addresses displayed in a third way (like in a circle... cuz... marketing dept...) anyways... omg the number of templates just increased to 15.

Having the ViewModel decoupled from the template itself would allow you to create a single template for each way you need addresses displayed:

   * address-circle.php
   * address-inline.php
   * address-multiline.php

plus a ModelView for each domain model:

   * ContactModelView
   * OrganizationModelView
   * PresenterModelView
   * SponsorModelView
   * VenueModelView

That's only 8 files instead of 15, and the ModelViews can be used with basically any Template that needs to pull data from their corresponding domain model, so they are reusable beyond their use for populating the data for addresses.

But on top of all of that, Views are defined by the Route and loaded by the Router at the same time as the Controller. Since Controllers also have access to the ModelView, they can take input from the request and use it to populate the ModelView with a specific domain model object, which would then be used to populate the View. So Templates can be reused with multiple ModelViews (and vice versa), which get paired together as a View, and Controllers can be coupled with any View that utilizes the correct domain model that the controller is designed to work with, so they are reusable as well.

This creates an extremely extensible and reusable system for generating UI elements.

Ultimately the Route has complete control over which domain model is used with which template, simply by defining both the Controller and View.


