<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

class ZeptoMailTransport extends AbstractTransport
{

    /**
     * Create a new message instance.
     */
    public function __construct(private string $token, private string $endpoint)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $recipients = fn (array $addresses) => collect($addresses)
            ->map(fn (Address $a) => ['email_address' => [
                'address' => $a->getAddress(),
                'name' => $a->getName() ?: $a->getAddress(),
            ]])->values()->all();

        $from = $email->getFrom()[0];

        $payload = array_filter([
            'from' => ['address' => $from->getAddress(), 'name' => $from->getName()],
            'to' => $recipients($email->getTo()),
            'cc' => $recipients($email->getCc()),
            'bcc' => $recipients($email->getBcc()),
            'subject' => $email->getSubject(),
            'htmlbody' => $email->getHtmlBody(),
            'textbody' => $email->getTextBody(),
        ]);

        Http::withHeaders([
            'Authorization' => str_starts_with($this->token, 'Zoho-enczapikey')
                ? $this->token
                : 'Zoho-enczapikey '.$this->token,
            'Accept' => 'application/json',
        ])->post($this->endpoint, $payload)->throw();
    }

    public function __toString(): string
    {
        return 'zeptomail';
    }
}
