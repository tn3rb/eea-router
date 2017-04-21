<?php

namespace EspressoRouter\presentation\views\venues;

use EEM_Venue;
use EspressoRouter\presentation\views\ViewModel;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class VenueViewModel
 * ModelView for Venue View classes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class VenueViewModel extends ViewModel
{

    public function __construct(EEM_Venue $model) {
        parent::__construct($model);
    }

}
// End of file VenueViewModel.php
// Location: presentation/views/venues/VenueViewModel.php