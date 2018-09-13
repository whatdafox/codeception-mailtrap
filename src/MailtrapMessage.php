<?php

namespace Codeception\Module;

/**
 * Represents a message in the MailTrap inbox
 *
 */
class MailtrapMessage
{

    /**
     * @var array Message payload
     */
    protected $data;

    /**
     * MailtrapMessage constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }
}