<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <!-- content -->

    <!-- <div class="bg-light-blue" style="width: 100%; height: 150px; position: absolute">
    </div> -->
    <template>
      <div>
        <q-bar style="background-color: #3E444E">
          <q-btn dense flat icon="close" color="white" @click="backToPage">
          </q-btn>
          <div class="text-h6 text-white">Edit Profile</div>
        </q-bar>
      </div>
      <div class="q-pa-md" style="background-color: #3E444E">
        <!-- <template>
          <div class="row items-center justify-between text-white" style="height: 150px; position: relative">
            <div class="items-center" style="font-size: 20px;">
              <q-btn
                class="bg-white text-primary shadow-3 q-btn--push q-mr-md"
                rounded
                @click="$router.push('/dashboard')"
                :icon=" 'fas fa-arrow-left' "
              />
              <span>Dashboard</span>
            </div>
          </div>
        </template> -->
        <q-form
          @submit="onSubmit"
          rounded
          class="text-center q-pa-md"
          ref="userForm"
          :model="userForm"
          style="max-width: 400px; width:100%; border-radius: 10px; margin: auto"
        >
          <q-card style="background-color: #3E444E; box-shadow: none">
            <!-- <q-card-section class="text-left q-pb-none">
              <span class="text-white">Name</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.full_name"></q-input>
            </q-card-section> -->
            <q-card-section class="text-left q-py-none">
              <span class="text-white">Full Name</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.name"></q-input>
            </q-card-section>
            <q-card-section class="text-left q-py-none">
              <span class="text-white">Mobile Phone</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.phone"></q-input>
            </q-card-section>
            <q-card-section class="text-left q-py-none">
              <span class="text-white">Depot Location</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.belongs"></q-input>
            </q-card-section>
            <q-card-section class="text-left q-py-none">
              <span class="text-white">Email</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.email"></q-input>
            </q-card-section>
            <!-- <q-card-section class="text-left q-py-none">
              <span class="text-white">ZipCode</span>
              <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userForm.zipcode"></q-input>
            </q-card-section> -->
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
                v-model="userForm.password"
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
                v-model="userForm.confirmPassword"
                normalize-bottom
                icon="mdi-card"
                clearable
                type="password"
                :rules="[val => val === userForm.password  || 'Password is not match']"
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
                  label="Logout"
                  color="blue-7"
                  @click="logout()"
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
          <!-- <q-page-sticky position="bottom-right" :offset="[108, 18]">
            <q-btn fab icon="save" color="purple-7" type="submit" />
          </q-page-sticky> -->
        </q-form>
      </div>
    </template>
    <!-- place QPageScroller at end of page -->
    <q-page-scroller position="bottom-right" :scroll-offset="150" :offset="[18, 18]">
      <q-btn fab icon="keyboard_arrow_up" color="purple-7" />
    </q-page-scroller>
  </q-page>
</template>

<script>

import { api } from 'src/boot/api'
import { Loading } from 'quasar'

export default {
  name: 'Profile',
  data: function () {
    return {
      isNewPage: false,
      userForm: {
        full_name: '',
        name: '',
        phone: '',
        email: '',
        zipcode: ''
      },
      currentUserID: ''
    }
  },
  computed: {
    user: {
      get() {
        return this.$store.state.auth.user;
      },
    },
    userType: {
      get() {
        return this.$store.state.auth.userType;
      },
    },
    userSubcontractor: {
      get () {
        return this.$store.state.auth.userSubcontractor
      }
    }
  },
  created () {
    // check this is create page or update existing vehicle page
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.getUserInfo()
  },
  methods: {
    logout () {
      this.$store.commit('auth/token', '')
      this.$store.commit('auth/setDpdSid', '')
      this.$store.commit('auth/user', {})
      this.$router.push('/login')
    },
    getUserInfo: async function () {
      const params = {}
      Loading.show()
      try {
        let res = await api.getMyProfile(params)
        this.userForm = res.data.user
        // ...and turn of loading indicator
        Loading.hide()
      } catch (e) {
        Loading.hide()
      }
    },
    async onSubmit () {
      const params = {
        data: this.userForm
      }
      try {
        let res = await api.updateUserInfo(params)
        console.log('res', res)
        this.$q.notify({
          color: 'positive',
          position: 'top',
          textColor: 'white',
          message: 'User is updated successfully'
        })
        // this.$router.push('/dashboard/schedules')
      } catch (e) {
        // this.$router.push('/dashboard/schedules')
      }
    },
    backToPage () {
      if (this.userType !== 'admin') {
        if (this.userSubcontractor === "Regular") {
          this.$router.push("/dashboard/schedules");
        } else {
          this.$router.push("/dashboard/dpd-modules");
        }
      } else {
        this.$router.push("/dashboard/users");
      }
    }
  }
}
</script>

<style>
</style>
