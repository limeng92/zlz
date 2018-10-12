import axios from '@/libs/api.request'

export const getGoodsCategoryList = (token) => {
  return axios.request({
    url: 'goodsCategoryInfo',
    params: {
      token
    },
    method: 'get'
  })
}

export const addGoodsCategoryData = (token,add_info) => {
  return axios.request({
    url: 'catAddSaveRe',
    params: {
      token,
      add_info
    },
    method: 'post'
  })
}

export const editGoodsCategoryData = (token,edit_info) => {
  return axios.request({
    url: 'catEditSaveRe',
    params: {
      token,
      edit_info
    },
    method: 'post'
  })
}

export const deleteGoodsCategoryData = (token,id) => {
  return axios.request({
    url: 'catDelRe',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const editGoodsCategoryInfo = (token,id) => {
  return axios.request({
    url: 'catEditInfo',
    params: {
      token,
      id
    },
    method: 'get'
  })
}



