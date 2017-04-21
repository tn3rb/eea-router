<?php

namespace EspressoRouter\presentation\views\venues;

use DomainException;
use EE_Error;
use EE_Venue;
use EEM_Venue;
use EspressoRouter\presentation\templates\Template;
use EspressoRouter\presentation\views\View;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFilePathException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class VenueHeader
 * View for displaying the header for a venue listing
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class VenueHeader extends View
{

    /**
     * VenueView constructor.
     *
     * @param EEM_Venue $model
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     * @throws DomainException
     * @throws EE_Error
     */
    public function __construct(EEM_Venue $model)
    {
        parent::__construct(
            new VenueViewModel($model),
            new Template(
                EE_ROUTER_BASE_PATH . 'presentation/templates/custom_post_types/post_header.php',
                array(
                    'ID'         => 'int',
                    'wrap_class' => 'string'
                )
            )
        );
    }



    /**
     * Called just before the template is rendered
     * which makes it a great place to set any template variables that need values
     *
     * @return void
     * @throws InvalidFilePathException
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     */
    public function preTemplateRender()
    {
        /** @var EE_Venue $venue */
        $venue = $this->view_model->getModelObject();
        $this->view_model->assignTemplateData(
            array(
                'ID'         => $venue->ID(),
                'wrap_class' => has_excerpt($venue->ID()) ? ' has-excerpt' : ''
            )
        );
    }

}
// End of file VenueHeader.php
// Location: presentation/views/venues/VenueHeader.php:68