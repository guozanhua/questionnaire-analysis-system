<template>
  <Card :bordered="false" :dis-hover="true">
    <template>
      <Button type="primary" @click="dimensionAdd_status = true">添加维度</Button>
      <Modal
        v-model="dimensionAdd_status"
        title="添加维度"
        @on-ok="handleSubmit('formValidate')"
        @on-cancel="handleReset('formValidate')"
      >
        <template>
          <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
            <FormItem label="维度名称" prop="name">
              <Input v-model="formValidate.name" />
            </FormItem>
            <FormItem label="维度意义" prop="desc">
              <Input
                v-model="formValidate.desc"
                type="textarea"
                :autosize="{minRows: 2,maxRows: 5}"
              />
            </FormItem>
          </Form>
        </template>
      </Modal>
    </template>
    <template>
      <Select v-model="questionnaireListModel" style="width:300px" placeholder disabled>
        <Option
          v-for="item in questionnaireList"
          :value="item.value"
          :key="item.value"
        >{{ item.label }}</Option>
      </Select>
    </template>
    <div class="page-table">
      <template>
        <Table border :columns="dimensionColumns" :data="dimensionData">
          <template slot-scope="{ row }" slot="id">
            <strong>{{ row.id+1+pageSize*(current-1) }}</strong>
          </template>
          <template slot-scope="{ row, index }" slot="action">
            <Button type="primary" size="small" @click="dimensionEdit(index)">编辑</Button>
            <Button type="success" size="small" @click="dimension_add_q(index)">添加</Button>

            <Button type="error" size="small" @click="dimensionDelete(index)">删除</Button>
          </template>
        </Table>
      </template>
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
    <!-- 编辑维度信息 -->
    <Modal
      v-model="dimensionEdit_status"
      title="维度信息编辑"
      @on-ok="dimension_edit('formItem')"
      @on-cancel="dimension_edit_cancel('formItem')"
    >
      <Form ref="formItem" :model="formItem" :rules="ruleValidate" :label-width="95">
        <FormItem label="维度名称" prop="name">
          <Input v-if="dimensionData[dimensionIndex]" v-model="formItem.name" placeholder=" " />
        </FormItem>
        <FormItem label="维度含义" prop="mean">
          <Input
            v-if="dimensionData[dimensionIndex]"
            v-model="formItem.mean"
            type="textarea"
            :autosize="{minRows: 2,maxRows: 5}"
            placeholder=" "
          />
        </FormItem>
        <FormItem label="维度标准分" prop="standardScore">
          <InputNumber
            v-if="dimensionData[dimensionIndex]"
            v-model="formItem.standardScore"
            :max="10"
            :min="0"
            :step="0.1"
          ></InputNumber>
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="dimensionAddQ_status"
      title="添加题目"
      @on-ok="dimension_add"
      @on-cancel="dimension_add_cancel"
    >
      <Table
        :loading="loadingStatus"
        height="400"
        border
        :columns="dimension_q_list_columns"
        :data="dimension_q_list_data"
        @on-select="q_on_select"
        @on-select-cancel="q_on_select_cancel"
        @on-select-all="q_on_select_all"
      ></Table>
    </Modal>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      //维度添加列表中，存储已经选择的项
      submitSelection: [{}],
      //维度列表，左侧添加按钮，加载中
      loadingStatus: true,
      //维度列表，编辑添加删除选中的下标
      dimensionIndex: "",
      //维度信息编辑
      formItem: {
        name: "",
        mean: "",
        standardScore: "",
      },
      //添加维度-维度名称内容
      formValidate: {
        name: "",
        desc: "",
      },
      //添加维度-校验信息
      ruleValidate: {
        name: [
          {
            required: true,
            message: "维度名称不能为空",
            trigger: "blur",
          },
        ],
        desc: [
          {
            required: false,
            message: "请输入维度意义",
            trigger: "blur",
          },
          {
            type: "string",
            max: 200,
            message: "内容不能超过200个字",
            trigger: "blur",
          },
        ],
        standardScore: [
          {
            required: true,
            type: "number",
            message: "维度标准分不能为空",
          },
        ],
      },
      //添加维度
      dimensionAdd_status: false,
      //编辑维度
      dimensionEdit_status: false,
      //维度下添加对应题目
      dimensionAddQ_status: false,
      dimension_q_list_columns: [
        {
          type: "selection",
          width: 60,
          align: "center",
        },
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
          title: "已绑定维度",
          key: "dimension",
        },
      ],
      dimension_q_list_data: [
        {
          qId: "",
          qContent: "",
          dimension: "",
          _disabled: true, //如果此题目已经被其他维度添加，此处为不可选
          _checked: true, //为选中状态
        },
      ],
      //问卷管理-维度管理-维度表格字段
      dimensionColumns: [
        {
          title: "序号",
          slot: "id",
        },
        {
          title: "维度名称",
          key: "name",
        },
        {
          title: "维度对应含义",
          key: "mean",
        },
        {
          title: "包含题目数量",
          key: "count",
        },
        {
          title: "维度标准分",
          key: "standardScore",
        },
        {
          title: "操作",
          slot: "action",
          width: 200,
          align: "center",
        },
      ],
      //问卷管理-维度管理-维度表格内容
      dimensionData: [
        {
          id: "",
          name: "",
          mean: "",
          count: "",
          standardScore: "",
        },
      ],
      //维度id-用户删除时使用
      bak: [
        {
          dimensionId: "",
        },
      ],
      //问卷管理-下拉-问卷名称列表
      questionnaireList: [
        {
          value: "",
          label: "",
        },
      ],
      questionnaireListModel: "",
      // 页面分页数据
      pageSize: 10,
      current: 1,
      total: 0,
      //用于维度列表中添加题目-存储选中项目
      q_add: [
        {
          id: "",
          question: "",
        },
      ],
      //用户维度列表中添加题目-存储取消选中
      q_cancel: [{}],
    };
  },
  created() {},
  mounted() {
    var that = this;
    that.getQuestionnaireList(that.$route.query.questionnaireId);
    that.questionnaireListChange(that.$route.query.questionnaireId);
  },
  methods: {
    //全选
    q_on_select_all(selection) {},
    //取消全选
    q_on_select_all_cancel(selection) {},
    //选中某一项
    q_on_select(selection, row) {
      this.submitSelection = selection;
    },
    //取消选中某一项
    q_on_select_cancel(selection, row) {
      this.submitSelection = selection;
    },
    //给维度添加题目-取消
    dimension_add_cancel() {
      this.submitSelection = []; //清除存储的选择数据
    },
    //给维度添加相应的题目-确定
    dimension_add() {
      var that = this;
      var data = {
        questionnaireId: that.questionnaireListModel,
        dimensionId: that.bak[that.dimensionIndex].dimensionId,
        submitSelection: that.submitSelection,
      };
      console.log(data);
      that.$axios
        .post("/index.php?function=dimensionAddQ", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            that.$Message.success(result.msg);
            that.questionnaireListChange(that.$route.query.questionnaireId);
          } else {
            that.$Message.error(result.msg);
            that.questionnaireListChange(that.$route.query.questionnaireId);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //维度列表-添加题目
    dimension_add_q(index) {
      var that = this;
      that.dimensionIndex = index;
      that.formItem = that.dimensionData[index];
      that.dimensionAddQ_status = true;
      var data = {
        questionnaireId: that.questionnaireListModel,
        dimensionId: that.bak[that.dimensionIndex].dimensionId,
      };
      that.$axios
        .post("/index.php?function=getDimensionQList", data)
        .then(function (response) {
          var result = response.data;
          // console.log(result);
          if (result.code == 1) {
            // console.log(result);
            // that.$Message.success(result.msg);
            that.dimension_q_list_data = result.data;
            that.total = result.total;
            that.loadingStatus = false; //表格加载状态
            // that.bak = result.bak;
          } else {
            that.$Message.error(result.msg);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    //维度管理-维度编辑
    dimensionEdit(index) {
      var that = this;
      that.dimensionIndex = index;
      that.formItem = that.dimensionData[index];
      that.dimensionEdit_status = true;
    },
    //维度管理-维度编辑-确定
    dimension_edit(value) {
      var that = this;
      var data = {
        questionnaireId: that.questionnaireListModel,
        dimensionId: that.bak[that.dimensionIndex].dimensionId,
        dimensionName: that.formItem.name, //修改后的维度名称
        dimensionMean: that.formItem.mean, //修改后的维度含义
        standardScore: that.formItem.standardScore, //修改后的维度标准分
      };
      // console.log(this.formItem);
      console.log(data);
      that.$refs[value].validate((valid) => {
        if (valid) {
          that.$axios
            .post("/index.php?function=editDimension", data)
            .then(function (response) {
              var result = response.data;
              if (result.code == 1) {
                that.questionnaireListChange(that.$route.query.questionnaireId);
                console.log(result.msg);
                that.$Message.success(result.msg);
              } else {
                that.questionnaireListChange(that.$route.query.questionnaireId);
                that.$Message.error(result.msg);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        } else {
          that.$Message.error("编辑失败 数据格式错误");
        }
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
      // console.log(data);
      that.$axios
        .post("/index.php?function=getDimensionList", data)
        .then(function (response) {
          var result = response.data;
          // console.log(result);
          if (result.code == 1) {
            // console.log(result);
            // that.$Message.success(result.msg);
            that.dimensionData = result.data;
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

    //添加维度，确定按钮
    handleSubmit(name) {
      var that = this;
      that.$refs[name].validate((valid) => {
        if (valid) {
          // that.$Message.success("维度添加成功!");
          // console.log(that.formValidate.name);
          var data = {
            dimensionName: that.formValidate.name,
            dimensionMean: that.formValidate.desc,
            questionnaireId: that.questionnaireListModel,
          };
          that.$axios
            .post("/index.php?function=add_dimension", data)
            .then(function (response) {
              var result = response.data;
              if (result.code == 1) {
                that.questionnaireListChange(that.$route.query.questionnaireId);
                that.$Message.success(result.msg);
              } else {
                that.$Message.error(result.msg);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        } else {
          that.$Message.error("添加失败 填写格式不规范");
        }
      });
      //清空输入框内容
      that.formValidate = {
        name: "",
        desc: "",
      };
    },
    //添加维度，取消按钮
    handleReset(name) {
      this.$refs[name].resetFields();
    },
    //维度管理-删除
    dimensionDelete(value) {
      var that = this;
      var data = {
        questionnaireId: that.questionnaireListModel,
        dimensionId: that.bak[value].dimensionId,
      };
      that.$Modal.confirm({
        title: "警告",
        content: "确定要删除该条维度记录吗？",
        onOk: () => {
          that.$axios
            .post("/index.php?function=deleteDimension", data)
            .then(function (response) {
              var result = response.data;
              if (result.code == 1) {
                that.questionnaireListChange(that.$route.query.questionnaireId);
                that.$Message.success(result.msg);
              } else {
                that.$Message.error(result.msg);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        },
        onCancel: () => {},
      });
    },
    //维度管理，维度编辑-取消
    dimension_edit_cancel() {},
    // 页面搜索
    pageSearch() {
      var that = this;
      that.current = 1;
      that.questionnaireListChange(that.$route.query.questionnaireId);
    },
    // 页码改变
    pageChange(value) {
      var that = this;
      that.current = value;
      that.questionnaireListChange(that.$route.query.questionnaireId);
    },
    // 页码大小改变
    pageSizeChange(value) {
      var that = this;
      that.current = 1;
      that.pageSize = value;
      that.questionnaireListChange(that.$route.query.questionnaireId);
    },
  },
};
</script>