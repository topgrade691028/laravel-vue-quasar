<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          title="MILAGE"
          :data="driverMileageList"
          :columns="userLevel === 'admin' ? columns_admin : columns"
          row-key="driver_name"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getMileageRecords"
          binary-state-sort
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn v-if="userLevel == 'driver'" rounded dense no-caps label="+Add Record" color="blue-7" style="width: 130px; height:40px;" @click="createNew()" />
              <q-select
                v-if="userLevel !== 'driver'"
                dense
                v-model="selectedVan"
                :options="vanNumbers"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.van_number"
                bg-color="transparent"
                color="blue-7"
                label="REG NO"
                label-color="white"
                behavior="menu"
                @input="onChangeVan"
                style="min-width: 130px"
                class="q-select-list"
              >
                <template v-if="selectedVan" v-slot:append>
                  <q-icon name="cancel" @click.stop="selectedVan = null" @click="onChangeVan" class="cursor-pointer text-white" />
                </template>
              </q-select>
            </div>
          </template>
          <template v-slot:top-right>
            <q-select
              v-if="userLevel !== 'driver'"
              dense
              v-model="selectedDriver"
              :options="drivers"
              :option-value="opt => opt == null ? null : opt"
              :option-label="opt => opt == null ? '- Null -' : opt.driver_name"
              bg-color="transparent"
              color="blue-7"
              label="DRIVER"
              label-color="white"
              behavior="menu"
              @input="onChangeDriver"
              style="min-width: 130px"
              class="q-select-list"
            >
              <template v-if="selectedDriver" v-slot:append>
                <q-icon name="cancel" @click.stop="selectedDriver = null" @click="onChangeDriver" class="cursor-pointer text-white" />
              </template>
            </q-select>
            <!-- <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white border-white" style="width: 130px;" color="blue-7" class="q-mb-sm">
              <template v-slot:append>
                <q-icon name="search" color="white" />
              </template>
            </q-input> -->
          </template>

          <template v-slot:body="props">
            <!-- <q-tr :props="props" @click.native="goToDetail(props.row)"> -->
            <q-tr :props="props" @click.native="remove(props.row)">
              <q-td key="index" :props="props">{{ props.row.index }}</q-td>
              <q-td key="created_at" :props="props">{{ changeDateFormat(props.row.created_at) }}</q-td>
              <q-td key="service_mileage" :props="props">{{ props.row.service_mileage }}</q-td>
              <q-td key="van_number" :props="props">{{ props.row.van_number }}</q-td>
              <q-td key="driver_name" :props="props">{{ props.row.driver_name }}</q-td>
              <q-td key="tyres" :props="props">{{ props.row.tyres ? 'YES': 'NO' }}</q-td>
              <q-td key="oil" :props="props">{{ props.row.oil ? 'YES': 'NO' }}</q-td>
              <q-td v-if="userLevel === 'admin'" key="user_name" :props="props">{{ props.row.user.name }}</q-td>
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-between items-center">
              <div class="q-my-auto">
                <q-btn
                  v-if="userLevel !== 'driver'"
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
              </div>
            </div>
          </template>
        </q-table>
      </div>
    </template>
  </q-page>
</template>

<style lang="stylus">
.my-sticky-dynamic
  /* height or max-height is important */
  height: calc(100vh - 62px)

  .q-table__top,
  .q-table__bottom
    background-color: #3E444E
    color: white
    border-radius: 0px !important

  thead tr:first-child th /* bg color is important for th; just specify one */
    background-color: #272B33
    color: white

  thead tr th
    position: sticky
    z-index: 1
  /* this will be the loading indicator */
  thead tr:last-child th
    /* height of all previous header rows */
    top: 48px
  thead tr:first-child th
    top: 0

.table-top-mobile
  height: calc(100vh - 170px) !important
  .q-table__top
    height: 104px !important
    padding: 0px 16px
</style>

<script>

