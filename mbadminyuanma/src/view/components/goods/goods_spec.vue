<template>
  <div>
    <div style="margin-bottom: 5px;">
      <Button @click="add" class="search-btn" style="margin-left: 2px;" type="success">新建</Button>
    </div>
    <Table border :columns="columns7" :data="data6"></Table>
    <Page style="float:right;" @on-change="getNextPage" :page-size="listRow" :total="totalNum" />
    <Modal
      v-model="modal"
      title="修改"
      width="80%"
      @on-ok="ok"
      @on-cancel="cancel">
      <Form :model="fData" :label-width="80">
        <FormItem label="规格名称">
          <Input v-model="fData.name" placeholder="规格名称" style="width: 40%;" />
        </FormItem>
        <FormItem label="所属商品模型">
          <Select v-model="fData.type_id" style="width:200px">
            <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}</Option>
          </Select>
        </FormItem>
        <FormItem label="规格项">
          <Input v-model="fData.item" type="textarea" style="width:400px;" :autosize="{minRows: 6,maxRows: 6}" ></Input>
          <p>一行为一个规格项，多个规格项用换行输入</p>
        </FormItem>
        <FormItem label="排序">
          <Input v-model="fData.order" placeholder="排序" style="width: 40%;" />
        </FormItem>
      </Form>
    </Modal>
  </div>
</template>

<script>
  import { getGoodsSpecList,addGoodsSpecData,editGoodsSpecData,deleteGoodsSpecData,editGoodsSpecInfo } from '@/api/goods_spec'
  export default {
    name: 'goods_attr',
    data () {
      return {
        modal: false,
        fData: {},
        typeList: [],
        columns7: [],
        data6: [],
        listRow: 0,
        totalNum: 0,
        currentPage: 1,
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
        var id = this.data6[index]['id'];
        editGoodsSpecInfo('',id).then(res => {
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
            deleteGoodsSpecData('',id).then(res => {
              if(res.code==200){
                this.$Message.success(res.msg);
                this.getGoodsSpecList(this.currentPage);
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
          editGoodsSpecData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsSpecList(this.currentPage);
              this.fData = {};
            }
          })
        }else{
          addGoodsSpecData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.getGoodsSpecList(this.currentPage);
              this.fData = {};
            }
          })
        }
      },
      //点击弹窗取消按钮
      cancel(){

      },
      //获取商品数据
      getGoodsSpecList(page){
        //初始化数据
        getGoodsSpecList('',page).then(res => {
          //加入操作按钮渲染
          res.data.goods_spec_title.push(this.actionData);
          this.data6 = res.data.goods_spec_body;
          this.columns7 = res.data.goods_spec_title;
          this.typeList = res.data.type_list;
          this.totalNum = res.data.total_num;
          this.listRow = res.data.list_row;
        })
      },
      getNextPage(page){
        this.currentPage = page;
        this.getGoodsSpecList(page);
      }
    },
    mounted: function(){
      this.getGoodsSpecList(this.currentPage);
    }
  }
</script>

