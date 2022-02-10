<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          :data="monthlyList"
          :columns="columns"
          row-key="report_date"
          :filter="filter"
          @request="getMonthlyListByDriver"
          binary-state-sort
          :pagination.sync="pagination"
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top>
            <!-- <div class="col-6 row justify-start">
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
                class="q-ml-xs q-select-list"
              >
                <template v-if="selectedRoute" v-slot:append>
                  <q-icon name="cancel" @click.stop="selectedRoute = null" @click="onChangeRoute" class="cursor-pointer text-white" />
                </template>
              </q-select>
            </div> -->
            <div class="row justify-end">
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
            <q-tr :props="props">
              <q-td key="index" :props="props">{{ props.row.index }}</q-td>
              <q-td key="report_date" :props="props" class="text-uppercase">{{ changeDateFormat(props.row.report_date) }}</q-td>
              <q-td key="route_number" :props="props">{{ props.row.route_number }}</q-td>
              <q-td key="pay_type" :props="props">{{ props.row.pay_type === 'fixed' ? 'FIXED' : 'PAY PER STOP' }}</q-td>
              <q-td key="stops" :props="props">{{ props.row.pay_type === 'fixed' ? '-' : props.row.stops }}</q-td>
              <q-td key="payment" :props="props">{{ props.row.payment ? '£' +  props.row.payment : '' }}</q-td>
              <q-td key="is_group" :props="props">{{ props.row.is_group === 1 ? 'DAILY':'EXTRA' }}</q-td>
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-between">
              <div class="q-my-auto">
                <!-- <q-btn
                  label="Export"
                  no-caps
                  dense
                  rounded
                  outline
                  @click="exportTable"
                  class="q-ma-none"
                  style="width: 60px; height:30px"
                /> -->
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

<style lang="stylus">
.q-table__top
  justify-content: flex-end
</style>

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
        { name: 'pay_type', required: true, label: 'PAY TYPE', align: 'left', field: 'pay_type' },
        { name: 'stops', required: true, label: 'STOPS', align: 'left', field: 'stops' },
        { name: 'payment', required: true, label: 'PAYMENT', align: 'left', field: 'payment' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' }
      ],
      monthlyList: [],
      monthlyAll: [],
      selectedRoute: null,
      routeNumbers: [],
      is_mobile: 'web',
      isDateFilter: false
    }
  },
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    driver: {
      get () {
        return this.$store.state.auth.driver
      }
    }
  },
  async mounted () {
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    // await this.getRouteAll()
    this.fromDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.fromDateAPI = date.formatDate(date.startOfDate(new Date(), 'month'), 'YYYY-MM-DD')
    this.endDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.endDateAPI = date.formatDate(new Date(), 'YYYY-MM-DD')
    this.getMonthlyListByDriver({
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
      return convertedDate
    },
    onFromDateChanged (fromdate) {
      this.fromDateAPI = date.formatDate(date.extractDate(this.fromDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    onEndDateChanged (enddate) {
      this.endDateAPI = date.formatDate(date.extractDate(this.endDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.monthlyList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getMonthlyListByDriver({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },

    getRouteAll: async function () {
      try {
        const params = {
          conditions: {
            driver_id: this.driver.id
          }
        };
        let res = await api.getRouteAllByDriver(params)
        this.routeNumbers = res.data.data
      } catch (e) {
      }
    },
    onChangeRoute () {
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        isScroll: false,
      });
    },
    getMonthlyListByDriver: async function (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter
      let isScroll = props.isScroll

      // get all rows if "All" (0) is selected
      let fetchCount = rowsPerPage === 0 ? rowsNumber : rowsPerPage

      // calculate starting row of driverList
      let startRow = (page - 1) * rowsPerPage

      const params = {
        conditions: {
          is_date_filter: true,
          driver_id: this.driver.id
        },
        start: startRow,
        numPerPage: fetchCount,
        sortBy: sortBy,
        descending: descending,
        fromDate: this.fromDateAPI,
        endDate: this.endDateAPI
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
        let res = await api.getMonthlyReportsByDriver(params)
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
      return (this.monthlyList.reduce( function(a, b){
        return a + Number(b['payment']);
      }, 0)).toFixed(2);
    }
    // getMonthlyAll: async function (props) {
    //   let filter = props.filter

    //   const params = {
    //     conditions: {
    //       is_date_filter: true,
    //       driver_id: this.driver.id
    //     },
    //     fromDate: this.fromDateAPI,
    //     endDate: this.endDateAPI
    //   }
    //   if (this.selectedRoute) {
    //     params.conditions.route_number = this.selectedRoute.route_number
    //   }
    //   if (!this.isDateFilter) {
    //     params.conditions.is_date_filter = false
    //   }

    //   // fetch vehicleList from "server"
    //   Loading.show()
    //   try {
    //     let res = await api.getMonthlyAllByDriver(params)
    //     Loading.hide()

    //     // clear out existing vehicleList and add new
    //     res.data.data.forEach((row, index) => {
    //       row.index = index + 1
    //     })
    //     this.monthlyAll = res.data.data

    //     // ...and turn of loading indicator
    //   } catch (e) {
    //     Loading.hide()
    //     console.log('errorrrrrrrrrr', e)
    //   }
    // },
    // async exportTable () {
    //   // get All records before export
    //   await this.getMonthlyAll({ filter: this.filter })
    //   // naive encoding to csv format
    //   let columns = this.columns
    //   const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
    //     this.monthlyAll.map(row => columns.map(col => {
    //       if (col.field === 'is_group') {
    //         return wrapCsvValue(
    //           row.is_group === 1 ? 'DAILY' : 'EXTRA',
    //           col.format
    //         )
    //       } else if (col.field === 'user_name') {
    //         return wrapCsvValue(
    //           row.user.full_name,
    //           col.format
    //         )
    //       } else {
    //         return wrapCsvValue(
    //           row[col.field],
    //           col.format
    //         )
    //       }
    //     }).join(','))
    //   ).join('\r\n')

    //   const status = exportFile(
    //     'table-export.csv',
    //     content,
    //     'text/csv'
    //   )

    //   if (status !== true) {
    //     this.$q.notify({
    //       message: 'Browser denied file download...',
    //       color: 'negative',
    //       icon: 'warning'
    //     })
    //   }
    // }
  },
  created () {
  }
}
</script>
