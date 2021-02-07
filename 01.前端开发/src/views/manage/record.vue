<template>
  <Card :bordered="false" :dis-hover="true">
    <template>
      <Select
        v-model="questionnaireListModel"
        style="width:300px; float:left; margin-right:10px"
        placeholder
        @on-change="questionnaireListChange(questionnaireListModel)"
        disabled
      >
        <Option
          v-for="item in questionnaireList"
          :value="item.value"
          :key="item.value"
        >{{ item.label }}</Option>
      </Select>
    </template>
    <Input search enter-button="搜索" placeholder="输入搜索内容" style="width:300px" />
    <div class="page-table">
      <Table border :columns="recordColumns" :data="recordData">
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id+1+pageSize*(current-1) }}</strong>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <Button
            type="primary"
            size="small"
            style="margin-right: 5px"
            @click="showRecordContent(index)"
          >详细</Button>

          <Button type="success" size="small" @click="showRecordReport(index)">测评报告</Button>
        </template>
      </Table>
    </div>
    <div class="pager">
      <Page
        v-if="total!=0"
        :total="total"
        :page-size="10"
        :page-size-opts="[5,10]"
        @on-change="pageChange"
        @on-page-size-change="pageSizeChange"
      />
    </div>
    <Modal v-model="showRecordContentStatus" title="详细信息">
      <Table
        :loading="loadingStatus"
        height="400"
        border
        ref="Qselection"
        :columns="record_content_columns"
        :data="record_content_data"
      ></Table>
    </Modal>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      //流水号-即每个填写用户的唯一id
      bak: [
        {
          joinid: "",
        },
      ],
      //记录列表，左侧添加按钮，加载中
      loadingStatus: true,
      //记录列表，加载报告，加载中
      reportLoadingStatus: false,
      //填写记录列表中，每一条记录点开后详细填写内容表格字段
      record_content_columns: [
        {
          title: "题号",
          width: 85,
          key: "qId",
        },
        {
          title: "题目内容",
          key: "qContent",
        },
        {
          title: "填写答案",
          key: "qAnswer",
        },
      ],
      //填写记录列表中，每一条记录点开后详细填写内容表格内容
      record_content_data: [
        {
          qId: "",
          qAnswer: "",
          qContent: "",
        },
      ],
      //问卷填写记录界面，点击详细按钮是否弹框的状态
      showRecordContentStatus: false,
      //问卷管理-下拉-问卷名称列表
      questionnaireList: [
        {
          value: "",
          label: "",
        },
      ],
      //问卷管理-填写记录-表格字段
      recordColumns: [
        {
          title: "序号",
          slot: "id",
        },
        {
          title: "姓名",
          key: "name",
        },
        {
          title: "性别",
          key: "sex",
        },
        {
          title: "学校",
          key: "school",
        },
        {
          title: "年级",
          key: "grade",
        },
        {
          title: "班级",
          key: "class",
        },
        {
          title: "总得分",
          key: "totalValue",
        },
        {
          title: "提交时间",
          key: "submitTime",
        },
        {
          title: "操作",
          slot: "action",
          width: 200,
          align: "center",
        },
      ],
      //问卷管理-填写记录-表格数据
      recordData: [
        {
          id: "",
          name: "",
          sex: "",
          school: "",
          grade: "",
          class: "",
          totalValue: "",
          submitTime: "",
        },
      ],
      questionnaireListModel: "",
      // 页面分页数据
      pageSize: 10,
      current: 1,
      total: 0,
    };
  },
  created() {},
  mounted() {
    var that = this;
    that.getQuestionnaireList(that.$route.query.questionnaireId);
    that.questionnaireListChange(that.$route.query.questionnaireId);
  },
  methods: {
    //问卷管理-问卷填写记录-查看详细信息
    showRecordContent(index) {
      var that = this;
      that.showRecordContentStatus = true;
      var data = {
        questionnaireId: that.questionnaireListModel,
        joinid: that.bak[index].joinid,
      };
      that.$axios
        .post("/index.php?function=getRecordContent", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            that.record_content_data = result.data;
            that.loadingStatus = false;
            // that.$Message.success(result.msg);
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //问卷管理-问卷填写记录-查看报告
    showRecordReport(index) {
      var that = this;
      var data = {
        questionnaireId: that.questionnaireListModel,
        joinid: that.bak[index].joinid,
      };
      that.$router.push({
        path: "/manage/report",
        query: {
          questionnaireId: that.questionnaireListModel,
          joinid: that.bak[index].joinid,
        },
      });
    },
    //问卷管理-下拉-更换选项时触发
    questionnaireListChange(vaule) {
      var that = this;
      var data = {
        questionnaireId: vaule,
        pageSize: that.pageSize, //每一页显示的条数
        current: that.current, //当前处于第几页
      };
      that.$axios
        .post("/index.php?function=getRecordList", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            that.recordData = result.data;
            that.total = result.total;
            that.bak = result.bak;
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //问卷管理-填写记录-下拉内容
    getQuestionnaireList(selected) {
      var that = this;
      var data = {};
      that.$axios
        .post("/index.php?function=getQuestionnaireList", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            that.questionnaireList = result.data;
            that.questionnaireListModel = selected;
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    // 页面搜索
    pageSearch() {
      // let that = this;
      // that.current = 1;
      // that.questionnaireListChange(that.$route.query.questionnaireId);
    },
    // 页码改变
    pageChange(value) {
      let that = this;
      that.current = value;
      that.questionnaireListChange(that.$route.query.questionnaireId);
    },
    // 页码大小改变
    pageSizeChange(value) {
      // let that = this;
      // that.current = 1;
      // that.pageSize = value;
      // that.questionnaireListChange(that.$route.query.questionnaireId);
    },
  },
};
</script>