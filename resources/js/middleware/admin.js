import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/user'].role !== 'SystemAdmin') {
    console.log('permission error')
    next({ name: from.name })
  } else {
    next()
  }
}