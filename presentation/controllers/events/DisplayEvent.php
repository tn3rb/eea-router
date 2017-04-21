<?php

namespace EspressoRouter\presentation\controllers\events;

use DomainException;
use EE_Error;
use EE_Event;
use EspressoRouter\presentation\controllers\BaseController;
use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DisplayEvent
 * Controller for displaying an event
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DisplayEvent extends BaseController
{

    /**
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     */
    public function execute()
    {
        // ?ee_route=event_details&event_id=59
        $event = $this->view_model->getModel()->get_one_by_ID(
            $this->request->get('event_id')
        );
        if(!$event instanceof EE_Event) {
            return;
        }
        $this->view_model->setModelObject($event);
    }


}
// End of file DisplayEvent.php
// Location: /presentation/controllers/events/DisplayEvent.php