<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class BrevoMailService
{
    /**
     * Kirim email via Brevo Transactional Email
     *
     * @param string $to
     * @param string $subject
     * @param string $html
     * @return Response
     */
    public static function send(string $to, string $subject, string $html): Response
    {
        return Http::withHeaders([
            'api-key'      => config('services.brevo.api_key'),
            'accept'       => 'application/json',
            'content-type' => 'application/json',
        ])->post(config('services.brevo.endpoint'), [
            'sender' => [
                'email' => config('mail.from.address'),
                'name'  => config('mail.from.name'),
            ],
            'to' => [
                ['email' => $to],
            ],
            'subject'     => $subject,
            'htmlContent' => $html,
        ]);
    }
}
