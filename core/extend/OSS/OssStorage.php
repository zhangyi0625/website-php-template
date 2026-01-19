<?php
namespace OSS;

use core\basic\Config;
use OSS\OssClient;
use OSS\Core\OssException;

Class OssStorage{

    private $accessKey=NULL;

    private $secretKey=NULL;

    private $bucket=NULL;

    private $domain=NULL;

    private $endpoint=NULL;

    public  function __construct(){
        $this->accessKey = Config::get('oss_access_key');
        $this->secretKey = Config::get('oss_secret_key');
        $this->bucket = Config::get('oss_bucket');
        $this->domain = Config::get('oss_url');
    }	
   
    public function saveFile($filePath,$fileName,$stream=false){
        if(strpos($this->domain,'.aliyuncs.com') !== false){
            $endpoint = array_splice(explode('.',$this->domain),1);
            $endpoint = "https://".implode('.',$endpoint);
            $this->endpoint = $endpoint;
            $ossClient = new OssClient($this->accessKey, $this->secretKey, $this->endpoint);
        }else{
            $this->endpoint = $this->domain;
            $ossClient = new OssClient($this->accessKey, $this->secretKey, $this->endpoint,true);
        }
        try {
            //判断下是否是数据流
            if ( !!$stream ) {
                $result = $ossClient->putObject($this->bucket, $fileName, $stream);
            }else{
                $result = $ossClient->uploadFile($this->bucket, $fileName, $filePath);
            }
            $save_file = $result['info']['url'];
            return array('code'=>1,'msg'=>$save_file);
        } catch (OssException $e) {
            return array('code'=>0,'msg'=>$e->getMessage());
        }
    }

}
