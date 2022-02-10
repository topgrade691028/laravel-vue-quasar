<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          :data="reportList"
          :columns="userLevel === 'admin' ? columns_admin : columns"
          row-key="report_date"
          :filter="filter"
          @request="getReportList"
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
              <q-input dense v-model="endDate" style="width:130px;" input-class="text-white" class="q-ml-xs q-mb-sm" color="blue-7" label="End date" label-color="white">
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
            <q-tr :props="props" @click.native="props.row.is_group === 1 ? goToDailyDetail(props.row) : goToExtraDetail(props.row)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <q-td key="report_date" :props="props" class="text-uppercase">{{ changeDateFormat(props.row.report_date) }}</q-td>
              <q-td key="is_group" :props="props">{{ props.row.is_group === 1 ? 'DAILY' : 'EXTRA' }} ({{props.row.route_count}})</q-td>
              <q-td v-if="userLevel === 'admin'" key="full_name" :props="props">{{ props.row.user.name }}</q-td>
              <q-td key="buttons" :props="props">
                <q-btn
                  rounded
                  flat
                  :icon=" 'fas fa-pen-alt' "
                  @click.native.stop
                  @click="props.row.is_group === 1 ? goToDailyDetail(props.row) : goToExtraDetail(props.row)"
                />
              </q-td>
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-between">
              <div class="q-my-auto">
              </div>
              <div class="row justify-end items-center">
                Total Records: {{pagination.rowsNumber}}
              </div>
            </div>
          </template>
        </q-table>
      </div>
      <q-dialog
        v-model="showAddDlg"
        persistent
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card style="background-color: #3E444E; max-width: 500px; min-width: 352px; min-height: 330px">
          <q-bar style="background-color: #272B33">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
            </q-btn>
          </q-bar>

          <q-separator />

          <div>
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
            <q-card-section class="text-center q-py-none">
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
      showAddDlg: false,
      add_options: [
        { label: 'DAILY', value: 'daily' },
        { label: 'EXTRA', value: 'extra' }
      ],
      selectedOption: '',
      pagination: {
        sortBy: 'report_date',
        descending: true,
        page: 1,
        rowsPerPage: 100,
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
        { name: 'buttons', label: '', field: 'buttons' }
      ],
      columns_admin: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'report_date', required: true, label: 'DATE', align: 'left', field: 'report_date' },
        { name: 'is_group', required: true, label: 'TYPE', align: 'left', field: 'is_group' },
        { name: 'full_name', required: true, label: 'USER', align: 'left', field: 'full_name' },
        { name: 'buttons', label: '', field: 'buttons' }
      ],
      reportList: [],
      is_mobile: 'web',
      rnc_id: '',
      isDateFilter: false,
      showEmptyConfirm: false,
    }
  },
  computed: {
    userLevel: {
      get () {
        return this.$store.state.auth.userLevel
      }
    }
  },
  async mounted () {
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.fromDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.fromDateAPI = date.formatDate(date.startOfDate(new Date(), 'month'), 'YYYY-MM-DD')
    this.endDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
    this.endDateAPI = date.formatDate(new Date(), 'YYYY-MM-DD')
    await this.getReportList({
      pagination: this.pagination,
      filter: undefined
    })

  },
  methods: {
    // createNew () {
    //   this.$router.push('/dashboard/schedules/new')
    // },
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
    // onDailyReportDateChanged (reportDate) {
    //   this.dailyReportForm.report_date = date.formatDate(date.extractDate(this.dailyReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
    // },
    // onExtraReportDateChanged (reportDate) {
    //   this.selectedRecord.report_date = date.formatDate(date.extractDate(this.extraReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
    // },
    onFromDateChanged (fromdate) {
      // this.fromDate = date.formatDate(fromdate, 'YYYY-MM-DD')
      this.fromDateAPI = date.formatDate(date.extractDate(this.fromDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getReportList({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    onEndDateChanged (enddate) {
      this.endDateAPI = date.formatDate(date.extractDate(this.endDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.isDateFilter = true
      this.pagination.page = 1
      this.getReportList({
        pagination: this.pagination,
        filter: this.filter
      })
    },
    exportTable () {
      // naive encoding to csv format
      let columns = this.userLevel === 'admin' ? this.columns_admin : this.columns
      const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
        this.reportList.map(row => columns.map(col => {
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
          } else if (col.field === 'report_date') {
            return wrapCsvValue(
              row.report_date,
              col.format
            )
          } else {
            return wrapCsvValue(
              row[col.field],
              col.format
            )
          }
        }
        ).join(','))
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
    },
    addRecord () {
      if (this.selectedOption === 'extra') {
        this.goToExtraDetail()
      } else if (this.selectedOption === 'daily') {
        this.goToDailyDetail()
      }
    },
    async goToExtraDetail (data) {
      if (data) {
        this.$router.push({ name: 'Edit Extra Schedule', params: { reportNo: data.report_no } })
      } else {
        this.$router.push({ name: 'New Extra Schedule'})
      }
    },
    async goToDailyDetail (data) {
      if (data) {
        this.$router.push({ name: 'Edit Daily Schedule', params: { reportNo: data.report_no } })
      } else {
        this.$router.push({ name: 'New Daily Schedule'})
      }
    },
    removeSelectedDriver (index) {
      this.dailyReportForm.report_data[index].driver_id = ''
      // this.dailyReportForm.report_data[index].driver_name = ''
    },
    // getRNCID: async function () {
    //   try {
    //     let res = await api.getRNCID()
    //     this.rnc_id = res.data.data
    //   } catch (e) {
    //   }
    // },
    
    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.reportList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getReportList({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },
    getReportList: async function (props) {
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
      if (filter) {
        params.conditions.filter = filter
      }
      if (!this.isDateFilter) {
        params.conditions.is_date_filter = false
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getReports(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 100 + index + 1
        })
        if (isScroll) {
          this.reportList = this.reportList.concat(res.data.data)
        } else {
          this.reportList = res.data.data
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
      }
    }
  },
  created () {
  }
}
</script>
