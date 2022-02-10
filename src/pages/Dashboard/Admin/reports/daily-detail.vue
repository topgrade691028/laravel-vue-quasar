<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/schedules')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Daily Report' : 'Edit Daily Report'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="dailyReportForm"
            ref="dailyReportForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section
                class="text-left q-py-none"
              >
                <span class="text-white">Date</span>
                <q-input dense outlined v-model="dailyReportDate" bg-color="white" color="blue-7" class="q-py-none" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="dailyReportDate"
                          minimal
                          @input="onDailyReportDateChanged"
                          mask="DD/MM/YY ddd"
                          color="blue-7"
                          first-day-of-week="1"
                        >
                          <div class="row items-center justify-end q-gutter-sm">
                            <q-btn label="OK" color="blue-7" @click="onDailyReportDateChanged" flat v-close-popup />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </q-card-section>              
              <q-card-section class="text-left q-py-none">
                <div v-for="(data, index) in dailyReportForm.report_data" :key="data.route_id" class="q-pt-md">
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
                        @input="addOption"
                        label-color="grey-7"
                        class="q-ma-none q-pb-md"
                        behavior="menu"
                        bg-color="white"
                        input-class="text-black"
                        :rules="[ val => checkDriverDuplicates(val) || 'This record is duplicated' ]"
                        :hide-dropdown-icon="true"
                        color="blue-7"
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
                      <q-input required dense outlined bg-color="blue-grey-4" class="q-pb-md" input-class="text-white q-pr-xl" label-color="grey-3" color="blue-7" :value="data.route_number" disable></q-input>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6 q-pr-sm">
                      <span class="text-white">Stops</span>
                      <q-input dense outlined bg-color="white" class="q-pb-md" input-class="text-black q-pr-xl" label-color="grey-3" color="blue-7" v-model="data.stops" @input="calcPayment(data)"></q-input>
                    </div>
                    <div class="col-6 q-pl-sm">
                      <span class="text-white">Payment</span>
                      <q-input dense outlined bg-color="white" class="q-pb-md" input-class="text-black q-pr-xl" label-color="grey-3" color="blue-7" v-model="data.payment"></q-input>
                    </div>
                  </div>
                  <q-separator color="grey-4" v-if="index < dailyReportForm.report_data.length-1" />
                </div>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Comments</span>
                <q-input dense outlined type="textarea" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-left" v-model="dailyReportForm.comments"></q-input>
              </q-card-section>
              <q-card-actions align="center">
                <div class="q-px-md">
                  <q-btn
                    v-if="!isNewPage"
                    no-caps
                    dense
                    rounded
                    label="Delete"
                    color="blue-7"
                    @click="removeDaily(dailyReportForm.report_no)"
                    style="width: 100px; height:40px;"
                  /> &nbsp;
                  <q-btn
                    color="blue-7"
                    :label="isNewPage ? 'Add' : 'Update'"
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
            <q-card style="background-color: #3E444E; max-width: 500px; min-width: 352px;">
              <q-bar style="background-color: #272B33">
                <q-btn dense flat icon="close" color="white" v-close-popup>
                  <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
                </q-btn>
              </q-bar>

              <div>
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
                    @click="addDailyRecords"
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
      isNewPage: false,
      drivers: [],
      dailyReportDate: '',
      dailyReportForm: {
        report_date: '',
        report_title: '',
        report_no: '',
        user_id: '',
        report_data: [],
        comments: ''
      },
      dailyRoutes: [],
      filteredDriverNames: [],
      is_mobile: 'web',
      showEmptyConfirm: false,
      dailyCreatedAt: ''
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    await this.getRNCID();
    if (!this.isNewPage) {
      await this.getDriverList(this.dailyCreatedAt);
      await this.getRegularRoutes(this.dailyCreatedAt)
      await this.getReportInfo(this.dailyReportForm.report_no)
      this.dailyReportDate = date.formatDate(date.extractDate(this.dailyReportForm.report_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
    } else {
      await this.getDriverList()
      await this.getRegularRoutes()
      this.dailyReportDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
      this.onDailyReportDateChanged()
      this.dailyReportForm.report_data = this.dailyRoutes.map(route => {
        return { driver_id: '', driver: {}, route_id: route.id, route_number: route.route_number, stops: '', payment: '', current_paytype: 'fixed' }
      })
    }
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
    },
  },
  methods: {
    isNew() {
      if (
        this.$router.currentRoute.params.reportNo !== null &&
        this.$router.currentRoute.params.reportNo !== undefined &&
        this.$router.currentRoute.params.reportNo !== ""
      ) {
        this.isNewPage = false;
        this.dailyReportForm.report_no = this.$router.currentRoute.params.reportNo;
      } else {
        this.isNewPage = true;
      }
    },
    onDailyReportDateChanged () {
      this.dailyReportForm.report_date = date.formatDate(date.extractDate(this.dailyReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.dailyCreatedAt = this.dailyReportForm.report_date
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
    getRegularRoutes: async function (created_at) {
      try {
        let res = await api.getRegularRoutes(created_at)
        this.dailyRoutes = res.data.data
      } catch (e) {
      }
    },
    getReportInfo: async function (reportNo) {
      Loading.show()
      try {
        let res = await api.getReportInfo(reportNo)
        Loading.hide()
        this.dailyReportForm.report_date = res.data.data[0].report_date
        this.dailyReportForm.report_title = res.data.data[0].report_title
        this.dailyReportForm.report_no = res.data.data[0].report_no
        this.dailyReportForm.comments = res.data.data[0].comments
        
        this.dailyCreatedAt = res.data.data[0].created_at

        this.dailyReportForm.report_data = res.data.data.map(data => {
          let reportData = {
            id: data.id,
            driver: data.driver,
            driver_id: data.driver_id,
            route_id: data.route_id,
            route_number: data.route.route_number,
            stops: data.stops,
            payment: data.payment,
            current_paytype: data.current_paytype
          }
          return reportData
        })
        // ...and turn of loading indicator
      } catch (e) {
        Loading.hide()
        // this.$router.push('/dashboard/schedules')
      }
    },
    async addDailyRecords () {
      this.dailyReportForm.report_data = this.dailyReportForm.report_data.map(data => {
        let temp = {}
        if (!data.driver_id) {
          if (data.id) {
            temp.id = data.id
          }
          temp.driver_id = this.rnc_id
          temp.route_id = data.route_id
          temp.stops = data.stops
          temp.payment = data.payment
          temp.current_paytype = data.current_paytype
          console.log('current paytype', data.current_paytype)
        } else {
          delete data.driver
          temp = data
        }
        return temp
      })
      const params = {
        data: this.dailyReportForm
      }
      if (!this.isNewPage) {
        params.conditions = {
          report_no: this.dailyReportForm.report_no
        }
        Loading.show()
        try {
          let res = await api.updateReport(params)
          Loading.hide()
          console.log('result', res.data)
          // this.$router.push('/dashboard/schedules')
        } catch (error) {
          Loading.hide()
          console.log('error', error)
          // this.$router.push('/dashboard/schedules')
        }
      } else {
        Loading.show()
        try {
          let res = await api.createReport(params)
          Loading.hide()
          console.log('result', res.data)
          // this.$router.push('/dashboard/schedules')
        } catch (error) {
          Loading.hide()
          console.log('error', error)
          // this.$router.push('/dashboard/schedules')
        }
      }
      // this.cancelDailyDetail()
      // this.hideAddRecordDlg()
      this.$router.push("/dashboard/schedules")
    },
    async onSubmit () {
      if (this.checkEmptyDrivers().length) {
        this.showEmptyConfirm = true
      } else {
        this.addDailyRecords()
      }
    },
    checkDriverDuplicates (val) {
      if (val) {
        let driverList = this.dailyReportForm.report_data.map((item) => item.driver_id)
        driverList = driverList.filter((item, index) => item !== '' && item !== this.rnc_id)
        let duplicates = driverList.filter((item, index) => item === val.id && driverList.indexOf(item) !== index)
        if (duplicates.length > 0) return false
        else return true
      } else {
        return true
      }
    },
    checkEmptyDrivers () {
      let emptyList = this.dailyReportForm.report_data.filter((item, index) => item.driver_id === '')
      let emptyRoutes = emptyList.map(item => item.route_number)
      return emptyRoutes
    },
    removeDaily (reportNo) {
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
            report_no: reportNo
          }
        }
        Loading.show()
        try {
          let res = await api.removeReport(params)
          Loading.hide()
          console.log('remove result', res)
          this.$q.notify({
            color: 'positive',
            position: 'top',
            message: 'Report is removed successfully !'
          })
          // this.cancelDailyDetail()
          // this.hideAddRecordDlg()
          this.$router.push("/dashboard/schedules");
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
      if (data.current_paytype == 'per_stop') {
        if (Number(data.stops)) {
          data.payment = (Number(data.stops) * Number(data.driver_payment)).toFixed(2);
        } else {
          data.payment = '';
        }
      }
    },
    addOption (option) {
      this.dailyReportForm.report_data.map(data => {
        if (data.driver.id === option.id) {
          data.driver_id = option.id
          data.driver_payment = option.pay_amount
          data.payment = Number(option.pay_amount).toFixed(2);
          data.current_paytype = option.pay_type
          if (data.current_paytype == 'per_stop' && Number(data.stops)) {
            data.payment = (Number(data.stops) * Number(data.driver_payment)).toFixed(2);
          }
        }
      })
    },
    removeSelectedDriver (index) {
      this.dailyReportForm.report_data[index].driver_id = ''
      this.dailyReportForm.report_data[index].driver = ''
      // this.dailyReportForm.report_data[index].driver_name = ''
    }
  },
};
</script>
