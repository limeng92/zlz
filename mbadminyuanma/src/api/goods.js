import axios from '@/libs/api.request'

export const getGoodsList = (token,page) => {
  return axios.request({
    url: 'goodsListInfo',
    params: {
      token,
      page
    },
    method: 'get'
  })
}

export const addGoodsData = (token,goods_info) => {
  return axios.request({
    url: 'addSaveRe',
    params: {
      token,
      goods_info
    },
    method: 'post'
  })
}

export const editGoodsData = (token,goods_info) => {
  return axios.request({
    url: 'editSaveRe',
    params: {
      token,
      goods_info
    },
    method: 'post'
  })
}

export const deleteGoodsData = (token,id) => {
  return axios.request({
    url: 'delGoodsRe',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const editGoodsInfo = (token,id) => {
  return axios.request({
    url: 'editInfo',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const goodsSpecAttrList = (token,id) => {
  return axios.request({
    url: 'goodsSpecAttrList',
    params: {
      token,
      id
    },
    method: 'get'
  })
}

export const goodsSpecInput = (token,spec_json) => {
  return axios.request({
    url: 'goodsSpecInput',
    params: {
      token,
      spec_json
    },
    method: 'post'
  })
}

export const getNextCat = (token,value,flag) => {
  return axios.request({
    url: 'nextCatList',
    params: {
      token,
      value,
      flag
    },
    method: 'post'
  })
}
