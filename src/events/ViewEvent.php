<?php
namespace bitboxde\minifier\events;

use bitboxde\minifier\minify\CSS;
use bitboxde\minifier\minify\JS;
use yii\base\Event;

class ViewEvent extends Event
{
    /** @var string */
    public $type;
    /** @var string */
    public $filePath;
    /** @var string */
    public $output;
}
