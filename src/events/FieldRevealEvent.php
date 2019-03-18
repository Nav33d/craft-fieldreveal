<?php

/**
 * Field Reveal plugin for Craft CMS 3
 *
 * View where your custom fields are being used.
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\fieldreveal\events;

use yii\base\Event;

class FieldRevealEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var string Element type
     */
    public $elementType;

    /**
     * @var int Field layout id
     */
    public $fieldLayoutId;

    /**
     * @var array Data about the field i.e. what section/group the field is attached to
     */
    public $sectionData;

    /**
     * @var string Title for the section / group
     */
    public $sectionTitle;
    
}