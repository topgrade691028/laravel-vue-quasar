import axios from 'axios'
import { Notify } from 'quasar'
// const API_URL = 'http://localhost:8000/api'
const API_URL = '/api'

class RestApi {
  instance = void 0
  uploadinstance = void 0
  __token = void 0
  constructor () {
    this.instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Access-Control-Allow-Origin': '*'
      }
    })
  }
  config ({ router, store }) {
    this.token = store.state.auth.token
    let that = this
    this.instance.interceptors.request.use(function (config) {
      config.headers.Authorization = 'Bearer ' + that.token
      return config
    }, function (error) {
      return Promise.reject(error)
    })

    this.instance.interceptors.response.use(function (response) {
      return response
    }, function (error) {
      if (error.response.status === 401) {
        Notify.create({
          color: 'red-5',
          textColor: 'white',
          position: 'top',
          icon: 'fas fa-exclamation-triangle',
          message: error.response.data.message
        })
        // LocalStorage.remove('token')
        store.commit('auth/token', '')
        store.commit('auth/setDpdSid', '')
        // LocalStorage.remove('user')
        store.commit('auth/user', {})
        if (router.currentRoute.name !== 'Login') {
          router.push('/')
        }
        return Promise.reject(error)
      }
      let data = error.response.data || {}
      let message = data.message
      Notify.create({
        color: 'red-5',
        textColor: 'white',
        position: 'top',
        icon: 'fas fa-exclamation-triangle',
        message: message
      })
      return Promise.reject(error)
    })
  }

  async getMyProfile (params) {
    return this.instance.get('/my-profile', params)
  }
  async updateUserInfo (params) {
    return this.instance.post('/update-profile', params)
  }
  async sendVerifyEmail (params) {
    return this.instance.post('/sendVerifyEmail', params)
  }
  async sendVerifyPhone (params) {
    return this.instance.post('/sendVerifyPhone', params)
  }
  async uploadUserAvatar (params, headers) {
    return this.instance.post('/uploadUserAvatar', params, headers)
  }
  async getClients (params) {
    return this.instance.post('/getClients', params)
  }
  async getUsers (params) {
    return this.instance.post('/getUsers', params)
  }
  async getClientInfo (id) {
    return this.instance.get('/getClientDetails?id=' + id)
  }
  async getUserInfo (id) {
    return this.instance.get('/getUserDetails?id=' + id)
  }
  async updateUser (params) {
    return this.instance.post('/updateUser', params)
  }
  async removeUser (params) {
    return this.instance.post('/removeUser', params)
  }
  async getReports (params) {
    return this.instance.post('/getReports', params)
  }
  async getReportsAll (params) {
    return this.instance.post('/getReportsAll', params)
  }
  async getMonthlyReports (params) {
    return this.instance.post('/getMonthlyReports', params)
  }
  async getMonthlyReportsByDriver (params) {
    return this.instance.post('/getMonthlyReportsByDriver', params)
  }
  async getMonthlyAllByDriver (params) {
    return this.instance.post('/getMonthlyAllByDriver', params)
  }
  async getRouteAllByDriver (params) {
    return this.instance.post('/getRouteAllByDriver', params)
  }
  async getMonthlyReportsAll (params) {
    return this.instance.post('/getMonthlyReportsAll', params)
  }
  async getReportInfo (reportNo) {
    return this.instance.get('/getReportDetails?reportNo=' + reportNo)
  }
  async getRegularRoutes (updated_at) {
    return this.instance.get('/getRegularRoutes?updated_at='+updated_at)
  }
  async getExtraRoutes (updated_at) {
    return this.instance.get('/getExtraRoutes?updated_at='+updated_at)
  }
  async getDriverList (updated_at) {
    return this.instance.get('/getDriverList?updated_at=' + updated_at)
  }
  async getDriversByUserID (updated_at) {
    return this.instance.get('/getDriversByUserID?updated_at=' + updated_at)
  }
  async getRNCID () {
    return this.instance.get('/getRNCID')
  }
  async getDrivers (params) {
    return this.instance.post('/getDrivers', params)
  }
  async getDriverInfo (id) {
    return this.instance.get('/getDriverDetails?id=' + id)
  }
  async createDriver (params) {
    return this.instance.post('/createDriver', params)
  }
  async updateDriver (params) {
    return this.instance.post('/updateDriver', params)
  }
  async removeDriver (params) {
    return this.instance.post('/removeDriver', params)
  }
      
  async getFleets (params) {
    return this.instance.post('/getFleets', params)
  }
  async createFleet (params) {
    return this.instance.post('/createFleet', params)
  }
  async updateFleet (params) {
    return this.instance.post('/updateFleet', params)
  }
  async removeFleet (params) {
    return this.instance.post('/removeFleet', params)
  }
  async getFleetInfo (id) {
    return this.instance.get('/getFleetDetails?id=' + id)
  }
  async getFleetAll() {
    return this.instance.get('/getFleetAll')
  }

  async getServices (params) {
    return this.instance.post('/getServices', params)
  }
  async createService (params) {
    return this.instance.post('/createService', params)
  }
  async updateService (params) {
    return this.instance.post('/updateService', params)
  }
  async removeService (params) {
    return this.instance.post('/removeService', params)
  }
  async getServiceInfo (id) {
    return this.instance.get('/getServiceDetails?id=' + id)
  }


  async getMileageDrivers() {
    return this.instance.get('/getMileageDrivers')
  }
  async getMileageAll(params) {
    return this.instance.post('/getMileageAll', params)
  }
  async getMileageRecords (params) {
    return this.instance.post('/getMileageRecords', params)
  }
  async createMileageRecord (params) {
    return this.instance.post('/createMileageRecord', params)
  }
  async updateMileageRecord (params) {
    return this.instance.post('/updateMileageRecord', params)
  }
  async removeMileageRecord (params) {
    return this.instance.post('/removeMileageRecord', params)
  }
  async getMileageInfo(driver_id, id) {
    if (id == undefined) {
      return this.instance.get('/getMileageDetails?driver_id=' + driver_id)
    } else {
      return this.instance.get('/getMileageDetails?driver_id=' + driver_id + '&id=' + id)
    }
  }

  
  async getFuels (params) {
    return this.instance.post('/getFuels', params)
  }
  async createFuel (params) {
    return this.instance.post('/createFuel', params)
  }
  async updateFuel (params) {
    return this.instance.post('/updateFuel', params)
  }
  async removeFuel (params) {
    return this.instance.post('/removeFuel', params)
  }
  async getFuelInfo (id) {
    return this.instance.get('/getFuelDetails?id=' + id)
  }
  
  async getRouteAll (updated_at) {
    return this.instance.get('/getRouteAll?updated_at=' + updated_at)
  }
  async getMonthlyRouteAll (updated_at) {
    return this.instance.get('/getMonthlyRouteAll')
  }
  async getRoutes (params) {
    return this.instance.post('/getRoutes', params)
  }
  async createRoute (params) {
    return this.instance.post('/createRoute', params)
  }
  async updateRoute (params) {
    return this.instance.post('/updateRoute', params)
  }
  async removeRoute (params) {
    return this.instance.post('/removeRoute', params)
  }
  async createSingleRecord (params) {
    return this.instance.post('/createSingleRecord', params)
  }
  async updateSingleRecord (params) {
    return this.instance.post('/updateSingleRecord', params)
  }
  async removeSingleRecord (params) {
    return this.instance.post('/removeSingleRecord', params)
  }
  async createReport (params) {
    return this.instance.post('/createReport', params)
  }
  async updateReport (params) {
    return this.instance.post('/updateReport', params)
  }
  async removeReport (params) {
    return this.instance.post('/removeReport', params)
  }
  async registerUser (params) {
    return this.instance.post('/register', params)
  }
  async login (params) {
    let response = await this.instance.post('/loginUser', params)
    this.token = response.data.token
    return response
  }
  async loginDriver (params) {
    let response = await this.instance.post('/loginDriver', params)
    this.token = response.data.token
    return response
  }




  async dpdLogin (params) {
    let response = await this.instance.post('/dpdLogin', params)
    return response
  }
  async getDpdRouteAll () {
    let response = await this.instance.get('/getDpdRouteAll')
    return response
  }
  async getDpdPeriods (params) {
    let response = await this.instance.post('/getDpdPeriods', params)
    return response
  }
  async getPeriods(currentDate) {
    let response = await this.instance.get('/getPeriods?date=' + currentDate)
    return response
  }
  async updateDpdData(params) {
    let response = await this.instance.post('/updateDpdData', params);
    return response
  }
  async getDpdDatas(params) {
    let response = await this.instance.post('/getDpdDatas', params);
    return response
  }
  async getDPDInfo(dpdDate) {
    let response = await this.instance.get('/getDPDInfo?dpdDate=' + dpdDate)
    return response
  }
  async updateDPDInfo(params) {
    let response = await this.instance.post('/updateDPDInfo', params)
    return response
  }
  async removeDpdInfo(params) {
    let response = await this.instance.post('/removeDpdInfo', params)
    return response
  }
  async getDpdDriverPerformanceList (params) {
    return this.instance.post('/getDpdDriverPerformanceList', params)
  }
  async getDpdDriverPerformanceAll (params) {
    return this.instance.post('/getDpdDriverPerformanceAll', params)
  }
  async getDpdPerformanceList (params) {
    return this.instance.post('/getDpdPerformanceList', params)
  }
  async getDpdPerformanceAll (params) {
    return this.instance.post('/getDpdPerformanceAll', params)
  }

  
  async getDpdDrivers (params) {
    return this.instance.post('/getDpdDrivers', params)
  }
  async getDpdDriverInfo (id) {
    return this.instance.get('/getDpdDriverDetails?id=' + id)
  }
  async createDpdDriver (params) {
    return this.instance.post('/createDpdDriver', params)
  }
  async updateDpdDriver (params) {
    return this.instance.post('/updateDpdDriver', params)
  }
  async removeDpdDriver (params) {
    return this.instance.post('/removeDpdDriver', params)
  }
  


  async forgotPassword (params) {
    return this.instance.post('/forgotPassword', params)
  }
  async resetPassword (params) {
    return this.instance.post('/resetPassword', params)
  }
  async confirmUser (params, token) {
    this.token = token
    return this.instance.post('/confirmUser', params)
  }
  async activeUser (params) {
    return this.instance.post('/activeUser', params)
  }
  async getLocatorResults (params) {
    return this.instance.post('/getLocatorResults', params)
  }
  async convertbng2latlong (params) {
    return this.instance.post('/convertbng2latlong', params)
  }
  // async getLocatorResults (params) {
  //   return this.instance.post('https://explorer.geowessex.com/plugins/general/core/HeaderBarSearchResults', params)
  // }
  // async convertbng2latlong (params) {
  //   return this.instance.get('https://cors-anywhere.herokuapp.com/https://api.getthedata.com/bng2latlong/' + params.northing + '/' + params.easting)
  // }
}

const api = new RestApi()

export default ({ Vue, router, store }) => {
  api.config({ router, store })
  Vue.prototype.$api = api
}
export { api }
