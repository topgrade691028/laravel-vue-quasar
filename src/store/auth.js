const state = {
  loggedIn: false,
  user: {},
  driver: {},
  token: '',
  isAdmin: '',
  pageTitle: '',
  userLevel: '',
  userRoles: '',
  userSubcontractor: '',
  dpdSid: ''
}

const getters = {
  isLoggedIn: state => {
    return state.loggedIn
  },
  token: state => {
    return state.token
  },
  user: state => {
    return state.user
  },
  driver: state => {
    return state.driver
  },
  isAdmin: state => {
    return state.isAdmin
  },
  pageTitle: state => {
    return state.pageTitle
  },
  userLevel: state => {
    return state.userLevel
  },
  userRoles: state => {
    return state.userRoles
  },
  userSubcontractor: state => {
    return state.userSubcontractor
  },
  dpdSid: state => {
    return state.dpdSid
  }
}

const actions = {
}

const mutations = {
  user (state, user) {
    state.user = user
  },
  driver (state, driver) {
    state.driver = driver
  },
  login (state, auth) {
    state.loggedIn = !!auth.token
    state.token = auth.token
    state.user = auth.user
    state.driver = auth.driver
  },
  token (state, token) {
    state.token = token
  },
  isAdmin (state, isAdmin) {
    state.isAdmin = isAdmin
  },
  pageTitle (state, pageTitle) {
    state.pageTitle = pageTitle
  },
  userLevel (state, userLevel) {
    state.userLevel = userLevel
  },
  userRoles (state, userRoles) {
    state.userRoles = userRoles
  },
  userSubcontractor(state, userSubcontractor) {
    state.userSubcontractor = userSubcontractor
  },
  logout (state) {
    state.loggedIn = false
    state.token = ''
    state.user = {}
    state.driver = {}
    state.dpdSid = ''
  },
  setDpdSid(state, dpdSid) {
    state.dpdSid = dpdSid
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
