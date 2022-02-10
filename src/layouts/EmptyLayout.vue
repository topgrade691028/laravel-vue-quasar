<template>
  <q-layout view="lHh lpr lFf" class="shadow-2 rounded-borders">
    <q-header bordered class="bg-white text-primary" v-if="!token">
      <!-- <q-toolbar>
        <router-link to="/">
          <div class="q-py-sm q-mx-lg">
            <q-img :src="require('../assets/images/logo.png')" style="width: 150px; height: 50px;" />
          </div>
        </router-link>
        <q-toolbar-title> </q-toolbar-title>
        <q-btn label="Login" color="green-8" flat :to="{name: 'Login'}"/>
        <q-btn label="Signup" color="green-8" flat :to="{name: 'Signup'}"/>
      </q-toolbar> -->
    </q-header>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'EmptyLayout',
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    token: {
      get () {
        return this.$store.state.auth.token
      }
    }
  }
}
</script>
