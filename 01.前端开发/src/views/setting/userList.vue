<template>
  <div>
    <Card :bordered="false" :dis-hover="true">
      <div class="page-card-oper">
        <!-- 操作按钮 -->
        <div class="oper">
          <Button type="primary" to="/setting/userAdd">创建用户</Button>
        </div>
      </div>

      <!-- <div class="page-table">
        <Table
          @on-selection-change="tableSelect"
          ref="selectionRef"
          :columns="columns"
          :data="data"
        >
          <template slot-scope="{ row }" slot="PreLoginTime">
          </template>
          <template slot-scope="{ row}" slot="action">
          </template>
        </Table>
      </div>-->

      <div class="page-table">
        <template>
          <Table border :columns="columns12" :data="data6">
            <!-- <template slot-scope="{ row }" slot="userName">
              <span>{{ row.userName }}</span>
            </template>-->
            <!-- <template slot-scope="{ row }" slot="userName">
              <span>{{ row.userName }}</span>
            </template>-->
            <template slot-scope="{ row, index }" slot="action">
              <Button
                type="primary"
                size="small"
                style="margin-right: 5px"
                @click="userEdit(row)"
              >编辑</Button>
              <Button type="error" size="small" @click="userDelete(row)">删除</Button>
            </template>
          </Table>
        </template>
        <div class="pager">
          <Page
            v-if="total!=0"
            :total="total"
            show-sizer
            @on-change="pageChange"
            @on-page-size-change="pageSizeChange"
          />
        </div>
        <!-- <div style="clear:both;"></div> -->
      </div>
    </Card>
  </div>
</template>

<script>
const statusMap = ["error", "success"];
const status = ["禁用", "启用"];
export default {
  data() {
    return {
      columns12: [
        {
          title: "序号",
          key: "id"
        },
        {
          title: "用户名",
          key: "userName"
        },
        {
          title: "角色",
          key: "userRole"
        },
        {
          title: "姓名",
          key: "name"
        },
        {
          title: "电话",
          key: "phone"
        },
        {
          title: "邮件",
          key: "email"
        },
        {
          title: "密码",
          key: "password"
        },
        {
          title: "操作",
          slot: "action",
          width: 150,
          align: "center"
        }
      ],
      data6: [
        {
          id: 1,
          userName: "admin",
          userRole: "系统管理员",
          name: "张三",
          phone: "15515671702",
          email: "1801168257@qq.com",
          password: "******"
        },
        {
          id: 2,
          userName: "Jim Green",
          userRole: "问卷管理员",
          name: "张三",
          phone: "15515671702",
          email: "1801168257@qq.com",
          password: "******"
        },
        {
          id: 3,
          userName: "Joe Black",
          userRole: "普通用户",
          name: "张三",
          phone: "15515671702",
          email: "1801168257@qq.com",
          password: "******"
        },
        {
          id: 4,
          userName: "Jon Snow",
          userRole: "普通用户",
          name: "张三",
          phone: "15515671702",
          email: "1801168257@qq.com",
          password: "******"
        }
      ],
      // 页面分页数据
      pageSize: 10,
      current: 1,
      total: 100,
      // 页面操作
      beachUploadShow: false,
      userListShow: false,
      selectNum: "0",
      modalLoading: true
    };
  },
  created() {},
  mounted() {
    this.getList();
  },
  methods: {
    // 页码改变
    pageChange(value) {
      let that = this;
      that.current = value;
      that.getList();
    },
    // 页码大小改变
    pageSizeChange(value) {
      let that = this;
      that.current = 1;
      that.pageSize = value;
      that.getList();
    },
    //编辑用户
    userEdit(row) {
      this.$router.push({
        path: "/setting/userEdit",
        query: {
          id: row.id,
          user: row.userName //传递用户名
        }
      });
    },
    //删除用户
    userDelete(row) {
      this.$Modal.confirm({
        title: "删除",
        content: "将删除该条记录",
        onOk: () => {
          var data = {
            userName: row.userName //传递用户名
          };
          this.$axios
            .post("/api/?r=user/user/delete-user", data)
            .then(res => {
              let result = res.data.data;
              if (result.code == 1) {
                this.$Message.success("删除成功");
                this.getList();
              } else {
                this.$Message.error(result.msg);
              }
            })
            .catch(function(error) {
              console.log(error);
            });
        }
      });
    },
    //获取列表数据
    getList() {
      var that = this;
      var data = {
        pageSize: that.pageSize, //当前分页大小
        current: that.current //当前处于第几个分页
      };
      that.$axios
        .post("/api/?r=user/user/get-user-list", data)
        .then(res => {
          let result = res.data.data;
          console.log(result);
          if (result.code == 1) {
            that.data6 = result.data.list;
            that.total = parseInt(result.data.total);
          } else {
            that.$Message.error(result.msg);
          }
        })
    },
  }
};
</script>