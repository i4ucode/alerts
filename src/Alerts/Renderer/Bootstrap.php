<?php
namespace Alerts\Renderer;

use Alerts\MessageCollection;

class Bootstrap implements RendererInterface {

    /**
     * Render the given MessageCollection as Bootstrap HTML
     * See: http://getbootstrap.com/
     *
     * @param MessageCollection $messages
     * @return string
     */
    public function render(MessageCollection $messages)
    {
        $output = '';

        foreach ($messages->pluck('_type') as $type) {
            switch ($type) {
                case 'success':
                    $output .=
                        '<div class="alert alert-success">' .
                        implode("<br>", $messages->filter(['_type' => $type])->getMessages()) .
                        '</div>';
                    break;
                case 'notice':
                    $output .=
                        '<div class="alert alert-info">' .
                        implode("<br>", $messages->filter(['_type' => $type])->getMessages()) .
                        '</div>';
                    break;
                case 'warning':
                    $output .=
                        '<div class="alert alert-warning">' .
                        implode("<br>", $messages->filter(['_type' => $type])->getMessages()) .
                        '</div>';
                    break;
                case 'error':
                    $output .=
                        '<div class="alert alert-danger">' .
                        implode("<br>", $messages->filter(['_type' => $type])->getMessages()) .
                        '</div>';
                    break;
            }
        }


        return $output;
    }

}
