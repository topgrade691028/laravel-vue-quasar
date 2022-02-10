<template>
  <q-layout view="lHh lpr lFf" class="shadow-2 rounded-borders">
    <q-header bordered class="bg-white text-primary">
      <q-toolbar>
        <router-link to="/">
          <q-img :src="require('../../assets/images/logo.png')" style="width: 150px;" />
        </router-link>
        <q-toolbar-title></q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div class="q-pa-md" style="margin-top: 63px;">
      <q-form
        style="max-width: 500px; border-radius: 10px; margin-top: 150px"
        class="q-mx-auto"
      >
        <div class="row justify-between q-col-gutter-md" >
          <div class="col-12">
            <h5 class="q-my-none">Forgot Your Password</h5>
          </div>
          <div class="col-12">
            <p class="q-my-none">
              Please enter your email address below to receive a link to reset your password.
            </p>
          </div>
          <div class="col-12 col-sm-8">
            <q-input outlined required label="Email" v-model="email" type="email" color="cyan-7"></q-input>
          </div>
          <div class="col-12">
            <q-btn outlined color="cyan-7" @click="forgotPassword">Reset</q-btn>
          </div>
          <div class="col-12">
            <q-btn outlined flat color="cyan-7">Back To Login</q-btn>
          </div>
        </div>
      </q-form>
    </div>
  </q-layout>
</template>

<script>
import { Loading } from 'quasar'
import { api } from 'src/boot/api'
export default {
  name: 'ForgotPassword',
  data () {
    return {
      email: ''
    }
  },
  methods: {
    async forgotPassword () {
      Loading.show()
      try {
        let res = await api.forgotPassword({ email: this.email })
        Loading.hide()
        this.$q.notify({
          color: 'positive',
          position: 'top',
          message: res.data.success
        })
      } catch (error) {
        Loading.hide()
      }
    }
  }
}
</script>
