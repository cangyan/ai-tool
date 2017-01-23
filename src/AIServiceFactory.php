<?php

namespace Cangyan\AITool;

use Cangyan\AITool\Domain\Services\BaiDuAIService;

class AIServiceFactory
{
    const APP_ID = '9234093';
    const API_KEY = 'VYG72GPvGdExb9mWGtYvdXSz';
    const SECRET_KEY = 'Zqraz7xLIxcqmYfStpkouE6GnFVCVB2w';
    
    public static function getBaiDuAIService()
    {
        return new BaiDuAIService(self::APP_ID, self::API_KEY, self::SECRET_KEY);
    }
}