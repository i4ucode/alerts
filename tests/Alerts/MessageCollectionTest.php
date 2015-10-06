<?php
/**
 * Created by PhpStorm.
 * User: jd
 * Date: 7/09/2015
 * Time: 12:11 PM
 */

namespace Alerts;


class MessageCollectionTest extends \PHPUnit_Framework_TestCase {

    /** @var  MessageCollection */
    protected $messages;

    public function setup()
    {
        $this->messages = new MessageCollection();
    }

    public function testEmptyMessages()
    {
        $this->assertEmpty($this->messages->all());
    }

    public function testMessagesCount()
    {
        $this->assertEquals(0, $this->messages->count());
        $this->messages->add(new Message('First Message'));
        $this->assertEquals(1, $this->messages->count());
    }

    public function testAddMessage()
    {
        $this->messages->add(new Message('One'));
        $this->assertEquals('One', $this->messages->get(0));
    }




}
