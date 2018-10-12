<template>
  <div>
    <div style="margin-bottom: 5px;">
      <Input placeholder="商品名称" style="width: 200px;padding:2px;" />
      <Input placeholder="商品货号" style="width: 200px;padding:2px;" />
      <Button class="search-btn" style="margin-left: 2px;" type="primary">搜索</Button>
      <Button @click="add" class="search-btn" style="margin-left: 2px;" type="success">新建</Button>
    </div>
    <Table border :columns="columns7" :data="data6"></Table>
    <Page style="float:right;" @on-change="getNextPage" :page-size="listRow" :total="totalNum" />
    <Modal
      v-model="modal"
      title="修改"
      width="80%"
      @on-ok="ok"
      :mask-closable = false
      @on-cancel="cancel">
      <Tabs type="card">
        <TabPane label="通用信息">
          <Form  :label-width="80">
            <FormItem label="商品名称">
              <Input v-model="baseData.goods_name" placeholder="商品名称" style="width: 40%;" />
            </FormItem>
            <FormItem label="商品简介">
              <Input v-model="baseData.goods_remark" type="textarea" style="width: 85%;" :autosize="{minRows: 2,maxRows: 5}" placeholder="商品简介" />
            </FormItem>
            <FormItem label="商品货号">
              <Input v-model="baseData.goods_sn" placeholder="商品货号" style="width: 40%;" />
            </FormItem>
            <FormItem label="商品分类">
              <Select @on-change="getNextCat(goodsCat.firstValue,'first')" v-model="goodsCat.firstValue"  style="width:100px">
                <Option v-for="item in goodsCat.first" :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
              <Select @on-change="getNextCat(goodsCat.firstValue,'second')" v-model="goodsCat.secondValue" style="width:100px">
                <Option v-for="item in goodsCat.second" :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
              <Select v-model="baseData.cat_id" style="width:100px">
                <Option v-for="item in goodsCat.third"  :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
            </FormItem>
            <FormItem label="本店售价">
              <Input v-model="baseData.shop_price" placeholder="本店售价" style="width: 40%;" />
            </FormItem>
            <FormItem label="市场价">
              <Input v-model="baseData.market_price" placeholder="市场价" style="width: 40%;" />
            </FormItem>
            <FormItem label="成本价">
              <Input v-model="baseData.cost_price" placeholder="成本价" style="width: 40%;" />
            </FormItem>
            <FormItem label="图片上传">
              <upload-pic ref="originalImg" :action="uploadAction"></upload-pic>
            </FormItem>
            <FormItem label="商品库存">
              <Input v-model="baseData.store_count" placeholder="商品库存" style="width: 40%;" />
            </FormItem>
            <FormItem label="是否上架">
              <RadioGroup v-model="baseData.is_on_sale">
                <Radio label="1">是</Radio>
                <Radio label="0">否</Radio>
              </RadioGroup>
            </FormItem>
            <FormItem label="商品详情">
              <editor ref="goodsContent"  :upImgUrl="uploadAction"  v-model="baseData.goods_content" @on-change="handleChange"/>
            </FormItem>
          </Form>
        </TabPane>
        <TabPane label="商品相册">
          <Form  :label-width="80">
            <FormItem label="商品相册">
              <upload-pic ref="goodsImgs" :action="uploadAction"></upload-pic>
            </FormItem>
          </Form>
        </TabPane>
        <TabPane label="商品模型">
          <Form :label-width="80">
            <FormItem label="商品模型">
              <Select @on-change="getGoodsSpecAttrInfo(baseData.goods_type)" v-model="baseData.goods_type" style="width:200px">
                <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
            </FormItem>
            <!--商品规格下面动态加载部分-->
            <Row type="flex" justify="start" align="top" class="code-row-bg">
              <!--左边规格部分-->
              <Col span="14">
                <div style="margin-left: 2px;font-size: medium;font-weight: bold;">商品规格:</div>
                <FormItem :key="index" v-for="(item,index) in specList" :label="item.name">
                  <Button :key="cindex" v-for="(childItem,cindex) in item.spec_item" @click="selectSpec(index,cindex)" :type="childItem.checked?'success':'dashed'">{{childItem.item}}</Button>
                </FormItem>
                <!--specInput-->
                <Table border :columns="specInputTitle" :data="specInputBody"></Table>
              </Col>
              <!--右边属性部分-->
              <Col span="6">
                <div style="margin-left: 2px;font-size: medium;font-weight: bold;">商品属性:</div>
                <FormItem v-for="(item,index) in attrList" :key="index" :label="item.attr_name">
                  <Input v-if="item.attr_input_type==0" v-model="attrList[index]['val']"  style="width: 40%;" />
                  <Input v-else-if="item.attr_input_type==2" v-model="attrList[index]['val']" type="textarea"  :autosize="{minRows: 2,maxRows: 5}"  />
                  <Select v-else="item.attr_input_type==1" v-model="attrList[index]['val']"  style="width:200px">
                    <Option v-for="citem in item.attr_values" :value="citem" :key="citem">{{ citem }}</Option>
                  </Select>
                </FormItem>
              </Col>
            </Row>
          </Form>
        </TabPane>
        <TabPane label="积分折扣">
          <Form>
            <FormItem label="赠送积分">
              <Input v-model="baseData.give_integral" style="width: 30%;" />
              订单完成后赠送积分
            </FormItem>
            <FormItem label="兑换积分">
              <Input v-model="baseData.exchange_integral"  style="width: 30%;" />
              不得高于商品最低价格与兑换比的积，如果设置0，则不支持积分抵扣
            </FormItem>
          </Form>
        </TabPane>
      </Tabs>
    </Modal>
  </div>
