
<style scoped>
.ssoUserSearch {
  float: left;
  margin-top: 10px;
}
.tips {
  float: left;
  margin-top: 10px;
  margin-left: 10px;
}
</style>>
<template>
  <div>
    <Card :bordered="false" :dis-hover="true">
      <p slot="title">{{pageTitle}}</p>
      <Row>
        <Col :xs="24" :sm="14" :md="12">
          <Form
            v-if="integrateStatus==0"
            ref="formValidateRef"
            label-colon
            :model="formValidate"
            :rules="ruleValidate"
            :label-width="150"
          >
            <FormItem label="用户名" prop="user_name">
              <Input
                :disabled="userDisabled"
                v-model="formValidate.user_name"
                placeholder="请输入用户名"
              />
            </FormItem>
            <FormItem label="密码" prop="password">
              <Input type="password" v-model="formValidate.password" placeholder="请输入您的密码" />
            </FormItem>
            <FormItem label="请输入密码" prop="re_pwd">
              <Input type="password" v-model="formValidate.re_pwd" placeholder="请再次确认密码" />
            </FormItem>
            <FormItem label="姓名" prop="real_name">
              <Input v-model="formValidate.real_name" placeholder="请输入真实姓名" />
            </FormItem>
            <FormItem label="角色" prop="role">
              <CheckboxGroup v-model="formValidate.role">
                <Checkbox label="0">超级管理员</Checkbox>
                <Checkbox label="1">问卷管理员</Checkbox>
                <Checkbox label="2">普通用户</Checkbox>
              </CheckboxGroup>
            </FormItem>
            <FormItem label="性别" prop="sex">
              <RadioGroup v-model="formValidate.sex">
                <Radio label="0">男</Radio>
                <Radio label="1">女</Radio>
                <Radio label="2">保密</Radio>
              </RadioGroup>
            </FormItem>
            <FormItem label="邮箱" prop="email">
              <Input v-model="formValidate.email" placeholder="请输入邮箱" />
            </FormItem>
            <FormItem label="手机" prop="mobile">
              <Input v-model="formValidate.mobile" placeholder="请输入手机号" />
            </FormItem>
            <FormItem>
              <Button type="primary" @click="handleSubmit">确定</Button>
              <Button style="margin-left: 8px" @click="onCancle">取消</Button>
            </FormItem>
          </Form>
        </Col>
      </Row>
    </Card>
  </div>
</template>
<script>
const validatePassCheck = (rule, value, callback) => {
  if (value === "") {
    callback(new Error("请确认密码"));
  } else if (value !== this.formValidate.pwd) {
    callback(new Error("两次输入密码不一致"));
  } else {
    callback();
  }
};
export default {
  data() {
    return {
      pageTitle: "用户添加",
      pageType: "add",
      visible: false,
      uploadList: [],
      userDisabled: false,
      // formValidate1: {
      //   user_name: "qwe123",
      //   password: "qwe123",
      //   re_pwd: "qwe123",
      //   real_name: "fasdfasdf",
      //   role: ["0", "1"],
      //   sex: "0",
      //   email: "132465@qq.com",
      //   mobile: "15517952753"
      // },
      formValidate: {
        user_name: "",
        password: "",
        re_pwd: "",
        real_name: "",
        role: [],
        sex: "0",
        email: "",
        mobile: ""
      },
      ruleValidate: {
        user_name: [
          {
            required: true,
            message: "登录名不能为空",
            trigger: "blur"
          },
          {
            type: "string",
            min: 4,
            message: "登录名不能小于4位",
            trigger: "blur"
          },
          {
            pattern: /^[A-Za-z0-9]{5,20}$/,
            message: "用户名由5-20个以英文字母开头的英文字母、数字组成",
            trigger: "blur"
          }
        ],
        password: [
          {
            required: true,
            message: "密码不能为空",
            trigger: "blur"
          },
          {
            type: "string",
            min: 6,
            max: 25,
            message: "密码不能小于6位，不能大于25位",
            trigger: "blur"
          },
          {
            pattern: /^[a-zA-Z][a-zA-Z0-9_!@#$%&*]{5,24}$/,
            message:
              "密码由6-25个以英文字母开头的英文字母、数字、!、@、#、$、%、^、&、*、组成",
            trigger: "blur"
          }
        ],
        re_pwd: [
          {
            required: true,
            message: "请重复输入密码",
            trigger: "blur"
          },
          {
            type: "string",
            min: 6,
            max: 25,
            message: "密码不能小于6位，不能大于25位",
            trigger: "blur"
          },
          {
            pattern: /^[a-zA-Z][a-zA-Z0-9_!@#$%&*]{5,24}$/,
            message:
              "密码由6-25个以英文字母开头的英文字母、数字、!、@、#、$、%、^、&、*、组成！",
            trigger: "blur"
          },
          {
            validator: validatePassCheck,
            trigger: "blur"
          }
        ],
        real_name: [
          {
            required: true,
            message: "请输入真实姓名",
            trigger: "blur"
          }
        ],
        email: [
          { required: true, message: "请输入邮箱", trigger: "blur" },
          {
            type: "email",
            message: "请输入正确的邮箱地址",
            trigger: "blur"
          }
        ],
        mobile: [
          {
            required: true,
            message: "请输入手机号",
            trigger: "blur"
          },
          {
            pattern: /^1[3-9][0-9]{9}$/,
            message: "请输入正确的手机号",
            trigger: "blur"
          }
        ],
        phone: [
          {
            pattern: /^0\d{2,3}-?\d{7,8}$/,
            message: "请输入正确的固定电话",
            trigger: "blur"
          }
        ],
        role: [
          {
            required: true,
            type: "array",
            min: 1,
            message: "至少选择一种角色",
            trigger: "change"
          }
        ],
        sex: [
          {
            required: true,
            trigger: "change"
          }
        ]
      },
      loading: false,
      previewVisible: false,
      integrateStatus: 0, //SSO集成状态
      selectedRows: [], //SSO选择用户
      searchSSObtn: false,
      submitSSObtn: false
    };
  },
  created() {},
  mounted() {},
  methods: {
    handleSubmit(name) {
      let path = "/api/?r=user/user/add-user";
      this.$axios
        .post(path, this.formValidate)
        .then(res => {
          let result = res.data.data;
          if (result.code == 1) {
            this.$Message.success(result.msg);
            this.$router.push({ path: "/user/list" });
          } else {
            this.$Message.error(result.msg);
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //
    // handleSelect(userID, e, row, index) {
    //   this.listData.forEach(items => {
    //     this.$set(items, "_checked", false);
    //   });
    //   this.listData[index]._checked = e;
    //   var formData = { userID: userID };
    //   this.$axios
    //     .post("/api/?r=user/user/ceshi", formData)
    //     .then(res => {
    //       var result = res.data;
    //       if (result.code == 1) {
    //         if (result.data.code == 1) {
    //           row.base = result.data.data.base;
    //           this.selectBook = row;
    //         } else {
    //           this.$Message.error(result.data.msg);
    //         }
    //       }
    //     })
    //     .catch(error => {});
    // }
  }
};
</script>