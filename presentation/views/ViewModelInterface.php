<?php

namespace EspressoRouter\presentation\views;

use DomainException;
use EE_Base_Class;
use EEM_Base;
use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * ViewModelInterface
 * Public contract for ViewModels
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
interface ViewModelInterface
{

    /**
     * @return string
     */
    public function getName();



    /**
     * @return EEM_Base
     */
    public function getModel();



    /**
     * @return string
     */
    public function getDataOverwrite();



    /**
     * @return EE_Base_Class
     */
    public function getModelObject();



    /**
     * @param EE_Base_Class $model_object
     */
    public function setModelObject(EE_Base_Class $model_object);



    /**
     * @param string $template_variable_name
     * @param mixed  $data
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function assignVariable($template_variable_name, $data = null);



    /**
     * @param array $template_args
     * @throws DomainException
     * @throws InvalidDataTypeException
     */
    public function assignTemplateData(array $template_args);



    /**
     * @return array
     */
    public function getTemplateData();

}
// End of file ViewModelInterface.php
// Location: /presentation/views/ViewModelInterface.php