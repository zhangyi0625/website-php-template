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
        $endpoint = array_splice(explode('.',$this->domain),1);
        $endpoint = "http://".implode('.',$endpoint);
        $this->endpoint = $endpoint;
    }	
   
    public function saveFile($filePath,$fileName){
        //判断下是否是远程图片
        if ( preg_match('/(http:\/\/)|bai(https:\/\/)/i', $filePath) ) {
            $content = file_get_contents($filePath);
            try {
                $ossClient = new OssClient($this->accessKey, $this->secretKey, $this->endpoint);
                $result = $ossClient->putObject($this->bucket, $fileName, $content);
                $save_file = $result['info']['url'];
                return array('code'=>1,'msg'=>$save_file);
            } catch (OssException $e) {
                return array('code'=>0,'msg'=>$e->getMessage());
            }
        }else{
            try{
                $ossClient = new OssClient($this->accessKey, $this->secretKey, $this->endpoint);
                $result = $ossClient->uploadFile($this->bucket, $fileName, $filePath);
                $save_file = $result['info']['url'];
                return array('code'=>1,'msg'=>$save_file);
            } catch(OssException $e) {
                return array('code'=>0,'msg'=>$e->getMessage());
            }
        }
    }






}
