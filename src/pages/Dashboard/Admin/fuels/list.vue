<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          title="FUEL"
          :data="fuelList"
          :columns="userLevel !== 'user' ? columns_admin : columns"
          row-key="card_no"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getFuels"
          binary-state-sort
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn rounded dense no-caps label="+Add Fuel" color="blue-7" style="width: 130px; height:40px;" @click="createNew()" />
            </div>
          </template>
          <template v-slot:top-right>
            <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white border-white" style="width: 130px;" color="blue-7" class="q-mb-sm">
              <template v-slot:append>
                <q-icon name="search" color="white" />
              </template>
            </q-input>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row.id)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <!-- <q-td key="full_name" :props="props">{{ props.row.full_name }}</q-td> -->
              <q-td key="depot_location" :props="props">{{ props.row.user.depot_location }}</q-td>
              <q-td key="driver_name" :props="props">{{ props.row.driver?props.row.driver.driver_name:'' }}</q-td>
              <q-td key="card_no" :props="props">{{ props.row.card_no }}</q-td>
              <q-td key="company" :props="props">{{ props.row.company }}</q-td>
              <q-td v-if="userLevel !== 'user'" key="user_name" :props="props">{{ props.row.user.name }}</q-td>
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
import { Loading } from 'quasar'

export default {
  name: 'FuelList',
  data () {
    return {
      filter: '',
      pagination: {
        sortBy: 'card_no',
        descending: false,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20
      },
      columns: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'depot_location', required: true, label: 'DEPOT LOCATION', align: 'left', field: 'depot_location' },
        { name: 'driver_name', required: true, label: 'DRIVER NAME', align: 'left', field: 'driver_name' },
        { name: 'card_no', required: true, label: 'CARD NO', align: 'left', field: 'card_no' },
        { name: 'company', required: true, label: 'COMPANY', align: 'left', field: 'company' },
      ],
      columns_admin: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'depot_location', required: true, label: 'DEPOT LOCATION', align: 'left', field: 'depot_location' },
        { name: 'driver_name', required: true, label: 'DRIVER NAME', align: 'left', field: 'driver_name' },
        { name: 'card_no', required: true, label: 'CARD NO', align: 'left', field: 'card_no' },
        { name: 'company', required: true, label: 'COMPANY', align: 'left', field: 'company' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' }
      ],
      fuelList: [],
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
    }
  },
  mounted () {
    this.checkPlatform()
    // get initial vehicleList from server (1st page)
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.getFuels({
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
      this.$router.push({ name: 'New Fuel' })
    },
    goToDetail (id) {
      this.$router.push({ name: 'Fuel Details', params: { id: id } })
    },
    // goToDetail (data) {
    //   if (data) {
    //     this.isNewRecord = false
    //     this.dialogTitle = 'Edit Name'
    //     this.selectedName.id = data.id
    //     this.selectedName.driver_name = data.driver_name
    //   } else {
    //     this.isNewRecord = true
    //     this.dialogTitle = 'Add New Name'
    //     this.selectedName = {
    //       driver_name: ''
    //     }
    //   }
    //   // this.$router.push({ name: 'BuyerDetail', params: { id: id } })
    //   this.showDetail = true
    // },
    async onScroll ({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination
      const lastIndex = this.fuelList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getFuels({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },
    getFuels: async function (props) {
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
      if (filter) {
        params.conditions.filter = filter
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getFuels(params)
        Loading.hide()

        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1
        })
        // clear out existing vehicleList and add new
        if (isScroll) {
          this.fuelList = this.fuelList.concat(res.data.data)
        } else {
          this.fuelList = res.data.data
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
  },
  created () {
  }
}
</script>
