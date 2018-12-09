const _connectDB = async () => {}
const _init = async () => {}

const DBConnection = {
    connectDB: _connectDB,
    init: _init,
    restore: function() { DBConnection.init = _init }
}
  
module.exports = DBConnection
  