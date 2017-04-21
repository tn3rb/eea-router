<?php

namespace EspressoRouter\presentation\views\events;

use EEM_Event;
use EspressoRouter\presentation\views\ViewModel;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class EventViewModel
 * ModelView for Event View classes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EventViewModel extends ViewModel
{

    public function __construct(EEM_Event $model)
    {
        parent::__construct($model);
    }


}
// End of file EventViewModel.php
// Location: EspressoRouter\presentation\views\events/EventViewModel.php