<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          :data="dpdPerformanceList"
          :columns="columns"
          row-key="dpdDate"
          :filter="filter"
          @request="getDpdPerformanceList"
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
                v-model="selectedDpdRoute"
                :options="dpdRouteNumbers"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.dpdRoute"
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
                <template v-if="selectedDpdRoute" v-slot:append>
                  <q-icon name="cancel" @click.stop="selectedDpdRoute = null" @click="onChangeRoute" class="cursor-pointer text-white" />
                </template>
              </q-select>
            </div>
            <div class="col-6 row justify-end">
              <q-select
                dense
                v-model="selectedPeriod"
                :options="periods"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.duration"
                bg-color="transparent"
                color="blue-7"
                label-color="white"
                behavior="menu"
                @input="onChangePeriod"
                class="q-select-list"
              />
            </div>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row)">
              <q-td key="index" :props="props">{{ props.row.index }}</q-td>
              <q-td key="dpdDate" :props="props" class="text-uppercase">{{ changeDateFormat(props.row.dpdDate) }}</q-td>
              <q-td key="dpdRoute" :props="props">{{ props.row.dpdRoute }}</q-td>
              <!-- <q-td key="driver_name" :props="props">{{ props.row.driver_name }}</q-td> -->
              <!-- <q-td key="dpdPayType" :props="props">{{ props.row.dpdPayType === 'fixed' ? 'FIXED' : 'PAY PER STOP' }}</q-td> -->
              <q-td key="dpdStops" :props="props">{{ props.row.dpdStops }}</q-td>
              <q-td key="dpdPayment" :props="props">{{ props.row.dpdPayment ? '£' +  props.row.dpdPayment : '' }}</q-td>
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
  name: 'DpdPerformanceList',
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
      pagination: {
        sortBy: 'dpdDate',
        descending: true,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20000
      },
      columns: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'dpdDate', required: true, label: 'DATE', align: 'left', field: 'dpdDate' },
        { name: 'dpdRoute', required: true, label: 'ROUTE', align: 'left', field: 'dpdRoute' },
        { name: 'dpdStops', required: true, label: 'STOPS', align: 'left', field: 'dpdStops' },
        { name: 'dpdPayment', required: true, label: 'PAYMENT', align: 'left', field: 'dpdPayment' }
      ],
      dpdPerformanceList: [],
      dpdPerformanceAll: [],
      is_mobile: 'web',
      isDateFilter: false,
      selectedPeriod: {},
      periods: [],
      selectedDpdRoute: null,
      dpdRouteNumbers: []
    }
  },
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    userLevel: {
      get () {
        return this.$store.state.auth.userLevel
      }
    },
    driver: {
      get () {
        return this.$store.state.auth.driver
      }
    }
  },
  async mounted () {
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    await this.getDpdRouteAll()
    await this.getPeriods();
    this.getDpdPerformanceList({
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
    
    getDpdRouteAll: async function () {
      try {
        let res = await api.getDpdRouteAll()
        this.dpdRouteNumbers = res.data.data
      } catch (e) {
      }
    },
    async getPeriods () {
      Loading.show();
      try {
        let currentDate = date.formatDate(new Date(), "YYYY-MM-DD");
        let res = await api.getPeriods(currentDate);
        Loading.hide();
        this.periods = res.data.data;
        if (this.periods.length) {
          this.selectedPeriod = this.periods.filter(period => {
            if (period.firstTransactionDate <= currentDate && period.lastTransactionDate >= currentDate) {
              return period
            }
          })[0]
        }
      } catch (error) {
        console.log("get periods error", error);
        Loading.hide();
      }
    },
    changeDateFormat (reportDate) {
      let convertedDate = date.formatDate(date.addToDate(date.extractDate(reportDate, 'YYYY-MM-DD'), { hours: 5 }), 'DD-MM-YY dddd')
      // let convertedDate = date.formatDate(date.extractDate(reportDate, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YY ddd HH:mm')
      return convertedDate
    },
    async onChangePeriod () {
      this.isDateFilter = true;
      await this.getDpdPerformanceList({
        pagination: this.pagination,
        filter: this.filter,
        isScroll: false,
      });
    },
    async goToDetail (data) {
      if (data) {
        this.$router.push({ name: 'Edit DPD Modules', params: { dpdDate: data.dpdDate } })
      }
    },

    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.dpdPerformanceList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getDpdPerformanceList({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },

    onChangeDriver () {
      this.getDpdPerformanceList({
        pagination: this.pagination,
        isScroll: false
      })
    },
    onChangeRoute () {
      this.getDpdPerformanceList({
        pagination: this.pagination,
        isScroll: false,
      });
    },
    getDpdPerformanceList: async function (props) {
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
        fromDate: "",
        endDate: "",
      }
      // if (filter) {
      //   params.conditions.filter = filter
      // }
      if (this.selectedDpdRoute) {
        params.conditions.dpdRoute = this.selectedDpdRoute.dpdRoute
      }
      if (!this.isDateFilter) {
        params.conditions.is_date_filter = false
      } else {
        params.fromDate = this.selectedPeriod.firstTransactionDate;
        params.endDate = this.selectedPeriod.lastTransactionDate;
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getDpdDriverPerformanceList(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 100 + index + 1
        })
        if (isScroll) {
          this.dpdPerformanceList = this.dpdPerformanceList.concat(res.data.data)
        } else {
          this.dpdPerformanceList = res.data.data
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

    // getDpdPerformanceAll: async function (props) {
    //   let filter = props.filter

    //   const params = {
    //     conditions: {
    //       is_date_filter: true,
    //       driver_id: this.driver.id
    //     },
    //     fromDate: "",
    //     endDate: ""
    //   }
    //   // if (filter) {
    //   //   params.conditions.filter = filter
    //   // }
    //   if (this.selectedDpdRoute) {
    //     params.conditions.dpdRoute = this.selectedDpdRoute.dpdRoute
    //   }
    //   if (!this.isDateFilter) {
    //     params.conditions.is_date_filter = false
    //   } else {
    //     params.fromDate = this.selectedPeriod.firstTransactionDate;
    //     params.endDate = this.selectedPeriod.lastTransactionDate;
    //   }

    //   // fetch vehicleList from "server"
    //   Loading.show()
    //   try {
    //     let res = await api.getDpdDriverPerformanceAll(params)
    //     Loading.hide()

    //     // clear out existing vehicleList and add new
    //     res.data.data.forEach((row, index) => {
    //       row.index = index + 1
    //     })
    //     this.dpdPerformanceAll = res.data.data

    //     // ...and turn of loading indicator
    //   } catch (e) {
    //     Loading.hide()
    //     console.log('errorrrrrrrrrr', e)
    //   }
    // },
    // async exportTable () {
    //   // get All records before export
    //   await this.getDpdPerformanceAll({ filter: this.filter })
    //   // naive encoding to csv format
    //   let columns = this.columns
    //   const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
    //     this.dpdPerformanceAll.map(row => columns.map(col => {
    //       if (col.field === 'dpdPayType') {
    //         return wrapCsvValue(
    //           row.is_group === 'fixed' ? 'FIXED' : 'PAY PER STOP',
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
    // },
    totalStops () {
      return this.dpdPerformanceList.reduce( function(a, b){
        if (b['dpdPayType'] == 'per_stop') {
          return a + Number(b['dpdStops']);
        } else {
          return a + 0;
        }
      }, 0);
    },
    totalPayment () {
      return (this.dpdPerformanceList.reduce( function(a, b){
        return a + Number(b['dpdPayment']);
      }, 0)).toFixed(2);
    }
  },
  created () {
  }
}
</script>
