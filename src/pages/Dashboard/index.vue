<template>
  <q-page padding>
    <!-- content -->
  </q-page>
</template>

<script>
export default {
  name: 'Index',
  mounted () {
    this.$store.commit('auth/pageTitle', 'Dashboard')
  }
}
</script>
