<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/drivers')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Driver' : 'Edit Driver'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="driver"
            ref="driverForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Full Name</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="driver.driver_name"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mobile</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="driver.phone"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <div class="row">
                  <div class="col-6 q-pr-sm">
                    <q-radio v-model="driver.pay_type" val="fixed" label="Fixed" color="white" class="text-white" keep-color/>
                  </div>
                  <div class="col-6 q-pr-sm">
                    <q-radio v-model="driver.pay_type" val="per_stop" label="Pay Per Stop" color="white" class="text-white" keep-color/>
                  </div>
                </div>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Payment</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="driver.pay_amount"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">E-Mail</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="driver.email"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none" v-if="!isNewPage">
                <q-checkbox v-model="isChangePassword" label="Do you want to change your password?" class="q-mx-auto text-white" color="grey" dark />
              </q-card-section>
              <q-card-section class="text-left q-py-none" v-if="isChangePassword || isNewPage">
                <span class="text-white">Password</span>
                <base-text-field
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  normalize-bottom
                  icon="mdi-card"
                  clearable
                  type="password"
                  hide-show-password
                  v-model="driver.password"
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-account-key" />
                  </template>
                </base-text-field>
              </q-card-section>
              <q-card-section class="text-left q-py-none" v-if="isChangePassword || isNewPage">
                <span class="text-white">Confirm</span>
                <base-text-field
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  v-model="driver.confirmPassword"
                  normalize-bottom
                  icon="mdi-card"
                  clearable
                  type="password"
                  :rules="[val => val === driver.password || 'Password is not match']"
                  hide-show-password
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-account-key" />
                  </template>
                </base-text-field>
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
import { Loading } from "quasar";
import { api } from "src/boot/api";
export default {
  name: "NewDriver",
  data() {
    return {
      driver: {
        id: "",
        full_name: "",
        driver_name: "",
        password: "",
        confirmPassword: "",
        phone: "",
        email: "",
        pay_type: "fixed",
        pay_amount: "",
        has_access: 1,
      },
      isChangePassword: false,
      userAvatar: {},
      isNewPage: false
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    if (!this.isNewPage) {
      await this.getDriverInfo(this.driver.id);
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
    userSubcontractor: {
      get () {
        return this.$store.state.auth.userSubcontractor
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
        this.driver.id = this.$router.currentRoute.params.id;
      } else {
        this.isChangePassword = true;
        this.isNewPage = true;
      }
    },
    getDriverInfo: async function (id) {
      Loading.show();
      try {
        let res = await api.getDriverInfo(id);
        this.driver = res.data.driver;
        console.log("driver details", res.data.driver);
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/drivers");
      }
    },
    addAvatar(files) {
      this.userAvatar = files[0];
    },
    async onSubmit() {
      if (this.isChangePassword === false) {
        delete this.driver['password'];
      }
      if (this.isNewPage) {
        Loading.show();
        try {
          let res = await api.createDriver(this.driver);
          console.log("res", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "Driver is registered successfully",
          });
          this.$router.push("/dashboard/drivers");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.driver,
        };
        params.conditions = {
          id: this.driver.id,
        };
        Loading.show();
        try {
          let res = await api.updateDriver(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/drivers");
      }
    },
    remove() {
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you surely remove " + this.driver.driver_name + "?",
          cancel: true,
          persistent: true,
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.driver.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeDriver(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message: this.driver.driver_name + " is removed successfully !",
            });
          } catch (e) {
            Loading.hide();
          }
          this.$router.push("/dashboard/drivers");
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    }
  },
};
</script>
