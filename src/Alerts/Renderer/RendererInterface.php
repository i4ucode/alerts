<?php
namespace Alerts\Renderer;

use Alerts\MessageCollection;

interface RendererInterface {
    /**
     * Render the supplied message collection
     * @param MessageCollection $messages
     * @return mixed
     */
    public function render(MessageCollection $messages);
}