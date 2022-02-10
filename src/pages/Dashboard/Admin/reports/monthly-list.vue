<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          :data="monthlyList"
          :columns="(userLevel === 'admin') ? columns_admin : columns"
          row-key="report_date"
          :filter="filter"
          @request="getMonthlyList"
          binary-state-sort
          :pagination.sync="pagination"
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top>
            <div class="col-6 row justify-start">
              <q-select
                dense
                v-model="selectedDriver"
                :options="driverNames"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.driver_name"
                bg-color="transparent"
                color="blue-7"
                label="DRIVER"
                label-color="white"
                behavior="menu"
                @input="onChangeDriver"
                style="min-width: 130px"
                class="text-white q-mr-xs q-mb-sm q-select-list"
              >
                <template v-if="selectedDriver" v-slot:append>
                  <q-icon name="cancel" @click.stop="selectedDriver = null" @click="onChangeDriver" class="cursor-pointer text-white" />
                </template>
              </q-select>
              <q-select
                dense
                v-model="selectedRoute"
                :options="routeNumbers"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.route_number"
                bg-color="transparent"
                color="blue-7"
                label="ROUTE"
                label-color="white"
                text-color="white"
                behavior="menu"
                @input="onChangeRoute"
                style="min-width: 130px"
                class="q-mb-sm q-select-list"
              >
                <template v-if="selectedRoute" v-slot:append>
                  <q-icon name="cancel" @click.stop="selectedRoute = null" @click="onChangeRoute" class="cursor-pointer text-white" />
                </template>
              </q-select>
              <!-- <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white" color="blue-7" style="width:130px;" >
                <template v-slot:append> <q-icon name="search" color="white"/> </template>
              </q-input> -->
            </div>
            <div class="col-6 row justify-end">
              <q-input dense v-model="fromDate" style="width:130px;" input-class="text-white" color="blue-7" class="q-mb-sm" label="Start date" label-color="white">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer" color="white">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="fromDate"
                        minimal
                        mask="DD/MM/YY ddd"
                        color="blue-7"
                        first-day-of-week="1"
                      >
                        <div class="row items-center justify-end q-gutter-sm">
                          <q-btn label="OK" color="blue-7" @click="onFromDateChanged" flat v-close-popup />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
              <q-input dense v-model="endDate" style="width:130px;" input-class="text-white" color="blue-7" class="q-ml-xs q-mb-sm" label="End date" label-color="white">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer" color="white">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="endDate"
                        minimal
                        mask="DD/MM/YY ddd"
                        color="blue-7"
                        first-day-of-week="1"
                      >
                        <div class="row items-center justify-end q-gutter-sm">
                          <q-btn label="OK" color="blue-7" @click="onEndDateChanged" flat v-close-popup />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" v-if="props.row.user" @click.native="props.row.is_group === 1 ? goToDailyDetail(props.row) : goToExtraDetail(props.row)">
              <q-td key="index" :props="props">{{ props.row.index }}</q-td>
              <q-td key="report_date" :props="props" class="text-uppercase">{{ changeDateFormat(props.row.report_date) }}</q-td>
              <q-td key="route_number" :props="props">{{ props.row.route_number }}</q-td>
              <q-td key="driver_name" :props="props">{{ props.row.driver_name }}</q-td>
              <q-td key="current_paytype" :props="props">{{ props.row.current_paytype === 'fixed' ? 'FIXED' : 'PAY PER STOP' }}</q-td>
              <q-td key="stops" :props="props">{{ props.row.current_paytype === 'fixed' ? '-' : props.row.stops }}</q-td>
              <q-td key="payment" :props="props">{{ props.row.payment ? '£' +  props.row.payment : '' }}</q-td>
              <!-- <q-td key="vat_percentage" :props="props">{{ props.row.vat_percentage * 100 + '%' }}</q-td> -->
              <q-td key="comments" :props="props">
                <q-icon v-if="props.row.comments">
                  <img width="22px" :src="require('../../../../assets/images/icons/comment.svg')">
                </q-icon>
                <q-icon v-else>
                  <img width="22px" style="opacity: 0.4" :src="require('../../../../assets/images/icons/no-comment.svg')">
                </q-icon>
              </q-td>
              <q-td key="is_group" :props="props">{{ props.row.is_group === 1 ? 'DAILY':'EXTRA' }}</q-td>
              <q-td v-if="userLevel === 'admin'" key="depot_location" :props="props">{{ props.row.user? props.row.user.depot_location : '' }}</q-td>
              <q-td v-if="userLevel === 'admin'" key="user_name" :props="props">{{ props.row.user?props.row.user.name:'' }}</q-td>
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-between">
              <div class="q-my-auto">
                <q-btn
                  label="Export"
                  no-caps
                  dense
                  rounded
                  outline
                  @click="exportTable"
                  class="q-ma-none"
                  style="width: 60px; height:30px"
                />
              </div>
              <div class="row justify-end items-center">
                STOPS: {{totalStops()}} &nbsp;
                PAYMENT: {{totalPayment()}} £
              </div>
            </div>
          </template>
        </q-table>
      </div>
    </template>
  </q-page>
