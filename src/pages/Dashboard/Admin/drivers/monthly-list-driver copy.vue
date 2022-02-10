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
            <div class="col-6 row justify-start">
              <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white" color="blue-7" style="width:168px;" >
                <template v-slot:append> <q-icon name="search" color="white"/> </template>
              </q-input>
            </div>
            <div class="col-6 row justify-end">
              <q-input dense v-model="fromDate" style="width:168px;" input-class="text-white" color="blue-7" class="q-mb-sm">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer" color="white">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="fromDate"
                        minimal
                        @input="onFromDateChanged"
                        mask="DD/MM/YY ddd"
                        color="blue-7"
                      >
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
              <q-input dense v-model="endDate" style="width:168px;" input-class="text-white" color="blue-7" class="q-ml-xs q-mb-sm">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer" color="white">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="endDate"
                        minimal
                        @input="onEndDateChanged"
                        mask="DD/MM/YY ddd"
                        color="blue-7"
                      >
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
              <q-td key="driver_name" :props="props">{{ props.row.driver_name }}</q-td>
              <q-td key="is_group" :props="props">{{ props.row.is_group === 1 ? 'DAILY':'EXTRA' }}</q-td>
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
                Total Records: {{pagination.rowsNumber}}
                <!-- <q-btn
                  icon="chevron_left"
                  color="grey-8"
                  round
                  dense
                  flat
                  :disable="props.isFirstPage"
                  @click="props.prevPage"
                />
                <span>{{props.pagination.page}} / {{Math.ceil(props.pagination.rowsNumber / props.pagination.rowsPerPage)}}</span>
                <q-btn
                  icon="chevron_right"
                  color="grey-8"
                  round
                  dense
                  flat
                  :disable="props.isLastPage"
                  @click="props.nextPage"
                /> -->
              </div>
            </div>
          </template>
        </q-table>
      </div>
      <!-- <q-dialog
        v-model="showDetail"
        persistent
        :maximized="true"
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card style="background-color: #3E444E">
          <q-bar>
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-primary">Close</q-tooltip>
            </q-btn>
            <div class="text-h6 text-white">{{dialogTitle}}</div>
          </q-bar>

          <q-separator />

          <q-card-section style="max-height: 50vh" class="scroll">
            <q-form
              @submit="onSubmit"
              ref="selectedRecord"
              :model="selectedRecord"
              style="max-width: 400px; margin: auto;"
            >
              <q-card style="background-color: #3E444E">
                <q-card-section>
                  <q-input dense outlined v-model="selectedRecord.report_date" bg-color="white" input-class="text-black text-center" color="blue-7">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                          <q-calendar
                            ref="calendar"
                            v-model="selectedRecord.report_date"
                            show-work-weeks
                            view="month"
                            mini-mode
                            enable-outside-days
                            bordered
                            locale="en-us"
                            :class="$q.dark.isActive ? 'bg-blue-grey-3': 'bg-grey-1' "
                            style="max-width: 300px; max-height:180px; min-width: auto; overflow: hidden"
                          />
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                </q-card-section>
                <q-separator color="grey-4" />
                <q-card-section>
                  <q-select
                    dense
                    label="Driver"
                    outlined
                    v-model="selectedRecord.driver_id"
                    use-input
                    hide-selected
                    fill-input
                    :options="filteredNames"
                    :option-value="opt => opt === null ? null : opt.id"
                    :option-label="opt => opt === null ? '- Null -' : opt.driver_name"
                    emit-value
                    map-options
                    @filter="filterFn"
                    class="q-mb-xs"
                    behavior="menu"
                    bg-color="white"
                    input-class="text-black"
                    color="blue-7"
                  >
                  </q-select>
                  <q-select
                    dense
                    required
                    label="Route"
                    outlined
                    v-model="selectedRecord.route_id"
                    use-input
                    hide-selected
                    fill-input
                    :options="routes"
                    :option-value="opt => opt === null ? null : opt.id"
                    :option-label="opt => opt === null ? '- Null -' : opt.route_number"
                    emit-value
                    map-options
                    class="q-mb-xs"
                    behavior="menu"
                    bg-color="white"
                    input-class="text-black"
                    color="blue-7"
                  >
                  </q-select>
                </q-card-section>
                <q-card-actions align="center">
                  <q-btn
                    color="blue-7"
                    :label="isNewRecord ? 'Add' : 'Update'"
                    no-caps
                    dense
                    rounded
                    class="q-mt-xs"
                    style="width: 100px; height:40px"
                    type="submit"
                  />
                </q-card-actions>
              </q-card>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog> -->
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
        // { name: 'user_name', required: true, label: 'MANAGER', align: 'left', field: 'user_name', sortable: true },
        { name: 'route_number', required: true, label: 'ROUTE', align: 'left', field: 'route_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' }
      ],
      columns_admin: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'report_date', required: true, label: 'DATE', align: 'left', field: 'report_date' },
        { name: 'route_number', required: true, label: 'ROUTE', align: 'left', field: 'route_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' },
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
  mounted () {
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
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
      // let convertedDate = date.formatDate(date.extractDate(reportDate, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YY ddd HH:mm')
      return convertedDate
    },
    onFromDateChanged (fromdate) {
      this.fromDateAPI = date.formatDate(date.extractDate(fromdate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    onEndDateChanged (enddate) {
      this.endDateAPI = date.formatDate(date.extractDate(enddate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    // async goToSingleDetail (data) {
    //   await this.getDriverList()
    //   await this.getExtraRoutes()
    //   if (data.is_group === 1) {
    //     this.$router.push({ name: 'Edit Schedule', params: { report_no: data.report_no } })
    //   } else {
    //     this.isNewRecord = false
    //     this.filteredNames = this.drivers
    //     this.dialogTitle = 'Edit Extra Route'
    //     this.selectedRecord.id = data.id
    //     this.selectedRecord.driver_id = data.driver_id
    //     this.selectedRecord.route_id = data.route_id
    //     this.selectedRecord.report_date = data.report_date
    //   }
    //   this.showDetail = true
    // },
    // addGroup () {
    //   this.$router.push('/dashboard/schedules/new')
    // },
    cancelDetail () {
      this.showDetail = false
      this.selectedRecord = {}
    },
    async onSubmit () {
      // this.selectedRecord.report_date = date.formatDate(date.addToDate(this.selectedRecord.report_date, { days: 1 }), 'YYYY-MM-DD')
      if (!this.selectedRecord.driver_id) {
        this.selectedRecord.driver_id = 'RNC'
      }
      const params = {
        data: this.selectedRecord
      }
      params.conditions = {
        id: this.selectedRecord.id
      }
      Loading.show()
      try {
        let res = await api.updateSingleRecord(params)
        Loading.hide()
        console.log('result', res.data)
      } catch (error) {
        Loading.hide()
        console.log('error', error)
      }
      this.cancelDetail()
      this.getMonthlyListByDriver({
        pagination: this.pagination,
        filter: this.filter
      })
      console.log('selected record', this.selectedRecord)
    },
    // getExtraRoutes: async function () {
    //   Loading.show()
    //   try {
    //     let res = await api.getExtraRoutes()
    //     Loading.hide()
    //     this.routes = res.data.data
    //   } catch (e) {
    //     Loading.hide()
    //     // this.$router.push('/dashboard/schedules')
    //   }
    // },
    // getDriverList: async function () {
    //   Loading.show()
    //   try {
    //     let res = await api.getDriverList()
    //     Loading.hide()
    //     this.drivers = res.data.data
    //   } catch (e) {
    //     Loading.hide()
    //   }
    // },
    filterFn (val, update, abort) {
      update(() => {
        if (val === '') {
          this.filteredNames = []
        } else {
          const needle = val.toLowerCase()
          this.filteredNames = this.drivers.filter(name => name.driver_name.toLowerCase().indexOf(needle) > -1)
          this.filteredNames = this.filteredNames.slice(0, 3)
        }
      },
      ref => {
        if (val !== '' && ref.options.length > 0) {
          const matchedName = ref.options.find(item => item.driver_name.toLowerCase() === val.toLowerCase())
          if (matchedName) {
            ref.add(matchedName)
          }
        }
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
      if (filter) {
        params.conditions.filter = filter
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

    getMonthlyAll: async function (props) {
      let filter = props.filter

      const params = {
        conditions: {
          is_date_filter: true
        },
        fromDate: this.fromDateAPI,
        endDate: this.endDateAPI
      }
      if (filter) {
        params.conditions.filter = filter
      }
      if (!this.isDateFilter) {
        params.conditions.is_date_filter = false
      }

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
        console.log('errorrrrrrrrrr', e)
      }
    },
    remove (report) {
      // Confirm Remove Vehicle
      this.$q.dialog({
        title: 'Confirm',
        message: 'Are you surely remove ' + report.report_title + '?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        const params = {
          conditions: {
            id: report.id
          }
        }
        Loading.show()
        try {
          let res = await api.removeReport(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: report.report_title + ' is removed successfully !'
          })
          this.getMonthlyListByDriver({
            pagination: this.pagination,
            filter: this.filter
          })
        } catch (e) {
          Loading.hide()
        }
      }).onCancel(() => {
      }).onDismiss(() => {
      })
    },
    async exportTable () {
      // get All records before export
      await this.getMonthlyAll({ filter: this.filter })
      // naive encoding to csv format
      let columns = this.columns
      const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
        this.monthlyAll.map(row => columns.map(col => {
          if (col.field === 'is_group') {
            return wrapCsvValue(
              row.is_group === 1 ? 'DAILY' : 'EXTRA',
              col.format
            )
          } else if (col.field === 'user_name') {
            return wrapCsvValue(
              row.user.full_name,
              col.format
            )
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
  created () {
  }
}
</script>
