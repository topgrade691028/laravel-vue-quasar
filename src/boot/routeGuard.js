// import something here
// import { LocalStorage } from 'quasar'

import { Notify } from "quasar"

function isAuthorized (roles, store) {
  // check session
  const userLevel = store.state.auth.userLevel
  if (!userLevel) {
    // not logged in
    return false
  }
  if (Array.isArray(roles)) {
    return roles.includes(userLevel)
  } else {
    return roles === userLevel
  }
}

export default ({ router, store }) => {
  router.beforeEach((to, from, next) => {
    if (to.meta.roles && !isAuthorized(to.meta.roles, store)) {
      // not authorized
      Notify.create({
        color: 'red-5',
        textColor: 'white',
        position: 'top',
        icon: 'fas fa-exclamation-triangle',
        message: 'Loggedin User doesnt have permission to access this page.'
      })
      next(from.path)
    } else {
      next()
    }
  })
}
