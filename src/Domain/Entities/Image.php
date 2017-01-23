<?php

namespace Cangyan\AITool\Domain\Entities;

class Image
{
    private $image;

    /**
     * Image constructor.
     * @param $image
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return base64_encode($this->image);
    }
}