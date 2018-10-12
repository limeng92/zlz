<template>
  <div>
    <div style="margin-bottom: 5px;">
      <Button @click="add" class="search-btn" style="margin-left: 2px;" type="success">新增分类</Button>
    </div>
    <tree-grid
      :items='data6'
      :columns='columns7'
      @on-row-click='rowClick'
    ></tree-grid>
    <Modal
    v-model="modal"
    title="修改"
    width="80%"
    @on-ok="ok"
    @on-cancel="cancel">
    <Form :model="fData" :label-width="80">
      <FormItem label="分类名称">
        <Input v-model="fData.name" placeholder="分类名称" style="width: 40%;" />
      </FormItem>
      <FormItem label="手机分类名称">
        <Input v-model="fData.mobile_name" placeholder="手机分类名称" style="width: 40%;" />
      </FormItem>
      <FormItem label="上级分类">
        <Select v-model="fData.parent_id" style="width:200px">
          <Option v-for="item in catList" :value="item.value" :key="item.value">{{ item.label }}</Option>
        </Select>
      </FormItem>
      <FormItem label="导航显示">
        <RadioGroup v-model="fData.is_show">
          <Radio label="1">是</Radio>
          <Radio label="0">否</Radio>
        </RadioGroup>
      </FormItem>
      <FormItem label="分类分组">
        <Select v-model="fData.cat_group" style="width:200px">
          <Option v-for="item in groupList" :value="item.value" :key="item.value">{{ item.label }}</Option>
        </Select>
      </FormItem>
      <FormItem label="分类展示图片">
        <div style="display: inline-block;vertical-align: middle;line-height: normal;">
          <upload-pic ref="categoryImg" :action="uploadAction"></upload-pic>
          <p>此分类图片用于手机端显示, 并有且仅是第三级分类上传的图片才有效</p>
        </div>
      </FormItem>
      <FormItem label="排序">
        <Input v-model="fData.sort_order" placeholder="排序" style="width: 40%;" />
      </FormItem>
    </Form>
  </Modal>
  </div>
</template>
<script>
  import { getGoodsCategoryList,editGoodsCategoryData,addGoodsCategoryData,deleteGoodsCategoryData,editGoodsCategoryInfo } from '@/api/goods_category'
  import TreeGrid from '@/components/grid/treeGrid2.0'
  import uploadPic from '@/components/upload/upload-pic'
  export default {
    name: 'goods_category',
    components: {
      uploadPic: uploadPic,
      treeGrid: TreeGrid
    },
    data () {
      return {
        modal: false,
        uploadAction: 'http://106.12.18.165/uploadPic.php',
        fData: {},
        catList: [],
        groupList: [],
        columns7: [],
        data6: []
      }
    },
    methods: {
      rowClick(data, index, event,name) {
        if(name=='编辑'){
          this.edit(data.id);
        }else if(name=='增加子分类'){
          //增加子分类
          this.add();
          this.fData.parent_id = data.id;

        }else{
          //删除
          this.remove(data.id);
        }
      },
      //点击添加按钮
      add(){
        this.delImages();
        this.fData = {};
        this.modal = true;
      },
      //点击列表修改按钮
      edit(id) {
        editGoodsCategoryInfo('',id).then(res => {
          if(res.code==200){
            if(res.data.image){
              this.$refs.categoryImg.getDft(res.data.image);
            }
            this.fData = res.data.edit_info;
            this.modal = true;
          }
        });
      },
      //点击列表删除按钮
      remove(id) {
        this.$Modal.confirm({
          title: '删除',
          content: '<p>您确定要删除吗？</p>',
          onOk: () => {
            deleteGoodsCategoryData('',id).then(res => {
              if(res.code==200){
                this.$Message.success(res.msg);
                this.goodsCategoryList();
              }
            })
          },
          onCancel: () => {

          }
        });


      },
      //点击弹窗确定后提交数据到后台
      ok(){
        var image = [];
        if(this.$refs.categoryImg.uploadList){
          for (var key in this.$refs.categoryImg.uploadList){
            image.push(this.$refs.categoryImg.uploadList[key]['url'])
          }
          //拼装数据
          var image = image.join();
          Object.assign(this.fData,{image:image});
        }

        //发起ajax
        if(this.fData.id){
          editGoodsCategoryData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.goodsCategoryList();
              this.delImages();
              this.fData = {};
            }
          })
        }else{
          addGoodsCategoryData('',this.fData).then(res => {
            if(res.code==200){
              this.$Message.success(res.msg);
              this.goodsCategoryList();
              this.delImages();
              this.fData = {};
            }
          })
        }
      },
      //点击弹窗取消按钮
      cancel(){

      },
      //清空图片数据
      delImages(){
        this.$refs.categoryImg.delImages();
      },
      //获取商品分类数据
      goodsCategoryList(){
        //初始化数据
        getGoodsCategoryList('').then(res => {
          //加入操作按钮渲染
          this.data6 = res.data.goods_category_body;
          this.columns7 = res.data.goods_category_title;
          this.catList = res.data.cat_list;
          this.groupList = res.data.group_list;
        })
      },
      getNextPage(page){
        this.currentPage = page;
        this.goodsCategoryList(page);
      }
    },
    mounted: function(){
      this.goodsCategoryList();
    }
  }
</script>
<style>
  .ivu-icon-plus-circled:before {
    content: "\F216";
  }
  .ivu-icon-minus-circled:before {
    content: "\F207";
  }
</style>
