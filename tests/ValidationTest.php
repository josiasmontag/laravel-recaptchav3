<?php
/**
 * Created by Josias Montag
 * Date: 10/30/18 12:58 PM
 * Mail: josias@montag.info
 */

namespace Lunaweb\RecaptchaV3\Tests;


use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class ValidationTest extends TestCase
{

    public function testInvalidToken()
    {

        RecaptchaV3::shouldReceive('verify')
            ->with('invalid_token', 'register')
            ->once()
            ->andReturn(false);

        $rules = [
            'g-recapctha-response' => 'required|recaptchav3:register,0.5'
        ];


        $data = [
            'g-recapctha-response' => 'invalid_token'
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertFalse($v->passes());


    }

    public function testValidToken()
    {


        RecaptchaV3::shouldReceive('verify')
            ->with('valid_token', 'register')
            ->once()
            ->andReturn(0.4);

        $rules = [
            'g-recapctha-response' => 'required|recaptchav3:register,0.3'
        ];


        $data = [
            'g-recapctha-response' => 'valid_token'
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());


    }

}
