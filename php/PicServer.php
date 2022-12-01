<?php

namespace App\server;
use lishuo\oss\Manager;
use lishuo\oss\storage\StorageConfig;
class PicServer
{
    //七牛云上传
    public function getPic($path){
        $config = new StorageConfig("ln5CNft4EH5l1mR4V_UxPIzaYkg8cGrfZaURmPzX", "JwlnK4iP3DwBGEWIhtIpOp7y1mOjZ031lQ30xGs1", "");
        $storage = Manager::storage("qiniu") // 阿里云：aliyun、腾讯云：tencent、七牛云：qiniu
        ->init($config) // 初始化配置
        ->bucket("asdsd1234"); // 指定操作的存储桶
        // 上传文件
        $paths =  md5(mt_rand(0000,9999).date('Ymd')).".png";
        $result = $storage->put($paths, $path);
        // 删除文件
        $name="http://rkyya2igw.hn-bkt.clouddn.com/".$paths;
        return $name;
    }
}
