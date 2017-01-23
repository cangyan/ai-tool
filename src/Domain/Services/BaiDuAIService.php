<?php

namespace Cangyan\AITool\Domain\Services;

use Cangyan\AITool\Domain\AIService;
use Cangyan\AITool\Domain\Entities\Image;

require_once __DIR__ . '/../../../deps/aip-ocr-php-sdk-1.1/AipOcr.php';

class BaiDuAIService implements AIService
{
    private $client;

    /**
     * BaiDuAIService constructor.
     * @param $appId
     * @param $appKey
     * @param $secretKey
     */
    public function __construct($appId, $appKey, $secretKey)
    {
        $this->client = new \AipOcr($appId, $appKey, $secretKey);
    }

    public function getIdCardRes(Image $image)
    {
        return $this->client->idcard(base64_decode($image->getImage()), true);
    }

    public function getBankCardRes(Image $image)
    {
        return $this->client->bankcard(base64_decode($image->getImage()));
    }

    public function getGeneral(Image $image)
    {
        return $this->client->general(base64_decode($image->getImage()));
    }
}