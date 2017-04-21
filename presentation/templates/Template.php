<?php

namespace EspressoRouter\presentation\templates;

use DomainException;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFilePathException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class Template
 * Finds, loads and renders an HTML template file.
 * Alternate paths can be specified via the $include_paths array,
 * and the first match found will be used instead of the default.
 * !!! IMPORTANT !!!
 * ALL template variables and their data types must be specified
 * in the $template_variables array. See below for more details.
 * ALL data for populating the template variables should be supplied
 * by the ViewModel class that was passed along to the View with this Template
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Template
{

    const INVALID_VAR_PREFIX = 'ee_td';

    /**
     * base filename for template
     *
     * @var string $template
     */
    private $template = '';

    /**
     * full directory filepath to template, including filename and extension
     *
     * @var string $template_path
     */
    private $template_path = '';

    /**
     * array of variable names and data types required by the template,
     * where the key is the variable name and the value is the data type
     * will be used to validate that all template data is available.
     * list of acceptable data types is:
     *  'string', 'integer', 'float', 'boolean', 'array', and 'object'
     *
     * @var array $template_variables
     */
    private $template_variables;

    /**
     * array of alternate file paths to search through for the file
     *
     * @var array $include_paths
     */
    private $include_paths = array();

    /**
     * confirmed location of template file
     *
     * @var string $resolved_path
     */
    private $resolved_path = '';



    /**
     * Template constructor
     *
     * @param string $template_path
     * @param array  $template_variables
     * @param array  $include_paths
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     * @throws DomainException
     */
    public function __construct($template_path, $template_variables = array(), $include_paths = array())
    {
        $this->setTemplateFile($template_path);
        $this->setIncludePaths($include_paths);
        $this->setTemplateVariables($template_variables);
    }



    /**
     * @param string $template_path
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     */
    private function setTemplateFile($template_path)
    {
        if (! is_string($template_path)) {
            throw new InvalidDataTypeException('$template_path', $template_path, 'string');
        }
        if (! is_readable($template_path)) {
            throw new InvalidFilePathException($template_path);
        }
        $this->template = basename($template_path);
        $this->template_path = $template_path;
    }



    /**
     * @param array|string $include_paths
     */
    private function setIncludePaths($include_paths = array())
    {
        $this->include_paths = is_array($include_paths) ? $include_paths : array($include_paths);
    }



    /**
     * @param array $template_variables
     * @throws DomainException
     */
    public function setTemplateVariables(array $template_variables)
    {
        if(empty($template_variables)){
            throw  new DomainException(
                sprintf(
                    esc_html__(
                        'The template variables array can not be empty. Please define a list of variables and their corresponding data types that are required for the "%1$s" template.',
                        'event_espresso'
                    ),
                    $this->template
                )
            );
        }
        $this->template_variables = $template_variables;
    }





    /**
     * First filters the list of include_paths,
     * checks if that list is empty, and if so, set the resolved_path to the default and return it.
     * if not empty, then loops through checking if any of the new paths are readable.
     * the first valid path that is found is set as the resolved_path
     *
     * @return string
     */
    private function resolvedFilePath()
    {
        // if a template file path has already been resolved, then return that
        if ($this->resolved_path !== '') {
            return $this->resolved_path;
        }
        // filter the $include_paths array
        $this->include_paths = (array)apply_filters(
            'FHEE__EventEspresso_core_services_views_Template__getResolvedFilePath__include_paths',
            $this->include_paths,
            $this->template,
            $this->template_path
        );
        // if the $include_paths array does not contain any non-empty entries,
        // then the template path set upon construction will be used for the resolved_path
        if (array_filter($this->include_paths) === array()) {
            $this->resolved_path = $this->template_path;
            return $this->resolved_path;
        }
        // the $include_paths array must contain some kind of info,
        // the first valid path encountered will be set as the $resolved_path
        // array has been reversed, so last entry gets checked first
        foreach (array_reverse($this->include_paths) as $include_path) {
            $include_path = rtrim($include_path, DS);
            if (is_readable($include_path . DS . $this->template)) {
                $this->resolved_path = $include_path . DS . $this->template;
                break;
            }
        }
        // one last check to see if we have a $resolved_path.
        // if not, then use the path set upon construction
        $this->resolved_path = $this->resolved_path !== '' ? $this->resolved_path : $this->template_path;
        return $this->resolved_path;
    }



    /**
     * using an output buffer to control what's displayed,
     * extract all template data into variables
     * (invalid keys will be prefixed with "ee_td_")
     * get the resolved template file path and include that file
     * return the contents of the output buffer
     *
     * @param array $template_data
     * @return string
     * @throws InvalidDataTypeException
     * @throws DomainException
     */
    public function render(array $template_data)
    {
        ob_start();
        $this->validateTemplateData($template_data);
        extract($template_data, EXTR_PREFIX_INVALID, Template::INVALID_VAR_PREFIX);
        include($this->resolvedFilePath());
        return ob_get_clean();
    }



    /**
     * uses a validator class like EEH_Template_Validator
     * to confirm that all data required by the template
     * has been set and is the correct type
     *
     * @param array $template_data
     * @return void
     * @throws InvalidDataTypeException
     * @throws DomainException
     */
    private function validateTemplateData(array $template_data)
    {
        foreach ($this->template_variables as $variable_name => $data_type) {
            if (! isset($template_data[$variable_name])) {
                throw new DomainException(
                    sprintf(
                        esc_html__(
                            'The "%1$s" variable is required for the %2$s template.',
                            'event_espresso'
                        ),
                        $variable_name,
                        $this->template
                    )
                );
            }
            $incorrect_data_type = false;
            switch ($data_type) {
                case  'string' :
                    if (! is_string($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                case  'integer' :
                    if (! is_int($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                case  'float' :
                    if (! is_float($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                case  'boolean' :
                    if (! is_bool($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                case  'array' :
                    if (! is_array($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                case  'object' :
                    if (! is_object($template_data[$variable_name])) {
                        $incorrect_data_type = true;
                    }
                    break;
                default :
            }
            if ($incorrect_data_type) {
                throw new InvalidDataTypeException(
                    $variable_name,
                    $template_data[$variable_name],
                    $data_type
                );
            }
        }
    }



}
// End of file Template.php
// Location: /presentation/views/Template.php