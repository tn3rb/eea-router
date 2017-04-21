<?php

namespace EspressoRouter\presentation\views\events;

use DomainException;
use EE_Error;
use EE_Event;
use EEM_Event;
use EspressoRouter\presentation\templates\Template;
use EspressoRouter\presentation\views\View;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFilePathException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class EventThumbnail
 * View for displaying a post thumbnail for an event
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EventThumbnail extends View
{

    /**
     * EventThumbnail constructor.
     *
     * @param EEM_Event $model
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     * @throws DomainException
     * @throws EE_Error
     */
    public function __construct(EEM_Event $model)
    {
        parent::__construct(
            new EventViewModel($model),
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
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     */
    public function preTemplateRender()
    {
        /** @var EE_Event $event */
        $event = $this->view_model->getModelObject();
        $this->view_model->assignTemplateData(
            array(
                'post'           => $event,
                'ID'             => $event->ID(),
                'link_target'    => '',
                'thumbnail_size' => 'full'
            )
        );
    }



}
// End of file EventThumbnail.php
// Location: /presentation/views/events/EventThumbnail.php