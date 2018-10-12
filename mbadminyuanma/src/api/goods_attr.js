import axios from '@/libs/api.request'

export const getGoodsAttrList = (token) => {
  return axios.request({
    url: 'goodsAttrInfo',
    params: {
      token
    },
    method: 'get'
  })
}

export const addGoodsAttrData = (token,add_info) => {
  return axios.request({
    url: 'gaAddSaveRe',
    params: {
      token,
      add_info
    },
    method: 'post'
  })
}

export const editGoodsAttrData = (token,edit_info) => {
  return axios.request({
    url: 'gaEditSaveRe',
    params: {
      token,
      edit_info
    },
    method: 'post'
  })
}

export const deleteGoodsAttrData = (token,id) => {
  return axios.request({
    url: 'gaDelRe',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const editGoodsAttrInfo = (token,id) => {
  return axios.request({
    url: 'gaEditInfo',
    params: {
      token,
      id
    },
    method: 'get'
  })
}



