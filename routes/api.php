<?php


use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::post('/post', function (Request $request) {
    $data = $request->all();
    dd($data);
});

/************* 更新user表信息的接口 ***********/
/*
 *
 */
Route::post('/user', function (Request $request) {
    $data1 = $request->all();
    //dd($data1);
    //定义验证规则
    $validator = Validator::make($data1, [
        'openId' => 'required|between:28,28',
        'unionId' => 'nullable|between:29,29',
        'avatarUrl' => 'nullable|url',
        'gender'=>'nullable|in:0,1,2',
    ], [
//        'user_openid.required' => 'openid必须提供',
//        'user_openid.between' => 'openid长度28位',
//        'user_nickname.required' => '昵称必须提供',
//        'user_avatar_url.required' => '头像地址必须提供',
//        'user_avatar_url.url' => '头像地址必须为有效域名',
    ]);
    if ($validator->fails()) {
        //验证没有通过
        $arr = [
            'code' => 601,
            'msg' => '请求参数错误',
        ];
    } else {
        //验证通过了
        $data = [
            'user_openid'=>$data1['openId'],
            'user_unionid'=>isset($data1['unionId'])?$data1['unionId']:'',
            'user_nickname'=>isset($data1['nickname'])?$data1['nickname']:'',
            'user_avatar_url'=>isset($data1['avatarUrl'])?$data1['avatarUrl']:'',
            'user_gender'=>isset($data1['gender'])?$data1['gender']:'',
            'user_city'=>isset($data1['city'])?$data1['city']:'',
            'user_province'=>isset($data1['province'])?$data1['province']:'',
            'user_country'=>isset($data1['country'])?$data1['country']:'',
        ];
        //判断数据库中是否有这条记录，有就更新，没有就新增
        $res = DB::table('user')->where('user_openid', $data['user_openid'])->first();
        if (!empty($res)) {
            //更新数据,成功则返回1,表示改变记录数量
            $data['user_updated_time'] = time();
            $db = DB::table('user')->where('user_id', '=', $res->user_id)->update($data);
        } else {
            //新增数据,成功则返回true
            $data['user_created_time'] = time();
            $db = DB::table('user')->insert($data);
        }
        if (false == $db) {
            $arr = [
                'code' => 602,
                'msg' => '数据库错误',
            ];
        } else {
            $arr = [
                'code' => 600,
                'msg' => 'success',
            ];
        }
    }
    return json_encode($arr);
});


/************* 开新团接口 ****************/
Route::post('/group/add', function (Request $request) {
    $data = $request->all();
    //dd($data);
    //定义验证规则
    $validator = Validator::make($data, [
        'openId' => 'required|between:28,28',
    ], []);
    if ($validator->fails()) {
        //验证没有通过,参数错误
        $arr3 = [
            'code' => 0,
            'msg' => '开团失败,参数错误',
        ];
    } else {
        //验证通过了
        //检查该用户是否开过团,且该团的状态未过期
        $db=DB::table('group')->where('group_creater_openid',$data['openId'])->orderBy('group_created_time','desc')->first();
        if(null != $db && time()<$db->group_created_time+24*3600 && $db->group_status==1){
            //已处于开团中,禁止开团
            $arr3 = [
                'code' => 0,
                'msg' => '开团失败,开团进行中',
            ];
        }else{
            //1.在开团表中新增一条记录
            $arr1 = [
                'group_creater_openid' => $data['openId'],//开团人
                'group_status' => 1,//开团中
                'group_members_total' => 4,//成员总数,固定
                'group_members_count' => 1,//参团人数
                'group_created_time' => time(),//开团时间
            ];
            DB::table('group')->insert($arr1);
            //2.在开团明细表中新增一条记录
            $db = DB::table('group')->select('group_id')->where('group_creater_openid', '=', $data['openId'])->orderBy('group_created_time', 'desc')->first();
            $groupId = $db->group_id;
            $time = time();
            $arr2 = [
                'detail_openid' => $data['openId'],//参团人
                'detail_created_time' => $time,//参团时间
                'detail_group_id' => $groupId,//团Id
            ];
            DB::table('group_detail')->insert($arr2);
            $arr3 = [
                'code' => 1,
                'msg' => '开团成功',
                'groupId' => $groupId,
                'groupCreatedTime' => $time,
            ];
        }
    }
    return json_encode($arr3);
});

