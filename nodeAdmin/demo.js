var express = require("express");
var app = express();
var request = require("request");

/**
 *解析请求的消息体
 */
var bodyParser = require("body-parser");
app.use(bodyParser.json()); //返回一个只解析json的中间件，最后保存的数据都放在req.body对象上
app.use(bodyParser.urlencoded({ extended: true })); //返回的对象为任意类型

//允许跨域请求
app.all("/*", function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Methods", "PUT, GET, POST, DELETE, OPTIONS");
  res.header("Access-Control-Allow-Headers", "X-Requested-With");
  res.header("Access-Control-Allow-Headers", "Content-Type");
  next();
});

//  主页输出 "Hello World"
app.get("/", function(req, res) {
  console.log("主页 GET 请求");
  res.send("Hello GET");
});

//请求第三方接口(本地配置的域名为www.lala.com的laravel接口)
app.post("/", function(req, res) {
  var method = req.method.toUpperCase();
  var proxy_url = "http://www.lala.com/api/goodsListInfo";
  var options = {
    headers: { Connection: "close" },
    url: proxy_url,
    method: method,
    json: true,
    //请求的参数
    body: req.body
  };
  function callback(error, response, data) {
    if (!error && response.statusCode == 200) {
      console.log("------接口数据------", data);
      //返回json数据
      res.json(data);
    }
  }
  request(options, callback);
});

app.get("/", function(req, res) {
  console.log("主页 GET 请求");
  res.send("Hello GET");
});

//请求第三方接口(本地配置的域名为www.lala.com的laravel接口)
app.post("/login", function(req, res) {
  var method = req.method.toUpperCase();
  var proxy_url = "http://www.lala.com/api/loginInfo";
  var options = {
    headers: { Connection: "close" },
    url: proxy_url,
    method: method,
    json: true,
    //请求的参数
    body: req.body
  };
  function callback(error, response, data) {
    if (!error && response.statusCode == 200) {
      console.log("------接口数据------", data);
      //返回json数据
      res.json(data);
    }
  }
  request(options, callback);
});

//  POST 请求
// app.post('/', function (req, res) {
//    console.log("主页 POST 请求");
// //    res.send('Hello POST');
//     var sendData = {
//         status: '200',
//         data: {
//             name:'Node'
//         },
//         msg: 'node测试查询成功!',
//     };
//     res.writeHead(200,{'Content-Type':'text/html;charset=utf-8'});//设置response编码为utf-8
//     res.end(JSON.stringify(sendData));
// })

//  /del_user 页面响应
app.get("/del_user", function(req, res) {
  console.log("/del_user 响应 DELETE 请求");
  res.send("删除页面");
});

//  /list_user 页面 GET 请求
app.get("/list_user", function(req, res) {
  console.log("/list_user GET 请求");
  res.send("用户列表页面");
});

// 对页面 abcd, abxcd, ab123cd, 等响应 GET 请求
app.get("/ab*cd", function(req, res) {
  console.log("/ab*cd GET 请求");
  res.send("正则匹配");
});

var server = app.listen(8081, function() {
  var host = server.address().address;
  var port = server.address().port;

  console.log("应用实例，访问地址为 http://%s:%s", host, port);
});
