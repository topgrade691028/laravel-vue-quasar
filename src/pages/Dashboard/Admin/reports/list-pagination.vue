<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          :data="reportList"
          :columns="columns"
          row-key="report_date"
          :filter="filter"
          @request="getReportList"
          binary-state-sort
          :pagination.sync="pagination"
        >
          <template v-slot:top>
            <div class="col-6 row justify-start">
              <!-- <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white border-white" style="width: 130px;" color="blue-7" class="q-mb-sm">
                <template v-slot:append>
                  <q-icon name="search" color="white" />
                </template>
              </q-input>
              &nbsp; -->
              <q-btn
                color="blue-7"
                label="+Add Record"
                no-caps
                dense
                rounded
                @click="showAddRecordDlg"
                style="width: 130px; height:40px;"
              />
            </div>
            <div class="col-6 row justify-end">
              <q-input dense v-model="fromDate" style="width:130px;" input-class="text-white" color="blue-7" class="q-mb-sm">
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
              <q-input dense v-model="endDate" style="width:130px;" input-class="text-white" class="q-ml-xs q-mb-sm" color="blue-7">
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
            <q-tr :props="props" @click.native="goToDetail(props.row)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <q-td key="report_date" :props="props" class="text-uppercase">{{ changeDateFormat(props.row.report_date) }}</q-td>
              <q-td key="is_group" :props="props">{{ props.row.is_group === 1 ? 'DAILY' : 'EXTRA' }}</q-td>
              <q-td key="user_name" :props="props">{{ props.row.user.full_name }}</q-td>
              <q-td key="buttons" :props="props">
                <q-btn
                  rounded
                  flat
                  :icon=" 'fas fa-pen-alt' "
                  @click.native.stop
                  @click="goToDetail(props.row)"
                />
                <!-- <q-btn
                  rounded
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="remove(props.row)"
                /> -->
              </q-td>
            </q-tr>
          </template>

          <template v-slot:bottom="props">
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
                  style="width: 60px; height:30px;"
                />
              </div>
              <div class="row justify-end items-center">
                Total Records: {{pagination.rowsNumber}}
                <q-btn
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
                />
              </div>
            </div>
          </template>
        </q-table>
      </div>
      <q-dialog
        v-model="showAddDlg"
        persistent
        :maximized="true"
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card style="background-color: #3E444E">
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
            </q-btn>
          </q-bar>

          <q-separator />

          <div class="layout-center">
            <q-card-section>
              <div class="text-h6 text-white text-center">What kind of record do you want to add?</div>
            </q-card-section>

            <q-card-section class="q-pt-none">
              <q-option-group
                :options="add_options"
                type="radio"
                v-model="selectedOption"
                size="xl"
                style="width: 140px"
                class="q-mx-auto text-white"
                color="white"
                keep-color
              />
            </q-card-section>
            <q-card-section class="text-center q-pt-none">
              <q-btn
                label="Add"
                color="blue-7"
                no-caps
                dense
                rounded
                @click="addRecord"
                class="q-mt-xs"
                style="width: 100px; height:40px;"
              />
            </q-card-section>
          </div>
        </q-card>
      </q-dialog>
      <q-dialog
        v-model="showDetail"
        persistent
        :maximized="true"
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card style="background-color: #3E444E">
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
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
              <div class="row justify-between q-col-gutter-md" >
                <div class="col-12">
                  <span class="text-white">Date</span>
                  <q-input dense outlined v-model="reportDate" color="blue-7" bg-color="white" input-class="text-black text-center">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                          <q-date v-model="reportDate"
                            minimal
                            mask="DD/MM/YY ddd"
                            color="blue-7"
                            first-day-of-week="1"
                          >
                            <div class="row items-center justify-end q-gutter-sm">
                              <q-btn label="OK" color="blue-7" @click="onReportDateChanged" flat v-close-popup />
                            </div>
                          </q-date>
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                  <q-separator class="q-my-md" color="grey-4" />
                  <span class="text-white">Driver</span>
                  <q-select
                    dense
                    required
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
                    :hide-dropdown-icon="true"
                  >
                  </q-select>
                  <span class="text-white">Route</span>
                  <q-select
                    dense
                    required
                    outlined
                    v-model="selectedRecord.route_id"
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
                    :hide-dropdown-icon="true"
                  >
                  </q-select>
                </div>
              </div>
              <q-card-actions align="center">
                <!-- <q-btn flat label="Cancel" color="primary" @click="cancelDetail"/> -->
                <q-btn
                  no-caps
                  dense
                  rounded
                  v-if="!isNewRecord"
                  label="Delete"
                  color="blue-7"
                  @click="remove"
                  style="width: 100px; height:40px;"
                />
                <q-btn
                  :label="isNewRecord ? 'Add' : 'Update'"
                  color="blue-7"
                  no-caps
                  dense
                  rounded
                  style="width: 100px; height:40px;"
                  type="submit"
                />
              </q-card-actions>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </template>
  </q-page>
