import env from './env'

// const DEV_URL = 'https://www.easy-mock.com/mock/5add9213ce4d0e69998a6f51/iview-admin/'
// const PRO_URL = 'https://produce.com'

//本地的api地址
// const DEV_URL = 'http://fengblog.com/api'
// const PRO_URL = 'http://fengblog.com/api'

//百度云线上api地址
const DEV_URL = 'http://106.12.18.165/api'
const PRO_URL = 'http://106.12.18.165/api'

export default env === 'development' ? DEV_URL : PRO_URL
