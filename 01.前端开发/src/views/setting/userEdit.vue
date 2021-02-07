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
            :model="userInfo"
            :rules="ruleValidate"
            :label-width="150"
          >
            <FormItem label="用户名" prop="user_name">
              <Input :disabled="userDisabled" v-model="userInfo.user_name" placeholder="请输入用户名" />
            </FormItem>
            <FormItem label="密码" prop="password">
              <Input type="password" v-model="userInfo.password" placeholder="请输入您的密码" />
            </FormItem>
            <FormItem label="请输入密码" prop="re_pwd">
              <Input type="password" v-model="userInfo.re_pwd" placeholder="请再次确认密码" />
            </FormItem>
            <FormItem label="姓名" prop="real_name">
              <Input v-model="userInfo.real_name" placeholder="请输入真实姓名" />
            </FormItem>
            <FormItem label="角色" prop="role">
              <CheckboxGroup v-model="userInfo.role">
                <Checkbox label="0">超级管理员</Checkbox>
                <Checkbox label="1">问卷管理员</Checkbox>
                <Checkbox label="2">普通用户</Checkbox>
              </CheckboxGroup>
            </FormItem>
            <FormItem label="性别" prop="sex">
              <RadioGroup v-model="userInfo.sex">
                <Radio label="0">男</Radio>
                <Radio label="1">女</Radio>
                <Radio label="2">保密</Radio>
              </RadioGroup>
            </FormItem>
            <FormItem label="邮箱" prop="email">
              <Input v-model="userInfo.email" placeholder="请输入邮箱" />
            </FormItem>
            <FormItem label="手机" prop="mobile">
              <Input v-model="userInfo.mobile" placeholder="请输入手机号" />
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
  } else if (value !== this.userInfo.pwd) {
    callback(new Error("两次输入密码不一致"));
  } else {
    callback();
  }
};
export default {
  data() {
    return {
      pageTitle: "用户修改",
      pageType: "add",
      userDisabled: false,
      userInfo: {
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
      ssoFormValidate: {
        user_name: "",
        pwd: "",
        rePwd: "",
        realName: "",
        role: [],
        sex: "0",
        photo: "",
        email: "",
        mobile: ""
      },
      loading: false,
      previewVisible: false,
      previewImage: "",
      fileList: [],
      fileSaveName: "",
      integrateStatus: 0, //SSO集成状态
      selectedRows: [], //SSO选择用户
      UserList: [], //SSO查询结果
      searchSSObtn: false,
      submitSSObtn: false,
      // 表格分页数据
      pagination: {
        showSizeChanger: true,
        showQuickJumper: true,
        pageSize: 10,
        current: 1,
        total: ""
      },
      columns: [
        {
          title: "选择",
          type: "_checked",
          align: "center",
          width: 64,
          render: (h, { row, index }) => {
            let _that = this;
            return h("div", [
              h("radio", {
                props: {
                  value: row._checked
                },
                on: {
                  "on-change": e => {
                    this.handleSelect(row.userID, e, row, index);
                  }
                }
              })
            ]);
          }
        },
        {
          title: "序号",
          width: 80,
          align: "center",
          render: (h, params) => {
            return h(
              "span",
              params.index +
                (this.pagination.current - 1) * this.pagination.pageSize +
                1
            );
          }
        },
        {
          title: "用户名",
          key: "LoginName"
        },
        {
          title: "真实姓名",
          key: "user_name"
        },

        {
          title: "邮箱",
          key: "Email"
        },
        {
          title: "状态",
          key: "Status"
        }
      ],
      org_list: []
    };
  },
  created() {
    this.getUserInfo();
    this.getOrgList();
  },
  mounted() {
    // this.getList();
    this.uploadList = this.$refs.upload.fileList;
  },
  methods: {
    //获取用户信息
    getUserInfo() {
      if (this.$route.query.id != undefined) {
        this.pageTitle = "修改用户";
        this.pageType = "edit";
        this.userDisabled = true;
        delete this.ruleValidate.password[0]; //??
        delete this.ruleValidate.re_pwd[0]; //??
        let photo = {};
        this.$axios
          .post("/api/?r=user/user/get-user-info", {
            id: this.$route.query.id,
            userName: this.$route.query.userName
          })
          .then(res => {
            let result = res.data.data;
            if (result.code == 1) {
              this.userInfo = result.data;
              if (result.data.avatar == "") {
                if (result.data.sex == "1") {
                  photo = {
                    name: result.data.userName,
                    url:
                      "api/?r=basic/upload/show&path=/data/template/template_woman.png",
                    status: "finished"
                  };
                  this.previewImage = "/data/template/template_woman.png";
                } else {
                  photo = {
                    name: result.data.userName,
                    url:
                      "api/?r=basic/upload/show&path=/data/template/template_man.png",
                    status: "finished"
                  };
                  this.previewImage = "/data/template/template_man.png";
                }
              } else {
                photo = {
                  name: result.data.userName,
                  url:
                    "api/?r=basic/upload/show&path=" + result.data.avatarPath,
                  status: "finished"
                };
                this.previewImage = result.data.avatarPath;
              }
              this.uploadList.push(photo);
            } else {
              this.$Message.error(result.msg);
            }
          });
      }
    },
    //获取组织信息
    // getOrgList() {
    //   this.$axios
    //     .post("/api/?r=user/user/get-org-list")
    //     .then(res => {
    //       let result = res.data.data;
    //       if (result.code == 1) {
    //         this.org_list = result.data;
    //         this.$Message.success(result.msg);
    //       } else {
    //         this.$Message.error(result.msg);
    //       }
    //     })
    //     .catch(function(error) {
    //       console.log(error);
    //     });
    // },

    //确定
    handleSubmit(name) {
      // let path = "/api/?r=user/user/add-user";
      // if (this.pageType == "edit") {
      //   path = "/api/?r=user/user/update-user";
      // }
      let path = "/api/?r=user/user/update-user"; //更新
      this.$axios
        .post(path, this.userInfo) //提交更新后的用户信息
        .then(res => {
          let result = res.data.data;
          if (result.code == 1) { //返回值为1代表成功
            this.$Message.success(result.msg);
            this.$router.push({ path: "/setting/userList" });
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
    // },

    // 取消
    onCancle() {
      this.$router.push({
        path: "/setting/userList"
      });
    }
  }
};
</script>