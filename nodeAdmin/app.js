import express from "express";
import db from "./db/mongodb.js";
import config from "config-lite";
import router from "./routes/index.js";
import chalk from "chalk";

const app = express();

/**
 *解析请求的消息体
 */
var bodyParser = require("body-parser");
app.use(bodyParser.json()); //返回一个只解析json的中间件，最后保存的数据都放在req.body对象上
app.use(bodyParser.urlencoded({ extended: true })); //返回的对象为任意类型

app.all("*", (req, res, next) => {
  const { origin, Origin, referer, Referer } = req.headers;
  const allowOrigin = origin || Origin || referer || Referer || "*";
  res.header("Access-Control-Allow-Origin", allowOrigin);
  res.header(
    "Access-Control-Allow-Headers",
    "Content-Type, Authorization, X-Requested-With"
  );
  res.header("Access-Control-Allow-Methods", "PUT,POST,GET,DELETE,OPTIONS");
  res.header("Access-Control-Allow-Credentials", true); //可以带cookies
  res.header("X-Powered-By", "Express");
  if (req.method == "OPTIONS") {
    res.sendStatus(200);
  } else {
    next();
  }
});

router(app);

var server = app.listen(8081, function() {
  var host = server.address().address;
  var port = server.address().port;

  console.log("应用实例，访问地址为 http://%s:%s", host, port);
});

// app.listen(config.port, () => {
//   console.log(chalk.green(`成功监听端口：${config.port}`));
// });
