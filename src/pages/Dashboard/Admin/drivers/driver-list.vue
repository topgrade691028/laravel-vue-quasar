<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          title="DRIVERS"
          :data="driverList"
          :columns="userSubcontractor !== 'DPD' ? (userLevel !== 'user' ? columns_admin : columns) : columns_dpd"
          row-key="driver_name"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getDrivers"
          binary-state-sort
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn rounded dense no-caps label="+Add Driver" color="blue-7" style="width: 130px; height:40px;" @click="createNew()" />
            </div>
          </template>
          <template v-slot:top-right>
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
            <q-tr :props="props" v-if="props.row.user" @click.native="goToDetail(props.row.id)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <!-- <q-td key="full_name" :props="props">{{ props.row.full_name }}</q-td> -->
              <q-td key="driver_name" :props="props">{{ props.row.driver_name }}</q-td>
              <q-td key="phone" :props="props">{{ props.row.phone }}</q-td>
              <q-td key="email" :props="props">{{ props.row.email }}</q-td>
              <q-td key="pay_amount" :props="props">{{ '£' + props.row.pay_amount }}</q-td>
              <q-td v-if="userSubcontractor !== 'DPD'" key="depot_location" :props="props">{{ props.row.user? props.row.user.depot_location : '' }}</q-td>
              <q-td v-if="userLevel !== 'user'" key="user_name" :props="props">{{ props.row.user?props.row.user.name:'' }}</q-td>
              <!-- <q-td key="buttons" :props="props">
                <q-btn
                  flat
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="remove(props.row)"
                />
              </q-td> -->
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-end items-center">
              Total Records: {{pagination.rowsNumber}}
            </div>
          </template>
        </q-table>
      </div>
      <!-- <q-dialog
        v-model="showDetail"
        persistent
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card style="background-color: #3E444E; max-width: 500px; min-height: 500px">
          <q-bar style="background-color: #272B33">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
            </q-btn>
            <div class="text-h6 text-white">{{dialogTitle}}</div>
          </q-bar>
          <q-separator />

          <q-card-section style="max-height: 50vh" class="scroll">
            <q-form
              @submit="onSubmit"
              ref="selectedName"
              :model="selectedName"
              style="width: 320px; margin: auto;"
            >
              <div class="row justify-between q-col-gutter-md" >
                <div class="col-12">
                  <span class="text-white">Driver</span>
                  <q-input dense outlined required color="blue-7" bg-color="white" input-class="text-black text-center" v-model="selectedName.driver_name"></q-input>
                </div>
              </div>
              <q-separator />
              <q-card-actions align="center">
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
      </q-dialog> -->
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
import { Loading } from 'quasar'

