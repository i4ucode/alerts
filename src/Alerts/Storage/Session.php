<?php
namespace Alerts\Storage;

use Alerts\Message;
use Alerts\MessageCollection;

class Session implements StorageInterface
{
    protected $sessionKey;

    public function __construct($key = '_alerts')
    {
        if (!session_id()) {
            session_start();
        }

        $this->sessionKey = $key;
    }

    public function store(MessageCollection $messages)
    {
        $data = array();
        foreach ($messages as $message) {
            $data[] = [ (string)$message->getMessage(), (array)$message->getData() ];
        }

        if (!empty($data)) {
            $_SESSION[$this->sessionKey] = $data;
        }
    }

    public function retrieve(MessageCollection $messages)
    {
        if (isset($_SESSION[$this->sessionKey])) {
            foreach ($_SESSION[$this->sessionKey] as $msg) {
                $messages->add(new Message($msg[0], $msg[1]));
            }

            // Remove from session
            unset($_SESSION[$this->sessionKey]);
        }
    }
}