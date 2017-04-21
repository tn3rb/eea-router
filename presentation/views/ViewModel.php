<?php

namespace EspressoRouter\presentation\views;

use DomainException;
use EE_Base_Class;
use EEM_Base;
use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class ViewModel
 * Abstract parent class for ViewModel, used for storing view state, including template data.
 * Template data gets converted into variables that can be used in the template.
 * Can optionally include an EEM_Base model for fetching data directly from the database.
 * A specific EE_Base_Class object can be added via a setter for populating template data.
 * .
 * The main purpose of a ViewModel is to decouple the template data
 * from the actual template itself, which allows for greater reusability.
 * For example:
 * Imagine having several templates for displaying addresses in different formats.
 * A ViewModel for an EE_Contact could populate any of those templates with the address for a person,
 * whereas a ViewModel for an EE_Venue could do the same, but for a building.
 * The template can now be reused for any ViewModel that can supply it's variables.
 * .
 * ViewModels can also be reused with different types of Templates.
 * For example,
 * The same ViewModel for an EE_Venue could be used for displaying other details about the Venue as well,
 * as long as the ViewModel supplies all of the variables required by the other template.
 * .
 * So a VenueViewModel might have one set of Templates for displaying it's data in the admin,
 * and another set of templates for displaying it's data on the frontend,
 * and some of those Templates may be used for displaying data for completely different ViewModels.
 * So decoupling allows both Templates and ModelViews to be reused and recombined in multiple ways.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class ViewModel implements ViewModelInterface
{

    /**
     * whether or not template data can be overwritten.
     * defaults to true
     *
     * @var boolean $data_overwrite
     */
    private $data_overwrite = true;

    /**
     * @var EEM_Base $model
     */
    protected $model;

    /**
     * @var EE_Base_Class $model_object
     */
    protected $model_object;

    /**
     * array of data to be extracted as variables for use in the actual template
     *
     * @var array $template_data
     */
    private $template_data;

    /**
     * @var string $name
     */
    private $name;



    /**
     * ViewModel constructor
     *
     * @param EEM_Base      $model
     * @param boolean       $data_overwrite
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function __construct(
        EEM_Base $model = null,
        $data_overwrite = true
    ) {
        $this->setModel($model);
        $this->setDataOverwrite($data_overwrite);
        $this->name = get_class($this);
    }



    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * @return EEM_Base
     */
    public function getModel()
    {
        return $this->model;
    }



    /**
     * @param EEM_Base $model
     */
    public function setModel(EEM_Base $model = null)
    {
        $this->model = $model;
    }



    /**
     * @return string
     */
    public function getDataOverwrite()
    {
        return $this->data_overwrite;
    }



    /**
     * @param string $data_overwrite
     */
    private function setDataOverwrite($data_overwrite)
    {
        $this->data_overwrite = filter_var($data_overwrite, FILTER_VALIDATE_BOOLEAN);
    }



    /**
     * @return EE_Base_Class
     */
    public function getModelObject()
    {
        return $this->model_object;
    }



    /**
     * @param EE_Base_Class $model_object
     */
    public function setModelObject(EE_Base_Class $model_object)
    {
        $this->model_object = $model_object;
    }



    /**
     * @param string $variable_name
     * @param mixed  $data
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function assignVariable($variable_name, $data = null)
    {
        if (! is_string($variable_name)) {
            throw new InvalidDataTypeException('$variable_name', $variable_name, 'string');
        }
        if (! $this->data_overwrite && isset($this->template_data[$variable_name])) {
            throw new DomainException(
                sprintf(
                    __(
                        'The "%1$s" template data is protected! The "%2$s" data parameter already exists and data overwriting is not allowed.',
                        'event_espresso'
                    ),
                    $this->getName(),
                    $variable_name
                )
            );
        }
        $this->template_data[$variable_name] = $data;
    }



    /**
     * @param array $template_data
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function assignTemplateData(array $template_data)
    {
        foreach ($template_data as $variable_name => $data) {
            $this->assignVariable($variable_name, $data);
        }
    }



    /**
     * @return array
     * @throws DomainException
     */
    public function getTemplateData()
    {
        return $this->template_data;
    }



}
// End of file ViewModel.php
// Location:  /presentation/views/ViewModel.php