<?php
namespace Alerts\Renderer;

use Alerts\MessageCollection;

class Text implements RendererInterface {

    public static $template = '%1$s: %2$s%3$s';

    public function render(MessageCollection $messages)
    {
        $output = '';

        foreach ($messages as $message) {
            $output .= sprintf(self::$template, ucfirst($message->get('_type')), $message->getMessage(), PHP_EOL);
        }

        return $output;
    }
}