<?php

namespace App\Http\Controllers\Admin;

//use App\fw_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Session\Session;


class LoginController extends Controller
{
    public function index(Request $request)
    {
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $username = $param['username'];
            $password = $param['password'];
            $menu = DB::table('operator')
                ->where([
                    ['username', $username],
                    ['password', md5($password)]
                ])
                ->first();
            if(!empty($menu)){
                //保存用户名和token
                $user_token = str_random(36);
                $expire_time = time()+60*60*24;
                $arr = array(
                    'user_token' => $user_token,
                    'expire_time' => $expire_time
                );
                DB::table('operator')
                    ->where('id', $menu->id)
                    ->update($arr);
                $returnArr = array(
                    'code' => 200,
                    'data' => array(
                        'user_token' => $user_token,
                        'user_id' => $menu->id
                    ),
                    'msg'  => '登录成功！'
                );
                return $returnArr;
            }else{
                $returnArr = array(
                    'code' => 403,
                    'data' => '',
                    'msg'  => '用户名或密码不正确！'
                );
                return $returnArr;
            }           
        }
    }

    public function loginOutInfo(){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $array = array(
                'code' => 200,
                'data' => null,
                'msg' => '退出登录成功！'
            );
            return $array;
        }
    }
}
