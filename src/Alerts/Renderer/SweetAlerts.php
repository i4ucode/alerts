<?php
namespace Alerts\Renderer;


use Alerts\AlertType;
use Alerts\Message;
use Alerts\MessageCollection;

class SweetAlerts implements RendererInterface
{
    protected $wrapOutput = true;
    protected $buttonText = 'OK';
    protected $successTitle = 'Success!';
    protected $warningTitle = 'Warning';
    protected $noticeTitle = 'Notice';
    protected $errorTitle = 'Error!';

    public function render(MessageCollection $messages)
    {
        $output = '';

        if ($messages->count()) {
            if ($this->wrapOutput) {
                $output .= "<script type=\"text/javascript\">\n";
            }

            foreach ($messages as $message) {
                switch ($message->get('_type')) {
                    case AlertType::ERROR:
                        $output .= $this->renderError($message);
                        break;
                    case AlertType::NOTICE:
                        $output .= $this->renderNotice($message);
                        break;
                    case AlertType::WARNING:
                        $output .= $this->renderWarning($message);
                        break;
                    case AlertType::SUCCESS:
                        $output .= $this->renderSuccess($message);
                        break;
                    default:
                        // Don't know how to handle this type
                        break;
                }

            }

            if ($this->wrapOutput) {
                $output .= "</script>\n";
            }
        }



        return $output;
    }

    protected function renderError(Message $message)
    {
        $swal = [
            'title' => $message->get('title', $this->errorTitle),
            'text' => $message->getMessage(),
            'type' => 'error',
            'confirmButtonText' => $message->get('buttonText', $this->buttonText),
            'html' => $message->get('html', false),
        ];

        return $this->renderSweetAlert($swal);
    }

    protected function renderWarning(Message $message)
    {
        $swal = [
            'title' => $message->get('title', $this->warningTitle),
            'text' => $message->getMessage(),
            'type' => 'warning',
            'confirmButtonText' => $message->get('buttonText', $this->buttonText),
            'html' => $message->get('html', false),
        ];

        return $this->renderSweetAlert($swal);
    }

    protected function renderNotice(Message $message)
    {
        $swal = [
            'title' => $message->get('title', $this->noticeTitle),
            'text' => $message->getMessage(),
            'type' => 'info',
            'confirmButtonText' => $message->get('buttonText', $this->buttonText),
            'html' => $message->get('html', false),
        ];

        return $this->renderSweetAlert($swal);
    }

    protected function renderSuccess(Message $message)
    {
        $swal = [
            'title' => $message->get('title', $this->successTitle),
            'text' => $message->getMessage(),
            'type' => 'success',
            'confirmButtonText' => $message->get('buttonText', $this->buttonText),
            'html' => $message->get('html', false),
        ];

        return $this->renderSweetAlert($swal);
    }

    protected function renderSweetAlert(array $swal = [])
    {
        return 'swal('.json_encode($swal, JSON_FORCE_OBJECT).');'."\n";
    }

}