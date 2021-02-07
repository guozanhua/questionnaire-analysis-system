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
    <div class="page-table q-card">
      <template>
        <!-- <Col span="15"> -->
          <Card :bordered="false" :dis-hover="true">
            <div style="text-align:center" slot="title">
              <h2>{{title}}</h2>
            </div>
            <Card :bordered="false" :dis-hover="true">
              <h3>
                <strong>{{title1}}</strong>
              </h3>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p1}}</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p2}}</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p3}}</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p4}}</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p5}}</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;{{p6}}</p>
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <img src="../../../static/images/model.png" style="width:800px; height:400px" />
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <p>{{p7}}</p>
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <div id="myradar" :style="{width: '600px', height: '600px'}"></div>
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <h3>
                <strong>{{title2}}</strong>
              </h3>
              <p>{{name}}</p>
            </Card :bordered="false" :dis-hover="true">
            <Card :bordered="false">
              <h3>
                <strong>{{title3}}</strong>
              </h3>
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <template>
                <Table :columns="resultColumns" :data="resultData"></Table>
              </template>
            </Card>
            <Card :bordered="false" :dis-hover="true">
              <template>
                <Button label="large" icon="ios-download-outline" type="primary">保存报告</Button>
              </template>
            </Card>
          </Card>
        <!-- </Col> -->
      </template>
    </div>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      title: "（高中版）自主学习力诊断测验",
      title1: "测评说明",
      title2: "测评用户",
      title3: "得分",
      name: "", //动态获取
      p1: "本测验的依据是自主学习力的“火箭模型”（见下图）。",
      p2:
        "自主学习力是学生学业发展的核心潜力，包括学习能力、学习动力和学习阻力三大成分。",
      p3: "学习能力又包括元认知能力、资源管理能力和认知能力三个类别；",
      p4:
        "学习动力又包括外部在动力、内在动力和深层动力三个类别；这些类别下，又可细分为多个维度。",
      p5: "学习阻力直接包括多个维度。",
      p6:
        "本测验的结果，就是这些维度上的分数。结果解释参照的标准，是前期测验数据中各学段的平均值和标准差（常模）。前期我们进行了多批次大规模调查（共20多万中小学生），基于这些数据，经过多次修订，形成了如下自主学习力的“火箭模型”。",
      p7:
        "下面是高中阶段学生自主学习力的平均水平（蓝色线条）和理想状态（橙色线条）的雷达图。本报告底部，会根据您的测验分数，生成您自己的雷达图，您可来分析自己的优势和发展空间。",
      p8: "",
      //表格字段
      resultColumns: [
        {
          title: "维度名称",
          key: "dname",
          width: 200,
        },
        {
          title: "得分",
          key: "score",
          width: 150,
        },
        {
          title: "标准分",
          key: "stand",
          width: 150,
        },
        {
          title: "结果与建议",
          key: "result",
        },
      ],
      //表格数据
      resultData: [
        {
          dname: "自我价值",
          score: 3.83,
          stand: 4.75,
          result: "维度含义、分数解释、训练方法等，正在更新……",
        },
      ],
      //问卷管理-下拉-问卷名称列表
      questionnaireList: [
        {
          value: "",
          label: "",
        },
      ],
      //选中问卷
      questionnaireListModel: "",
    };
  },
  created() {},
  mounted() {
    var that = this;
    that.getQuestionnaireList(that.$route.query.questionnaireId);
    that.getReportData();
  },
  methods: {
    //获取报告中的数据
    getReportData() {
      var that = this;
      var data = {
        joinid: that.$route.query.joinid,
      };
      that.$axios
        .post("/index.php?function=getReportData", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            that.name = result.name; //姓名
            that.resultData = result.table_all; //表格数据
            that.radar(
              result.radar_person,
              result.radar_stand,
              result.radar_indicator,
              result.radar_gaosan,
              result.radar_lixiang
            );
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //雷达图加载
    radar(
      radar_person,
      radar_stand,
      radar_indicator,
      radar_gaosan,
      radar_lixiang
    ) {
      var that = this;
      let myradar = that.$echarts.init(document.getElementById("myradar"));
      var option = {
        title: {
          text: "",
        },
        tooltip: {},
        legend: {
          data: ["高三均值", "理想状态", "标准分", "个人得分"],
        },
        radar: {
          // shape: 'circle',
          name: {
            textStyle: {
              color: "#fff",
              backgroundColor: "#999",
              borderRadius: 3,
              padding: [3, 5],
            },
          },
          indicator: [],
        },
        series: [
          {
            name: "自主学习力诊断",
            type: "radar",
            areaStyle: { normal: {} },
            data: [
              {
                value: [],
                name: "高三均值",
              },
              {
                value: [],
                name: "理想状态",
              },
              {
                value: [],
                name: "标准分",
              },
              {
                value: [],
                name: "个人得分",
              },
            ],
          },
        ],
      };
      option.series[0].data[0].value = radar_gaosan;
      option.series[0].data[1].value = radar_lixiang;
      option.series[0].data[2].value = radar_stand;
      option.series[0].data[3].value = radar_person;
      option.radar.indicator = radar_indicator;
      myradar.setOption(option);
      // console.log(option.series[0].data[0].value); //高三均值--数据来源未知
      // console.log(option.series[0].data[1].value); //理想状态--数据来源未知
      // console.log(option.series[0].data[2].value); //标准分--固定且已知
      // console.log(option.series[0].data[3].value); //个人得分--每个维度包含的所有题目的得分之和除以题目的数量
      // console.log(option.radar.indicator); //维度名称和均值
    },
    //最上方-下拉内容-提醒当前为哪个问卷
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