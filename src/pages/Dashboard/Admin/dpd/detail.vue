<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/dpd-modules')">
            </q-btn>
            <div class="text-h6 text-white">Edit DPD Modules</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="dpdReportData"
            ref="dpdReportData"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section
                class="text-left q-py-none"
              >
                <span class="text-white">Date</span>
                <q-input dense outlined disable :value="changeDateFormat(dpdDate)" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black" />
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <div v-for="(data, index) in dpdReportData" :key="data.dpdRoute" class="q-pt-md">
                  <div class="row">
                    <div class="col-6 q-pr-sm">
                      <span class="text-white">Driver</span>
                      <q-select
                        dense
                        outlined
                        v-model="data.driver"
                        use-input
                        hide-selected
                        fill-input
                        :options="filteredDriverNames"
                        :option-value="opt => opt === null ? null : opt"
                        :option-label="opt => opt === null ? '- Null -' : opt.driver_name"
                        @filter="filterFnDrivers"
                        label-color="grey-7"
                        class="q-ma-none q-pb-md"
                        behavior="menu"
                        bg-color="white"
                        input-class="text-black"
                        :rules="[ val => checkDriverDuplicates(val) || 'This record is duplicated' ]"
                        :hide-dropdown-icon="true"
                        color="blue-7"
                        @input="addOption"
                      >
                        <template v-slot:append>
                          <q-icon
                            v-if="data.driver_id !== ''"
                            class="cursor-pointer"
                            name="clear"
                            @click="removeSelectedDriver(index)"
                          />
                        </template>
                      </q-select>
                    </div>
                    <div class="col-6 q-pl-sm">
                      <span class="text-white">Route</span>
                      <q-input required dense outlined bg-color="blue-grey-4" class="q-pb-md" input-class="text-white q-pr-xl" label-color="grey-3" color="blue-7" :value="data.dpdRoute" disable></q-input>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6 q-pr-sm">
                      <span class="text-white">Stops</span>
                      <q-input required dense outlined bg-color="blue-grey-4" class="q-pb-md" input-class="text-white q-pr-xl" label-color="grey-3" color="blue-7" :value="data.dpdStops" @input="calcPayment(data)"></q-input>
                    </div>
                    <div class="col-6 q-pl-sm">
                      <span class="text-white">Payment</span>
                      <q-input dense outlined bg-color="white" class="q-pb-md" input-class="text-black q-pr-xl" label-color="grey-3" color="blue-7" v-model="data.dpdPayment"></q-input>
                    </div>
                  </div>
                  <q-separator color="grey-4" v-if="index < dpdReportData.length-1" />
                </div>
              </q-card-section>
              <q-card-actions align="center">
                <div class="q-px-md">
                  <q-btn
                    no-caps
                    dense
                    rounded
                    label="Delete"
                    color="blue-7"
                    @click="removeDpd(dpdDate)"
                    style="width: 100px; height:40px;"
                  /> &nbsp;
                  <q-btn
                    color="blue-7"
                    label="Update"
                    no-caps
                    dense
                    rounded
                    style="width: 100px; height:40px"
                    type="submit"
                  />
                </div>
              </q-card-actions>
            </q-card>
          </q-form>
          <q-dialog
            v-model="showEmptyConfirm"
            persistent
            transition-show="scale"
            transition-hide="scale"
          >
            <q-card style="background-color: #3E444E; max-width: 500px; min-width: 370px; min-height: 90vh;">
              <q-bar style="background-color: #272B33">
                <q-btn dense flat icon="close" color="white" v-close-popup>
                  <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
                </q-btn>
              </q-bar>

              <div class="layout-center">
                <q-card-section class="text-white">
                  <div class="text-h6 text-center">You did not provide the driver name of the routes {{checkEmptyDrivers().join(', ')}}.</div>
                  <div class="text-h6 text-center">Do you want to continue?</div>
                </q-card-section>

                <q-card-section class="text-center q-pt-none">
                  <q-btn
                    color="blue-7"
                    label="Back"
                    no-caps
                    dense
                    rounded
                    @click="showEmptyConfirm = false"
                    class="q-mt-xs q-mx-xs"
                    style="width: 100px; height:40px"
                  />
                  <q-btn
                    color="blue-7"
                    label="Yes"
                    no-caps
                    dense
                    rounded
                    @click="addDpdRecords"
                    class="q-mt-xs q-mx-xs"
                    style="width: 100px; height:40px"
                  />
                </q-card-section>
              </div>
            </q-card>
          </q-dialog>
        </div>
      </div>
    </template>
  </q-page>
</template>

