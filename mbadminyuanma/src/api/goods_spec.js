import axios from '@/libs/api.request'

export const getGoodsSpecList = (token,page) => {
  return axios.request({
    url: 'goodsSpecInfo',
    params: {
      token,
      page
    },
    method: 'get'
  })
}

export const addGoodsSpecData = (token,add_info) => {
  return axios.request({
    url: 'spAddSaveRe',
    params: {
      token,
      add_info
    },
    method: 'post'
  })
}

export const editGoodsSpecData = (token,edit_info) => {
  return axios.request({
    url: 'spEditSaveRe',
    params: {
      token,
      edit_info
    },
    method: 'post'
  })
}

export const deleteGoodsSpecData = (token,id) => {
  return axios.request({
    url: 'spDelRe',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const editGoodsSpecInfo = (token,id) => {
  return axios.request({
    url: 'spEditInfo',
    params: {
      token,
      id
    },
    method: 'get'
  })
}



