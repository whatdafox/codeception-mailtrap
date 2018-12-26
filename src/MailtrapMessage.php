<?php

namespace Codeception\Module;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;

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
     * @var Client
     */
    protected $client;

    /**
     * @var string HTML body of the message
     */
    protected $html_body;

    /**
     * @var string Text body of the message
     */
    protected $text_body;

    /**
     * MailtrapMessage constructor.
     *
     * @param array $data
     * @param Client $client
     */
    public function __construct($data = [], Client $client)
    {
        $this->data = $data;
        $this->client = $client;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if (in_array($name, ['html_body', 'text_body'])) {
            return $this->getMessageData($name);
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Get the body data for a message
     *
     * @param $key
     *
     * @return bool|mixed|null|string
     */
    public function getMessageData($key)
    {
        if ($this->{$key}) {
            return $this->{$key};
        }

        $data_key = str_replace(['text_body', 'html_body'], ['txt_path', 'html_path'], $key);

        $data = $this->retrieveMessageData($data_key);

        if (!$data) {
            return '';
        }

        $this->{$key} = $data;

        return $data;
    }

    /**
     * Retrieve the body data from the MailTrap API
     *
     * @param $key
     *
     * @return bool|string
     */
    protected function retrieveMessageData($key)
    {
        $data = $this->client->get($this->data[$key])->getBody();

        if ($data instanceof Stream) {
            return $data->getContents();
        }

        return false;
    }
}
