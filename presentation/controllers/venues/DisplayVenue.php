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
        // ?ee_route=venue_details&venue_id=61
        $venue = $this->view_model->getModel()->get_one_by_ID(
            $this->request->get('venue_id')
        );
        if (! $venue instanceof EE_Venue) {
            return;
        }
        $this->view_model->setModelObject($venue);
    }
}
// End of file DisplayVenue.php
// Location: EspressoRouter\presentation\controllers/DisplayVenue.php