</template>

<script>

import { api } from 'src/boot/api'
import { Loading, date, exportFile } from 'quasar'
// import monthpicker from 'quasar-monthpicker'

function wrapCsvValue (val, formatFn) {
  let formatted = formatFn !== void 0
    ? formatFn(val)
    : val

  formatted = formatted === void 0 || formatted === null
    ? ''
    : String(formatted)

  formatted = formatted.split('"').join('""')

  return `"${formatted}"`
}

export default {
  name: 'MonthlyReportList',
  components: {
    // monthpicker
  },
  data () {
    return {
      filter: '',
      fromDate: '',
      fromDateAPI: '',
      endDate: '',
      endDateAPI: '',
      showDetail: false,
      pagination: {
        sortBy: 'report_date',
        descending: true,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20000
      },
      columns: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'report_date', required: true, label: 'DATE', align: 'left', field: 'report_date' },
        { name: 'route_number', required: true, label: 'ROUTE', align: 'left', field: 'route_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'current_paytype', required: true, label: 'PAY TYPE', align: 'left', field: 'current_paytype' },
        { name: 'stops', required: true, label: 'STOPS', align: 'left', field: 'stops' },
        { name: 'payment', required: true, label: 'PAYMENT', align: 'left', field: 'payment' },
        { name: 'comments', required: true, label: 'COMMENTS', align: 'center', field: 'comments' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' }
      ],
      columns_admin: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'report_date', required: true, label: 'DATE', align: 'left', field: 'report_date' },
        { name: 'route_number', required: true, label: 'ROUTE', align: 'left', field: 'route_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'current_paytype', required: true, label: 'PAY TYPE', align: 'left', field: 'current_paytype' },
        { name: 'stops', required: true, label: 'STOPS', align: 'left', field: 'stops' },
        { name: 'payment', required: true, label: 'PAYMENT', align: 'left', field: 'payment' },
        { name: 'comments', required: true, label: 'COMMENTS', align: 'center', field: 'comments' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' },
        { name: 'depot_location', required: true, label: 'DEPOT', align: 'left', field: 'depot_location' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' }
      ],
      monthlyList: [],
      monthlyAll: [],
      selectedRecord: {
        id: '',
        report_date: '',
        report_title: '',
        driver_id: '',
        route_id: ''
      },
      isNewRecord: false,
      dialogTitle: '',
      routes: [],
      drivers: [],
      filteredNames: [],
      rnc_id: '',
      is_mobile: 'web',
      isDateFilter: false,
      selectedDriver: null,
      driverNames: [],
      selectedRoute: null,
      routeNumbers: []
    }
  },
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    userLevel: {
      get () {
        return this.$store.state.auth.userLevel
      }
    }
  },
  async mounted () {
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    await this.getDriverAll()
    await this.getRouteAll()
    this.fromDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.fromDateAPI = date.formatDate(date.startOfDate(new Date(), 'month'), 'YYYY-MM-DD')
    this.endDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.endDateAPI = date.formatDate(new Date(), 'YYYY-MM-DD')
    this.getMonthlyList({
      pagination: this.pagination,
      filter: undefined
    })
  },
  methods: {
    checkPlatform () {
      if (this.$q.platform.is.mobile) {
        if (this.$q.platform.is.ios) {
          this.is_mobile = 'ios'
        } else {
          this.is_mobile = 'android'
        }
      } else {
        this.is_mobile = 'web'
      }
    },
    changeDateFormat (reportDate) {
      let convertedDate = date.formatDate(date.addToDate(date.extractDate(reportDate, 'YYYY-MM-DD'), { hours: 5 }), 'DD-MM-YY dddd')
      // let convertedDate = date.formatDate(date.extractDate(reportDate, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YY ddd HH:mm')
      return convertedDate
    },
    onFromDateChanged (fromdate) {
      this.fromDateAPI = date.formatDate(date.extractDate(this.fromDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyList({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    onEndDateChanged (enddate) {
      this.endDateAPI = date.formatDate(date.extractDate(this.endDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyList({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    async goToDailyDetail (data) {
      if (data) {
        this.$router.push({ name: 'Edit Daily Schedule', params: { reportNo: data.report_no } })
      }
    },
    async goToExtraDetail (data) {
      if (data) {
        this.$router.push({ name: 'Edit Extra Schedule', params: { reportNo: data.report_no } })
      }
    },

    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.monthlyList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getMonthlyList({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },

    getDriverAll: async function () {
      try {
        let res = await api.getDriverList()
        this.driverNames = res.data.data
      } catch (e) {
      }
    },
    getRouteAll: async function () {
      try {
        let res = await api.getMonthlyRouteAll()
        this.routeNumbers = res.data.data
      } catch (e) {
      }
    },
    onChangeDriver () {
      this.getMonthlyList({
        pagination: this.pagination,
        isScroll: false
      })
    },
    onChangeRoute () {
      this.getMonthlyList({
        pagination: this.pagination,
        isScroll: false,
      });
    },
    getMonthlyList: async function (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter
      let isScroll = props.isScroll

      // get all rows if "All" (0) is selected
      let fetchCount = rowsPerPage === 0 ? rowsNumber : rowsPerPage

      // calculate starting row of driverList
      let startRow = (page - 1) * rowsPerPage

      const params = {
        conditions: {
          is_date_filter: true
        },
        start: startRow,
        numPerPage: fetchCount,
        sortBy: sortBy,
        descending: descending,
        fromDate: this.fromDateAPI,
        endDate: this.endDateAPI
      }
      // if (filter) {
      //   params.conditions.filter = filter
      // }
      if (this.selectedDriver) {
        params.conditions.driver_name = this.selectedDriver.driver_name
      }
      if (this.selectedRoute) {
        params.conditions.route_number = this.selectedRoute.route_number
      }
      if (!this.isDateFilter) {
        params.conditions.is_date_filter = false
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getMonthlyReports(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 100 + index + 1
        })
        if (isScroll) {
          this.monthlyList = this.monthlyList.concat(res.data.data)
        } else {
          this.monthlyList = res.data.data
        }

        // update rowsCount with appropriate value
        this.pagination.rowsNumber = res.data.totalCount

        // don't forget to update local pagination object
        this.pagination.page = page
        this.pagination.rowsPerPage = rowsPerPage
        this.pagination.sortBy = sortBy
        this.pagination.descending = descending

        // ...and turn of loading indicator
      } catch (e) {
        Loading.hide()
        console.log('errorrrrrrrrrr', e)
      }
    },

    getMonthlyAll: async function (props) {
      let filter = props.filter

      const params = {
        conditions: {
          is_date_filter: true
        },
        fromDate: this.fromDateAPI,
        endDate: this.endDateAPI
      }
      if (this.selectedDriver) {
        params.conditions.driver_name = this.selectedDriver.driver_name
      }
      if (this.selectedRoute) {
        params.conditions.route_number = this.selectedRoute.route_number
      }
      if (!this.isDateFilter) {
        params.conditions.is_date_filter = false
        this.$q.notify({
          color: 'negative',
          position: 'top',
          message: 'Please choose the date range for exporting'
        })
      } else {
        // fetch vehicleList from "server"
        Loading.show()
        try {
          let res = await api.getMonthlyReportsAll(params)
          Loading.hide()

          // clear out existing vehicleList and add new
          res.data.data.forEach((row, index) => {
            row.index = index + 1
          })
          this.monthlyAll = res.data.data
          // ...and turn of loading indicator
        } catch (e) {
          Loading.hide()
          console.log('errorrrrrrrr', e)
        }
      }
    },
    async exportTable () {
      // get All records before export
      await this.getMonthlyAll({ filter: this.filter })
      if (this.monthlyAll.length) {
        // naive encoding to csv format
        let columns = this.userLevel === 'admin' ? this.columns_admin : this.columns
        const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
          this.monthlyAll.map(row => columns.map(col => {
            if (col.field === 'is_group') {
              return wrapCsvValue(
                row.is_group === 1 ? 'DAILY' : 'EXTRA',
                col.format
              )
            } else if (col.field === 'user_name') {
              return wrapCsvValue(
                row.user.name,
                col.format
              )
            } else if (col.field === 'depot_location') {
              return wrapCsvValue(
                row.user.depot_location,
                col.format
              )
            } else if (col.field === 'current_paytype') {
              return wrapCsvValue(row.current_paytype === 'fixed' ? 'FIXED' : 'PAY PER STOP', col.format)
            } else {
              return wrapCsvValue(
                row[col.field],
                col.format
              )
            }
          }).join(','))
        ).join('\r\n')

        const status = exportFile(
          'table-export.csv',
          content,
          'text/csv'
        )

        if (status !== true) {
          this.$q.notify({
            message: 'Browser denied file download...',
            color: 'negative',
            icon: 'warning'
          })
        }
      }
    },
    totalStops () {
      return this.monthlyList.reduce( function(a, b){
        if (b['current_paytype'] == 'per_stop') {
          return a + Number(b['stops']);
        } else {
          return a + 0;
        }
      }, 0);
    },
    totalPayment () {
      let total = 0;
      total = this.monthlyList.reduce( function(a, b){
        return a + Number(b['payment']);
      }, 0);
      return total.toFixed(2);
    }
  },
  created () {
  }
}
</script>
