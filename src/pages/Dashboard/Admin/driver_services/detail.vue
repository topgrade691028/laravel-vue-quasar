<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/driver-services')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Service Data' : 'Edit Service Data'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="service_data"
            ref="serviceForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Van Plate Number</span>
                <q-select
                  dense
                  outlined
                  v-model="service_data.fleet_id"
                  :options="fleets"
                  :option-value="opt => opt === null ? null : opt.id"
                  :option-label="opt => opt === null ? '- Null -' : opt.van_number"
                  emit-value
                  map-options
                  options-cover
                  label-color="grey-7"
                  class="q-ma-none q-pb-md"
                  behavior="menu"
                  bg-color="white"
                  color="blue-7"
                  :disable="!isNewPage"
                >
                  <template v-slot:append>
                    <q-icon
                      v-if="service_data.fleet_id !== ''"
                      class="cursor-pointer"
                      name="clear"
                      @click="removeSelectedVan()"
                    />
                  </template>
                </q-select>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Date</span>
                <q-input dense outlined v-model="serviceDate" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer" color="black">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="serviceDate"
                          minimal
                          mask="DD/MM/YY ddd"
                          color="blue-7"
                          first-day-of-week="1"
                        >
                          <div class="row items-center justify-end q-gutter-sm">
                            <q-btn label="OK" color="blue-7" @click="onServiceDateChanged" flat v-close-popup />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mileage on Clock</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="service_data.service_mileage" @input="getTotalMileage"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Comments</span>
                <q-input dense outlined required type="textarea" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-left" v-model="service_data.service_comments"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Next Service Mileage</span>
                <q-input dense outlined v-model="service_data.next_service_mileage" bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mileage Left to Service</span>
                <q-input dense outlined disable required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" :value="service_data.next_service_mileage - service_data.service_mileage"></q-input>
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
  name: "NewServiceData",
  data() {
    return {
      service_data: {
        id: "",
        fleet_id: "",
        service_date: "",
        service_mileage: 0,
        service_comments: "",
        next_service_mileage: 0,
        total_mileage: 0,
        is_first: 1
      },
      totalMileage: "",
      serviceDate: "",
      isNewPage: false,
      fleets: [],
      filteredVanNumbers: [],
      createdAt: ''
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    await this.getFleetAll();
    if (!this.isNewPage) {
      await this.getServiceInfo(this.service_data.id);
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
        this.service_data.id = this.$router.currentRoute.params.id;
      } else {
        this.isNewPage = true;
      }
    },
    onServiceDateChanged (expireDate) {
      this.service_data.service_date = date.formatDate(date.extractDate(this.serviceDate, 'DD/MM/YY ddd'), 'YYYY-MM-DD')
    },
    onChangeVan () {
      console.log('ddddddddddddddd', this.service_data.fleet_id)
    },
    getServiceInfo: async function (id) {
      Loading.show();
      try {
        let res = await api.getServiceInfo(id);
        this.totalMileage = res.data.data.total_mileage;
        this.service_data = res.data.data;
        this.getTotalMileage();
        this.serviceDate = date.formatDate(date.extractDate(this.service_data.service_date, 'YYYY-MM-DD'), 'DD/MM/YY ddd')
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/driver-services");
      }
    },
    async onSubmit() {
      if (this.isNewPage) {
        Loading.show();
        try {
          let res = await api.createService(this.service_data);
          console.log("res", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "Service Data is added successfully",
          });
          this.$router.push("/dashboard/driver-services");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.service_data,
        };
        params.conditions = {
          id: this.service_data.id,
        };
        Loading.show();
        try {
          let res = await api.updateService(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/driver-services");
      }
    },
    remove() {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove this service data?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.service_data.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeService(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: "Service data is removed successfully !",
            });
            // this.getUsers({
            //   pagination: this.pagination,
            //   filter: undefined
            // })
          } catch (e) {
            Loading.hide();
          }
          this.$router.push("/dashboard/driver-services");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    },
    getFleetAll: async function () {
      try {
        let res = await api.getFleetAll()
        this.fleets = res.data.data
        this.filteredVanNumbers = this.fleets
      } catch (e) {
      }
    },
    getTotalMileage () {
      this.service_data.total_mileage = this.totalMileage + this.service_data.service_mileage;
    },
    filterFnVans (val, update, abort) {
      update(() => {
        if (val === '') {
          this.filteredVanNumbers = []
        } else {
          const needle = val.toLowerCase()
          this.filteredVanNumbers = this.fleets.filter(fleet => fleet.van_number.toLowerCase().indexOf(needle) > -1)
          // this.filteredDriverNames = this.filteredDriverNames.slice(0, 3)
        }
      },
      ref => {
        if (val !== '' && ref.options.length > 0) {
          const matchedName = ref.options.find(item => item.van_number.toLowerCase() === val.toLowerCase())
          if (matchedName) {
            ref.add(matchedName) // reset optionIndex in case there is something selected
          }
        }
      })
    },
    removeSelectedVan () {
      this.service_data.fleet_id = ""
    }
  },
};
</script>
