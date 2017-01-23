<?php

namespace Cangyan\AITool\Test\Domain\Entities;

use Cangyan\AITool\Domain\Entities\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testImage1()
    {
        $str = 'aabbcc';
        $target = base64_encode($str);

        $eImage = new Image($str);

        $this->assertEquals($eImage->getImage(), $target);
    }
}