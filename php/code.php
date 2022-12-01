<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Server\GetCode;
use App\Server\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    /**
     * 验证码发送
     * 2022/11/28  15:36
     */
    public function getCodeLogin(Request $request)
    {
        try {
            //获取手机号码
            $phone = $request->get('phone');
            //判断手机号码是否为空
            if (empty($phone)) {
                return $this->fail('手机号码不能为空');
            }
            //判断手机号码是否符合规则
            if (!preg_match("/^1[3456789][0123456789]{9}$/", $phone)) {
                return $this->fail('手机号码格式不正确');
            }
            $codePhone = 'phoneCode_' . $phone;//存入验证码
            $timePhone = 'phoneTime_' . $phone;//存入的时间
            $todayPhone = 'phoneToday_' . $phone . date('Ymd');//一天的次数
            //判断是否存入验证码
            if (Cache::get($codePhone)) {

                if (time() - Cache::get($timePhone) < 60) {
                    $res = 60 - (time() - Cache::get($timePhone));
                    return $this->fail("还剩{$res}秒后重试");
                }
            }
            if (Cache::get($todayPhone) >= 5) {
                return $this->fail("今天次数已经用完，请明天再来尝试");
            }

            //获取随机四位验证码
            $code = mt_rand(1000, 9999);

            //   $result= (new GetCode())->GetCodePhone($phone,$code);
            Cache::put($codePhone, $code, 300);//验证码300秒后失效
            Cache::put($timePhone, time());//存入当前时间
            Cache::put($todayPhone, Cache::increment($todayPhone));//自增

//            if (!$result){
//                return $this->fail($result);
//            }

            return $this->success('短信获取成功', $code);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 登录
     * 2022/11/28  19:57
     */
    public function login(Request $request)
    {
        $code = $request->post('code');
        $phone = $request->post('phone');
        if (empty($phone)) {
            return $this->fail('手机号码不能为空');
        }
        if (empty($code)) {
            return $this->fail('验证码不能为空');
        }
        //判断手机号码是否符合规则
        if (!preg_match("/^1[3456789][0123456789]{9}$/", $phone)) {
            return $this->fail('手机号码格式不正确');
        }
        $codePhone = 'phoneCode_' . $phone;//取出验证码
        $codePhone = Cache::get($codePhone);
        if ($codePhone != $code) {
            return $this->fail('验证码不一致，请重新输入');
        }
        $phoneInto = User::where('phone', $phone)->first();
        if (!$phoneInto) {
            $phoneInto=User::create([
                'phone'=>$phone
            ]);
        }
       $token= (new JwtToken())->Encryption($phoneInto['id']);
        return  $this->success('登录成功',$token);
    }


}
