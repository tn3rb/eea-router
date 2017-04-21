<?php

namespace EspressoRouter\presentation\views;

use DomainException;
use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * ViewInterface
 * Public contract for Views
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
interface ViewInterface
{

    /**
     * @param $view_identifier
     * @return boolean
     */
    public function hasView($view_identifier);



    /**
     * @param View   $view
     * @param string $view_identifier string identifier, if none given, will use ModelView class name
     * @return void
     */
    public function addView(View $view, $view_identifier = '');



    /**
     * @return View[]
     */
    public function getViews();



    /**
     * @param string $view_identifier
     * @return void
     */
    public function removeView($view_identifier);



    /**
     * @return ViewModelInterface
     */
    public function getViewModel();



    /**
     * Called just before the template is rendered
     * which makes it a great place to set any template variables that need values
     *
     * @return void
     */
    public function preTemplateRender();



    /**
     * assigns all sub views to template data on model
     * then passes template data to template
     * and returns rendered HTML
     *
     * @return string
     * @throws InvalidDataTypeException
     * @throws DomainException
     */
    public function display();

}
// End of file ViewInterface.php
// Location:  /presentation/views/ViewInterface.php