<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/schedules')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Extra Report' : 'Edit Extra Report'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="extraReportForm"
            ref="extraReportForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section
                class="text-left q-py-none"
              >
                <span class="text-white">Date</span>
                <q-input dense outlined v-model="extraReportDate" color="blue-7" bg-color="white" input-class="text-black text-center">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="extraReportDate"
                          minimal
                          mask="DD/MM/YY ddd"
                          color="blue-7"
                          first-day-of-week="1"
                        >
                          <div class="row items-center justify-end q-gutter-sm">
                            <q-btn label="OK" color="blue-7" @click="onExtraReportDateChanged" flat v-close-popup />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none" v-if="extraRoutes.length">
                <span class="text-white">Driver</span>
                <q-select
                  dense
                  required
                  outlined
                  v-model="extraReportForm.driver"
                  use-input
                  hide-selected
                  fill-input
                  :options="filteredDriverNames"
                  :option-value="opt => opt === null ? null : opt"
                  :option-label="opt => opt === null ? '- Null -' : opt.driver_name"
                  @filter="filterFnDrivers"
                  @input="addOption"
                  class="q-mb-xs"
                  behavior="menu"
                  bg-color="white"
                  input-class="text-black"
                  color="blue-7"
                  :hide-dropdown-icon="true"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="extraReportForm.driver !== ''"
                      class="cursor-pointer"
                      name="clear"
                      @click="removeSelectedDriver"
                    />
                  </template>
                </q-select>
                <div class="row">
                  <div class="col-6 q-pr-sm">
                    <span class="text-white">Route</span>
                    <q-select
                      dense
                      required
                      outlined
                      v-model="extraReportForm.route_id"
                      :options="extraRoutes"
                      :option-value="opt => opt === null ? null : opt.id"
                      :option-label="opt => opt === null ? '- Null -' : opt.route_number"
                      emit-value
                      map-options
                      class="q-mb-xs"
                      behavior="menu"
                      bg-color="white"
                      input-class="text-black"
                      color="blue-7"
                      :hide-dropdown-icon="true"
                    >
                      <template v-slot:append>
                        <q-icon
                          v-if="extraReportForm.route_id !== ''"
                          class="cursor-pointer"
                          name="clear"
                          @click="removeSelectedRoute"
                        />
                      </template>
                    </q-select>
                  </div>
                  <div class="col-6 q-pl-sm">
                    <span class="text-white">Payment</span>
                    <q-input dense outlined bg-color="white" class="q-pb-md" input-class="text-black q-pr-xl" label-color="grey-3" color="blue-7" v-model="extraReportForm.payment"></q-input>
                  </div>
                </div>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Comments</span>
                <q-input dense outlined type="textarea" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-left" v-model="extraReportForm.comments"></q-input>
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
                    @click="removeExtra(extraReportForm.report_no)"
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
            <q-card style="background-color: #3E444E; max-width: 500px; min-width: 370px; min-height: 90vh;">
              <q-bar style="background-color: #272B33">
                <q-btn dense flat icon="close" color="white" v-close-popup>
                  <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
                </q-btn>
              </q-bar>

              <div class="layout-center">
                <q-card-section class="text-white">
                  <div class="text-h6 text-center">You did not provide the driver name.</div>
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
                    @click="addExtraRecords"
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
  name: "NewExtra",
  data() {
    return {
      isNewPage: false,
      drivers: [],
      extraReportDate: '',
      extraReportForm: {
        id: '',
        report_date: '',
        report_title: '',
        report_no: '',
        driver_id: '',
        route_id: '',
        route_number: '',
        payment: '',
        current_paytype: '',
        driver: '',
        comments: ''
      },
      extraRoutes: [],
      filteredDriverNames: [],
      is_mobile: 'web',
      showEmptyConfirm: false,
      extraCreatedAt: ''
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    await this.getRNCID();
    if (!this.isNewPage) {
      await this.getDriverList(this.extraCreatedAt);
    } else {
      await this.getDriverList()
      this.extraReportDate = date.formatDate(new Date(), 'DD/MM/YY ddd')
      this.onExtraReportDateChanged()
      // this.extraReportForm.report_date = date.formatDate(date.extractDate(this.extraReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
    }
    if (!this.isNewPage) {      
      await this.getExtraInfo(this.extraReportForm.report_no)
      await this.getExtraRoutes(this.extraCreatedAt)
      this.extraReportDate = date.formatDate(date.extractDate(this.extraReportForm.report_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
    } else {
      await this.getExtraRoutes()
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
        this.extraReportForm.report_no = this.$router.currentRoute.params.reportNo;
      } else {
        this.isNewPage = true;
      }
    },
    onExtraReportDateChanged () {
      this.extraReportForm.report_date = date.formatDate(date.extractDate(this.extraReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
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
    getExtraRoutes: async function (created_at) {
      try {
        let res = await api.getExtraRoutes(created_at)
        this.extraRoutes = res.data.data
      } catch (e) {
        // this.$router.push('/dashboard/schedules')
      }
    },
    getExtraInfo: async function (reportNo) {
      Loading.show()
      try {
        let res = await api.getReportInfo(reportNo)
        Loading.hide()
        
        this.extraCreatedAt = res.data.data[0].created_at

        this.extraReportForm = {
          id: res.data.data[0].id,
          report_date: res.data.data[0].report_date,
          report_title: res.data.data[0].report_title,
          report_no: res.data.data[0].report_no,
          driver: res.data.data[0].driver,
          driver_id: res.data.data[0].driver_id,
          route_id: res.data.data[0].route_id,
          route_number: res.data.data[0].route.route_number,
          payment: res.data.data[0].payment,
          current_paytype: res.data.data[0].current_paytype,
          comments: res.data.data[0].comments
        }

        // ...and turn of loading indicator
      } catch (e) {
        Loading.hide()
        // this.$router.push('/dashboard/schedules')
      }
    },
    async addExtraRecords () {
      if (this.extraReportForm.route_id) {
        this.extraReportForm.report_date = date.formatDate(date.extractDate(this.extraReportDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
        if (!this.extraReportForm.driver_id) {
          this.extraReportForm.driver_id = this.rnc_id
        }
        delete this.extraReportForm.driver
        const params = {
          data: this.extraReportForm
        }
        if (!this.isNewPage) {
          params.conditions = {
            id: this.extraReportForm.id
          }
          Loading.show()
          try {
            let res = await api.updateSingleRecord(params)
            Loading.hide()
            console.log('result', res.data)
          } catch (error) {
            Loading.hide()
            console.log('error', error)
          }
          // this.$router.push('/dashboard/schedules')
        } else {
          Loading.show()
          try {
            let res = await api.createSingleRecord(params)
            Loading.hide()
            console.log('result', res.data)
          } catch (error) {
            Loading.hide()
            console.log('error', error)
          }
        }
        this.$router.push("/dashboard/schedules")
      } else {
        this.$q.notify({
          color: 'negative',
          position: 'top',
          message: 'You must choose the route number !'
        })
      }
    },
    async onSubmit () {
      if (!this.extraReportForm.driver_id) {
        this.showEmptyConfirm = true
      } else {
        this.addExtraRecords()
      }
    },
    removeExtra (reportNo) {
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
    addOption (option) {
      this.extraReportForm.driver_id = option.id
      this.extraReportForm.payment = option.pay_amount
      this.extraReportForm.current_paytype = option.pay_type
    },
    removeSelectedDriver () {
      this.extraReportForm.driver_id = ''
      this.extraReportForm.driver = ''
    },
    removeSelectedRoute () {
      this.extraReportForm.route_id = ''
      this.extraReportForm.route_number = ''
    }
  },
};
</script>
