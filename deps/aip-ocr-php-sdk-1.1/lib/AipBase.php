<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

require_once 'AipHttpClient.php';
require_once 'AipBCEUtil.php';

/**
 * Aip Base 基类
 */
class AipBase {

    /**
     * 获取access token url
     * @var string
     */
    protected $accessTokenUrl = 'https://aip.baidubce.com/oauth/2.0/token';

    /**
     * appId
     * @var string
     */
    protected $appId = '';

    /**
     * apiKey
     * @var string
     */
    protected $apiKey = '';
    
    /**
     * secretKey
     * @var string
     */
    protected $secretKey = '';

    /**
     * 权限
     * @var array
     */
    protected $scopes = array(
        'vis-ocr_ocr',
        'vis-ocr_bankcard',
        'vis-faceattribute_faceattribute',
        'nlp_wordseg',
        'nlp_simnet',
        'nlp_wordemb',
        'nlp_comtag',
        'nlp_wordpos',
        'nlp_dnnlm_cn',
        'vis-antiporn_antiporn_v2',
        'audio_voice_assistant_get',
        'audio_tts_post',
    );

    /**
     * @param string $appId 
     * @param string $apiKey
     * @param string $secretKey
     */
    public function __construct($appId, $apiKey, $secretKey){
        $this->appId = trim($appId);
        $this->apiKey = trim($apiKey);
        $this->secretKey = trim($secretKey);
        $this->isCloudUser = null;
        $this->client = new AipHttpClient(array(
            'User-Agent' => 'baidu-aip-php-sdk-1.0.0.1',
        ));
    }

    /**
     * Api 请求
     * @param  string $url
     * @param  mixed $data
     * @return mixed
     */
    protected function request($url, $data){

        $params = array();
        $authObj = $this->auth();
        $headers = $this->getAuthHeaders('POST', $url);

        if($this->isCloudUser === false){
            $params['access_token'] = $authObj['access_token'];
        }

        $response = $this->client->post($url, $data, $params, $headers);
        $obj = $this->proccessResult($response['content']);

        if(!$this->isCloudUser && isset($obj['error_code']) && $obj['error_code'] == 110){
            $authObj = $this->auth(true);
            $params['access_token'] = $authObj['access_token'];
            $response = $this->client->post($url, $data, $params, $headers);
            $obj = $this->proccessResult($response['content']);
        }

        if(empty($obj) || !isset($obj['error_code'])){
            $this->writeAuthObj($authObj);
        }

        return $obj;
    }

    /**
     * 格式化结果
     * @param $content string
     * @return mixed
     */
    protected function proccessResult($content){
        return json_decode($content, true);
    }

    /**
     * 返回 access token 路径
     * @return string
     */
    private function getAuthFilePath(){
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . md5($this->apiKey);
    }

    /**
     * 写入本地文件
     * @param  array $obj
     * @return void
     */
    private function writeAuthObj($obj){
        if($obj === null || (isset($obj['is_read']) && $obj['is_read'] === true)){
            return;
        }

        $obj['time'] = time();
        $obj['is_cloud_user'] = $this->isCloudUser;
        @file_put_contents($this->getAuthFilePath(), json_encode($obj));
    }

    /**
     * 读取本地缓存
     * @return array
     */
    private function readAuthObj(){
        $content = @file_get_contents($this->getAuthFilePath());
        if($content !== false){
            $obj = json_decode($content, true);
            $this->isCloudUser = $obj['is_cloud_user'];
            $obj['is_read'] = true;
            if($this->isCloudUser || $obj['time'] + $obj['expires_in'] - 30 > time()){
                return $obj;
            }
        }

        return null;
    }

    /**
     * 认证
     * @param bool $refresh 是否刷新
     * @return array
     */
    private function auth($refresh=false){
       
        //apiKey长度 云的老用户是 32
        if(strlen($this->apiKey) === 32){
            $this->isCloudUser = true;
            return null;
        }

        //非过期刷新
        if(!$refresh){
            $obj = $this->readAuthObj();
            if(!empty($obj)){
                return $obj;
            }
        }

        $response = $this->client->get($this->accessTokenUrl, array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->secretKey,
        ));

        $obj = json_decode($response['content'], true);

        $this->isCloudUser = !$this->isPermission($obj);
        return $obj;
    }

    /**
     * 判断认证是否有权限
     * @param  array   $authObj 
     * @return boolean          
     */
    private function isPermission($authObj)
    {
        if(empty($authObj) || !isset($authObj['scope'])){
            return false;
        }

        $scopes = explode(' ', $authObj['scope']);
        $intersection = array_intersect($scopes, $this->scopes);

        return !empty($intersection);
    }

    /**
     * @param  string $method HTTP method
     * @param  string $url
     * @param  array $param 参数
     * @return array
     */
    private function getAuthHeaders($method, $url, $params=array()){
        
        //不是云的老用户则不用在header中签名 认证
        if($this->isCloudUser === false){
            return array();
        }

        $obj = parse_url($url);
        //UTC 时间戳
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $headers = array(
            'Host' => isset($obj['port']) ? sprintf('%s:%s', $obj['host'], $obj['port']) : $obj['host'],
            'x-bce-date' => $timestamp,
            'accept' => '*/*',
        );

        //签名
        $headers['authorization'] = AipSampleSigner::sign(array(
            'ak' => $this->apiKey,
            'sk' => $this->secretKey,
        ), $method, $obj['path'], $headers, $params, array(
            'timestamp' => $timestamp,
            'headersToSign' => array(
                'host',
            ),
        ));

        return $headers;
    }

}