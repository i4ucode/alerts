<?php
namespace Alerts;

use Alerts\Storage\StorageInterface;
use Alerts\Renderer\RendererInterface;

/**
 * Provides a simple interface for managing, filtering and rendering a list of alerts.
 *
 * @package Alerts
 * @author Jodie Dunlop <jodie@i4u.com.au>
 */


class Alerts implements \IteratorAggregate, \Countable
{
    /** @var MessageCollection  */
    protected $messages;

    /** @var RendererInterface */
    protected $renderer;

    /** @var  StorageInterface */
    protected $storage;

    public function __construct(RendererInterface $renderer = null, StorageInterface $storage = null)
    {
        $this->render = $renderer;
        $this->storage = $storage;
        $this->messages = new MessageCollection([], $renderer);

        $this->retrieve();
    }

    /**
     * Get the current (default) renderer for outputting alerts
     * @return RendererInterface|null
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set the default renderer
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->messages->setRenderer($renderer);
    }

    /**
     * Get the current (default) storage interface for persisting alert messages
     * @return StorageInterface|null
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set the default storage interface for persisting alert messages
     * Note: Calling this method will clobber any existing messages and automatically call retrieve()
     * to fetch from the storage layer.
     * @param StorageInterface $storage
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->clear();
        $this->retrieve();
    }

    /**
     * Add a message to the collection
     * @param string $message The message to be added
     * @param array $data Optional array data to associated with the message
     * @return $this
     */
    public function add($message, array $data = [])
    {
        $this->messages->add(new Message($message, $data));

        return $this;
    }

    /**
     * Add a success message
     * @param string $message
     * @param array $data
     * @return Alerts
     */
    public function success($message, array $data = [])
    {
        $data = array_merge($data, [ '_type' => AlertType::SUCCESS]);
        return $this->add($message, $data);
    }

    /**
     * Get a list of success messages
     * @param array $filterData
     * @return MessageCollection
     */
    public function successes(array $filterData = [])
    {
        $filterData = array_merge($filterData, [ '_type' => AlertType::SUCCESS]);
        return $this->filter($filterData);
    }

    /**
     * Add a notice message
     * @param $message
     * @param array $data
     * @return Alerts
     */
    public function notice($message, array $data = [])
    {
        $data = array_merge($data, [ '_type' => AlertType::NOTICE]);
        return $this->add($message, $data);

    }

    /**
     * Get a list of notices
     * @param array $filterData
     * @return MessageCollection
     */
    public function notices(array $filterData = [])
    {
        $filterData = array_merge($filterData, [ '_type' => AlertType::NOTICE]);
        return $this->filter($filterData);
    }

    /**
     * Add a warning message
     * @param $message
     * @param array $data
     * @return Alerts
     */
    public function warning($message, array $data = [])
    {
        $data = array_merge($data, [ '_type' => AlertType::WARNING]);
        return $this->add($message, $data);

    }

    /**
     * Return a list of warnings
     * @param array $filterData
     * @return MessageCollection
     */
    public function warnings(array $filterData = [])
    {
        $filterData = array_merge($filterData, [ '_type' => AlertType::WARNING]);
        return $this->filter($filterData);
    }

    /**
     * Add an error
     * @param $message
     * @param array $data
     * @return Alerts
     */
    public function error($message, array $data = [])
    {
        $data = array_merge($data, [ '_type' => AlertType::ERROR]);
        return $this->add($message, $data);
    }

    /**
     * Return only those messages with a _type of ERROR
     * @param array $filterData Optionally pass additional filter key values that must be satisfied.
     * @return MessageCollection
     */
    public function errors(array $filterData = [])
    {
        $filterData = array_merge($filterData, [ '_type' => AlertType::ERROR]);
        return $this->filter($filterData);
    }

    /**
     * Returns all of the messages.
     * @return MessageCollection
     */
    public function all()
    {
        return $this->messages;
    }

    /**
     * Filter the messages by
     * @param array $filterData
     * @return MessageCollection
     */
    public function filter(array $filterData = [])
    {
        return $this->all()->filter($filterData);
    }

    /**
     * @param array $filterData
     * @return MessageCollection
     */
    public function filterWithout(array $filterData = [])
    {
        //print_r($filterData);
        return $this->all()->filterWithout($filterData);
    }

    /**
     * @return int Total alert messages
     */
    public function count()
    {
        return count($this->messages);
    }


    /**
     * Implements the IteratorAggregate interface by returning the MessageCollection object which contains all
     * the alert messages.
     * @return MessageCollection
     */
    public function getIterator()
    {
        return $this->all();
    }

    /**
     * Return all of the messages as an array.
     * This is a shortcut method which is the same as calling $alerts->all()->toArray()
     * @return array
     */
    public function toArray()
    {
        return $this->all()->toArray();
    }

    /**
     * Renders the alert messages as a string by using either the default renderer or the supplied renderer object.
     * If no renderer is defined a RuntimeException will be thrown.
     *
     * @param array $filterData
     * @param RendererInterface $renderer
     * @return string
     */
    public function render(RendererInterface $renderer = null, array $filterData = [])
    {
        if (!$renderer) {
            if (!$this->renderer) {
                throw new \RuntimeException('Unable to render Alerts, no renderer configured');
            }
            $renderer = $this->renderer;
        }

        // Get messages to be rendered
        $messages = !empty($filterData) ? $this->filter($filterData) : $this->all();
        //print_r($messages);

        // Pass a MessageCollection to the renderer object
        return $messages->render($renderer);
    }

    /**
     * Explicitly call to persist the messages via the storage engine.
     * Note: this is called automatically by the destructor and will not normally need to be called manually.
     * @return bool Returns false when there is no storage engine is present.
     */
    public function store()
    {
        if ($this->storage) {
            $this->storage->store($this->messages);
            return true;
        }

        return false;
    }

    /**
     * Retrieve a list of messages from the storage engine.  This is called automatically during construction
     * and should not
     * @return bool Returns false when no storage engine is present
     */
    public function retrieve()
    {
        if ($this->storage) {
            $this->storage->retrieve($this->messages);
            return true;
        }

        return false;
    }

    /**
     * Clears all the messages
     */
    public function clear()
    {
        $this->messages->clear();
    }

    public function __destruct()
    {
        $this->store();
    }

    public function __toString()
    {
        return $this->renderer ? $this->render() : '';
    }
}
