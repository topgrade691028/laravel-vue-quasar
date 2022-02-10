<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          title="SERVICE"
          :data="serviceList"
          :columns="userLevel === 'admin' ? columns_admin : columns"
          row-key="van_number"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getServices"
          binary-state-sort
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn rounded dense no-caps label="+Add Service" color="blue-7" style="width: 130px; height:40px;" @click="createNew()" />
            </div>
          </template>
          <template v-slot:top-right>
            <q-select
              dense
              v-model="selectedVan"
              :options="vanNumbers"
              :option-value="opt => opt == null ? null : opt"
              :option-label="opt => opt == null ? '- Null -' : opt.van_number"
              bg-color="transparent"
              color="blue-7"
              label="REG NO"
              label-color="white"
              text-color="white"
              behavior="menu"
              @input="onChangeVan"
              style="min-width: 130px"
              class="q-ml-xs q-select-list"
            >
              <template v-if="selectedVan" v-slot:append>
                <q-icon name="cancel" @click.stop="selectedVan = null" @click="onChangeVan" class="cursor-pointer text-white" />
              </template>
            </q-select>
            <!-- <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white border-white" style="width: 130px;" color="blue-7" class="q-mb-sm">
              <template v-slot:append>
                <q-icon name="search" color="white" />
              </template>
            </q-input> -->
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row.id)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <q-td key="left_mileage" :props="props" :class="props.row.left_mileage < 2000 ? 'text-red':''">{{ props.row.left_mileage }}</q-td>
              <q-td key="van_number" :props="props">{{ props.row.van_number }}</q-td>
              <q-td key="service_date" :props="props">{{ props.row.service_date }}</q-td>
              <q-td v-if="userLevel === 'admin'" key="user_name" :props="props">{{ props.row.user.name }}</q-td>
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
  name: 'DriverServiceList',
  data () {
    return {
      filter: '',
      pagination: {
        sortBy: 'van_number',
        descending: false,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20
      },
      columns: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'left_mileage', required: true, label: 'MILEAGE LEFT TO SERVICE', align: 'left', field: 'left_mileage' },
        { name: 'van_number', required: true, label: 'REG NO', align: 'left', field: 'van_number' },
        { name: 'service_date', required: true, label: 'SERVICE DATE', align: 'left', field: 'service_date' }
      ],
      columns_admin: [
        { name: 'no', required: true, label: 'NO', align: 'left', field: 'no' },
        { name: 'left_mileage', required: true, label: 'MILEAGE LEFT TO SERVICE', align: 'left', field: 'left_mileage' },
        { name: 'van_number', required: true, label: 'REG NO', align: 'left', field: 'van_number' },
        { name: 'service_date', required: true, label: 'SERVICE DATE', align: 'left', field: 'service_date' },
        { name: 'user_name', required: true, label: 'USER', align: 'left', field: 'user_name' }
      ],
      serviceList: [],
      selectedVan: null,
      vanNumbers: [],
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
  async mounted () {
    this.checkPlatform()
    // get initial vehicleList from server (1st page)
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    await this.getVanNumbers()
    this.getServices({
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
      this.$router.push({ name: 'New Driver Service' })
    },
    goToDetail (id) {
      this.$router.push({ name: 'Driver Service Details', params: { id: id } })
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
      const lastIndex = this.serviceList.length - 1
      const lastPage = Math.ceil(rowsNumber / rowsPerPage)
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++
        await this.getServices({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true
        })
      }
    },
    getVanNumbers: async function () {
      try {
        let res = await api.getFleetAll()
        this.vanNumbers = res.data.data
      } catch (e) {
      }
    },
    onChangeVan () {
      this.getServices({
        pagination: this.pagination,
        isScroll: false,
      });
    },
    getServices: async function (props) {
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
      if (this.selectedVan) {
        params.conditions.van_number = this.selectedVan.van_number
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getServices(params)
        Loading.hide()

        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1
        })
        // clear out existing vehicleList and add new
        if (isScroll) {
          this.serviceList = this.serviceList.concat(res.data.data)
        } else {
          this.serviceList = res.data.data
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
    // remove (data) {
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
    //       let res = await api.removeFleet(params)
    //       Loading.hide()
    //       console.log('remove result', res)
    //       this.$q.notify({
    //         color: 'positive',
    //         position: 'top',
    //         message: this.selectedName.driver_name + ' is removed successfully !'
    //       })
    //       this.getFleets({
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
