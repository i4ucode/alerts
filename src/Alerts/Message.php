<?php
namespace Alerts;
/**
 * Represents a message which contains a message string an optionally an array of data to
 * associate with a message.
 * @package Alerts
 */
class Message
{
    /** @var string */
    protected $message;

    /** @var array */
    protected $data;

    public function __construct($message = '', array $data = [])
    {
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get the message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the message (string)
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


    /**
     * Get the array data associated with the message (may be empty)
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set an array of additional data to associate with the message.
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }


    /**
     * Check if the message data contains one or more key => val matches.
     * @param array $data
     * @return bool True if all conditions are satisfied with strict type checking.
     */
    public function contains(array $data = [])
    {
        // Check data
        foreach  ($data as $key => $val) {
            if (!isset($this->data[$key]) || $this->data[$key] !== $data[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the value for a given key within the message data
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Sets a key value within the message data.
     * @param $key
     * @param $val
     */
    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * Returns the message string (same as calling getMessage())
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }


    /**
     * Returns an array representation of the message:
     * [ 'message' => '...', 'data' => [ 'key' => 'val' ] ]
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => (string)$this->message,
            'data' => $this->data,
        ];
    }

}