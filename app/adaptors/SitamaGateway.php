<?php


namespace App\adaptors;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Class SitamaGateway
 * @package App\adaptor
 */
class SitamaGateway
{

    /**
     * @return \Illuminate\Http\Client\Response
     */
    protected function postOAuthToken()
    {
        return Http::post('https://smsgw.sitama.co.id/api/oauth/token', [
            'Username' => 'grosirmobil',
            'password' => 'APIgm@2020'
        ]);
    }

    /**
     * @param $optCode
     * @param $phoneNumber
     */
    public function sendOtp($optCode, $phoneNumber)
    {
        $response = $this->postOAuthToken();
        $token = json_decode($response, true)['access_token'];
        Http::withToken($token)->post('https://smsgw.sitama.co.id/api/SMS/smssitama', [
            "notelp" => $phoneNumber,
            "textsms" => "agreesip.com%0A{$optCode} is your Agreesip verification code",
            "desc" => "Agreesip"
        ]);
    }

    /**
     * @param $email
     * @param $name
     * @param $otp
     */
    public function sendMail($email, $name, $content)
    {
        Mail::raw($content, function ($message) use ($email) {
            $message
                ->from('noreply@agreesip.com', 'Agreesip')
                ->to($email)
                ->subject("Agreesip [PT Solusi Integrasi Pratama]");
        });
    }

    /**
     * @param $email
     * @param $name
     * @param $otp
     */
    public function sendMails($content)
    {
        $emails = ['agreesip@gmail.com','erwin.tambuwun@sitama.co.id','dickyciptapradana@gmail.com','sitama.developer@gmail.com'];
        Mail::raw($content, function ($message) use ($emails) {
            $message
                ->from('noreply@agreesip.com', 'Agreesip')
                ->to($emails)
                ->subject("Agreesip [PT Solusi Integrasi Pratama]");
        });
    }

}
