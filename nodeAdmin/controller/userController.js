"use strict";

import UserModel from "../models/user";

class User {
  constructor() {
    this.login = this.login.bind(this);
  }
  async login(req, res, next) {
    //TODO 接收参数
    const { userName, passWord } = req.body;
    console.log(">>>>>>>>>>>>>>>>>userName", userName);
    console.log(">>>>>>>>>>>>>>>>>passWord", passWord);

    try {
      if (!userName) {
        throw new Error("用户名参数错误");
      } else if (!passWord) {
        throw new Error("密码参数错误");
      }
    } catch (err) {
      console.log(err.message, err);
      res.send({
        status: 0,
        type: "GET_ERROR_PARAM",
        message: err.message
      });
      return;
    }

    try {
      const admin = await UserModel.findOne({ user_name: userName });
      if (admin) {
        res.send({
          status: 1,
          success: "登录成功"
        });
      } else {
        res.send({
          status: 0,
          success: "登录失败"
        });
      }
    } catch (err) {
      console.log("登录管理员失败", err);
      res.send({
        status: 0,
        type: "LOGIN_ADMIN_FAILED",
        message: "登录管理员失败"
      });
    }
  }
}

export default new User();
