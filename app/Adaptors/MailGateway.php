<?php


namespace App\Adaptors;

use Illuminate\Support\Facades\Mail;

/**
 * Class SitamaGateway
 * @package App\adaptor
 */
class MailGateway
{

    /**
     * @param $email
     * @param $name
     * @param $otp
     */
    public function sendMail($email, $name, $content)
    {
        Mail::raw($content, function ($message) use ($email) {
            $message
                ->from('noreply@homestead.com', 'homestead.com')
                ->to($email)
                ->subject("homestead.com [homestead.com]");
        });
    }

    /**
     * @param $email
     * @param $name
     * @param $otp
     */
    public function sendMails($content)
    {
        $emails = ['homestead@gmail.com'];
        Mail::raw($content, function ($message) use ($emails) {
            $message
                ->from('noreply@homestead.com', 'homestead.com')
                ->to($emails)
                ->subject("homestead.com [homestead.com]");
        });
    }
}