</template>

<style lang="stylus">
.q-btn__wrapper
  padding 4px 6px
.layout-center
  position: absolute;
  width: 100%;
  top: 50%;
  left: 50%;
  transform: translateY(-50%) translateX(-50%);
  padding-left: 25px;
  padding-right: 25px;

</style>

<script>

import { api } from 'src/boot/api'
import { Loading, date, exportFile } from 'quasar'

function wrapCsvValue (val, formatFn) {
  console.log('csv', val)
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
  name: 'ReportList',
  data () {
    return {
      filter: '',
      showDetail: false,
      showAddDlg: false,
      add_options: [
        { label: 'FD routes', value: 'fd_routes' },
        { label: 'Extra route', value: 'extra_route' }
      ],
      selectedOption: '',
      pagination: {
        sortBy: 'report_date',
        descending: true,
        page: 1,
        rowsPerPage: 30,
        rowsNumber: 20
      },
      fromDate: '',
      fromDateAPI: '',
      endDate: '',
      endDateAPI: '',
      columns: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'report_date', required: true, label: 'DATE', align: 'left', field: 'report_date' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' },
        { name: 'buttons', label: '', field: 'buttons' }
      ],
      reportList: [],
      reportDate: '',
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
      is_mobile: 'web',
      rnc_id: ''
    }
  },
  mounted () {
    // get initial vehicleList from server (1st page)
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.fromDate = date.formatDate(date.startOfDate(new Date(), 'month'), 'DD/MM/YY ddd')
    this.fromDateAPI = date.formatDate(date.startOfDate(new Date(), 'month'), 'YYYY-MM-DD')
    this.endDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.endDateAPI = date.formatDate(new Date(), 'YYYY-MM-DD')
    this.getReportList({
      pagination: this.pagination,
      filter: undefined
    })
  },
  methods: {
    createNew () {
      this.$router.push('/dashboard/schedules/new')
    },
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
      let convertedDate = date.formatDate(date.addToDate(date.extractDate(reportDate, 'YYYY-MM-DD'), { hours: 5 }), 'DD-MM-YY ddd')
      // let convertedDate = date.formatDate(date.extractDate(reportDate, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YY ddd HH:mm')
      return convertedDate
    },
    showAddRecordDlg () {
      this.showAddDlg = true
    },
    hideAddRecordDlg () {
      this.showAddDlg = false
      this.selectedOption = ''
    },
    onReportDateChanged (reportDate) {
      this.selectedRecord.report_date = date.formatDate(date.extractDate(this.reportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
    },
    onFromDateChanged (fromdate) {
      // this.fromDate = date.formatDate(fromdate, 'YYYY-MM-DD')
      this.fromDateAPI = date.formatDate(date.extractDate(this.fromDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.getReportList({
        pagination: this.pagination,
        filter: undefined
      })
    },
    onEndDateChanged (enddate) {
      this.endDateAPI = date.formatDate(date.extractDate(this.endDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.getReportList({
        pagination: this.pagination,
        filter: undefined
      })
    },
    exportTable () {
      // naive encoding to csv format
      const content = [ this.columns.map(col => wrapCsvValue(col.label)) ].concat(
        this.reportList.map(row => this.columns.map(col => {
          if (col.field === 'is_group') {
            return wrapCsvValue(
              row.is_group === 1 ? 'DAILY' : 'EXTRA',
              col.format
            )
          }
          if (col.field === 'user_name') {
            return wrapCsvValue(
              row.user.full_name,
              col.format
            )
          }
          if (col.field === 'report_date') {
            return wrapCsvValue(
              row.report_date,
              col.format
            )
          }
        }
        ).join(','))
      ).join('\r\n')

      const status = exportFile(
        'table-export.xls',
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
    },
    addRecord () {
      if (this.selectedOption === 'extra_route') {
        this.goToDetail()
      } else if (this.selectedOption === 'fd_routes') {
        this.$router.push('/dashboard/schedules/new')
      }
    },
    async goToDetail (data) {
      await this.getRNCID()
      await this.getDriverList()
      await this.getExtraRoutes()
      if (data) {
        if (data.is_group === 1) {
          this.$router.push({ name: 'Edit Schedule', params: { report_no: data.report_no } })
        } else {
          this.isNewRecord = false
          this.filteredNames = this.drivers
          this.dialogTitle = 'Edit Extra Route'
          this.selectedRecord.id = data.id
          this.selectedRecord.driver_id = data.driver_id
          this.selectedRecord.route_id = data.route_id
          this.selectedRecord.report_date = data.report_date
          this.reportDate = date.formatDate(date.extractDate(data.report_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
        }
      } else {
        this.dialogTitle = 'Add Extra Route'
        this.isNewRecord = true
        this.selectedRecord.report_date = date.formatDate(new Date(), 'YYYY-MM-DD')
        this.selectedRecord.driver_id = ''
        this.selectedRecord.route_id = ''
        this.reportDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
      }
      this.showDetail = true
    },
    cancelDetail () {
      this.showDetail = false
      this.selectedRecord = {
        id: '',
        report_date: '',
        report_title: '',
        driver_id: '',
        route_id: ''
      }
    },
    getRNCID: async function () {
      Loading.show()
      try {
        let res = await api.getRNCID()
        Loading.hide()
        this.rnc_id = res.data.data
      } catch (e) {
        Loading.hide()
      }
    },
    async onSubmit () {
      this.selectedRecord.report_date = date.formatDate(date.extractDate(this.reportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      if (!this.selectedRecord.driver_id) {
        this.selectedRecord.driver_id = this.rnc_id
      }
      const params = {
        data: this.selectedRecord
      }
      if (!this.isNewRecord) {
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
        this.$router.push('/dashboard/schedules')
      } else {
        Loading.show()
        try {
          let res = await api.createSingleRecord(params)
          Loading.hide()
          console.log('result', res.data)
        } catch (error) {
          Loading.hide()
          console.log('error', error)
        }
      }
      this.cancelDetail()
      this.hideAddRecordDlg()
      this.getReportList({
        pagination: this.pagination,
        filter: this.filter
      })
      console.log('selected record', this.selectedRecord)
    },
    getExtraRoutes: async function () {
      Loading.show()
      try {
        let res = await api.getExtraRoutes()
        Loading.hide()
        this.routes = res.data.data
      } catch (e) {
        Loading.hide()
        // this.$router.push('/dashboard/schedules')
      }
    },
    getDriverList: async function () {
      Loading.show()
      try {
        let res = await api.getDriverList()
        Loading.hide()
        this.drivers = res.data.data
      } catch (e) {
        Loading.hide()
        // this.$router.push('/dashboard/schedules')
      }
    },
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
          // ref.moveOptionSelection(1, true)
          const matchedName = ref.options.find(item => item.driver_name.toLowerCase() === val.toLowerCase())
          // console.log('matched', matchedName)
          if (matchedName) {
            ref.add(matchedName) // reset optionIndex in case there is something selected
            // ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
          }
        }
      })
    },
    // getReportsAll: async function (props) {
    //   let { page, rowsPerPage, sortBy, descending } = props.pagination
    //   let filter = props.filter

    //   const params = {
    //     conditions: {},
    //     sortBy: sortBy,
    //     descending: descending,
    //     fromDate: this.fromDateAPI,
    //     endDate: this.endDateAPI
    //   }
    //   if (filter) {
    //     params.conditions.filter = filter
    //   }

    //   // fetch vehicleList from "server"
    //   Loading.show()
    //   try {
    //     let res = await api.getReportsAll(params)
    //     Loading.hide()

    //     // clear out existing vehicleList and add new
    //     this.reportList = res.data.data
    //     this.reportList.forEach((row, index) => {
    //       row.index = index + 1
    //     })

    //     // update rowsCount with appropriate value
    //     this.pagination.rowsNumber = res.data.totalCount

    //     // don't forget to update local pagination object
    //     this.pagination.page = page
    //     this.pagination.rowsPerPage = rowsPerPage
    //     this.pagination.sortBy = sortBy
    //     this.pagination.descending = descending

    //     // ...and turn of loading indicator
    //   } catch (e) {
    //     Loading.hide()
    //   }
    // },

    getReportList: async function (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter

      // get all rows if "All" (0) is selected
      let fetchCount = rowsPerPage === 0 ? rowsNumber : rowsPerPage

      // calculate starting row of driverList
      let startRow = (page - 1) * rowsPerPage

      const params = {
        conditions: {},
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

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getReports(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        this.reportList = res.data.data
        this.reportList.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1
        })

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
      }
    },
    remove () {
      // Confirm Remove Vehicle
      this.$q.dialog({
        title: 'Confirm',
        message: 'Are you surely remove this report?',
        cancel: true,
        persistent: true,
        color: 'blue-7'
      }).onOk(async () => {
        const params = {
          conditions: {
            id: this.selectedRecord.id
          }
        }
        Loading.show()
        try {
          let res = await api.removeSingleRecord(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: 'Report is removed successfully !'
          })
          this.cancelDetail()
          this.hideAddRecordDlg()
          this.getReportList({
            pagination: this.pagination,
            filter: undefined
          })
        } catch (e) {
          Loading.hide()
        }
      }).onCancel(() => {
      }).onDismiss(() => {
      })
    }
  },
  created () {
  }
}
</script>