import { api } from 'src/boot/api'
import { Loading, date,exportFile } from 'quasar'

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
  name: 'MileageRecordList',
  data () {
    return {
      filter: '',
      pagination: {
        sortBy: 'driver_name',
        descending: false,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20
      },
      columns: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'created_at', required: true, label: 'DATE', align: 'left', field: 'created_at' },
        { name: 'service_mileage', required: true, label: 'MILEAGE', align: 'left', field: 'service_mileage' },
        { name: 'van_number', required: true, label: 'REG NO', align: 'left', field: 'van_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'oil', required: true, label: 'OIL', align: 'left', field: 'oil' },
        { name: 'tyres', required: true, label: 'TYRES', align: 'left', field: 'tyres' }
      ],
      columns_admin: [
        { name: 'index', required: true, label: 'NO', align: 'left', field: 'index' },
        { name: 'created_at', required: true, label: 'DATE', align: 'left', field: 'created_at' },
        { name: 'service_mileage', required: true, label: 'MILEAGE', align: 'left', field: 'service_mileage' },
        { name: 'van_number', required: true, label: 'REG NO', align: 'left', field: 'van_number' },
        { name: 'driver_name', required: true, label: 'DRIVER', align: 'left', field: 'driver_name' },
        { name: 'oil', required: true, label: 'OIL', align: 'left', field: 'oil' },
        { name: 'tyres', required: true, label: 'TYRES', align: 'left', field: 'tyres' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' }
      ],
      driverMileageList: [],
      mileageAll: [],
      is_mobile: 'web',
      selectedVan: null,
      vanNumbers: [],
      selectedDriver: null,
      drivers: [],
      isNewRecord: true
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
    // get initial vehicleList from server (1st page)
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    if (this.userLevel != "driver") {
      await this.getVanNumbers()
      await this.getDriverAll()
    }
    await this.getMileageRecords({
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
    createNew () {
      this.$router.push({ name: 'New Odometer Record' })
    },
    changeDateFormat(datetime) {
      datetime = datetime.slice(0, -8);
      return date.formatDate(date.extractDate(datetime, 'YYYY-MM-DDTHH:mm:ss'), 'DD/MM/YY - HH:mm')
    },
    getVanNumbers: async function () {
      try {
        let res = await api.getFleetAll()
        this.vanNumbers = res.data.data
      } catch (e) {
      }
    },
    getDriverAll: async function () {
      try {
        let res = await api.getMileageDrivers()
        this.drivers = res.data.data
      } catch (e) {
      }
    },
    onChangeVan() {
      this.getMileageRecords({
        pagination: this.pagination,
        isScroll: false
      })
    },
    onChangeDriver () {
      this.getMileageRecords({
        pagination: this.pagination,
        isScroll: false
      })
    },
    goToDetail (data) {
      if (!data.is_first && this.userLevel === 'driver') {
        this.$router.push({ name: 'Odometer Record Details', params: { id: data.id } })
      }
    },
    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.driverMileageList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getMileageRecords({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },
    getMileageRecords: async function (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter
      let isScroll = props.isScroll

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
        showAll: true
      }
      if (this.selectedVan) {
        params.conditions.van_number = this.selectedVan.van_number
      }
      if (this.selectedDriver) {
        params.conditions.driver_name = this.selectedDriver.driver_name
      }
      if (this.userLevel === 'driver') {
        params.driver_id = this.driver.id
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getMileageRecords(params)
        Loading.hide()

        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1
        })
        // clear out existing vehicleList and add new
        if (isScroll) {
          this.driverMileageList = this.driverMileageList.concat(res.data.data)
        } else {
          this.driverMileageList = res.data.data
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
    },
    remove(data) {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove this odometer record?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: data.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeMileageRecord(params);
            this.getMileageRecords({
              pagination: this.pagination,
              filter: this.filter,
              isScroll: false
            });
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: "Odometer Record is removed successfully !",
            });
          } catch (e) {
            Loading.hide();
          }
          // this.$router.push("/dashboard/odometer-records");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    },
    async exportTable () {
      // get All records before export
      await this.getMileageAll({ filter: this.filter })
      // naive encoding to csv format
      let columns = this.columns
      const content = [ columns.map(col => wrapCsvValue(col.label)) ].concat(
        this.mileageAll.map(row => columns.map(col => {
          if (col.field === 'tyres') {
            return wrapCsvValue(
              row.tyres === '1' ? 'YES' : 'NO',
              col.format
            )
          } else if (col.field === 'oil') {
            return wrapCsvValue(
              row.oil === '1' ? 'YES' : 'NO',
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
    },
    getMileageAll: async function (props) {
      let filter = props.filter
      const params = {
        conditions: {}
      }
      if (filter) {
        params.conditions.filter = filter
      }
      if (this.userLevel === 'driver') {
        params.driver_id = this.driver.id
      }

      Loading.show()
      try {
        let res = await api.getMileageAll(params)
        Loading.hide()

        res.data.data.forEach((row, index) => {
          row.index = index + 1
        })
        this.mileageAll = res.data.data
      } catch (e) {
        Loading.hide()
      }
    },
  },
  created () {
  }
}
</script>
