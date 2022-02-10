<template>
  <div id="bgImage" style="display: flex">
    <q-header bordered class="bg-white text-primary">
      <q-toolbar reveal >
        <!-- <router-link to="/">
          <div class="q-py-sm q-mx-lg">
            <q-img :src="require('../../assets/images/ukcourier_logo.svg')" style="max-height:60px; width: 100%" />
          </div>
        </router-link> -->
        <q-toolbar-title></q-toolbar-title>
        <q-btn label="ABOUT US" color="black" flat :to="{name: 'AboutUS'}"/>
        <q-btn label="FAQ" color="black" flat :to="{name: 'Faq'}"/>
        <q-btn label="DRIVER" color="black" flat :to="{name: 'Driver'}"/>
        <q-btn label="SIGN UP" color="black" flat :to="{name: 'Signup'}"/>
        <q-btn label="LOG IN" color="black" flat :to="{name: 'Login'}"/>
      </q-toolbar>
    </q-header>
    <div class="q-pa-md text-center" style="margin: auto">
      <q-form
        @submit="login"
        class="text-center q-pa-md q-mx-auto"
        style="max-width: 400px; border-radius: 10px; background-color: #42424296"
      >
        <div class="q-my-md" >
          <img :src="require('../../assets/images/ukcourier_logo.svg')" style="max-height:60px; width: 100%" />
        </div>
        <div class="row justify-between q-col-gutter-md" >
          <div class="col-12">
            <q-input outlined rounded borderless label-color="black" color="white" bg-color="white" v-model="userData.email" clearable placeholder="E-Mail" class="q-ml-none">
              <template v-slot:prepend>
                <q-icon name="account_circle" />
              </template>
            </q-input>
          </div>
          <div class="col-12">
            <base-text-field
              outlined
              rounded
              borderless
              label-color="black"
              color="white"
              bg-color="white"
              v-model="userData.password"
              normalize-bottom
              placeholder="Password"
              icon="mdi-card"
              clearable
              class="q-ml-none q-pb-none"
              type="password"
              hide-show-password
            >
              <template v-slot:prepend>
                <q-icon name="mdi-account-key" />
              </template>
            </base-text-field>
          </div>
          <div class="col-6">
            <q-radio v-model="login_type" val="user" label="USER" class="q-mx-auto text-white" color="white" keep-color />
          </div>
          <div class="col-6">
            <q-radio v-model="login_type" val="driver" label="DRIVER" class="q-mx-auto text-white" color="white" keep-color />
          </div>
        </div>
        <!-- <q-item class="q-mx-none q-px-none">
          <q-item-section>
            <router-link to="/">
              Forgot your password?
            </router-link>
          </q-item-section>
        </q-item> -->
        <q-btn type="submit" rounded color="blue-7" class="full-width text-white q-my-md">
          Sign In
        </q-btn>
        <!-- <q-btn :to="{name: 'Signup'}" rounded color="blue-7" class="full-width text-white q-mb-md">
          Sign Up
        </q-btn> -->
      </q-form>
    </div>
  </div>
</template>

<script>
import { Loading } from 'quasar'
import { api } from 'src/boot/api'
export default {
  name: 'Login',
  data () {
    return {
      userData: {
        email: '',
        password: ''
      },
      login_type: 'user',
      accept: false,
      remember: false
    }
  },
  created () {
    // console.log('current Route', this.$router.currentRoute)
    this.$q.dark.set(false)
  },
  methods: {
    async login () {
      Loading.show()
      try {
        if (this.login_type === 'user') {
          let res = await api.login(this.userData)
          Loading.hide()
          this.$q.notify({
            color: 'positive',
            textColor: 'white',
            position: 'top',
            message: 'User is logged in successfully'
          })
          this.$store.commit('auth/token', res.data.token)
          this.$store.commit('auth/user', res.data.user)
          let userType = res.data.user.user_type === 0 ? 'admin' : (res.data.user.user_type === 1 ? 'client' : 'user')
          this.$store.commit('auth/userLevel', userType)
          this.$store.commit('auth/userRoles', res.data.user.user_roles)
          this.$router.push('/dashboard/schedules')
        } else {
          let res = await api.loginDriver(this.userData)
          Loading.hide()
          this.$q.notify({
            color: 'positive',
            textColor: 'white',
            position: 'top',
            message: 'Driver is logged in successfully'
          })
          this.$store.commit('auth/token', res.data.token)
          this.$store.commit('auth/driver', res.data.driver)
          this.$store.commit('auth/userLevel', 'driver')
          this.$router.push('/dashboard/driver-performance')
        }
      } catch (error) {
        console.log('error', error)
        Loading.hide()
      }
    }
  }
}
</script>
