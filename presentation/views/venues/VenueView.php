<?php

namespace EspressoRouter\presentation\views\venues;

use DomainException;
use EE_Error;
use EEM_Venue;
use EspressoRouter\presentation\templates\Template;
use EspressoRouter\presentation\views\View;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFilePathException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class VenueView
 * View for displaying a venue
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class VenueView extends View
{

    /**
     * VenueView constructor.
     *
     * @param EEM_Venue      $model
     * @param VenueHeader    $venue_header
     * @param VenueThumbnail $venue_thumbnail
     * @throws DomainException
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     */
    public function __construct(EEM_Venue $model, VenueHeader $venue_header, VenueThumbnail $venue_thumbnail)
    {
        parent::__construct(
            new VenueViewModel($model),
            new Template(
                EE_ROUTER_BASE_PATH . 'presentation/templates/custom_post_types/post_content.php',
                array(
                    'post'       => 'object',
                    'ID'         => 'int',
                    'wrap_class' => 'string'
                )
            )
        );
        $this->addView($venue_header, 'post_header');
        $this->addView($venue_thumbnail, 'post_thumbnail');
    }



    /**
     * Called just before the template is rendered
     * which makes it a great place to set any template variables that need values
     *
     * @return void
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     */
    public function preTemplateRender()
    {
        $venue = $this->view_model->getModelObject();
        $this->view_model->assignTemplateData(
            array(
                'post'   => $venue,
                'ID'     => $venue->ID(),
                'wrap_class' => has_excerpt($venue->ID()) ? ' has-excerpt' : ''
            )
        );
    }



}
// End of file VenueView.php
// Location: /presentation/views/venues/VenueView.php