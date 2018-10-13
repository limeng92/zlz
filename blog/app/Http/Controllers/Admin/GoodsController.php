<?php

namespace App\Http\Controllers\Admin;

//use App\fw_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;



class GoodsController extends Controller
{
    public function getGoodsList(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $listRow = 10;
            $param = $request->all();
            $start = ($param['page']-1)*$listRow;
            $totalNum = DB::table('goods')->count();
            $sql = "select a.goods_id,a.goods_name,a.goods_sn,b.name as cat_name,a.market_price,a.is_on_sale,a.is_new,a.is_hot
                    from goods a left join goods_category b on a.cat_id=b.id order by a.goods_id desc limit $start,$listRow  ";
            $goods_list_body = DB::select($sql,[]);


            $typeList = $this->getGoodsTypeList();

            $goods_cat = $this->getGoodsCat();


            return array(
                'code'=>200,
                'data'=> array(
                    'goods_list_body'=>$goods_list_body,
                    'total_num' => $totalNum,
                    'list_row' => $listRow,
                    'type_list' => $typeList,
                    'goods_cat' => $goods_cat
                ),
                'msg'=> '查询成功！'
            );
        }
    }


    /*
     *获取商品模型
     */
    public function getGoodsTypeList(){
        $goods_type = DB::table('goods_type')
            ->select('id','name')
            ->get();
        $typeList = [];
        foreach(json_decode($goods_type,true) as $k=>$v){
            $arr = array(
                'value' => $v['id'],
                'label' => $v['name']
            );
            $typeList[] = $arr;
        }
        return $typeList;
    }

    /*
     * 获取商品分类
     */
    public function getGoodsCat(){
        $firstCat = DB::table('goods_category')
            ->select('id','name')
            ->where([
                ['parent_id',0]
            ])
            ->get();
        $catList = [];
        foreach(json_decode($firstCat,true) as $k=>$v){
            $arr = array(
                'value' => (string)$v['id'],
                'label' => $v['name']
            );
            $first[] = $arr;
        }
        $catList['first'] = $first;
        $catList['second'] = [];
        $catList['third'] = [];
        $catList['firstValue'] = '';
        $catList['secondValue'] = '';

        return $catList;
    }

    /*
     * ajax获取下级分类
     */
    public function getNextCatList(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $id = $param['value'];
            $flag = $param['flag'];
            $catList = DB::table('goods_category')
                ->select('id','name')
                ->where([
                    ['parent_id',$id]
                ])
                ->get();

            foreach(json_decode($catList,true) as $k=>$v){
                $arr = array(
                    'value' => (string)$v['id'],
                    'label' => $v['name']
                );
                $return[] = $arr;
            }

            return array(

                'code'=>200,
                'data'=> array(
                    'flag'=>$flag,
                    'catList' => $return
                ),
                'msg'=> '查询成功！'

            );

        }
    }




    /*
     * 添加商品
     */
    public function addSaveRe(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $goodsinfo = json_decode($param['goods_info'],true);
            //基本信息
            $baseData = json_decode($goodsinfo['baseData'],true);
            //商品相册
            $goodsImgs = explode(',',$goodsinfo['goodsImgs']);
            //商品规格
            $specInputBody = json_decode($goodsinfo['specInputBody'],true);
            //商品属性
            $attrList = json_decode($goodsinfo['attrList'],true);

            //开启事务
            DB::beginTransaction();

            try{
                //添加商品基本信息
                $goods_id = DB::table('goods')->insertGetId($baseData);
                //添加商品相册信息
                if(!empty($goodsImgs)){
                    foreach ($goodsImgs as $k=>$v){
                        $arr = array(
                            'goods_id'=>$goods_id,
                            'image_url'=>$v
                        );
                        DB::table('goods_images')->insertGetId($arr);
                    }
                }

                //添加商品规格信息
                if(!empty($specInputBody)){
                    foreach($specInputBody as $k=>$v){
                        $arr = array(
                            'goods_id' => $goods_id,
                            'key' => $v['key'],
                            'key_name' => $v['key_name'],
                            'price' => $v['price'],
                            'market_price' => $v['market_price'],
                            'cost_price' => $v['cost_price'],
                            'store_count' => $v['store_count'],
                            'commission' => $v['commission'],
                            'sku' => $v['sku'],
                        );
                        DB::table('spec_goods_price')->insertGetId($arr);
                    }
                }


                //添加商品属性信息
                if(!empty($attrList)){
                    foreach($attrList as $k=>$v){
                        $arr = array(
                            'goods_id'=> $goods_id,
                            'attr_id' => $v['attr_id'],
                            'attr_value'=> $v['val'],
                            'attr_price' => 0
                        );
                        DB::table('goods_attr')->insertGetId($arr);
                    }
                }



                return array('code'=>200,'data'=>'','msg'=>'添加商品成功！');

                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return array(
                    'code'=>201,
                    'data'=>'',
                    'msg'=> $e->getMessage()
                );
            }

        }
    }

    /*
     * 修改商品
     */
    public function editSaveRe(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $goodsinfo = json_decode($param['goods_info'],true);
            //基本信息
            $baseData = json_decode($goodsinfo['baseData'],true);
            //商品相册
            $goodsImgs = explode(',',$goodsinfo['goodsImgs']);
            //商品规格
            $specInputBody = json_decode($goodsinfo['specInputBody'],true);
            //商品属性
            $attrList = json_decode($goodsinfo['attrList'],true);

            //开启事务
            DB::beginTransaction();
            try{
                //修改基本信息
                $re = DB::table('goods')
                    ->where('goods_id', $baseData['goods_id'])
                    ->update($baseData);


                //修改商品相册信息
                if(!empty($goodsImgs)){
                    // 首先查询goods_images表
                    $imgRe = DB::table('goods_images')
                        ->select('img_id','image_url')
                        ->where([
                            ['goods_id', $baseData['goods_id']]
                        ])
                        ->get();
                    $imgRe = json_decode($imgRe,true);
                    $imgReLen = count($imgRe);
                    //goods_images 修改的情况
                    foreach($goodsImgs as $k=>$v){
                        if($k<$imgReLen){
                            $arr = array(
                                'image_url' => $v
                            );
                            DB::table('goods_images')
                                ->where('img_id',$imgRe[$k]['img_id'])
                                ->update($arr);
                            unset($goodsImgs[$k]);
                            unset($imgRe[$k]);
                        }
                    }

                    //goods_images 增加的情况
                    if(!empty($goodsImgs)){
                        foreach($goodsImgs as $k=>$v){
                            $addInfo = array(
                                'goods_id'=>$baseData['goods_id'],
                                'image_url' => $v
                            );
                            DB::table('goods_images')
                                ->insertGetId($addInfo);
                        }
                    }
                    //goods_images 删除的情况
                    if(!empty($imgRe)){
                        foreach($imgRe as $k=>$v){
                            DB::table('goods_images')
                                ->where('img_id',$v['img_id'])
                                ->delete();
                        }
                    }
                }


                //修改商品规格信息
                if(!empty($specInputBody)){
                    // 首先查询spec_goods_price表
                    $specRe = DB::table('spec_goods_price')
                        ->select('item_id','goods_id')
                        ->where([
                            ['goods_id', $baseData['goods_id']]
                        ])
                        ->get();
                    $specRe = json_decode($specRe,true);
                    $specReLen = count($specRe);
                    foreach($specInputBody as $k=>$v){
                        if($k<$specReLen){
                            $arr = array(
                                'goods_id' => $baseData['goods_id'],
                                'key' => $v['key'],
                                'key_name' => $v['key_name'],
                                'price' => $v['price'],
                                'market_price' => $v['market_price'],
                                'cost_price' => $v['cost_price'],
                                'store_count' => $v['store_count'],
                                'commission' => $v['commission'],
                                'sku' => $v['sku'],
                            );
                            DB::table('spec_goods_price')
                                ->where('item_id',$specRe[$k]['item_id'])
                                ->update($arr);
                            unset($specInputBody[$k]);
                            unset($specRe[$k]);
                        }
                    }
                    //spec_goods_price 增加的情况
                    if(!empty($specInputBody)){
                        foreach($specInputBody as $k=>$v){
                            $arr = array(
                                'goods_id' => $baseData['goods_id'],
                                'key' => $v['key'],
                                'key_name' => $v['key_name'],
                                'price' => $v['price'],
                                'market_price' => $v['market_price'],
                                'cost_price' => $v['cost_price'],
                                'store_count' => $v['store_count'],
                                'commission' => $v['commission'],
                                'sku' => $v['sku'],
                            );
                            DB::table('spec_goods_price')->insertGetId($arr);
                        }
                    }
                    //spec_goods_price 删除的情况
                    if(!empty($specRe)){
                        foreach($specRe as $k=>$v){
                            DB::table('spec_goods_price')
                                ->where('item_id',$v['item_id'])
                                ->delete();
                        }
                    }
                }

                //添加商品属性信息
                if(!empty($attrList)){
                    // 首先查询goods_attr表
                    $attrRe = DB::table('goods_attr')
                        ->select('goods_attr_id','goods_id')
                        ->where([
                            ['goods_id', $baseData['goods_id']]
                        ])
                        ->get();
                    $attrRe = json_decode($attrRe,true);
                    $attrReLen = count($attrRe);
                    foreach($attrList as $k=>$v){
                        if($k<$attrReLen){
                            $arr = array(
                                'goods_id'=> $baseData['goods_id'],
                                'attr_id' => $v['attr_id'],
                                'attr_value'=> $v['val'],
                                'attr_price' => 0
                            );
                            DB::table('goods_attr')
                                ->where('goods_attr_id',$attrRe[$k]['goods_attr_id'])
                                ->update($arr);
                            unset($attrList[$k]);
                            unset($attrRe[$k]);
                            DB::table('goods_attr')->insertGetId($arr);
                        }
                    }
                    //goods_attr 删除的情况
                    if(!empty($attrRe)){
                        foreach($attrRe as $k=>$v){
                            DB::table('goods_attr')
                                ->where('goods_attr_id',$v['goods_attr_id'])
                                ->delete();
                        }
                    }
                }



                return array('code'=>200,'data'=>'','msg'=>'修改商品成功！');
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return array(
                    'code'=>201,
                    'data'=>'',
                    'msg'=> $e->getMessage()
                );
            }

        }
    }

    /*
     * 获取修改商品信息
     */
    public function editInfo(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $id = $param['id'];
            $goodsInfo = DB::table('goods')
                ->select('goods_id','goods_name','goods_remark','goods_sn','cat_id','shop_price','original_img','market_price','goods_type','cost_price','store_count','is_on_sale','goods_content','give_integral','exchange_integral')
                ->where([
                    ['goods_id', $id]
                ])
                ->first();
            $goodsInfo->is_on_sale = (string)$goodsInfo->is_on_sale;
            $goodsInfo->cat_id = (string)$goodsInfo->cat_id;
            $goodsInfo->goods_content = stripslashes(html_entity_decode($goodsInfo->goods_content));
            //商品父级分类
            $parentCat = $this->getParentCat($goodsInfo->cat_id);
            //商品原图
            $originalImg = [];
            foreach(explode(',',$goodsInfo->original_img) as $k=>$v){
                $originalImg[] = [
                    'name' => $k,
                    'url' => $v
                ];
            };


            //商品相册
            $goodsImgs = [];
            $imgs = DB::table('goods_images')
                ->select('img_id','image_url')
                ->where([
                    ['goods_id',$id]
                ])
                ->get();
            foreach($imgs as $k=>$v){
                $goodsImgs[] = [
                    'name' => $k,
                    'url' => $v->image_url,
                    'img_id'=> $v->img_id
                ];
            };


            unset($goodsInfo->goods_img);
            //获取模型下规格和属性
            $goodsSpecList = $this->getSpecList($goodsInfo->goods_type);
            $goodsAttrList = $this->getAttrList($goodsInfo->goods_type);


            //规格输入列表值
            $goodsSpecInput = DB::table('spec_goods_price')
                ->select('key','key_name','price','market_price','cost_price','commission','store_count','sku')
                ->where([
                    ['goods_id',$id]
                ])
                ->get();

            //组成规格列表选中
            $keyArr = [];
            foreach(json_decode($goodsSpecInput,true) as $k=>$v){
                foreach(explode('_',$v['key']) as $kk=>$vv){
                    $keyArr[] = $vv;
                };
            }
            $keyArr = array_unique($keyArr);

            foreach($goodsSpecList as $k=>$v){
                foreach($v['spec_item'] as $kk=>$vv){
                    foreach($keyArr as $kkk=>$vvv){
                        if($vv['id']==$vvv){
                            $goodsSpecList[$k]['spec_item'][$kk]['checked'] = true;
                        }
                    }
                }
            }

            //组成规格输入列表选中的规格
            $arr = array();
            foreach(json_decode($goodsSpecInput,true) as $k=>$v){
                $arr[$k] = explode('_',$v['key']);
            }

            //规格列表头
            $specInputTitle = $this->getSpecInputTitle($arr);

            //规格列表主体
            $specInputBody = $this->getSpecInputBody($arr);
            $goodsSpecInput = json_decode($goodsSpecInput,true);
            foreach($specInputBody as $k=>$v){
                $specInputBody[$k]['price'] = $goodsSpecInput[$k]['price'];
                $specInputBody[$k]['market_price'] = $goodsSpecInput[$k]['market_price'];
                $specInputBody[$k]['cost_price'] = $goodsSpecInput[$k]['cost_price'];
                $specInputBody[$k]['commission'] = $goodsSpecInput[$k]['commission'];
                $specInputBody[$k]['store_count'] = $goodsSpecInput[$k]['store_count'];
                $specInputBody[$k]['sku'] = $goodsSpecInput[$k]['sku'];
            }


            //属性输入值
            $goodsAttr = DB::table('goods_attr')
                ->select('attr_id','attr_value')
                ->where([
                    ['goods_id',$id]
                ])
                ->get();
            foreach(json_decode($goodsAttr,true) as $k=>$v){
                foreach ($goodsAttrList as $kk=>$vv){
                    if($v['attr_id']==$vv['attr_id']){
                        $goodsAttrList[$kk]['val'] = $v['attr_value'];
                    }
                }
            }



            return array(
                'code'=>200,
                'data'=> array(
                    'goods_info' => $goodsInfo,
                    'original_img' => $originalImg,
                    'goods_imgs' => $goodsImgs,
                    'goods_spec_list' => $goodsSpecList,
                    'goods_attr_list' => $goodsAttrList,
                    'spec_input_title' => $specInputTitle,
                    'spec_input_body' => $specInputBody,
                    'parent_cat' => $parentCat

                ),
                'msg'=> '查询成功！'
            );
        }
    }

    /*
     * 获取商品父级分类信息
     */
    public function getParentCat($cat_id){
        $re = DB::table('goods_category')
                ->select('parent_id_path')
                ->where([
                    ['id', $cat_id]
                ])
                ->first();
        return explode('_',$re->parent_id_path);
    }

    /*
     * 删除商品
     */
    public function delGoodsRe(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $id = $param['id'];
            //开启事务
            DB::beginTransaction();
            try{
                 DB::table('goods')
                    ->where([
                        ['goods_id', $id]
                    ])
                    ->delete();
                DB::table('goods_images')
                    ->where([
                        ['goods_id', $id]
                    ])
                    ->delete();
                DB::table('spec_goods_price')
                    ->where([
                        ['goods_id', $id]
                    ])
                    ->delete();
                DB::table('goods_attr')
                    ->where([
                        ['goods_id', $id]
                    ])
                    ->delete();

                return array('code'=>200,'data'=>'','msg'=>'删除成功！');
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return array(
                    'code'=>201,
                    'data'=>'',
                    'msg'=> $e->getMessage()
                );
            }

        }
    }

    /*
     * 根据所选模型获取商品规格属性
     */
    public function getGoodsSpecAttrList(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $type_id = $param['id'];

            $specList = $this->getSpecList($type_id);
            $attrList = $this->getAttrList($type_id);

            return array(
                'code'=>200,
                'data'=> array(
                    'spec_list' => $specList,
                    'attr_list' => $attrList
                ),
                'msg'=> '查询成功！'
            );


        }
    }


    /*
     * 获取规格
     */
    public function getSpecList($type_id){
        //规格
        $specList = DB::table('spec')
            ->select('id','type_id','name')
            ->where([
                ['type_id',$type_id]
            ])
            ->get();
        $specList = json_decode($specList,true);
        foreach($specList as $k=>$v){
            $spec_id = $v['id'];
            $spec_item = DB::table('spec_item')
                ->select('id','item')
                ->where('spec_id',$spec_id)
                ->get();
            $spec_item = json_decode($spec_item,true);
            foreach($spec_item as $kk=>$vv){
                $spec_item[$kk]['checked'] = false;
            }
            $specList[$k]['spec_item'] = $spec_item;
        }
        return $specList;
    }

    /*
     * 获取属性
     */
    public function getAttrList($type_id){
        //属性
        $attrList = DB::table('goods_attribute')
            ->select('attr_id','attr_name','attr_index','attr_type','attr_input_type','attr_values')
            ->where([
                ['type_id',$type_id]
            ])
            ->get();
        $attrList = json_decode($attrList,true);
        foreach($attrList as $k=>$v){
            if(!empty($v['attr_values'])){
                $attrList[$k]['attr_values'] = explode("\n",$v['attr_values']);
            }
            $attrList[$k]['val'] = '';
        }
        return $attrList;
    }


    /*
     * ajax获取选中规格后结果
     */
    public function getGoodsSpecInput(Request $request){
        if(strtoupper($_SERVER['REQUEST_METHOD'])!= 'OPTIONS'){
            $param = $request->all();
            $spec_josn = $param['spec_json'];
            $spec_josn = json_decode($spec_josn,true);
            $checkedArr = array();
            foreach($spec_josn as $k=>$v){
                foreach($v['spec_item'] as $kk=>$vv){
                    if($vv['checked']){
                        $checkedArr[$k][] = $vv['id'];
                    }
                }
            }
            //笛卡尔积乘
            $specInput = $this->getSpecInput($checkedArr);

            //返回列表头
            $specInputTitle = $this->getSpecInputTitle($specInput);
            //返回列表主体
            $specInputBody = $this->getSpecInputBody($specInput);

            return array(
                'code'=>200,
                'data'=> array(
                    'spec_input_title' => $specInputTitle,
                    'spec_input_body' => $specInputBody
                ),
                'msg'=> '查询成功！'
            );

        }
    }

    public function getSpecInputTitle($specInput){
//        $baseTitleArr = array(
//            0 => array(
//                'title'=>'购买价',
//                'key'=> 'price'
//            ),
//            1=> array(
//                'title'=>'市场价',
//                'key'=> 'market_price',
//            ),
//            2=> array(
//                'title'=>'成本价',
//                'key'=> 'cost_price'
//            ),
//            3=> array(
//                'title'=> '佣金',
//                'key'=> 'commission'
//            ),
//            4=> array(
//                'title'=> '库存',
//                'key' => 'store_count'
//            ),
//            5=> array(
//                'title' => 'SKU',
//                'key' => 'sku'
//            )
//        );

        //组成title数组
        $specInputTitle = array();
        if(!empty($specInput)){
            foreach($specInput[0] as $k=>$v){
                $sql = "select b.id,b.name from spec_item a left join spec b on a.spec_id=b.id WHERE a.id='$v'";
                $re = DB::select($sql,[]);
                $arr = array(
                    'title' => $re[0]->name,
                    'key' => $re[0]->id
                );
                $specInputTitle[] = $arr;
            }
        }


//        foreach($baseTitleArr as $k=> $v){
//            $specInputTitle[] = $v;
//        }

        return $specInputTitle;
    }



    public function getSpecInputBody($specInput){

        $baseBodyArr = array(
            'price' => '',
            'market_price' => '',
            'cost_price' =>'',
            'commission' => '',
            'store_count' => '',
            'sku' => ''
         );
        $specInputBody = [];
        if(!empty($specInput)){
            foreach($specInput as $k=>$v){
                $arr = [];
                $key = [];
                $keyName = [];
                foreach($v as $kk=>$vv){
                    $sql = "select a.item,b.id,b.name from spec_item a left join spec b on a.spec_id = b.id WHERE a.id='$vv'";
                    $re = DB::select($sql,[]);
                    $arr[$re[0]->id] = $re[0]->item;
                    $key[] = $vv;
                    $keyName[] = $re[0]->name.':'.$re[0]->item;
                }
                $arr['key'] = implode('_',$key);
                $arr['key_name'] = implode('  ',$keyName);
                foreach($baseBodyArr as $bk=>$bv){
                    $arr[$bk] = $bv;
                }
                $specInputBody[$k] = $arr;
            }
        }
        return $specInputBody;

    }


    public function getSpecInput($spec_arr){
        // 排序
        foreach ($spec_arr as $k => $v)
        {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);
        foreach ($spec_arr_sort as $key =>$val)
        {
            $spec_arr2[$key] = $spec_arr[$key];
        }


//        $clo_name = array_keys($spec_arr2);
        $spec_arr2 = $this->combineDika($spec_arr2); //  获取 规格的 笛卡尔积
        return $spec_arr2;
//        var_dump($spec_arr2);exit;
    }


    /**
     * 多个数组的笛卡尔积
     *
     * @param unknown_type $data
     */
    public function combineDika() {
        $data = func_get_args();
        $data = current($data);
        $cnt = count($data);
        $result = array();
        $arr1 = array_shift($data);
        foreach($arr1 as $key=>$item)
        {
            $result[] = array($item);
        }

        foreach($data as $key=>$item)
        {
            $result = $this->combineArray($result,$item);
        }
        return $result;
    }


    /**
     * 两个数组的笛卡尔积
     * @param unknown_type $arr1
     * @param unknown_type $arr2
     */
    public function combineArray($arr1,$arr2) {
        $result = array();
        foreach ($arr1 as $item1)
        {
            foreach ($arr2 as $item2)
            {
                $temp = $item1;
                $temp[] = $item2;
                $result[] = $temp;
            }
        }
        return $result;
    }


}
