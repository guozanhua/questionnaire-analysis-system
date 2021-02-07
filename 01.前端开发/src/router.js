const routers = [
    {
        name: 'manage',
        path: '/',
        redirect: '/manage',
        meta: {
            title: ''
        },
        component: (resolve) => require(['./views/layout/BasicLayout.vue'], resolve),
        children: [
            {
                name: 'manage-',
                path: '/manage',
                redirect: '/manage/list',
                meta: {
                    title: '问卷管理',
                    isNav: true
                },
                component: (resolve) => require(['./views/layout/BlankLayout.vue'], resolve),
                children: [
                    {
                        name: 'manage-new',
                        path: '/manage/new',
                        meta: {
                            title: '新建问卷',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/new.vue'], resolve),
                    },
                    {
                        name: 'manage-edit',
                        path: '/manage/edit',
                        meta: {
                            title: '问卷编辑',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/edit.vue'], resolve)
                    },
                    {
                        name: 'manage-analyse',
                        path: '/manage/analyse',
                        meta: {
                            title: '问卷分析',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/analyse.vue'], resolve)
                    },
                    {
                        name: 'manage-list',
                        path: '/manage/list',
                        meta: {
                            title: '问卷列表',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/list.vue'], resolve)
                    },
                    {
                        name: 'manage-dimension',
                        path: '/manage/dimension',
                        meta: {
                            title: '维度管理',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/dimension.vue'], resolve)
                    },
                    {
                        name: 'manage-record',
                        path: '/manage/record',
                        meta: {
                            title: '填写记录',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/record.vue'], resolve)
                    },
                    {
                        name: 'manage-report',
                        path: '/manage/report',
                        meta: {
                            title: '测评报告',
                            isNav: false
                        },
                        component: (resolve) => require(['./views/manage/report.vue'], resolve)
                    },
                ]
            },
            // {
            //     name: 'mould',
            //     path: '/mould/mould',
            //     // redirect: '/mould/mould',
            //     meta: {
            //         title: '维度管理',
            //         isNav: true
            //     },
            //     component: (resolve) => require(['./views/mould/mould.vue'], resolve)
            // },
            {
                name: 'setting',
                path: '/setting',
                // redirect: '/setting/userList',
                meta: {
                    title: '平台对接',
                    isNav: true
                },
                component: (resolve) => require(['./views/setting/limit.vue'], resolve),
                // children: [
                //     {
                //         name: 'user-list',
                //         path: '/setting/userList',
                //         meta: {
                //             title: '用户管理',
                //             isNav: true
                //         },
                //         component: (resolve) => require(['./views/setting/userList.vue'], resolve),
                //     },
                //     {
                //         name: 'user-edit',
                //         path: '/setting/userEdit',
                //         meta: {
                //             title: '用户修改',
                //             isNav: false
                //         },
                //         component: (resolve) => require(['./views/setting/userEdit.vue'], resolve)
                //     },
                //     {
                //         name: 'user-add',
                //         path: '/setting/userAdd',
                //         meta: {
                //             title: '用户添加',
                //             isNav: false
                //         },
                //         component: (resolve) => require(['./views/setting/userAdd.vue'], resolve)
                //     },
                //     {
                //         name: 'limit',
                //         path: '/setting/limit',
                //         meta: {
                //             title: '系统配置',
                //             isNav: true
                //         },
                //         component: (resolve) => require(['./views/setting/limit.vue'], resolve)
                //     }
                // ]
            }
        ]
    },
];

export default routers;