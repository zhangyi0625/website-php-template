<?php
namespace Qiniu;

use core\basic\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

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
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传 Token
        $this->token = $auth->uploadToken($this->bucket);
        $this->domain = Config::get('qiniu_url');
    }	
   
    public function saveFile($filePath,$fileName,$stream=false){
        $uploadMgr = new UploadManager();
        //如果是数据流
        if ( !!$stream ) {
            // 调用 UploadManager 的 put 方法进行数据流上传
            list($ret, $err) = $uploadMgr->put($this->token, $fileName, $stream);
        }else{
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($this->token, $fileName, $filePath);
        }
        if ($err !== null) {
            return array('code'=>0,'msg'=>$err->message());
        } else {
            $save_file = '//' . $this->domain . '/' .$ret['key'];
            return array('code'=>1,'msg'=>$save_file);
        }
    }

}