/******************** 一起拆(参团)接口 ***********************/
Route::post('/group/update', function (Request $request) {
    $data = $request->all();
    //定义验证规则
    $validator = Validator::make($data, [
        'openId' => 'required|between:28,28',
        'groupId' => 'required',
    ], []);
    if ($validator->fails()) {
        //验证没有通过,参数错误
        $arr = [
            'code' => 0,
            'msg' => '参数错误',
        ];
        return json_encode($arr);
    } else {
        //验证通过了
        //如果该组中已有此成员则参团失败
        $groupId = $data['groupId'];
        $openId = $data['openId'];
        $res = DB::table('group_detail')->where('detail_group_id', '=', $groupId)->where('detail_openid','=',$openId)->get();
        if(!empty($res)){
            //该组中已有该成员,不能重复参团
            $arr = [
                'code' => 0,
                'msg' => '该组中已有该成员,不能重复参团',
            ];
            return json_encode($arr);
        }else{
            //该组中没有该成员,可以参团
            $groupData1 = DB::table('group')->where('group_id', '=', $groupId)->first();
            switch ($groupData1->group_status) {
                case 1: //开团中
                    $time = time();
                    if ($groupData1->group_created_time + 24 * 3600 < $time) {
                        //如果开团时间超过24小时,把开团信息改为开团失败
                        $group_arr = [
                            'group_status' => 3,
                        ];
                        DB::table('group')->where('group_id', '=', $groupId)->update($group_arr);
                        $arr = [
                            'code' => 0,
                            'groupStatus' => 3,
                        ];
                        return json_encode($arr);
                    } else {
                        if ($groupData1->group_members_count < $groupData1->group_members_total - 1) {
                            //如果参团人数为1,2,则参团人数加1,明细表增加一条记录
                            DB::table('group')->where('group_id', '=', $groupId)->increment('group_members_count');
                            $detail_arr = [
                                'detail_openid' => $data['openId'],
                                'detail_group_id' => $groupId,
                                'detail_created_time' => time(),
                            ];
                            DB::table('group_detail')->insert($detail_arr);
                            $arr = [
                                'code' => 1,
                                'groupStatus' => 1,
                            ];
                            return json_encode($arr);
                        } elseif ($groupData1->group_members_count == $groupData1->group_members_total - 1) {
                            //如果参团人数为3,则参团人数加1,开团状态改为已成团,明细表增加一条记录,发券
                            $group_arr = [
                                'group_members_count' => $groupData1->group_members_total,
                                'group_status' => 2,
                            ];
                            DB::table('group')->where('group_id', '=', $groupId)->update($group_arr);
                            $detail_arr = [
                                'detail_openid' => $data['openId'],
                                'detail_group_id' => $groupId,
                                'detail_created_time' => time(),
                            ];
                            DB::table('group_detail')->insert($detail_arr);
                            //开始发券
                            $card_code = [];
                            for ($i = 0; $i < $groupData1->group_members_total; $i++) {
                                $card_code[] = mt_rand(); //TODO:发券,先随机生成4个券码,后面调用接口生成
                            }
                            $detailIds = DB::table('group_detail')->select(DB::raw("group_concat(detail_id) as detailIds"))->where('detail_group_id', '=', $groupId)->get();
                            //dd($detailIds[0]->detailIds);
                            $detailIds = explode(',', $detailIds[0]->detailIds);
                            for ($i = 0; $i < $groupData1->group_members_total; $i++) {
                                $card_code_arr = [
                                    'detail_card_code' => $card_code[$i],
                                    'detail_updated_time' => time(),
                                ];
                                DB::table('group_detail')->where('detail_id', '=', $detailIds[$i])->update($card_code_arr);
                            }
                            $arr = [
                                'code' => 1,
                                'groupStatus' => 2,
                            ];
                            return json_encode($arr);
                        }
                    }
                    break;
                case 2: //已成团
                    $arr = [
                        'code' => 0,
                        'groupStatus' => 2,
                    ];
                    return json_encode($arr);
                case 3: //开团失败
                    $arr = [
                        'code' => 0,
                        'groupStatus' => 3,
                    ];
                    return json_encode($arr);
            }
        }
    }
});


