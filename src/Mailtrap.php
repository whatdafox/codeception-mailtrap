<?php namespace Codeception\Module;

use Codeception\Module;
use GuzzleHttp\Client;

/**
 * This module allows you to test emails using Mailtrap <https://mailtrap.io>.
 * Please try it and leave your feedback.
 *
 * ## Project repository
 *
 * <https://github.com/WhatDaFox/codeception-mailtrap>
 *
 * ## Status
 *
 * * Maintainer: **Valentin Prugnaud**
 * * Stability: **dev**
 * * Contact: valentin@whatdafox.com
 *
 * ## Config
 *
 * * client_id: `string`, default `` - Your mailtrap API key.
 * * inbox_id: `string`, default `` - The inbox ID to use for the tests
 * * version: `string`, default `v1` - Version of the API to use, default to v1. (For future use)
 *
 * ## API
 *
 * * client - `\Guzzle\Http\Client` Guzzle client for API requests
 */
class Mailtrap extends Module
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $baseUrl = 'https://mailtrap.io/api/{version}/';

    /**
     * @var array
     */
    protected $config = ['client_id' => null, 'inbox_id' => null, 'version' => 'v1'];

    /**
     * @var array
     */
    protected $requiredFields = ['client_id', 'inbox_id'];

    /**
     * Initialize.
     *
     * @return void
     */
    public function _initialize()
    {
        $this->client = new Client([
            'base_url' => [$this->baseUrl, ['version' => $this->config['version']]],
            'defaults' => [
                'headers' => ['Api-Token' => $this->config['client_id']],
            ],
        ]);
    }

    /**
     * Clean the inbox after each scenario.
     *
     * @param \Codeception\TestCase $test
     */
    public function _after(\Codeception\TestCase $test)
    {
        $this->cleanInbox();
    }

    /**
     * Check if the latest email received contains $params.
     *
     * @param $params
     *
     * @return mixed
     */
    public function receiveAnEmail($params)
    {
        $message = $this->fetchLastMessage();
        foreach ($params as $param => $value) {
            $this->assertEquals($value, $message[$param]);
        }
    }

    /**
     * Check if the latest email received is from $senderEmail.
     *
     * @param $senderEmail
     *
     * @return mixed
     */
    public function receiveAnEmailFromEmail($senderEmail)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($senderEmail, $message['from_email']);
    }

    /**
     * Check if the latest email received is from $senderName.
     *
     * @param $senderName
     *
     * @return mixed
     */
    public function receiveAnEmailFromName($senderName)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($senderName, $message['from_name']);
    }

    /**
     * Check if the latest email was received by $recipientEmail.
     *
     * @param $recipientEmail
     *
     * @return mixed
     */
    public function receiveAnEmailToEmail($recipientEmail)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($recipientEmail, $message['to_email']);
    }

    /**
     * Check if the latest email was received by $recipientName.
     *
     * @param $recipientName
     *
     * @return mixed
     */
    public function receiveAnEmailToName($recipientName)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($recipientName, $message['to_name']);
    }

    /**
     * Check if the latest email received has the $subject.
     *
     * @param $subject
     *
     * @return mixed
     */
    public function receiveAnEmailWithSubject($subject)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($subject, $message['subject']);
    }

    /**
     * Check if the latest email received has the $textBody.
     *
     * @param $textBody
     *
     * @return mixed
     */
    public function receiveAnEmailWithTextBody($textBody)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($textBody, $message['text_body']);
    }

    /**
     * Check if the latest email received has the $htmlBody.
     *
     * @param $htmlBody
     *
     * @return mixed
     */
    public function receiveAnEmailWithHtmlBody($htmlBody)
    {
        $message = $this->fetchLastMessage();
        $this->assertEquals($htmlBody, $message['html_body']);
    }

    /**
     * Look for a string in the most recent email (Text).
     *
     * @param $expected
     *
     * @return mixed
     */
    public function seeInEmailTextBody($expected)
    {
        $email = $this->fetchLastMessage();
        $this->assertContains($expected, $email['text_body'], "Email body contains text");
    }

    /**
     * Look for a string in the most recent email (HTML).
     *
     * @param $expected
     *
     * @return mixed
     */
    public function seeInEmailHtmlBody($expected)
    {
        $email = $this->fetchLastMessage();
        $this->assertContains($expected, $email['html_body'], "Email body contains HTML");
    }

    /**
     * Get the most recent message of the default inbox.
     *
     * @return array
     */
    protected function fetchLastMessage()
    {
        $messages = $this->client->get("inboxes/{$this->config['inbox_id']}/messages")->json();

        return array_shift($messages);
    }

    /**
     * Clean all the messages from inbox.
     *
     * @return void
     */
    public function cleanInbox()
    {
        $this->client->patch("inboxes/{$this->config['inbox_id']}/clean");
    }
}
