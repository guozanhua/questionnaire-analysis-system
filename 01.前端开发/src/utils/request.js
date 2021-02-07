import axios from 'axios'
// 创建axios对象
const request = axios.create({
   baseURL: '/', 
   timeout:5000  //请求超时时间
})
// 请求拦截器
request.interceptors.request.use(config=>{
   // 请求拦截
   return config
},error=>{
   // 异常
   return Promise.reject(error)
})
// 响应拦截器
request.interceptors.response.use(response=>{
   // 响应的数据  response.data
   return response
},error=>{
   // 异常
   return Promise.reject(error)
})
 
export default request  //导出自定义创建的axios对象