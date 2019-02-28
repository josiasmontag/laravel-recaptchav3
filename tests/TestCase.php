<?php
/**
 * Created by Josias Montag
 * Date: 10/30/18 11:09 AM
 * Mail: josias@montag.info
 */

namespace Lunaweb\RecaptchaV3\Tests;

use Lunaweb\RecaptchaV3\Providers\RecaptchaV3ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Mockery;
use GuzzleHttp\Psr7\Response;

abstract class TestCase extends OrchestraTestCase
{

    protected $http;



    public function setUp(): void
    {
        parent::setUp();
        $this->mockGuzzle();
    }

    protected function getPackageProviders($app)
    {
        return [
            RecaptchaV3ServiceProvider::class,
        ];
    }
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptchav3.sitekey', 'some_sitekey');
        $app['config']->set('recaptchav3.secret', 'some_secret');


    }

    protected function mockGuzzle() {
        $this->http = Mockery::mock(\GuzzleHttp\Client::class);
        $this->app->instance(\GuzzleHttp\Client::class, $this->http);
    }


    protected function mockRecaptchaResponse($token, $response)
    {

        $this->http
            ->shouldReceive('request')
            ->with('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $this->app['config']->get('recaptchav3.secret'),
                    'response' => $token,
                    'remoteip' => '127.0.0.1',
                ]
            ])
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $response));


    }

}
