<template>
  <Card :bordered="false" :dis-hover="true">
    <template>
      <Button type="primary" @click="duijie_add_model = true" disabled>添加问卷</Button>
      <Modal
        v-model="duijie_add_model"
        title="添加问卷"
        @on-ok="duijie_add('add_wjx')"
        @on-cancel="duijie_add_cancel('add_wjx')"
      >
        <template>
          <Form
            ref="add_wjx"
            :model="add_wjx"
            :rules="ruleValidate"
            label-position="left"
            :label-width="100"
          >
            <FormItem label="问卷平台" prop="qresource">
              <Input v-model="add_wjx.qresource" disabled />
            </FormItem>
            <FormItem label="问卷ID" prop="questionnaireId">
              <Input v-model="add_wjx.questionnaireId" />
            </FormItem>
            <FormItem label="问卷标题" prop="questionnaireName">
              <Input v-model="add_wjx.questionnaireName" placeholder="请输入问卷标题" />
            </FormItem>
          </Form>
        </template>
      </Modal>
    </template>
    <div class="page-table q-card" v-for="item in wjxList" :key="item.id">
      <template>
        <Row>
          <Col span="13">
            <Card>
              <div slot="title" style="font-weight: 700;">{{item.questionnaireName}}</div>
              <div slot="extra">
                <div style="float:right; width:120px">{{item.qtime}}</div>
                <div style="float:right; width:120px">已接收: {{item.qreceive}}</div>
                <div style="float:right; width:130px">问卷id: {{item.questionnaireId}}</div>
                <div
                  style="float:right; width:56px; margin-right: 10px; background-color:#19be6b;color:#ffffff;float:right;border-radius:10px;text-align:center"
                  v-if="item.qresource == '问卷星'"
                >{{item.qresource}}</div>
              </div>
              <Button type="text" icon="ios-power" :to="{path: '/manage/analyse', query: {questionnaireId:item.questionnaireId }}">问卷分析</Button>
              <Button
                type="text"
                icon="ios-power"
                @click="api = true"
                v-if="item.qresource == '问卷星'"
              >数据推送API</Button>
              <Modal v-model="api" title="数据推送API">
                <template>
                  <Input v-model="apiText" placeholder=" " style="width: 420px" />
                  <Button type="primary" @click="doCopy">复制</Button>
                </template>
              </Modal>
              <Button
                type="text"
                style="float: right"
                icon="ios-trash"
                @click="delete_confirm(item.questionnaireId)"
              >删除</Button>
              <Button
                type="text"
                style="float: right"
                icon="ios-power"
                @click="pause_start(item.questionnaireId)"
              >{{item.qstatus}}</Button>
              <Button
                type="text"
                style="float: right"
                icon="ios-power"
                :to="{path: '/manage/dimension', query: {questionnaireId:item.questionnaireId }}"
              >维度管理</Button>
              <Button
                type="text"
                style="float: right"
                icon="ios-power"
                :to="{path: '/manage/record', query: {questionnaireId:item.questionnaireId }}"
              >填写记录</Button>
            </Card>
          </Col>
        </Row>
      </template>
    </div>
    <div class="pager">
      <Page
        v-if="total!=0"
        :total="total"
        :page-size="5"
        :page-size-opts="[5,10]"
        @on-change="pageChange"
        @on-page-size-change="pageSizeChange"
      />
    </div>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      //当前用户的用户ID
      createUserId: 1,
      //问卷星-列表问卷参数
      wjxList: [
        {
          id: "",
          questionnaireName: "",
          qresource: "",
          questionnaireId: "",
          qreceive: "",
          qtime: "",
          qstatus: "",
        },
      ],
      //问卷星-添加弹出框
      add_wjx: {
        qresource: "问卷星",
        questionnaireId: "",
        questionnaireName: "",
      },
      //问卷星-添加弹出框-显示状态
      duijie_add_model: false,
      //问卷星-填写校验
      ruleValidate: {
        questionnaireId: [
          {
            required: true,
            message: "问卷ID不能为空",
            trigger: "blur",
          },
        ],
        questionnaireName: [
          {
            required: true,
            message: "请输入问卷题目",
            trigger: "blur",
          },
          {
            type: "string",
            max: 50,
            message: "内容不能超过50个字",
            trigger: "blur",
          },
        ],
      },
      //问卷星-api
      apiText: "http://127.0.0.1/controller/api.php",
      api: false,
      // 页面分页数据
      pageSize: 5,
      current: 1,
      total: 0,
    };
  },
  methods: {
    duijie_add_cancel(name) {},
    //复制内容到剪切板
    doCopy: function () {
      var that = this;
      that.$copyText(this.apiText).then(
        function (e) {
          that.$Message.success("复制成功");
        },
        function (e) {
          that.$Message.success("复制失败，请手动复制");
        }
      );
    },
    //问卷星-添加问卷
    duijie_add(name) {
      var that = this;
      var data = {
        qresource: that.add_wjx.qresource,
        questionnaireId: that.add_wjx.questionnaireId,
        questionnaireName: that.add_wjx.questionnaireName,
        createUserId: that.createUserId,
      };
      that.$refs[name].validate((valid) => {
        if (valid) {
          // console.log(data);
          that.$axios
            .post("/index.php?function=add_wjx", data)
            .then(function (response) {
              var result = response.data;
              // console.log(result);
              if (result.code == 1) {
                that.$Message.success(result.msg);
                that.all_getList();
              } else {
                that.$Message.error(result.msg);
                that.all_getList();
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        } else {
          that.$Message.error("添加失败");
        }
      });
      //清空输入框内容
      that.add_wjx = {
        qresource: "问卷星",
        questionnaireId: "",
        questionnaireName: "",
      };
    },
    //问卷星-删除问卷前提醒
    delete_confirm(questionnaireId) {
      var that = this;
      that.$Modal.confirm({
        title: "警告",
        content: "确定要删除该条问卷吗？",
        onOk: () => {
          that.delete_wjx(questionnaireId);
        },
        onCancel: () => {
          // this.$Message.info("Clicked cancel");
        },
      });
    },
    //问卷星-删除问卷
    delete_wjx(questionnaireId) {
      var that = this;
      var data = {
        questionnaireId: questionnaireId,
      };
      // console.log(data);
      that.$axios
        .post("/index.php?function=delete_wjx", data)
        .then(function (response) {
          var result = response.data;
          // console.log(result);
          if (result.code == 1) {
            that.$Message.success(result.msg);
            that.all_getList();
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //问卷星-开始暂停
    pause_start(questionnaireId) {
      var that = this;
      var data = {
        questionnaireId: questionnaireId,
      };
      that.$axios
        .post("/index.php?function=pause_start", data)
        .then(function (response) {
          var result = response.data;
          // console.log(result);
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            that.all_getList();
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //获取所有问卷列表数据
    all_getList() {
      var that = this;
      var data = {
        pageSize: that.pageSize, //每一页显示的条数
        current: that.current, //当前处于第几页
        // total: that.total //记录的总条数
      };
      // console.log(data);
      that.$axios
        .post("/index.php?function=all_getList", data)
        .then(function (response) {
          var result = response.data;
          // console.log(result);
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            // console.log(result.data);
            that.wjxList = result.data;
            that.total = result.total;
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    // 页码改变
    pageChange(value) {
      var that = this;
      that.current = value;
      that.all_getList();
    },
    // 页码大小改变
    pageSizeChange(value) {
      var that = this;
      that.current = 1;
      that.pageSize = value;
      that.all_getList();
    },
  },
  created() {},
  mounted() {
    this.all_getList();
  },
};
</script>