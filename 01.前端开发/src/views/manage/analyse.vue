<template>
  <Card :bordered="false" :dis-hover="true">
    <template>
      <Select
        v-model="questionnaireListModel"
        style="width:300px"
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
    <div class="page-table">
      <div v-for="(item,index) in analyseTitleList" :key="item.id">
        <div class="page-table">
          <strong>第{{item.qid}}题：</strong>
          {{item.qcontent}}[{{item.qtype}}]
        </div>
        <Button
          size="small"
          type="primary"
          v-if="item.qtype == '填空题' | item.qtype == '矩阵文本题' |item.qtype == '滑动条'"
          @click="showAnalyseContent(item.qid)"
        >查看详情</Button>
        <Table
          :columns="analyseColumns"
          :data="analyseData[index]"
          v-if="item.qtype == '单选题' | item.qtype == '多选题' "
        ></Table>
      </div>
    </div>
    <Modal v-model="showContentStatus" title="详细信息">
      <Table
        :loading="loadingStatus"
        height="400"
        border
        :columns="contenColumns"
        :data="contentData"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id+1 }}</strong>
        </template>
      </Table>
    </Modal>
  </Card>
</template>
<script>
// 单选题 1
// 多选题 2
// 填空题-单个填空 3
// 填空题-多个填空 4
// 矩阵题-单选 5
// 矩阵题-多选 6
//
export default {
  data() {
    return {
      loadingStatus: false, //弹出表格加载中
      showContentStatus: false,
      //弹出表格字段
      contenColumns: [
        {
          title: "序号",
          slot: "id",
          width: 80,
        },
        {
          title: "填写人",
          key: "name",
          width: 80,
        },
        {
          title: "答案文本",
          key: "option",
        },
        {
          title: "填写时间",
          key: "time",
          width: 150,
        },
      ],
      //弹出表格数据
      contentData: [
        {
          id: "",
          name: "",
          option: "",
          time: "",
        },
      ],
      //表格字段
      analyseColumns: [
        {
          title: "选项",
          key: "option",
        },
        {
          title: "小计",
          key: "count",
        },
        {
          title: "比例",
          key: "percent",
        },
      ],
      //表格内容
      analyseData: [
        {
          option: "",
          count: "",
          percent: "",
        },
      ],
      //题目数据
      analyseTitleList: [
        {
          id: "",
          qid: "",
          qcontent: "",
          qtype: "",
        },
      ],
      //顶部下拉
      questionnaireListModel: "",
      //问卷管理-下拉-问卷名称列表
      questionnaireList: [
        {
          value: "",
          label: "",
        },
      ],
      //问卷展示列表-查看详情-modal默认为不显示
      analyse_list_modal1: false,
    };
  },
  created() {},
  mounted() {
    var that = this;
    that.getQuestionnaireList(that.$route.query.questionnaireId);
    that.getAnalyseList(that.$route.query.questionnaireId);
  },
  methods: {
    //点击查看本道题目所有人的填写情况
    showAnalyseContent(value) {
      var that = this;
      that.showContentStatus = true;
      var data = {
        questionnaireId: that.$route.query.questionnaireId,
        qid: value,
      };
      that.$axios
        .post("/index.php?function=showAnalyseContent", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            that.contentData = result.contentData;
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //获取题目列表信息
    getAnalyseList(questionnaireId) {
      var that = this;
      var data = {
        questionnaireId: questionnaireId,
      };
      that.$axios
        .post("/index.php?function=getAnalyseList", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            // that.$Message.success(result.msg);
            that.analyseTitleList = result.analyseTitleList;
            // that.analyseData = result.analyseData;
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
  },
};
</script>