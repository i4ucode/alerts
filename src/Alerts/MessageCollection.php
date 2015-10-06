<?php
namespace Alerts;

use Alerts\Renderer\RendererInterface;

/**
 * Provides a container for a list of Message objects.  Includes methods for filtering and managing Messages.
 * @package Alerts
 */
class MessageCollection implements \Iterator, \Countable, \ArrayAccess
{
    /** @var Message[]  */
    protected $messages;

    /** @var RendererInterface  */
    protected $renderer;

    /** @var int  Iterator cursor */
    protected $position = 0;

    public function __construct(array $messages = [], RendererInterface $renderer = null)
    {
        $this->messages = $messages;
        $this->renderer = $renderer;
        $this->position = 0;
    }

    /**
     * Get the current renderer (if set)
     * @return RendererInterface|null
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set the renderer which will be used by the render() method to return a string representation
     * of the message collection.
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Add a Message object on to the collection
     * @param Message $message
     */
    public function add(Message $message)
    {
        $this->messages[] = $message;
    }
    /**
     * Get a Message object by given index
     * @param $index
     * @return Message|null
     */
    public function get($index)
    {
        return isset($this->messages[$index]) ? $this->messages[$index] : null;
    }

    /**
     * Set (replace) the Message object for a given index
     * @param $index
     * @param Message $message
     */
    public function set($index, Message $message)
    {
        $this->messages[$index] = $message;
    }

    /**
     * Get the number of messages
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Returns a new MessageCollection object containing all the messages
     * @return MessageCollection
     */
    public function all()
    {
        return new static($this->messages, $this->renderer);
    }

    /**
     * Creates a new MessageCollection from messages that match the supplied filter data
     * @param array $filterData
     * @param callable|null $callback
     * @return MessageCollection
     */
    public function filter(array $filterData = [], callable $callback = null)
    {
        $func = isset($callback) ? $callback : [$this, 'filterCallback'];

        $messages = array_filter($this->messages, function(Message $message) use ($func, $filterData) {
            return call_user_func($func, $message, $filterData);
        });

        return new static(array_values($messages), $this->renderer);
    }

    /**
     * Creates a new MessageCollection from messages that match the supplied filter data
     * @param array $filterData
     * @return MessageCollection
     */
    public function filterWithout(array $filterData = [])
    {
        return $this->filter($filterData, [$this, 'filterWithoutCallback']);
    }

    /**
     * The default callback for the filter() function. Returns true if the item matches the filter data.
     * This can be overridden in a sub-class if necessary.
     *
     * @param Message $message
     * @param array $filterData
     * @return bool
     */
    protected function filterCallback(Message $message, array $filterData  = [])
    {
        return $message->contains($filterData) ? true : false;
    }

    /**
     * The default callback for the filterWithout() function.  Returns true if the item does NOT match the filter data.
     * @param Message $message
     * @param array $filterData
     * @return bool
     */
    protected function filterWithoutCallback(Message $message, array $filterData  = [])
    {
        return $message->contains($filterData) ? false : true;
    }

    /**
     * Clears all messages by resetting the internal array
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * Checks if a Message exists at the given offset
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }

    /**
     * Gets a Message via the given offset
     * @param mixed $offset
     * @return Message|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Sets (replaces) a Message at the given offset
     * @param mixed $offset
     * @param mixed $val Must be an instance of Message
     */
    public function offsetSet($offset, $val)
    {
        if (!($val instanceof Message)) {
            throw new \InvalidArgumentException('Value must be of type Message');
        }

        if (is_null($offset)) {
            $this->messages[] = $val;
        } else {
            $this->messages[$offset] = $val;
        }
    }

    /**
     * Unsets a Message at the given offset
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->messages[$offset]);
    }

    /**
     * Resets the iterator cursor
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Plucks a field value from each message in the collection and returns it as a unique array.
     * @param string $field
     * @return array
     */
    public function pluck($field)
    {
        $values = array_map(function(Message $message) use ($field) {
            return $message->get($field);
        }, $this->messages);

        return array_unique($values);
    }

    /**
     * Gets the current Message based on the cursor position
     * @return Message
     */
    public function current()
    {
        return $this->messages[$this->position];
    }


    /**
     * Gets the current cursor position (key)
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Increment the iterator cursor position
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Check if there is a Message object at the current cursor position
     * @return bool
     */
    public function valid()
    {
        return isset($this->messages[$this->position]);
    }

    /**
     * Return message collection as an array of message strings
     * @return array
     */
    public function getMessages()
    {
        return array_map(function(Message $message) {
            return $message->getMessage();
        }, $this->messages);
    }

    /**
     * Return message collection as an array, suitable for serializing (eg. to JSON)
     * @return array
     */
    public function toArray()
    {
        return array_map(function(Message $message) {
            return $message->toArray();
        }, $this->messages);
    }

    /**
     *
     * @param RendererInterface|null $renderer
     * @return mixed
     */
    public function render(RendererInterface $renderer = null)
    {
        if (!$renderer) {
            if (!$this->renderer) {
                throw new \RuntimeException('Unable to render messages - no renderer defined');
            }
            $renderer = $this->renderer;
        }

        return $renderer->render($this);
    }

    public function __toString()
    {
        return $this->render();
    }
}
