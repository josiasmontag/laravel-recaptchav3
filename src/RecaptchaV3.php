<?php
/**
 * Created by Josias Montag
 * Date: 10/30/18 11:04 AM
 * Mail: josias@montag.info
 */

namespace Lunaweb\RecaptchaV3;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class RecaptchaV3
{

    /**
     * @var string
     */
    protected $secret;
    /**
     * @var string
     */
    protected $sitekey;
    /**
     * @var string
     */
    protected $origin;
    /**
     * @var string
     */
    protected $locale;
    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * RecaptchaV3 constructor.
     *
     * @param $secret
     * @param $sitekey
     */
    public function __construct(Repository $config, Client $client, Request $request, Application $app)
    {
        $this->secret = $config['recaptchav3']['secret'];
        $this->sitekey = $config['recaptchav3']['sitekey'];
        $this->origin = $config['recaptchav3']['origin'] ?? 'https://www.google.com/recaptcha';
        $this->locale = $config['recaptchav3']['locale'] ?? $app->getLocale();
        $this->http = $client;
        $this->request = $request;
    }


    /*
     * Verify the given token and retutn the score.
     * Returns false if token is invalid.
     * Returns the score if the token is valid.
     *
     * @param $token
     */
    public function verify($token, $action = null)
    {

        $response = $this->http->request('POST', $this->origin . '/api/siteverify', [
            'form_params' => [
                'secret'   => $this->secret,
                'response' => $token,
                'remoteip' => $this->request->getClientIp(),
            ],
        ]);


        $body = json_decode($response->getBody(), true);

        if (!isset($body['success']) || $body['success'] !== true) {
            return false;
        }

        if ($action && (!isset($body['action']) || $action != $body['action'])) {
            return false;
        }


        return isset($body['score']) ? $body['score'] : false;

    }


    /**
     * @return string
     */
    public function sitekey()
    {
        return $this->sitekey;
    }

    /**
     * @return string
     */
    public function initJs()
    {
        return '<script src="' . $this->origin . '/api.js?hl=' . $this->locale . '&render=' . $this->sitekey . '"></script>';
    }


    /**
     * @param $action
     */
    public function field($action, $name = 'g-recaptcha-response')
    {
        $fieldId = uniqid($name . '-', false);
        $html = '<input type="hidden" name="' . $name . '" id="' . $fieldId . '">';
        $html .= "<script>
  grecaptcha.ready(function() {
      grecaptcha.execute('" . $this->sitekey . "', {action: '" . $action . "'}).then(function(token) {
         document.getElementById('" . $fieldId . "').value = token;
      });
  });
  </script>";
        return $html;
    }


}
