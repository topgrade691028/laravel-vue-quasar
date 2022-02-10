<template>
  <q-page class="q-mt-none" style="background-color: #3e444e">
    <!-- content -->
    <!-- <div class="bg-light-blue" style="width: 100%; height: 150px; position: absolute">
    </div> -->
    <template>
      <div>
        <!-- <template>
          <div class="row items-center justify-between text-white" style="height: 150px; position: relative">
            <div class="items-center" style="font-size: 20px;">
              <q-btn
                class="bg-white text-primary shadow-3 q-btn--push q-mr-md"
                rounded
                @click="$router.push('/dashboard/users')"
                :icon=" 'fas fa-arrow-left' "
              />
              <span>{{isNewPage ? 'New User' : 'User Detail'}}</span>
            </div>
          </div>
        </template> -->
        <div>
          <q-bar style="background-color: #3e444e">
            <q-btn
              dense
              flat
              icon="close"
              color="white"
              @click="$router.push('/dashboard/users')"
            >
            </q-btn>
            <div class="text-h6 text-white">
              {{ isNewPage ? "Add User" : "Edit User" }}
            </div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3e444e">
          <q-form
            @submit="onSubmit"
            :model="userData"
            ref="userForm"
            class="text-center q-px-md"
            style="max-width: 400px; width: 100%; margin: auto"
          >
            <q-card style="background-color: #3e444e; box-shadow: none">
              <!--<div class="col-12 row items-center justify-center">-->
              <!--<q-uploader-->
              <!--color="teal"-->
              <!--flat-->
              <!--bordered-->
              <!--accept=".jpg, .png, image/*"-->
              <!--ref="uploader"-->
              <!--auto-expand-->
              <!--label="Upload Avatar"-->
              <!--hide-upload-btn-->
              <!--@added="addAvatar"-->
              <!--/>-->
              <!--</div>-->
              <!-- <q-card-section class="text-left q-pb-none">
                <span class="text-white">Name</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.full_name"></q-input>
              </q-card-section> -->
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Full Name</span>
                <q-input
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  input-class="text-black text-center"
                  v-model="userData.name"
                ></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mobile Phone</span>
                <q-input
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  input-class="text-black text-center"
                  v-model="userData.phone"
                ></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Depot Location</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.depot_location"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Email</span>
                <q-input
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  input-class="text-black text-center"
                  v-model="userData.email"
                ></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
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
                  v-model="userData.password"
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-account-key" />
                  </template>
                </base-text-field>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Confirm</span>
                <base-text-field
                  dense
                  outlined
                  required
                  bg-color="white"
                  color="blue-7"
                  class="q-pb-md"
                  v-model="userData.confirmPassword"
                  normalize-bottom
                  icon="mdi-card"
                  clearable
                  type="password"
                  :rules="[
                    (val) =>
                      val === userData.password || 'Password is not match',
                  ]"
                  hide-show-password
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-account-key" />
                  </template>
                </base-text-field>
              </q-card-section>
              <q-card-section class="text-left q-py-none row">
                <div class="col-12">
                  <q-checkbox
                    @input="selectAll"
                    :value="allSelected"
                    label="ALL PLATFORM"
                    color="grey"
                    class="q-mx-auto text-white"
                    dark
                  >
                  </q-checkbox>
                  <q-option-group
                    type="checkbox"
                    color="grey"
                    class="q-mx-auto text-white"
                    dark
                    inline
                    :options="roleOptions"
                    v-model="userData.user_roles"
                  >
                  </q-option-group>
                  <!-- <q-option-group
                    v-model="userData.user_roles"
                    :options="roleOptions"
                    color="grey"
                    class="q-mx-auto text-white"
                    dark
                    type="checkbox"
                  /> -->
                </div>
              </q-card-section>
              <q-card-actions align="center">
                <div class="q-px-md">
                  <q-btn
                    color="blue-7"
                    :label="isNewPage ? 'Add New' : 'Update'"
                    no-caps
                    dense
                    rounded
                    style="width: 100px; height: 40px"
                    type="submit"
                  />
                  &nbsp;
                  <q-btn
                    no-caps
                    dense
                    rounded
                    v-if="userLevel === 'admin'"
                    :label="userData.is_active ? 'DEACTIVATE' : 'ACTIVATE'"
                    color="blue-7"
                    @click="activeUser()"
                    style="width: 100px; height: 40px"
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
  name: "NewUser",
  data() {
    return {
      userData: {
        id: "",
        parent_id: "",
        user_roles: [],
        name: "",
        password: "",
        confirmPassword: "",
        phone: "",
        email: "",
        full_name: "",
        depot_location: "",
        belongs: "",
        zipcode: "",
        user_type: 2,
        is_active: 0,
        subcontractor: ""
      },
      userAvatar: {},
      isNewPage: false,
      roleOptions: [
        { label: "SCHEDULE", value: "schedules" },
        { label: "PERFORMANCE", value: "performance" },
        { label: "DRIVERS", value: "drivers" },
        { label: "ROUTES", value: "routes" },
      ]
    };
  },
  async created() {
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    this.isNew();
    if (!this.isNewPage) {
      await this.getUserInfo(this.userData.id);
    }
  },
  computed: {
    selectLabel() {
      return this.allSelected ? "DESELECT ALL" : "SELECT ALL";
    },
    allSelected() {
      return this.userData.user_roles.length === this.roleOptions.length;
    },
    userLevel: {
      get() {
        return this.$store.state.auth.userLevel;
      },
    },
    user: {
      get() {
        return this.$store.state.auth.user;
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
        this.userData.id = this.$router.currentRoute.params.id;
      } else {
        this.isNewPage = true;
      }
    },
    selectAll: function () {
      if (this.allSelected) {
        this.userData.user_roles = [];
      } else {
        this.userData.user_roles = this.roleOptions.map(
          (option) => option.value
        );
      }
    },
    getUserInfo: async function (id) {
      Loading.show();
      try {
        let res = await api.getUserInfo(id);
        this.userData = res.data.user;
        if (this.userData.user_roles) {
          this.userData.user_roles = this.userData.user_roles.split(",");
        } else {
          this.userData.user_roles = [];
        }
        console.log("user details", this.userData.user_roles);
        Loading.hide();
      } catch (e) {
        Loading.hide();
        this.$router.push("/dashboard/users");
      }
    },
    addAvatar(files) {
      this.userAvatar = files[0];
    },
    async onSubmit() {
      if (this.isNewPage) {
        Loading.show();
        try {
          this.userData.subcontractor = this.user.subcontractor;
          let res = await api.registerUser(this.userData);
          console.log("res", res.data);
          Loading.hide();
          this.$q.notify({
            color: "positive",
            textColor: "white",
            position: "top",
            message: "User is registered successfully",
          });
          this.$router.push("/dashboard/users");
        } catch (error) {
          Loading.hide();
        }
      } else {
        const params = {
          data: this.userData,
        };
        params.conditions = {
          id: this.userData.id,
        };
        Loading.show();
        try {
          let res = await api.updateUser(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.$router.push("/dashboard/users");
      }
    },
    async activeUser() {
      const params = {
        is_active: !this.userData.is_active,
      };
      params.conditions = {
        id: this.userData.id,
      };
      Loading.show();
      try {
        let res = await api.activeUser(params);
        console.log("res", res.data);
        Loading.hide();
        this.$q.notify({
          color: "positive",
          textColor: "white",
          position: "top",
          message: !this.userData.is_active
            ? "User is activated"
            : "User is deactivated",
        });
        this.$router.push("/dashboard/users");
      } catch (error) {
        Loading.hide();
      }
    }
  },
};
</script>
