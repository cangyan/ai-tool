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

    /**
     * @group IdCard
     */
    public function testRecognitionIdCard1()
    {
        $result = AIServiceApp::recognitionIdCard(file_get_contents(__DIR__ . '/../deps/aip-ocr-php-sdk-1.1/demo/idcard.jpg'));

        $this->assertEquals($result['words_result']['姓名']['words'], '韦小宝');
    }

    /**
     * @group BankCard
     */
    public function testRecognitionBankCard1()
    {
        $result = AIServiceApp::recognitionBankCard(file_get_contents(__DIR__ . '/../deps/aip-ocr-php-sdk-1.1/demo/bankcard.jpg'));
        $this->assertEquals($result['result']['bank_card_number'], '8888 8888 8888 8888');
    }

    /**
     * @group general
     */
    public function testRecognitionGeneral1()
    {
        $result = AIServiceApp::recognitionGeneral(file_get_contents(__DIR__ . '/../deps/aip-ocr-php-sdk-1.1/demo/idcard.jpg'));

        $this->assertContains('韦小宝', json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}