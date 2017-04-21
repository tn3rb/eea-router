<?php

namespace EspressoRouter\presentation\controllers\venues;

use DomainException;
use EE_Error;
use EE_Venue;
use EspressoRouter\presentation\controllers\BaseController;
use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DisplayVenue
 * View for displaying a venue
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DisplayVenue extends BaseController
{

    /**
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     */
    public function execute()
    {
        global $post;
        if (
            $post instanceof \WP_Post
            && $post->post_type === 'espresso_venues'
            && $post->EE_Venue instanceof EE_Venue
        ) {
            $venue = $post->EE_Venue;
        } else {
            $venue_id = $this->request->get('venue_id');
            if (! $venue_id) {
                return;
            }
            $venue = $this->view_model->getModel()->get_one_by_ID($venue_id);
        }
        if (! $venue instanceof EE_Venue) {
            return;
        }
        $this->view_model->setModelObject($venue);
    }
}
// End of file DisplayVenue.php
// Location: EspressoRouter\presentation\controllers/DisplayVenue.php