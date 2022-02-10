<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/fleets')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Fleet' : 'Edit Fleet'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="fleet"
            ref="fleetForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Driver</span>
                <q-select
                  dense
                  outlined
                  v-model="fleet.driver_id"
                  use-input
                  hide-selected
                  fill-input
                  :options="filteredDriverNames"
                  :option-value="opt => opt === null ? null : opt.id"
                  :option-label="opt => opt === null ? '- Null -' : opt.driver_name"
                  emit-value
                  map-options
                  @filter="filterFnDrivers"
                  label-color="grey-7"
                  class="q-ma-none q-pb-md"
                  behavior="menu"
                  bg-color="white"
                  input-class="text-black"
                  :hide-dropdown-icon="true"
                  color="blue-7"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="fleet.driver_id !== ''"
                      class="cursor-pointer"
                      name="clear"
                      @click="removeSelectedDriver(index)"
                    />
                  </template>
                </q-select>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Van Plate Number</span>
                <q-input dense outlined required :disable="!isNewPage" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="fleet.van_number"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Insured with (Company name)</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="fleet.insurance_company"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Insurance Expire Date</span>
                <q-input dense outlined v-model="insuranceExpireDate" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="insuranceExpireDate"
                          minimal
                          mask="DD/MM/YY ddd"
                          color="blue-7"
                          first-day-of-week="1"
                        >
                          <div class="row items-center justify-end q-gutter-sm">
                            <q-btn label="OK" color="blue-7" @click="onInsuranceExpireDateChanged" flat v-close-popup />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">INSURANCE Days Left</span>
                <q-input dense outlined disable v-model="fleet.left_renewal" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black" />
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">MOT Expire Date</span>
                <q-input dense outlined v-model="motExpireDate" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="motExpireDate"
                          minimal
                          mask="DD/MM/YY ddd"
                          color="blue-7"
                          first-day-of-week="1"
                        >
                          <div class="row items-center justify-end q-gutter-sm">
                            <q-btn label="OK" color="blue-7" @click="onMotExpireDateChanged" flat v-close-popup />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">MOT Days Left</span>
                <q-input dense outlined disable v-model="fleet.left_mot" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black" />
                  </template>
                </q-input>
              </q-card-section>
              <!-- <q-card-section class="text-left q-py-none">
                <q-checkbox v-model="fleet.mot_reminder" :val="true" label="MOT Reminder" class="q-mx-auto text-white" color="grey" dark />
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <q-checkbox v-model="fleet.insurance_reminder" :val="true" label="Insurance Renewal Reminder" class="q-mx-auto text-white" color="grey" dark />
              </q-card-section> -->
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Comments</span>
                <q-input dense outlined type="textarea" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-left" v-model="fleet.comments"></q-input>
              </q-card-section>
              <q-card-actions align="center">
                <div class="q-px-md">
                  <q-btn
                    no-caps
                    dense
                    rounded
                    v-if="!isNewPage"
                    label="Delete"
                    color="blue-7"
                    @click="remove"
                    style="width: 100px; height:40px;"
                  /> &nbsp;
                  <q-btn
                    color="blue-7"
                    :label="isNewPage ? 'Add New' : 'Update'"
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
        </div>
      </div>
    </template>
  </q-page>
</template>

<script>
import { Loading, date } from "quasar";
import { api } from "src/boot/api";
export default {
  name: "NewFleet",
  data() {
    return {
      fleet: {
        id: "",
        driver_id: "",
        van_number: "",
        insurance_company: "",
        insurance_expire_date: "",
        left_renewal: "",
        mot_expire_date: "",
        left_mot: "",
        // mot_reminder: false,
        // insurance_reminder: false,
        comments: ""
      },
      insuranceExpireDate: "",
      motExpireDate: "",
      lastServiceDate: "",
      isNewPage: false,
      drivers: [],
      filteredDriverNames: [],
      createdAt: ''
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    if (!this.isNewPage) {
      await this.getDriverList(this.createdAt);
      await this.getFleetInfo(this.fleet.id);
    } else  {
      await this.getDriverList();
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
        this.$router.currentRoute.params.id !== null &&
        this.$router.currentRoute.params.id !== undefined &&
        this.$router.currentRoute.params.id !== ""
      ) {
        this.isNewPage = false;
        this.fleet.id = this.$router.currentRoute.params.id;
      } else {
        this.isNewPage = true;
      }
    },
    onInsuranceExpireDateChanged (expireDate) {
      this.fleet.insurance_expire_date = date.formatDate(date.extractDate(this.insuranceExpireDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.getInsuranceLeftDays()
    },
    getInsuranceLeftDays() {
      let currentDate = date.formatDate(new Date(), 'YYYY-MM-DD')
      this.fleet.left_renewal = date.getDateDiff(this.fleet.insurance_expire_date, currentDate, 'days')
    },
    onMotExpireDateChanged (expireDate) {
      this.fleet.mot_expire_date = date.formatDate(date.extractDate(this.motExpireDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
      this.getMotLeftDays()
    },
    getMotLeftDays() {
      let currentDate = date.formatDate(new Date(), 'YYYY-MM-DD')
      this.fleet.left_mot = date.getDateDiff(this.fleet.mot_expire_date, currentDate, 'days')
    },
    getFleetInfo: async function (id) {
      Loading.show();
      try {
        let res = await api.getFleetInfo(id);
        this.fleet = res.data.data;
        this.createdAt = res.data.data.updatd_at;
        this.insuranceExpireDate = date.formatDate(date.extractDate(this.fleet.insurance_expire_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
        this.motExpireDate = date.formatDate(date.extractDate(this.fleet.mot_expire_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
        // this.fleet.mot_reminder = this.fleet.mot_reminder ? true : false;
        // this.fleet.insurance_reminder = this.fleet.insurance_reminder ? true : false;
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/fleets");
      }
    },
    async onSubmit() {
      if (this.isNewPage) {
        Loading.show();
        try {
          let res = await api.createFleet(this.fleet);
          console.log("res", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "Fleet is registered successfully",
          });
          this.$router.push("/dashboard/fleets");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.fleet,
        };
        params.conditions = {
          id: this.fleet.id,
        };
        Loading.show();
        try {
          let res = await api.updateFleet(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/fleets");
      }
    },
    remove() {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove " + this.fleet.van_number + "?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.fleet.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeFleet(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: this.fleet.van_number + " is removed successfully !",
            });
            // this.getUsers({
            //   pagination: this.pagination,
            //   filter: undefined
            // })
          } catch (e) {
            Loading.hide();
          }
          this.$router.push("/dashboard/fleets");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    },
    getDriverList: async function (created_at) {
      try {
        let res = await api.getDriverList(created_at)
        this.drivers = res.data.data
        this.filteredDriverNames = this.drivers
      } catch (e) {
      }
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
    removeSelectedDriver (index) {
      this.fleet.driver_id = ''
    }
  },
};
</script>
