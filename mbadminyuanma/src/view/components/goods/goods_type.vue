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
        <FormItem label="模型名称">
          <Input v-model="fData.name" placeholder="模型名称" style="width: 40%;" />
        </FormItem>
      </Form>
    </Modal>
  </div>
</template>

<script>
  import { getGoodsTypeList,addGoodsTypeData,editGoodsTypeData,deleteGoodsTypeData,editGoodsTypeInfo } from '@/api/goods_type'
  export default {
    name: 'goods_type',
    data () {
      return {
        modal: false,
        fData: {},
        columns7: [],
        data6: [],
        actionData: {
          title: '操作',
          key: 'action',
          width: 400,
          align: 'center',
          render: (h, params) => {
            return h('div', [
              h('Button', {
                props: {
                  type: 'default',
                  size: 'small'
                },
                style: {
                  marginRight: '5px',
                },
                on: {
                  click: () => {
                    this.goodsAttrList()
                  }
                }
              }, '属性列表'),
              h('Button', {
                props: {
                  type: 'default',
                  size: 'small'
                },
                style: {
                  marginRight: '5px',
                },
                on: {
                  click: () => {
                    this.goodsSpecList()
                  }
                }
              }, '规格列表'),
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
        var id = this.data6[index]['id'];
        editGoodsTypeInfo('',id).then(res => {
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
            var id = this.data6[index]['id'];
            deleteGoodsTypeData('',id).then(res => {
              if(res.code==200){
                this.$Message.success(res.msg);
                this.getGoodsTypeList();
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
        if(this.fData.id){
          editGoodsTypeData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsTypeList();
              this.fData = {};
            }
          })
        }else{
          addGoodsTypeData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsTypeList();
              this.fData = {};
            }
          })
        }
      },
      //点击弹窗取消按钮
      cancel(){

      },
      //获取商品数据
      getGoodsTypeList(){
        //初始化数据
        getGoodsTypeList('').then(res => {
          //加入操作按钮渲染
          res.data.goods_type_title.push(this.actionData);
          this.data6 = res.data.goods_type_body;
          this.columns7 = res.data.goods_type_title;
        })
      },
      goodsAttrList(){
        this.$router.push({
          name: 'goods_attr'
        })
      },
      goodsSpecList(){
        this.$router.push({
          name: 'goods_spec'
        })
      }
    },
    mounted: function(){
      this.getGoodsTypeList();
    }
  }
</script>
<style>
  .goods_info{
    margin-top: 8px;
  }
</style>
