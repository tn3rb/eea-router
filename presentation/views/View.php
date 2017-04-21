<?php

namespace EspressoRouter\presentation\views;

use DomainException;
use EspressoRouter\presentation\templates\Template;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use Exception;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class View
 * A bridge between controller type classes, Templates, and ModelView classes.
 * A place for common abstracted display related logic, like pagination
 * Should NOT hold any model state, or template data,
 * which is contained in the corresponding ModelView.
 * Class follows Composite pattern so Views can be added to Views,
 * and calling display() on one will call display() on all subviews.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class View implements ViewInterface
{

    /**
     * @var View[] $views
     */
    protected $views = array();

    /**
     * @var ViewModelInterface $view_model
     */
    protected $view_model;


    /**
     * @var Template $template
     */
    protected $template;



    /**
     * View constructor
     *
     * @param ViewModelInterface $view_model
     * @param Template           $template
     */
    public function __construct(ViewModelInterface $view_model, Template $template = null)
    {
        $this->view_model = $view_model;
        $this->template = $template;
    }



    /**
     * @param Template $template
     */
    protected function setTemplate(Template $template)
    {
        $this->template = $template;
    }



    /**
     * @param $view_identifier
     * @return boolean
     */
    public function hasView($view_identifier)
    {
        return array_key_exists($view_identifier, $this->views);
    }



    /**
     * @param View   $view
     * @param string $view_identifier
     * @return void
     */
    public function addView(View $view, $view_identifier = '')
    {
        $view_identifier = ! empty($view_identifier)
            ? $view_identifier
            : get_class($view->view_model);
        if (! $this->hasView($view_identifier)) {
            $this->views[$view_identifier] = $view;
        }
    }



    /**
     * @return View[]
     */
    public function getViews()
    {
        return $this->views;
    }



    /**
     * @param string $view_identifier
     * @return void
     */
    public function removeView($view_identifier)
    {
        unset($this->views[$view_identifier]);
    }



    /**
     * @return ViewModelInterface
     */
    public function getViewModel()
    {
        return $this->view_model;
    }



    /**
     * Called just before the template is rendered
     * which makes it a great place to set any template variables that need values
     *
     * @return void
     */
    abstract public function preTemplateRender();


    /**
     * assigns all sub views to template data on model
     * then passes template data to template
     * and returns rendered HTML
     *
     * @return string
     * @throws InvalidDataTypeException
     * @throws DomainException
     */
    public function display()
    {
        $this->preTemplateRender();
        foreach ($this->getViews() as $view_identifier => $view) {
            // if any sub Views are using the same ViewModel as us, then share our ViewModel's model object
            if($this->view_model->getName() === $view->getViewModel()->getName()) {
                $view->getViewModel()->setModelObject($this->view_model->getModelObject());
            }
            $this->view_model->assignVariable($view_identifier, $view->display());
        }
        return $this->template->render($this->view_model->getTemplateData());
    }



    /**
     * in case someone tries to echo this class directly
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->display();
        } catch (Exception $e) {
            // todo: log Exception
        }
        return '';
    }


}
// End of file View.php
// Location:  /presentation/views/View.php