<script>
import { Loading, date } from "quasar";
import { api } from "src/boot/api";
export default {
  name: "NewDaily",
  data() {
    return {
      drivers: [],
      dpdDate: '',
      dpdReportData: [],
      filteredDriverNames: [],
      is_mobile: 'web',
      showEmptyConfirm: false,
      dpdCreatedAt: '',
      moneyFormatForDirective: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        suffix: ' #',
        precision: 2,
        masked: false /* doesn't work with directive */
      }
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.dpdDate = this.$router.currentRoute.params.dpdDate;
    await this.getDPDInfo(this.dpdDate);
    await this.getRNCID();
    await this.getDriverList(this.dpdCreatedAt);
  },
  computed: {
    user: {
      get() {
        return this.$store.state.auth.user;
      },
    },
    userLevel: {
      get() {
        return this.$store.state.auth.userLevel;
      },
    }
  },
  methods: {
    changeDateFormat (dpdDate) {
      return date.formatDate(date.extractDate(dpdDate, 'YYYY-MM-DD'), 'DD/MM/YY ddd');
    },
    getRNCID: async function () {
      try {
        let res = await api.getRNCID()
        this.rnc_id = res.data.data
      } catch (e) {
      }
    },
    getDriverList: async function (created_at) {
      try {
        let res = await api.getDriverList(created_at)
        this.drivers = res.data.data
        this.filteredDriverNames = this.drivers
      } catch (e) {
      }
    },
    getDPDInfo: async function (dpdDate) {
      Loading.show()
      try {
        let res = await api.getDPDInfo(dpdDate)
        Loading.hide()
        // this.dpdReportData = res.data.data
        this.dpdReportData = res.data.data.map(data => {
          return {
            id: data.id,
            driver: data.driver?data.driver: {},
            driver_id: data.driver_id?data.driver_id: '',
            dpdRoute: data.dpdRoute,
            dpdStops: data.dpdStops,
            dpdPayment: data.dpdPayment?data.dpdPayment: '',
            dpdPayType: data.dpdPayType,
            dpdDate: data.dpdDate
          }
        })
        this.dpdCreatedAt = res.data.data[0].created_at ? res.data.data[0].created_at : date.formatDate(new Date(), "YYYY-MM-DD")
        // ...and turn of loading indicator
      } catch (e) {
        Loading.hide()
        // this.$router.push('/dashboard/schedules')
      }
    },
    async addDpdRecords () {
      this.dpdReportData = this.dpdReportData.map(data => {
        let temp = {}
        if (!data.driver_id) {
          if (data.id) {
            temp.id = data.id
          }
          temp.driver_id = this.rnc_id
          temp.dpdRoute = data.dpdRoute
          temp.dpdStops = data.dpdStops
          temp.dpdPayment = data.dpdPayment
          temp.dpdPayType = data.dpdPayType
          temp.dpdDate = data.dpdDate
          temp.user_id = data.user_id
        } else {
          delete data.driver
          temp = data
        }
        return temp
      })
      const params = {
        data: this.dpdReportData
      }
      params.conditions = {
        dpdDate: this.dpdReportData[0].dpdDate
      }
      Loading.show()
      try {
        let res = await api.updateDPDInfo(params)
        Loading.hide()
        console.log('result', res.data)
        // this.$router.push('/dashboard/schedules')
      } catch (error) {
        Loading.hide()
        console.log('error', error)
        // this.$router.push('/dashboard/schedules')
      }
      this.$router.push("/dashboard/dpd-modules")
    },
    async onSubmit () {
      if (this.checkEmptyDrivers().length) {
        this.showEmptyConfirm = true
      } else {
        this.addDpdRecords()
      }
    },
    checkDriverDuplicates (val) {
      if (val) {
        let driverList = this.dpdReportData.map((item) => item.driver_id)
        driverList = driverList.filter((item, index) => item !== '' && item !== this.rnc_id)
        let duplicates = driverList.filter((item, index) => item === val.id && driverList.indexOf(item) !== index)
        if (duplicates.length > 0) return false
        else return true
      } else {
        return true
      }
    },
    checkEmptyDrivers () {
      let emptyList = this.dpdReportData.filter((item, index) => item.driver_id === '')
      let emptyRoutes = emptyList.map(item => item.dpdRoute)
      return emptyRoutes
    },
    removeDpd (dpdDate) {
      // Confirm Remove Vehicle
      this.$q.dialog({
        title: 'Confirm',
        message: 'Are you surely remove this DPD data?',
        cancel: true,
        persistent: true,
        color: 'blue-7'
      }).onOk(async () => {
        const params = {
          conditions: {
            dpdDate: dpdDate
          }
        }
        Loading.show()
        try {
          let res = await api.removeDpdInfo(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: 'Report is removed successfully !'
          })
          this.$router.push("/dashboard/dpd-modules");
        } catch (e) {
          Loading.hide()
        }
      }).onCancel(() => {
      }).onDismiss(() => {
      })
    },
    filterFnDrivers (val, update, abort) {
      update(() => {
        if (val === '') {
          this.filteredDriverNames = []
        } else {
          const needle = val.toLowerCase()
          this.filteredDriverNames = this.drivers.filter(name => name.driver_name.toLowerCase().indexOf(needle) > -1)
          // this.filteredDriverNames = this.filteredDriverNames.slice(0, 3)
        }
      },
      ref => {
        if (val !== '' && ref.options.length > 0) {
          const matchedName = ref.options.find(item => item.driver_name.toLowerCase() === val.toLowerCase())
          if (matchedName) {
            ref.add(matchedName) // reset optionIndex in case there is something selected
          }
        }
      })
    },
    calcPayment (data) {
      if (data.dpdPayType == 'per_stop') {
        if (Number(data.dpdStops)) {
          data.dpdPayment = (Number(data.dpdStops) * Number(data.driverPayment)).toFixed(2);
        } else {
          data.dpdPayment = '';
        }
      }
    },
    addOption (option) {
      this.dpdReportData.map(data => {
        if (data.driver.id === option.id) {
          data.driver_id = option.id
          data.dpdPayType = option.pay_type
          data.driverPayment =option.pay_amount
          data.dpdPayment = Number(data.driverPayment).toFixed(2);
          if (option.pay_type == 'per_stop' && Number(data.dpdStops)) {
            data.dpdPayment = parseFloat(Number(data.driverPayment) * Number(data.dpdStops)).toFixed(2);
          }
        }
      })
    },
    removeSelectedDriver (index) {
      this.dpdReportData[index].driver_id = ''
      this.dpdReportData[index].driver = ''
      // this.dailyReportForm.report_data[index].driver_name = ''
    }
  },
};
</script>
