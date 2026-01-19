<?php
namespace Qiniu;

use core\basic\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

Class QiniuStorage{

    private $token=NULL;

    private $errMsg=NULL;

    private $domain=NULL;

    private $auth=NULL;

    public  function __construct(){
        $this->accessKey = Config::get('qiniu_access_key');
        $this->secretKey = Config::get('qiniu_secret_key');
        $this->bucket = Config::get('qiniu_bucket');
        // 构建鉴权对象
        $this->auth = $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传 Token
        $this->token = $auth->uploadToken($this->bucket);
        $this->domain = Config::get('qiniu_url');
    }	
   
    public function saveFile($filePath,$fileName){
        //判断下是否是远程图片
        if ( preg_match('/(http:\/\/)|bai(https:\/\/)/i', $filePath) ) {
            $bucketManager = new BucketManager($this->auth);
            $ret = $bucketManager->fetch($filePath,$this->bucket,$fileName);
            $save_file = 'http://' . $this->domain . '/' .$ret[0]['key'];
            return array('code'=>1,'msg'=>$save_file);
        }else{
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($this->token, $fileName, $filePath);
            if ($err !== null) {
                return array('code'=>0,'msg'=>$err->message());
            } else {
                $save_file = 'http://' . $this->domain . '/' .$ret['key'];
                return array('code'=>1,'msg'=>$save_file);
            }
        }
    }


}
