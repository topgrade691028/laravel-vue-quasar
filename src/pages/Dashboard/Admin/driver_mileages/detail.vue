<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/odometer-records')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Service Data' : 'Edit Service Data'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="mileage_data"
            ref="serviceForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Date Time</span>
                <q-input dense outlined required disable bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" :value="changeDateFormat(mileage_data.created_at)"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Driver</span>
                <q-input dense outlined required disable bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="mileage_data.driver_name"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Van Plate Number</span>
                <q-input dense outlined required disable bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="mileage_data.van_number"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mileage on Clock</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="mileage_data.service_mileage" :rules="[ val => (prevMileage>0 && val>prevMileage) || 'Cant put less than '+ prevMileage ]"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mileage Left to Service</span>
                <q-input dense outlined disable required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" :value="mileage_data.next_service_mileage - mileage_data.service_mileage"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <q-checkbox v-model="mileage_data.tyres" :val="true" label="Tyres are Okay" class="q-mx-auto text-white" color="grey" dark />
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <q-checkbox v-model="mileage_data.oil" :val="true" label="Oil Level Correct" class="q-mx-auto text-white" color="grey" dark />
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
  name: "NewMileageData",
  data() {
    return {
      mileage_data: {
        id: "",
        fleet_id: "",
        service_date: "",
        service_mileage: 0,
        service_comments: "",
        next_service_mileage: 0,
        tyres: false,
        oil: false,
        is_first: 0,
        created_at: ""
      },
      prevMileage: "",
      isNewPage: false,
      createdAt: ''
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    if (!this.isNewPage) {
      await this.getMileageInfo(this.mileage_data.id);
    } else {
      await this.getMileageInfo();
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
    driver: {
      get () {
        return this.$store.state.auth.driver
      }
    }
  },
  methods: {
    isNew() {
      if (
        this.$router.currentRoute.params.id !== null &&
        this.$router.currentRoute.params.id !== undefined &&
        this.$router.currentRoute.params.id !== ""
      ) {
        this.isNewPage = false;
        this.mileage_data.id = this.$router.currentRoute.params.id;
      } else {
        this.isNewPage = true;
      }
    },
    changeDateFormat(datetime) {
      if (datetime) {
        datetime = datetime.slice(0, -8);
        return date.formatDate(date.extractDate(datetime, 'YYYY-MM-DDTHH:mm:ss'), 'YYYY-MM-DD HH:mm:ss')
      } else {
        return date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss')
      }
    },
    // getTotalMileage () {
    //   this.mileage_data.total_mileage = Number(this.totalMileage) + Number(this.mileage_data.service_mileage);
    // },
    getMileageInfo: async function (id) {
      Loading.show();
      try {
        if (id) {
          let res = await api.getMileageInfo(this.driver.id, id);
          // this.totalMileage = res.data.data.total_mileage;
          // console.log('total', this.totalMileage)
          this.mileage_data = res.data.data;
          // this.mileage_data.date_time = date.formatDate(date.extractDate(this.mileage_data.date_time, 'YYYY-MM-DD HH:mm:ss'), 'DD/MM/YY ddd HH:mm:ss')
          this.mileage_data.tyres = this.mileage_data.tyres ? true : false;
          this.mileage_data.oil = this.mileage_data.oil ? true : false;
          // this.getTotalMileage();
        } else {
          let res = await api.getMileageInfo(this.driver.id);
          // this.mileage_data.driver_id = res.data.data.driver_id;
          // this.totalMileage = res.data.data.total_mileage;
          this.mileage_data = res.data.data;
          this.prevMileage = this.mileage_data.service_mileage;
          this.mileage_data.service_mileage = 0;
          this.mileage_data.created_at = "";
          this.mileage_data.is_first = 0;
          delete this.mileage_data['id'];
          delete this.mileage_data['updated_at'];
          // this.getTotalMileage();

          this.mileage_data.tyres = this.mileage_data.tyres ? true : false;
          this.mileage_data.oil = this.mileage_data.oil ? true : false;
        }
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/odometer-records");
      }
    },
    async onSubmit() {
      if (this.isNewPage) {
        Loading.show();
        try {
      console.log('this.mileage_data', this.mileage_data)
          let res = await api.createMileageRecord(this.mileage_data);
          console.log("res1", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "Odometer Record is added successfully",
          });
          this.$router.push("/dashboard/odometer-records");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.mileage_data,
        };
        params.conditions = {
          id: this.mileage_data.id,
        };
        Loading.show();
        try {
          let res = await api.updateMileageRecord(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/odometer-records");
      }
    },
    remove() {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove this odometer record?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.mileage_data.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeMileageRecord(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: "Odometer Record is removed successfully !",
            });
            // this.getUsers({
            //   pagination: this.pagination,
            //   filter: undefined
            // })
          } catch (e) {
            Loading.hide();
          }
          this.$router.push("/dashboard/odometer-records");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    }
  },
};
</script>
