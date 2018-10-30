<?php
/**
 * Created by Josias Montag
 * Date: 10/30/18 11:52 AM
 * Mail: josias@montag.info
 */


namespace Lunaweb\RecaptchaV3\Tests\Integration;



use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Lunaweb\RecaptchaV3\Tests\TestCase;



class VerifyTest extends TestCase
{



    public function testVerifyWithInvalidToken()
    {
        $this->mockRecaptchaResponse('invalid_token', '{
                  "success": false,
                  "error-codes": [
                    "invalid-input-secret"
                  ]
                }');

        $this->assertFalse(RecaptchaV3::verify('invalid_token'));

    }


    public function testVerifyWithValidToken()
    {
        $this->mockRecaptchaResponse('valid_token', '{
                  "success": true,
                  "score": 0.5,
                  "action": "my_action"
                }');

        $this->assertEquals(0.5, RecaptchaV3::verify('valid_token'));

    }



    public function testVerifiesAction()
    {
        $this->mockRecaptchaResponse('valid_token', '{
                  "success": true,
                  "score": 0.5,
                  "action": "my_action"
                }');

        $this->assertFalse(RecaptchaV3::verify('valid_token', 'other_action'));
        $this->assertEquals(0.5, RecaptchaV3::verify('valid_token', 'my_action'));

    }





}
