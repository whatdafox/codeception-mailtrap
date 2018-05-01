<?php

namespace Codeception\Module;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;

/**
 * This module allows you to test emails using Mailtrap <https://mailtrap.io>.
 * Please try it and leave your feedback.
 *
 * ## Project repository
 *
 * <https://github.com/WhatDaFox/Codeception-Mailtrap>
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
 * * cleanup: `boolean`, default `true` - Clean the inbox after each scenario
 *
 * ## API
 *
 * * client - `GuzzleHttp\Client` Guzzle client for API requests
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
    protected $baseUrl = 'https://mailtrap.io/api/v1/';

    /**
     * @var array
     */
    protected $config = ['client_id' => null, 'inbox_id' => null, 'cleanup' => true];

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
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Api-Token' => $this->config['client_id'],
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
        if ($this->config['cleanup']) {
            $this->cleanInbox();
        }
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
     * Get the most recent message of the default inbox.
     *
     * @return array
     */
    public function fetchLastMessage()
    {
        $messages = $this->client->get("inboxes/{$this->config['inbox_id']}/messages")->getBody();

        if ($messages instanceof Stream) {
            $messages = $messages->getContents();
        }

        $messages = json_decode($messages, true);

        return array_shift($messages);
    }

    /**
     * Gets the attachments on the last message.
     *
     * @return array
     */
    public function fetchAttachmentsOfLastMessage()
    {
        $email = $this->fetchLastMessage();
        $response = $this->client->get("inboxes/{$this->config['inbox_id']}/messages/{$email['id']}/attachments")->getBody();

        return json_decode($response, true);
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
        $this->assertContains($expected, $email['text_body'], 'Email body contains text');
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
        $this->assertContains($expected, $email['html_body'], 'Email body contains HTML');
    }

	/**
	 * Look for a string in the most recent email subject.
	 *
	 * @param string $expected
	 *
	 * @return mixed
	 */
	public function seeInEmailSubject($expected)
	{
		$email = $this->fetchLastMessage();
		$this->assertContains($expected, $email['subject'], 'Email subject contains text');
	}

    /**
     * Look for an attachment on the most recent email.
     *
     * @param $count
     */
    public function seeAttachments($count)
    {
        $attachments = $this->fetchAttachmentsOfLastMessage();
        $this->assertEquals($count, count($attachments));
    }

    /**
     * Look for an attachment on the most recent email.
     *
     * @param $bool
     */
    public function seeAnAttachment($bool)
    {
        $attachments = $this->fetchAttachmentsOfLastMessage();
        $this->assertEquals($bool, count($attachments) > 0);
    }

    /**
     * Get the most recent messages of the default inbox.
     *
     * @param int $number
     *
     * @return array
     */
    public function fetchLastMessages($number = 1)
    {
        $messages = $this->client->get("inboxes/{$this->config['inbox_id']}/messages")->getBody();

        if ($messages instanceof Stream) {
            $messages = $messages->getContents();
        }

        $messages = json_decode($messages, true);

        $firstIndex = count($messages) - $number;

        $messages = array_slice($messages, $firstIndex, $number);

        $this->assertCount($number, $messages);

        return $messages;
    }

    /**
     * Get the bcc property of a message
     *
     * @param int $messageId
     *
     * @return string
     */
    public function getBccEmailOfMessage($messageId)
    {
        $message = $this->client->get("inboxes/{$this->config['inbox_id']}/messages/$messageId/body.eml")->getBody();
        
        if ($message instanceof Stream) {
            $message = $message->getContents();
        }
        $matches = [];
        preg_match('/Bcc:\s[\w.-]+@[\w.-]+\.[a-z]{2,6}/', $message, $matches);

        $bcc = substr(array_shift($matches), 5);

        return $bcc;
    }
}
