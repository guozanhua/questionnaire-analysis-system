<style scoped lang="less">
.index {
  width: 100%;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  text-align: center;
  h1 {
    height: 150px;
    img {
      height: 100%;
    }
  }
  h2 {
    color: #666;
    margin-bottom: 200px;
    p {
      margin: 0 0 50px;
    }
  }
  .ivu-row-flex {
    height: 100%;
  }
}
</style>
<template>
  <div class="index">
    <Row type="flex" justify="center" align="middle">
      <Col span="24">
        <Input
          v-model="loginForm.username"
          prefix="ios-contact"
          placeholder="请输入用户名"
          style="width: 382px"
        />
        <br />
        <br />
        <Input
          v-model="loginForm.password"
          prefix="md-lock"
          type="password"
          password
          placeholder="请输入密码"
          style="width: 382px"
        />
        <br />
        <br />
        <Button @click="login" type="primary" style="width: 382px">登录</Button>
      </Col>
    </Row>
  </div>
</template>
<script>
import { mapMutations } from 'vuex';
export default {
  data() {
    return {
      loginForm: {
        username: "",
        password: "",
      },
    };
  },
  methods: {
    // ...mapMutations(["changeLogin"]),
    login() {
      var that = this;
      var data = {
        username: that.loginForm.username,
        password: that.loginForm.password,
      };
      that.$axios
        .post("/index.php?function=login", data)
        .then(function (response) {
          var result = response.data;
          if (result.code == 1) {
            that.$Message.success(result.msg);
            that.$store.commit('$_setToken', result.token);
            that.$router.push("/manage");
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
