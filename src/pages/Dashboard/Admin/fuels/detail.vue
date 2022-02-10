<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/fuels')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Fuel' : 'Edit Fuel'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="fuel"
            ref="fuelForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section
                class="text-left q-py-none"
              >
                <span class="text-white">Driver Name</span>
                <q-select
                  dense
                  required
                  outlined
                  v-model="fuel.driver_id"
                  use-input
                  hide-selected
                  fill-input
                  :options="filteredDrivers"
                  :option-value="(opt) => (opt === null ? null : opt.id)"
                  :option-label="(opt) => (opt === null ? '- Null -' : opt.driver_name)"
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
                      v-if="fuel.driver_id !== ''"
                      class="cursor-pointer"
                      name="clear"
                      @click="removeSelectedDriver()"
                    />
                  </template>
                </q-select>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Card No</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="fuel.card_no"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Company</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="fuel.company"></q-input>
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
  name: "NewFuel",
  data() {
    return {
      fuel: {
        id: "",
        driver_id: "",
        card_no: "",
        company: ""
      },
      isNewPage: false,
      drivers: [],
      filteredDrivers: []
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    await this.getDriverList("");
    if (!this.isNewPage) {
      this.filteredDrivers = this.drivers;
      await this.getFuelInfo(this.fuel.id);
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
        this.fuel.id = this.$router.currentRoute.params.id;
      } else {
        this.isNewPage = true;
      }
    },
    getDriverList: async function() {
      try {
        let res = await api.getDriversByUserID()
        this.drivers = res.data.data
      } catch (e) {
      }
    },
    getFuelInfo: async function (id) {
      Loading.show();
      try {
        let res = await api.getFuelInfo(id);
        this.fuel = res.data.data;
        console.log("fuel details", res.data.data);
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/fuels");
      }
    },
    async onSubmit() {
      if (this.isNewPage) {
        Loading.show();
        try {
          let res = await api.createFuel(this.fuel);
          console.log("res", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "Fuel is registered successfully",
          });
          this.$router.push("/dashboard/fuels");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.fuel,
        };
        params.conditions = {
          id: this.fuel.id,
        };
        Loading.show();
        try {
          let res = await api.updateFuel(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/fuels");
      }
    },
    remove() {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove " + this.fuel.card_no + "?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.fuel.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeFuel(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: this.fuel.card_no + " is removed successfully !",
            });
          } catch (e) {
            Loading.hide();
          }
          this.$router.push("/dashboard/fuels");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    },
    filterFnDrivers (val, update, abort) {
      update(() => {
        if (val === '') {
          this.filteredDrivers = []
        } else {
          const needle = val.toLowerCase()
          this.filteredDrivers = this.drivers.filter(driver => driver.driver_name.toLowerCase().indexOf(needle) > -1)
          // this.filteredDrivers = this.filteredDrivers.slice(0, 3)
        }
      },
      ref => {
        if (val !== '' && ref.options.length > 0) {
          const matchedDriver = ref.options.find(item => item.driver_name.toLowerCase() === val.toLowerCase())
          if (matchedDriver) {
            ref.add(matchedDriver) // reset optionIndex in case there is something selected
          }
        }
      })
    },
    removeSelectedDriver () {
      this.fuel.driver_id = '';
    }
  },
};
</script>
