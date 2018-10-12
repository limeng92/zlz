import axios from '@/libs/api.request'

export const getGoodsTypeList = (token) => {
  return axios.request({
    url: 'goodsTypeInfo',
    params: {
      token
    },
    method: 'get'
  })
}

export const addGoodsTypeData = (token,add_info) => {
  return axios.request({
    url: 'gtAddSaveRe',
    params: {
      token,
      add_info
    },
    method: 'post'
  })
}

export const editGoodsTypeData = (token,edit_info) => {
  return axios.request({
    url: 'gtEditSaveRe',
    params: {
      token,
      edit_info
    },
    method: 'post'
  })
}

export const deleteGoodsTypeData = (token,id) => {
  return axios.request({
    url: 'gtDelRe',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const editGoodsTypeInfo = (token,id) => {
  return axios.request({
    url: 'gtEditInfo',
    params: {
      token,
      id
    },
    method: 'get'
  })
}



