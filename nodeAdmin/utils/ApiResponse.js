/*
 * Exception class
 */
const crosHeaders = {
  'Access-Control-Allow-Origin': process.env.CORS_ALLOW_ORIGIN || 'www.checkyourconfig.com',
  'Access-Control-Allow-Methods': process.env.CORS_ALLOW_METHODS || '',
  'Access-Control-Allow-Headers': process.env.CORS_ALLOW_HEADERS || ''
}

const customError = {
  '401': 'Unauthorized',
  '404': 'Not Found',
  '500': 'Service Error'
}

class ApiResponse {
  // Define Error Code
  static get E401 () {
    return '401'
  }
  static get E404 () {
    return '404'
  }
  static get E500 () {
    return '500'
  }
  static success (res = {}, statusCode = 200) {
  
    const response = {
      statusCode: statusCode,
      headers: crosHeaders,
      body: JSON.stringify({
        code: 200,
        msg: 'success',
        data:res.data
      })
    }
    console.log(response)
    return response
  }

  static error (statusCode = 500, customCode = ApiResponse.E500, error, data = {}) {
    if (!error && customError[customCode]) {
      error = new Error(customError[customCode])
    }
    console.error(error)
    const response = {
      statusCode: statusCode,
      headers: crosHeaders,
      body: JSON.stringify({
        code: customCode,
        msg: error.toString(),
        data
      })
    }
    console.log(response)
    return response
  }
}

module.exports = {
  ApiResponse
}
