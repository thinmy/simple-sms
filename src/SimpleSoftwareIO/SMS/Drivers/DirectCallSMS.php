<?php namespace SimpleSoftwareIO\SMS\Drivers;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use SimpleSoftwareIO\SMS\OutgoingMessage;
use GuzzleHttp\Client;

class DirectCallSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://api.directcallsoft.com';

    /**
    * The Guzzle HTTP Client
    *
    * @var \GuzzleHttp\Client
    *
    */
    protected $client;

    /**
     * Create the CallFire instance.
     *
     * @param accessToken
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The SMS message instance.
     * @return void
     */
    public function send(OutgoingMessage $message)
    {
        $from = $message->getFrom();
        $to = $message->getTo();
        $composeMessage = $message->composeMessage();

        $data = [
            'origem' => $from,
            'destino' => $to,
            'tipo' => 'texto',
            'texto' => $composeMessage,
            'format' => 'JSON',
        ];

        $this->buildCall('/sms/send');
        $this->buildBody($data);

        $this->postRequest();
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param $rawMessage
     * @return mixed
     */
    protected function processReceive($rawMessage)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom((string)$rawMessage->msg->origem);
        $incomingMessage->setMessage((string)$rawMessage->msg->texto);
        $incomingMessage->setTo((string)$rawMessage->msg->destino);
        $incomingMessage->setDate((string)$rawMessage->msg->dataHora);

        return $incomingMessage;
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     * @return array
     */
    public function checkMessages(Array $options = array())
    {
        $this->buildCall('/sms/recebimento');

        $rawMessages = $this->getRequest()->json();

        return $this->makeMessages($rawMessages->Text);
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param $messageId
     * @return IncomingMessage
     * @throws \RuntimeException
     */
    public function getMessage($messageId)
    {
        throw new \RuntimeException('DirectCall search message is not supported.');
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param $raw
     * @return \SimpleSoftwareIO\SMS\IncomingMessage|void
     * @throws \RuntimeException
     */
    public function receive($raw)
    {
        throw new \RuntimeException('DirectCall push messages is not supported.');
    }

}
