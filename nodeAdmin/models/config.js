/**
 * @
 * @date 2018/12/8 0001$
 * @Description:
 */
const path = require("path");
require("dotenv").config({ path: path.join(__dirname, "../../.env") });
//
const globalEnv = process.env;
const mongodbIp = globalEnv["MONGO_ENDPOINT"] || "localhost:8000";
const mongodbName = globalEnv["MONGO_DB_NAME"] || "test";
const username = globalEnv["MONGO_USER"] || "";
const password = globalEnv["MONGO_PASSWORD"] || "";

const dbTimeout = globalEnv["MONGO_TIMEOUT"] || "10000";
const dbReconnectInterval = globalEnv["MONGO_RECONNECT_INTERVAL"] || "500";
const dbKeepAlive = globalEnv["MONGO_KEEPALIVE"] || "300000";
const dbPoolSize = globalEnv["MONGO_POOL_SIZE"] || "5";

const getFrontendEndpoint = {
  local: "http://127.0.0.1",
  prd: "http://www.meng-bao.top"
};

let rulesDebug = globalEnv.NODE_ENV === "local";
const options = {
  connectTimeoutMS: dbTimeout,
  autoIndex: false, // Don't build indexes
  reconnectTries: Number.MAX_VALUE, // Never stop trying to reconnect
  reconnectInterval: dbReconnectInterval, // Reconnect every 500ms
  poolSize: dbPoolSize, // Maintain up to 10 socket connections
  keepAliveInitialDelay: dbKeepAlive
};

module.exports = {
  mongodbIp: mongodbIp,
  mongodbName: mongodbName,
  username: username,
  password: password,
  mongodbDebug: globalEnv.NODE_ENV !== "prd",
  rules_debug: rulesDebug,
  frontendEndpoint: getFrontendEndpoint[globalEnv.NODE_ENV],
  options
};
