<?php

namespace Cangyan\AITool\Test;

use Cangyan\AITool\AIServiceApp;

class AIServiceAppTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testRecognitionIdCard1()
    {
        $result = AIServiceApp::recognitionIdCard();

        $this->assertTrue($result);
    }

    public function testRecognitionBankCard1()
    {
        $result = AIServiceApp::recognitionBankCard();

        $this->assertTrue($result);
    }
}