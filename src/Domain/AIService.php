<?php

namespace Cangyan\AITool\Domain;

use Cangyan\AITool\Domain\Entities\Image;

interface AIService
{
    public function getIdCardRes(Image $image);
}