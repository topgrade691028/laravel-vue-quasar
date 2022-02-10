<template>
  <q-page class="q-mt-none" style="background-color: #3E444E">
    <template>
      <div>
        <div>
          <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/clients')">
            </q-btn>
            <div class="text-h6 text-white">{{isNewPage ? 'Add Client' : 'Edit Client'}}</div>
          </q-bar>
        </div>
        <div class="q-px-md" style="background-color: #3E444E">
          <q-form
            @submit="onSubmit"
            :model="userData"
            ref="userForm"
            class="text-center q-px-md"
            style="max-width: 400px; width:100%; margin: auto;"
          >
            <q-card style="background-color: #3E444E; box-shadow: none">
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Full Name</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.name"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Company</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.belongs"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Depot Location</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.depot_location"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Mobile</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.phone"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Sub-Contractor</span>
                <q-select
                  outlined
                  dense
                  label-color="black"
                  color="blue-7"
                  bg-color="white"
                  class="q-pb-md"
                  v-model="userData.subcontractor"
                  :options="subContractors"
                  emit-value
                  map-options
                  required
                />
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Address</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.address"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">ZipCode</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.zipcode"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Country</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.country"></q-input>
              </q-card-section>
              <q-card-section class="text-left q-py-none">
                <span class="text-white">Email</span>
                <q-input dense outlined required bg-color="white" color="blue-7" class="q-pb-md" input-class="text-black text-center" v-model="userData.email"></q-input>
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
                  :rules="[val => val === userData.password  || 'Password is not match']"
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
                    color="blue-7"
                    :label="isNewPage ? 'Add New' : 'Update'"
                    no-caps
                    dense
                    rounded
                    style="width: 100px; height:40px"
                    type="submit"
                  /> &nbsp;
                  <q-btn
                    v-if="!isNewPage == true"
                    no-caps
                    dense
                    rounded
                    :label="userData.is_active ? 'DEACTIVATE' : 'ACTIVATE'"
                    color="blue-7"
                    @click="activeUser()"
                    style="width: 100px; height:40px;"
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
import { Loading } from 'quasar'
import { api } from 'src/boot/api'
export default {
  name: 'NewUser',
  data () {
    return {
      userData: {
        id: '',
        depot_location: '',
        parent_id: '',
        user_roles: '',
        name: '',
        password: '',
        confirmPassword: '',
        phone: '',
        email: '',
        full_name: '',
        belongs: '',
        country: '',
        address: '',
        zipcode: '',
        user_type: 1,
        is_active: 0,
        subcontractor: 'Regular'
      },
      subContractors: [
        'DPD', 'Regular'
      ],
      userAvatar: {},
      isNewPage: false
    }
  },
  async created () {
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.isNew()
    if (!this.isNewPage) {
      await this.getUserInfo(this.userData.id)
    }
  },
  methods: {
    isNew () {
      if (this.$router.currentRoute.params.id !== null && this.$router.currentRoute.params.id !== undefined && this.$router.currentRoute.params.id !== '') {
        this.isNewPage = false
        this.userData.id = this.$router.currentRoute.params.id
      } else {
        this.isNewPage = true
      }
    },
    getUserInfo: async function (id) {
      Loading.show()
      try {
        let res = await api.getUserInfo(id)
        this.userData = res.data.user
        console.log('user details', res.data.user)
        Loading.hide()
      } catch (e) {
        Loading.hide()
        this.$router.push('/dashboard/clients')
      }
    },
    addAvatar (files) {
      this.userAvatar = files[0]
    },
    async onSubmit () {
      if (this.isNewPage) {
        Loading.show()
        try {
          let res = await api.registerUser(this.userData)
          console.log('res', res.data)
          Loading.hide()
          this.$q.notify({
            color: 'positive',
            textColor: 'white',
            position: 'top',
            message: 'Client is registered successfully'
          })
          this.$router.push('/dashboard/clients')
        } catch (error) {
          Loading.hide()
        }
      } else {
        const params = {
          data: this.userData
        }
        params.conditions = {
          id: this.userData.id
        }
        Loading.show()
        try {
          let res = await api.updateUser(params)
          Loading.hide()
          console.log('result', res.data)
          this.$q.notify({
            color: 'positive',
            textColor: 'white',
            position: 'top',
            message: 'Client is updated successfully'
          })
          this.$router.push('/dashboard/clients')
        } catch (error) {
          Loading.hide()
          console.log('error', error)
        }
      }
    },
    async activeUser () {
      const params = {
        is_active: !this.userData.is_active
      }
      params.conditions = {
        id: this.userData.id
      }
      Loading.show()
      try {
        let res = await api.activeUser(params)
        console.log('res', res.data)
        Loading.hide()
        this.$q.notify({
          color: 'positive',
          textColor: 'white',
          position: 'top',
          message: !this.userData.is_active ? 'Client is activated' : 'Client is deactivated'
        })
        this.$router.push('/dashboard/clients')
      } catch (error) {
        Loading.hide()
      }
    }
  }
}
</script>