/*******************查询团队成员信息接口************************/
Route::post('/group/query', function (Request $request) {
    $data = $request->all();
    $groupId = $data['groupId'];
    $groupData2 = DB::table('group')->where('group_id', '=', $groupId)->first();
    if (empty($groupData2)) {
        //未查到该团的任何信息
        $group_info_arr = [
            'code' => 0,  //0表示查询失败,1表示查询成功
        ];
        return json_encode($group_info_arr);
    } else {
        switch ($groupData2->group_status) {
            case 1: //开团中
                if (time() > $groupData2->group_created_time + 24 * 3600) {
                    //如果发现超时24小时,就把开团状态改为失败
                    $group_arr = [
                        'group_status' => 3,
                    ];
                    DB::table('group')->where('group_id', '=', $groupId)->update($group_arr);
                    $group_info_arr = [
                        'code' => 1,
                        'groupStatus' => 3,
                    ];
                    return json_encode($group_info_arr);
                } else {
                    //返回团员信息
                    $openIds = DB::table('group_detail')->select(DB::raw("(detail_openid) as openIds"))->where('detail_group_id', '=', $groupId)->orderBy('detail_created_time', 'asc')->get(); //TODO:此行的返回值格式需要调试测试,期望得到 str1,str2,str3格式的字符串
                    foreach($openIds as $v){
                        $temp[]= $v->openIds;
                    }
                    $openIds = $temp;
                    //$openIds = explode(',', $openIds[0]->openIds); //所有团员的openId
                    $count = count($openIds); //团员的数量
                    $membersData = DB::table('user')->select('user_openid','user_avatar_url')->whereIn('user_openid', $openIds)->get()->toArray();
                    $group_info_arr = [
                        'code' => 1,
                        'groupStatus' => 1,
                        'groupCreatedTime' => $groupData2->group_created_time,
                        'membersCount' => $count,
                        'membersInfo' => $membersData,
                    ];
                    return json_encode($group_info_arr);
                }
                break;
            case 2: //已成团
                //返回团员信息
                $openIds = DB::table('group_detail')->select(DB::raw("(detail_openid) as openIds"))->where('detail_group_id', '=', $groupId)->orderBy('detail_created_time', 'asc')->get();
                foreach($openIds as $v){
                    $temp[]= $v->openIds;
                }
                $openIds = $temp;
                //$openIds = explode(',', $openIds[0]->openIds); //所有团员的openId
                $membersData = DB::table('user')->select('user_openid','user_avatar_url')->whereIn('user_openid', $openIds)->get()->toArray();
                //$membersData = json_encode($membersData);
                /*foreach($membersData as $v){
                    $temp2[] = json_encode($v);
                }*/
                //$membersData = json_encode($temp2);
                //dd($membersData);
                $group_info_arr = [
                    'code' => 1,
                    'groupStatus' => 2,
                    'groupCreatedTime' => $groupData2->group_created_time,
                    'membersCount' => $groupData2->group_members_total,
                    'membersInfo' => $membersData,
                ];
                return json_encode($group_info_arr);
            case 3:  //开团失败
                $group_info_arr = [
                    'code' => 1,
                    'groupStatus' => 3,
                ];
                return json_encode($group_info_arr);
        }
    }
});