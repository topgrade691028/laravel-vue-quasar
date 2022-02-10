<template>
  <div id="bgImage" style="display:flex">
    <q-header bordered class="bg-white text-primary">
      <q-toolbar reveal >
        <!-- <router-link to="/">
          <div class="q-py-sm q-mx-lg">
            <q-img :src="require('../../assets/images/ukcourier_logo.svg')" style="max-height:60px; width: 100%" />
          </div>
        </router-link> -->
        <q-toolbar-title></q-toolbar-title>
        <q-btn label="ABOUT US" color="black" flat :to="{name: 'AboutUS'}"/>
        <q-btn label="SIGN UP" color="black" flat :to="{name: 'Signup'}"/>
        <q-btn label="LOG IN" color="black" flat :to="{name: 'Login'}"/>
      </q-toolbar>
    </q-header>
    <div class="q-pa-md text-center" style="margin: auto">
      <q-form
        @submit="registerUser"
        class="text-center q-pa-md q-mx-auto"
        style="max-width: 600px; border-radius: 10px; background-color: #42424296"
      >
        <div class="q-my-md" >
          <img :src="require('../../assets/images/ukcourier_logo.svg')" style="max-height:60px; width: 100%" />
        </div>
        <div class="row justify-between q-col-gutter-md" >
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
          <!-- <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Full Name" v-model="user.full_name"></q-input>
          </div> -->
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" placeholder="Full Name" v-model="user.name"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Company" v-model="user.belongs"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Mobile Phone" v-model="user.phone"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-select
              outlined
              rounded
              dense
              label-color="black"
              color="black"
              bg-color="white"
              v-model="user.subcontractor"
              :options="subContractors"
              emit-value
              map-options
              required
              label="Sub-Contractor"
            />
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Address" v-model="user.address"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Post Code" v-model="user.zipcode"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Country" v-model="user.country"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" placeholder="Email" type="email" v-model="user.email"></q-input>
          </div>
          <div class="col-12 col-sm-6">
            <base-text-field
              outlined
              rounded
              borderless
              dense
              label-color="black"
              color="white"
              bg-color="white"
              v-model="user.password"
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
          <div class="col-12 col-sm-6">
            <base-text-field
              outlined
              rounded
              borderless
              dense
              label-color="black"
              color="white"
              bg-color="white"
              v-model="user.confirmPassword"
              normalize-bottom
              placeholder="Confirm Password"
              icon="mdi-card"
              clearable
              class="q-ml-none q-pb-none"
              type="password"
              :rules="[val => val === user.password  || 'Password is not match']"
              hide-show-password
            >
              <template v-slot:prepend>
                <q-icon name="mdi-account-key" />
              </template>
            </base-text-field>
          </div>
          <div class="col-12 col-sm-6">
            <q-input outlined rounded borderless dense label-color="black" color="white" bg-color="white" required placeholder="Depot Location" v-model="user.depot_location"></q-input>
          </div>
          <div class="col-12">
            <q-btn type="submit" rounded color="blue-7" class="full-width text-white q-mb-md">
              Sign Up
            </q-btn>
            <!-- <q-btn :to="{name: 'Login'}" rounded color="blue-7" class="full-width text-white q-mb-md">
              Sign In
            </q-btn> -->
          </div>
        </div>
      </q-form>
    </div>
  </div>
</template>

<script>
import { Loading } from 'quasar'
import { api } from 'src/boot/api'
export default {
  name: 'SignUp',
  data () {
    return {
      user: {
        name: '',
        password: '',
        confirmPassword: '',
        phone: '',
        email: '',
        full_name: '',
        address: '',
        country: '',
        zipcode: '',
        is_active: 0,
        user_type: 1,
        belongs: '',
        parent_id: '',
        subcontractor: 'Regular',
        depot_location: ''
      },
      subContractors: [
        'DPD', 'Regular'
      ],
      userAvatar: {},
      accept: false
    }
  },
  created () {
  },
  methods: {
    addAvatar (files) {
      this.userAvatar = files[0]
    },
    async registerUser () {
      Loading.show()
      try {
        // if (Object.keys(this.userAvatar).length !== 0) {
        //   const fd = new FormData()
        //   fd.append('image', this.userAvatar)
        //   const headers = 'Content-Type = multipart/form-data'
        //   let res1 = await api.uploadUserAvatar(fd, headers)
        //   this.user.avatar = res1.data.path
        // } else {
        //   this.user.avatar = ''
        // }
        try {
          let res = await api.registerUser(this.user)
          // LocalStorage.setItem('user', res.data)
          console.log('res', res.data)
          Loading.hide()
          this.$q.notify({
            color: 'positive',
            textColor: 'white',
            position: 'top',
            message: 'User is registered successfully'
          })
          this.$router.push('/')
        } catch (error) {
          Loading.hide()
        }
      } catch (error) {
        Loading.hide()
      }
    }
  }
}
</script>