export default {
  name: 'DriverList',
  data () {
    return {
      filter: '',
      // showDetail: false,
      pagination: {
        sortBy: 'driver_name',
        descending: false,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20
      },
      columns: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'driver_name', required: true, label: 'FULL NAME', align: 'left', field: 'driver_name' },
        { name: 'phone', required: true, label: 'MOBILE', align: 'left', field: 'phone' },
        { name: 'email', required: true, label: 'E-MAIL', align: 'left', field: 'email' },
        { name: 'pay_amount', required: true, label: 'PAYMENT', align: 'left', field: 'pay_amount' },
        { name: 'depot_location', required: true, label: 'DEPOT LOCATION', align: 'left', field: 'depot_location' }
      ],
      columns_dpd: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'driver_name', required: true, label: 'FULL NAME', align: 'left', field: 'driver_name' },
        { name: 'phone', required: true, label: 'MOBILE', align: 'left', field: 'phone' },
        { name: 'email', required: true, label: 'E-MAIL', align: 'left', field: 'email' },
        { name: 'pay_amount', required: true, label: 'PAYMENT', align: 'left', field: 'pay_amount' }
      ],
      columns_admin: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'driver_name', required: true, label: 'FULL NAME', align: 'left', field: 'driver_name' },
        { name: 'phone', required: true, label: 'MOBILE', align: 'left', field: 'phone' },
        { name: 'email', required: true, label: 'E-MAIL', align: 'left', field: 'email' },
        { name: 'pay_amount', required: true, label: 'PAYMENT', align: 'left', field: 'pay_amount' },
        { name: 'depot_location', required: true, label: 'DEPOT LOCATION', align: 'left', field: 'depot_location' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' }
      ],
      driverList: [],
      driverNames: [],
      // selectedName: {
      //   driver_name: ''
      // },
      selectedDriver: null,
      dialogTitle: '',
      is_mobile: 'web',
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
    userSubcontractor: {
      get () {
        return this.$store.state.auth.userSubcontractor
      }
    }
  },
  async mounted () {
    this.checkPlatform()
    // get initial vehicleList from server (1st page)
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    
    await this.getDriverAll()
    this.getDrivers({
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
      this.$router.push({ name: 'New Driver' })
    },
    goToDetail (id) {
      this.$router.push({ name: 'Driver Details', params: { id: id } })
    },
    getDriverAll: async function () {
      try {
        let res = await api.getDriverList()
        this.driverNames = res.data.data
      } catch (e) {
      }
    },
    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.driverList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getDrivers({
          pagination: this.pagination,
          isScroll: true
        })
      }
    },
    onChangeDriver () {
      this.getDrivers({
        pagination: this.pagination,
        isScroll: false
      })
    },
    getDrivers: async function (props) {
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
        descending: descending
      }
      if (this.selectedDriver) {
        params.conditions.driver_name = this.selectedDriver.driver_name
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getDrivers(params)
        Loading.hide()

        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1
        })
        // clear out existing vehicleList and add new
        if (isScroll) {
          this.driverList = this.driverList.concat(res.data.data)
        } else {
          this.driverList = res.data.data
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
    // cancelDetail () {
    //   this.showDetail = false
    //   this.selectedName = {}
    // },
    // async onSubmit () {
    //   const params = {
    //     data: this.selectedName
    //   }
    //   if (this.selectedName.id) {
    //     params.conditions = {
    //       id: this.selectedName.id
    //     }
    //     Loading.show()
    //     try {
    //       let res = await api.updateDriver(params)
    //       Loading.hide()
    //       console.log('result', res.data)
    //     } catch (error) {
    //       Loading.hide()
    //       console.log('error', error)
    //     }
    //     this.cancelDetail()
    //   } else {
    //     Loading.show()
    //     try {
    //       let res = await api.createDriver(params)
    //       Loading.hide()
    //       console.log('result', res.data)
    //     } catch (error) {
    //       Loading.hide()
    //       console.log('error', error)
    //     }
    //     this.cancelDetail()
    //   }
    //   this.getDrivers({
    //     pagination: this.pagination,
    //     filter: undefined
    //   })
    // },
    // remove () {
    //   // Confirm Remove Vehicle
    //   this.$q.dialog({
    //     title: 'Confirm',
    //     message: 'Are you surely remove ' + this.selectedName.driver_name + '?',
    //     cancel: true,
    //     persistent: true,
    //     color: 'blue-7'
    //   }).onOk(async () => {
    //     const params = {
    //       conditions: {
    //         id: this.selectedName.id
    //       }
    //     }
    //     Loading.show()
    //     try {
    //       let res = await api.removeDriver(params)
    //       Loading.hide()
    //       console.log('remove result', res)
    //       this.$q.notify({
    //         color: 'positive',
    //         position: 'top',
    //         message: this.selectedName.driver_name + ' is removed successfully !'
    //       })
    //       this.cancelDetail()
    //       this.getDrivers({
    //         pagination: this.pagination,
    //         filter: undefined
    //       })
    //     } catch (e) {
    //       Loading.hide()
    //     }
    //   }).onCancel(() => {
    //   }).onDismiss(() => {
    //   })
    // }
  },
  created () {
  }
}
</script>
