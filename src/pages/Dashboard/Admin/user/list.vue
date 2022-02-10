<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="is_mobile === 'ios'?'my-sticky-dynamic table-top-ios':is_mobile==='android'?'my-sticky-dynamic table-top-android': 'my-sticky-dynamic'"
          title="Users"
          :data="userList"
          :columns="columns"
          row-key="name"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getUsers"
          binary-state-sort
        >
          <!-- <template v-slot:top-right>
            <q-input dense debounce="300" v-model="filter" placeholder="Search">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
            <div class="items-center q-ml-xl">
              <q-btn class="bg-white text-primary shadow-3 q-btn--push" @click="createNew()">
                New User
              </q-btn>
            </div>
          </template> -->
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn rounded dense no-caps label="+Add User" color="blue-7" style="width: 130px; height:40px;" @click="createNew()" />
            </div>
          </template>
          <template v-slot:top-right>
            <q-input dense debounce="300" v-model="filter" placeholder="Search" input-class="text-white border-white" style="width: 130px;" color="blue-7">
              <template v-slot:append>
                <q-icon name="search" color="white" />
              </template>
            </q-input>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row.id)">
              <!-- <q-td key="full_name" :props="props">{{ props.row.full_name }}</q-td> -->
              <q-td key="name" :props="props">{{ props.row.name }}</q-td>
              <q-td key="email" :props="props">{{ props.row.email }}</q-td>
              <q-td key="phone" :props="props">{{ props.row.phone }}</q-td>
              <q-td key="zipcode" :props="props">{{ props.row.zipcode }}</q-td>
              <q-td key="depot_location" :props="props">{{ props.row.depot_location }}</q-td>
              <q-td key="active_status" :props="props">{{ props.row.is_active?'ACTIVATED':'DEACTIVATED' }}</q-td>
              <q-td key="buttons" :props="props">
                <q-btn
                  flat
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="remove(props.row)"
                />
              </q-td>
            </q-tr>
          </template>
          <template v-slot:bottom="props">
            <div class="col-12 row justify-end items-center">
              Total Records: {{props.pagination.rowsNumber}}
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
</style>

<script>

import { api } from 'src/boot/api'
import { Loading } from 'quasar'

export default {
  name: 'Users',
  data () {
    return {
      filter: '',
      showDetail: false,
      pagination: {
        sortBy: 'name',
        descending: false,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 20
      },
      columns: [
        // { name: 'full_name', required: true, label: 'Full Name', align: 'left', field: 'full_name', sortable: false },
        { name: 'name', required: true, label: 'Full Name', align: 'left', field: 'name', sortable: false },
        { name: 'email', required: true, label: 'Email', align: 'center', field: 'email', sortable: false },
        { name: 'phone', required: true, label: 'Phone', align: 'center', field: 'phone', sortable: false },
        { name: 'zipcode', required: true, label: 'Zipcode', align: 'center', field: 'zipcode', sortable: false },
        { name: 'depot_location', required: true, label: 'Depot Location', align: 'center', field: 'depot_location', sortable: false },
        { name: 'active_status', required: true, label: 'Active Status', align: 'center', field: 'active_status', sortable: false },
        { name: 'buttons', label: '', field: 'buttons' }
      ],
      userList: [],
      is_mobile: 'web'
    }
  },
  mounted () {
    // get initial vehicleList from server (1st page)
    this.checkPlatform()
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.getUsers({
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
      this.$router.push({ name: 'New User' })
    },
    goToDetail (id) {
      this.$router.push({ name: 'User Detail', params: { id: id } })
    },
    getUsers: async function (props) {
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
        descending: descending
      }
      if (filter) {
        params.conditions.filter = filter
      }

      // fetch vehicleList from "server"
      Loading.show()
      try {
        let res = await api.getUsers(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        this.userList = res.data.data

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
    remove (user) {
      // Confirm Remove Vehicle
      this.$q.dialog({
        title: 'Confirm',
        message: 'Are you surely remove ' + user.name + '?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        const params = {
          conditions: {
            id: user.id
          }
        }
        Loading.show()
        try {
          let res = await api.removeUser(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: user.name + ' is removed successfully !'
          })
          this.getUsers({
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
