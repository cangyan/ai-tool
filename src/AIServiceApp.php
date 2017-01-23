<?php

namespace Cangyan\AITool;

use Cangyan\AITool\Domain\Entities\Image;

class AIServiceApp
{
    public static function recognitionIdCard($image)
    {
        $eImage = new Image($image);
        $service = AIServiceFactory::getBaiDuAIService();
        $res = $service->getIdCardRes($eImage);
        return $res;
    }

    public static function recognitionBankCard($image)
    {
        $eImage = new Image($image);
        $service = AIServiceFactory::getBaiDuAIService();
        $res = $service->getBankCardRes($eImage);
        
        return $res;
    }

    public static function recognitionGeneral($image)
    {
        $eImage = new Image($image);
        $service = AIServiceFactory::getBaiDuAIService();
        $res = $service->getGeneral($eImage);

        return $res;
    }
}