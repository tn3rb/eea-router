<?php

namespace EspressoRouter\presentation\controllers\events;

use DomainException;
use EE_Error;
use EE_Event;
use EspressoRouter\presentation\controllers\BaseController;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use WP_Post;

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
     * @return void
     */
    public function execute()
    {
        global $post;
        if (
            $post instanceof WP_Post
            && $post->post_type === 'espresso_events'
            && $post->EE_Event instanceof EE_Event
        ) {
            $event = $post->EE_Event;
        } else {
            $event_id = $this->request->get('event_id');
            if (! $event_id) {
                return;
            }
            $event = $this->view_model->getModel()->get_one_by_ID($event_id);
        }
        if(!$event instanceof EE_Event) {
            return;
        }
        $this->view_model->setModelObject($event);
    }


}
// End of file DisplayEvent.php
// Location: /presentation/controllers/events/DisplayEvent.php


