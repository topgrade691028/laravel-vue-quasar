<template>
  <q-page padding>
    <template>
      <div class="q-pa-md">
        <q-table
          title="Buyers"
          :data="buyerList"
          :columns="columns"
          row-key="name"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getBuyerList"
          binary-state-sort
        >
          <template v-slot:top-right>
            <q-input dense debounce="300" v-model="filter" placeholder="Search">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
            <div class="items-center q-ml-xl">
              <q-btn class="bg-white text-primary shadow-3 q-btn--push" @click="goToDetail()">
                New Buyer
              </q-btn>
            </div>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row.id)">
              <q-td key="name" :props="props">{{ props.row.name }}</q-td>
              <q-td key="sex" :props="props">{{ props.row.sex }}</q-td>
              <q-td key="age" :props="props">{{ props.row.age }}</q-td>
              <q-td key="buttons" :props="props">
                <q-btn
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="remove(props.row)"
                />
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </div>
      <q-dialog
        v-model="showDetail"
        persistent
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card>
          <q-card-section>
            <div class="text-h6">{{dialogTitle}}</div>
          </q-card-section>

          <q-separator />

          <q-card-section style="max-height: 50vh" class="scroll">
            <q-form
              @submit="onSubmit"
              ref="selectedBuyer"
              :model="selectedBuyer"
            >
              <div class="row justify-between q-col-gutter-md" >
                <div class="col-12">
                  <q-input outlined required label="Name" color="cyan-7" v-model="selectedBuyer.name"></q-input>
                </div>
                <div class="col-12">
                  <q-select required outlined v-model="selectedBuyer.sex"
                            :options="sex"
                            :option-value="opt => opt === null ? null : opt.value"
                            :option-label="opt => opt === null ? '- Null -' : opt.label"
                            emit-value
                            map-options
                            options-cover
                            transition-show="flip-up"
                            transition-hide="flip-down"
                            ref="sex" label="Sex"
                  />
                </div>
                <div class="col-12">
                  <q-input
                    outlined
                    required
                    label="Age"
                    color="cyan-7"
                    type="number"
                    v-model="selectedBuyer.age"
                    :rules="[val => val < 100 || 'Please input correct age']"
                  ></q-input>
                </div>
              </div>
              <q-separator />
              <q-card-actions align="right">
                <q-btn flat label="Cancel" color="primary" @click="cancelDetail"/>
                <q-btn flat label="Save" color="primary"  type="submit" />
              </q-card-actions>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </template>
  </q-page>
</template>

<script>

import { api } from 'src/boot/api'
import { Loading } from 'quasar'

export default {
  name: 'BuyerList',
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
        { name: 'name', required: true, label: 'Name', align: 'left', field: 'name', sortable: true },
        { name: 'sex', required: true, label: 'Sex', align: 'center', field: 'sex', sortable: true },
        { name: 'age', required: true, label: 'Age', align: 'center', field: 'age', sortable: true },
        { name: 'buttons', label: '', field: 'buttons' }
      ],
      buyerList: [],
      sex: [
        { label: 'Male', value: 'male' },
        { label: 'Female', value: 'female' }
      ],
      selectedBuyer: {
        name: '',
        sex: '',
        age: ''
      },
      dialogTitle: ''
    }
  },
  mounted () {
    // get initial vehicleList from server (1st page)
    this.getBuyerList({
      pagination: this.pagination,
      filter: undefined
    })
  },
  methods: {
    createNew () {
      this.$router.push({ name: 'NewBuyer' })
    },
    goToDetail (id) {
      if (id) {
        this.dialogTitle = 'Buyer Detail'
        this.selectedBuyer.id = id
        this.getBuyerInfo(id)
      } else {
        this.dialogTitle = 'Create New Buyer'
        this.selectedBuyer = {
          name: '',
          sex: '',
          age: ''
        }
      }
      // this.$router.push({ name: 'BuyerDetail', params: { id: id } })
      this.showDetail = true
    },
    getBuyerList: async function (props) {
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
        let res = await api.getBuyers(params)
        Loading.hide()

        // clear out existing vehicleList and add new
        this.buyerList = res.data.data

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
    async getBuyerInfo (id) {
      Loading.show()
      try {
        let res = await api.getBuyerInfo(id)
        Loading.hide()
        this.selectedBuyer = res.data.data
      } catch (error) {
        Loading.hide()
        console.log('error', error)
      }
    },
    cancelDetail () {
      this.showDetail = false
      this.selectedBuyer = {}
    },
    async onSubmit () {
      const params = {
        data: this.selectedBuyer
      }
      if (this.selectedBuyer.id) {
        params.conditions = {
          id: this.selectedBuyer.id
        }
        Loading.show()
        try {
          let res = await api.updateBuyer(params)
          Loading.hide()
          console.log('result', res.data)
        } catch (error) {
          Loading.hide()
          console.log('error', error)
        }
        this.cancelDetail()
      } else {
        Loading.show()
        try {
          let res = await api.createBuyer(params)
          Loading.hide()
          console.log('result', res.data)
        } catch (error) {
          Loading.hide()
          console.log('error', error)
        }
        this.cancelDetail()
      }
      this.getBuyerList({
        pagination: this.pagination,
        filter: undefined
      })
    },
    remove (buyer) {
      // Confirm Remove Vehicle
      this.$q.dialog({
        title: 'Confirm',
        message: 'Are you surely remove ' + buyer.name + '?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        const params = {
          conditions: {
            id: buyer.id
          }
        }
        Loading.show()
        try {
          let res = await api.removeBuyer(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: buyer.name + ' is removed successfully !'
          })
          this.getBuyerList({
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
