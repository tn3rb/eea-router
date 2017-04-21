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
 * Class VenueThumbnail
 * View for displaying a post thumbnail for a venue
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class VenueThumbnail extends View
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
                EE_ROUTER_BASE_PATH . 'presentation/templates/custom_post_types/post_thumbnail.php',
                array(
                    'post'           => 'object',
                    'ID'             => 'int',
                    'link_target'    => 'string',
                    'thumbnail_size' => 'string',
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
        $venue = $this->view_model->getModelObject();
        $this->view_model->assignTemplateData(
            array(
                'post'           => $venue,
                'ID'             => $venue->ID(),
                'link_target'    => '',
                'thumbnail_size' => 'large'
            )
        );
    }



}
// End of file VenueThumbnail.php
// Location: EspressoRouter\presentation\views\venues/VenueThumbnail.php