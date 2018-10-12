<template>
  <div>
    <div style="margin-bottom: 5px;">
      <Button @click="add" class="search-btn" style="margin-left: 2px;" type="success">新建</Button>
    </div>
    <Table border :columns="columns7" :data="data6"></Table>
    <Modal
      v-model="modal"
      title="修改"
      width="80%"
      @on-ok="ok"
      @on-cancel="cancel">
      <Form :model="fData" :label-width="80">
        <FormItem label="属性名称">
          <Input v-model="fData.attr_name" placeholder="属性名称" style="width: 40%;" />
        </FormItem>
        <FormItem label="所属商品模型">
          <Select v-model="fData.type_id" style="width:200px">
            <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}</Option>
          </Select>
        </FormItem>
        <FormItem label="是否进行检索">
          <RadioGroup v-model="fData.attr_index">
            <Radio label="1">关键字检索</Radio>
            <Radio label="0">不需要检索</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="该属性值录入方式">
          <RadioGroup v-model="fData.attr_input_type">
            <Radio label="2">多行文本框</Radio>
            <Radio label="1">从下面的列表选择(一行代表一个可选值)</Radio>
            <Radio label="0">手工录入</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="可选值列表">
          <Input v-model="fData.attr_values" type="textarea" style="width:400px;" :autosize="{minRows: 6,maxRows: 6}" ></Input>
          <p>录入方式为手工或多行文本时，此输入框不需要填写</p>
        </FormItem>
      </Form>
    </Modal>
  </div>
</template>

<script>
  import { getGoodsAttrList,addGoodsAttrData,editGoodsAttrData,deleteGoodsAttrData,editGoodsAttrInfo } from '@/api/goods_attr'
  export default {
    name: 'goods_attr',
    data () {
      return {
        modal: false,
        fData: {},
        typeList: [],
        columns7: [],
        data6: [],
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
        }
      }
    },
    methods: {
      //点击添加按钮
      add(){
        this.fData = {};
        this.modal = true;
      },
      //点击列表修改按钮
      edit(index) {
        var id = this.data6[index]['attr_id'];
        editGoodsAttrInfo('',id).then(res => {
          if(res.code==200){
            this.fData = res.data.edit_info;
            this.modal = true;
          }
        });
      },
      //点击列表删除按钮
      remove(index) {
        this.$Modal.confirm({
          title: '删除',
          content: '<p>您确定要删除吗？</p>',
          onOk: () => {
            var id = this.data6[index]['attr_id'];
            deleteGoodsAttrData('',id).then(res => {
              if(res.code==200){
                this.$Message.success(res.msg);
                this.getGoodsAttrList();
              }
            })
          },
          onCancel: () => {

          }
        });
      },
      //点击弹窗确定后提交数据到后台
      ok(){
        //发起ajax
        if(this.fData.attr_id){
          editGoodsAttrData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsAttrList();
              this.fData = {};
            }
          })
        }else{
          addGoodsAttrData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsAttrList();
              this.fData = {};
            }
          })
        }
      },
      //点击弹窗取消按钮
      cancel(){

      },
      //获取商品数据
      getGoodsAttrList(){
        //初始化数据
        getGoodsAttrList('').then(res => {
          //加入操作按钮渲染
          res.data.goods_type_title.push(this.actionData);
          this.data6 = res.data.goods_type_body;
          this.columns7 = res.data.goods_type_title;
          this.typeList = res.data.type_list;
        })
      }
    },
    mounted: function(){
      this.getGoodsAttrList();
    }
  }
</script>

