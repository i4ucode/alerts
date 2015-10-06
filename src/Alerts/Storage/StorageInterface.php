<?php
namespace Alerts\Storage;

use Alerts\MessageCollection;

interface StorageInterface {

    /**
     * Store the message collection
     * @param MessageCollection $messages
     * @return void
     */
    public function store(MessageCollection $messages);

    /**
     * Retrieve messages into the MessageCollection
     * @param MessageCollection $messages
     * @return void
     */
    public function retrieve(MessageCollection $messages);
}