</template>

<script>
import { getGoodsList,addGoodsData,editGoodsData,editGoodsInfo,deleteGoodsData,goodsSpecAttrList,goodsSpecInput,getNextCat } from '@/api/goods'
import uploadPic from '@/components/upload/upload-pic'
import Editor from '_c/editor'
export default {
  name: 'goods_list',
  components: {
    uploadPic: uploadPic,
    Editor: Editor
  },
  data () {
    return {
      //弹出框默认隐藏
      modal: false,
      //上传地址
      uploadAction: 'http://106.12.18.165/uploadPic.php',
      //商品表单基本信息
      baseData: {},
      //数据总条数
      totalNum: 0,
      //每页数据条数
      listRow: 0,
      //当前页
      currentPage: 1,
      //商品分类
      goodsCat:{},
      //模型
      typeList: [],
      //规格
      specList: [],
      //属性
      attrList: [],
      //规格列表头
      specInputTitle: [],
      //规格列表主体
      specInputBody: [],
      //商品列表头
      columns7: [],
      //商品列表主体
      data6: [],
      //商品列表头基础数据
      actionData: {
        title: '操作',
        key: 'action',
        width: 150,
        align: 'center',
        render: (h, params) => {
          return h('div', [
            h('Button', {
              props: {
                type: 'primary',
                size: 'small'
              },
              style: {
                marginRight: '5px'
              },
              on: {
                click: () => {
                  this.edit(params.index)
                }
              }
            }, '修改'),
            h('Button', {
              props: {
                type: 'error',
                size: 'small'
              },
              on: {
                click: () => {
                  this.remove(params.index)
                }
              }
            }, '删除')
          ]);
        }
      },
      //规格列表基础数据
      specInputActionData: [
        {
          title: '购买价',
          key: 'price',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.price,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['price'] = event.target.value;
                  }
                }
              })
            ]);
          }
        },
        {
          title: '市场价',
          key: 'market_price',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.market_price,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['market_price'] = event.target.value;
                  }
                }
              })
            ]);
          }
        },
        {
          title: '成本价',
          key: 'cost_price',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.cost_price,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['cost_price'] = event.target.value;
                  }
                }
              })
            ]);
          }
        },
        {
          title: '佣金',
          key: 'commission',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.commission,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['commission'] = event.target.value;
                  }
                }
              })
            ]);
          }
        },
        {
          title: '库存',
          key: 'store_count',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.store_count,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['store_count'] = event.target.value;
                  }
                }
              })
            ]);
          }
        },
        {
          title: 'SKU',
          key: 'sku',
          render: (h, params) => {
            var vm = this;
            return h('div', [
              h('Input', {
                props: {
                  value: params.row.sku,
                },
                on: {
                  'on-blur':(event) => {
                    var index = params.index;
                    vm.specInputBody[index]['sku'] = event.target.value;
                  }
                }
              })
            ]);
          }
        }
      ]
    }
  },
  methods: {
    //点击添加按钮
    add(){
      this.delImages();
      this.baseData = {};
      this.specList = [];
      this.attrList = [];
      this.specInputTitle = [];
      this.specInputBody = [];
      this.modal = true;
    },
    //点击列表修改按钮
    edit(index) {
      this.modal = true;
      var id = this.data6[index]['goods_id'];
      editGoodsInfo('',id).then(res => {
        if(res.code==200){
          //商品原图
          this.$refs.originalImg.getDft(res.data.original_img);
          //商品相册
          this.$refs.goodsImgs.getDft(res.data.goods_imgs);
          this.baseData = res.data.goods_info;
          //规格
          this.specList = res.data.goods_spec_list;
          //属性
          this.attrList = res.data.goods_attr_list;
          //商品分类默认选中
          this.getNextCat(res.data.parent_cat[1],'first');
          this.getNextCat(res.data.parent_cat[2],'second');
          this.goodsCat.firstValue = res.data.parent_cat[1];
          this.goodsCat.secondValue = res.data.parent_cat[2];
          //规格输入项
          this.specInputActionData.forEach(function(value,key){
            res.data.spec_input_title.push(value);
          })
          // console.log(res.data.spec_input_title);
          this.specInputTitle = res.data.spec_input_title;
          this.specInputBody = res.data.spec_input_body;
          //重新赋值富文本
          this.$refs.goodsContent.secondRender(this.baseData.goods_content);
        }
      });
    },
    //点击列表删除按钮
    remove(index) {
      this.$Modal.confirm({
        title: '删除',
        content: '<p>您确定要删除吗？</p>',
        onOk: () => {
          var id = this.data6[index]['goods_id'];
          deleteGoodsData('',id).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.goodsList(this.currentPage);
            }
          })
        },
        onCancel: () => {

        }
      });


    },
    //点击弹窗确定后提交数据到后台
    ok(){
      //商品原图
      var original_img = [];
      //商品相册
      var goods_imgs = [];
      if(this.$refs.originalImg.uploadList){
        for (var key in this.$refs.originalImg.uploadList){
          original_img.push(this.$refs.originalImg.uploadList[key]['url'])
        }
      }
      if(this.$refs.goodsImgs.uploadList){
        for (var key in this.$refs.goodsImgs.uploadList){
          goods_imgs.push(this.$refs.goodsImgs.uploadList[key]['url'])
        }
      }
      //商品相册
      var goods_imgs = goods_imgs.join();
      // 拼装数据
      var original_img = original_img.join();
      Object.assign(this.baseData,{original_img:original_img});

      var sendData = {
        //基础数据
        baseData: JSON.stringify(this.baseData),
        //商品相册
        goodsImgs: goods_imgs,
        //商品规格
        specInputBody: JSON.stringify(this.specInputBody),
        //商品属性
        attrList: JSON.stringify(this.attrList)
      };

      // 发起ajax
      if(this.baseData.goods_id){
        editGoodsData('',sendData).then(res => {
          if(res.code==200){
            this.$Message.success(res.msg);
            this.goodsList(this.currentPage);
            this.delImages();
            this.baseData = {};
          }
        })
      }else{
        addGoodsData('',sendData).then(res => {
          if(res.code==200){
            this.$Message.success(res.msg);
            this.goodsList(1);
            this.delImages();
            this.baseData = {};
          }
        })
      }
    },
    //点击弹窗取消按钮
    cancel(){

    },
    //清空图片数据
    delImages(){
      this.$refs.originalImg.delImages();
      this.$refs.goodsImgs.delImages();
    },
    //获取商品数据
    goodsList(page){
      //初始化数据
      getGoodsList('',page).then(res => {
        //加入操作按钮渲染
        res.data.goods_list_title.push(this.actionData);
        this.data6 = res.data.goods_list_body;
        this.columns7 = res.data.goods_list_title;
        this.totalNum = res.data.total_num;
        this.listRow = res.data.list_row;
        this.typeList = res.data.type_list;
        this.goodsCat = res.data.goods_cat;
      })
    },
    //ajax根据模型id获取规格和属性
    getGoodsSpecAttrInfo(type_id){
      goodsSpecAttrList('',type_id).then(res => {
        this.specList = res.data.spec_list;
        this.attrList = res.data.attr_list;
        console.log(this.attrList);
        this.specInputTitle = [];
        this.specInputBody = [];
      })
    },
    //规格点击选中
    selectSpec(index,cindex){
      this.specList[index]['spec_item'][cindex]['checked'] = !this.specList[index]['spec_item'][cindex]['checked'];
      var spec_json = JSON.stringify(this.specList);
      // console.log(spec_json);
      goodsSpecInput('',spec_json).then(res => {
        this.specInputActionData.forEach(function(value,key){
          res.data.spec_input_title.push(value);
        })
        // console.log(res.data.spec_input_title);
        this.specInputTitle = res.data.spec_input_title;
        this.specInputBody = res.data.spec_input_body;
      })
    },
    getNextCat(value,flag){
      getNextCat('',value,flag).then(res => {
        if(res.data.flag=='first'){
          this.goodsCat.second=res.data.catList;
        }else{
          this.goodsCat.third=res.data.catList;
        }
      })
    },
    //分页
    getNextPage(page){
      this.currentPage = page;
      this.goodsList(page);
    },
    //富文本
    handleChange (html, text) {
      console.log(html, text)
    }
  },
  mounted: function(){
    this.goodsList(1);
  }
}
</script>

