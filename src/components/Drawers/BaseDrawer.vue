<template>
  <div>
    <!-- <q-item-label header>Menu</q-item-label> -->
    <q-item
      clickable
      to="/dashboard/clients"
      v-if="userLevel==='admin'"
      active-class="router--active"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/001-group.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section>
        <q-item-label style="color: white">CLIENTS</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-item
      clickable
      to="/dashboard/users"
      v-if="(userLevel==='admin' || userLevel==='client') && userSubcontractor !== 'DPD'"
      active-class="router--active"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/001-group.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section>
        <q-item-label style="color: white">USERS</q-item-label>
      </q-item-section>
    </q-item> -->
    <q-item
      clickable
      to="/dashboard/schedules"
      active-class="router--active"
      v-if="((userLevel!=='driver') && userSubcontractor !== 'DPD')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/003-calendar.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>SCHEDULE</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-separator color="white" v-if="(userLevel!=='driver' && userLevel !== 'user') || checkUserRoles('schedules')"/> -->
    <q-item
      clickable
      to="/dashboard/performance"
      active-class="router--active"
      v-if="((userLevel!=='driver') && userSubcontractor !== 'DPD')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/004-graphic.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>PERFORMANCE</q-item-label>
      </q-item-section>
    </q-item>
    <q-item
      clickable
      to="/dashboard/dpd-modules"
      active-class="router--active"
      v-if="((userLevel!=='driver') && userSubcontractor === 'DPD')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/003-calendar.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>SCHEDULE</q-item-label>
      </q-item-section>
    </q-item>
    <q-item
      clickable
      to="/dashboard/dpd-performance"
      active-class="router--active"
      v-if="((userLevel!=='driver') && userSubcontractor === 'DPD')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/004-graphic.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>PERFORMANCE</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-separator color="white" v-if="(userLevel!=='driver' && userLevel !== 'user') || checkUserRoles('performance')"/> -->
    <q-item
      clickable
      to="/dashboard/drivers"
      active-class="router--active"
      v-if="(userLevel!=='driver')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/005-driving.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>DRIVERS</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-separator color="white" v-if="(userLevel!=='driver' && userLevel !== 'user') || checkUserRoles('drivers')"/> -->
    <q-item
      clickable
      to="/dashboard/routes"
      active-class="router--active"
      v-if="((userLevel!=='driver') && userSubcontractor !== 'DPD')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/006-way.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>ROUTES</q-item-label>
      </q-item-section>
    </q-item>
    <q-item
      clickable
      to="/dashboard/fleets"
      active-class="router--active"
      v-if="(userLevel!=='driver')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/fleet.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>FLEET</q-item-label>
      </q-item-section>
    </q-item>
    <q-item
      clickable
      to="/dashboard/driver-services"
      active-class="router--active"
      v-if="(userLevel!=='driver')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/service_records_icon.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>SERVICE</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-item
      clickable
      v-if="(userLevel==='driver')"
      to="/dashboard/driver-performance"
      active-class="router--active"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/odometer_records_icon.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>PERFORMANCE</q-item-label>
      </q-item-section>
    </q-item> -->
    <q-item
      clickable
      to="/dashboard/odometer-records"
      active-class="router--active"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/odometer_records_icon.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>ODOMETER</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-separator color="white" v-if="(userLevel!=='driver')"/> -->
    <q-item
      clickable
      to="/dashboard/fuels"
      active-class="router--active"
      v-if="(userLevel!=='driver')"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="25px" :src="require('../../assets/images/icons/fuel.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>FUEL</q-item-label>
      </q-item-section>
    </q-item>
    <!-- <q-separator color="white" v-if="(userLevel!=='driver')"/> -->
    <q-item
      clickable
      to="/dashboard/driver-performance"
      active-class="router--active"
      v-if="(userLevel==='driver') && userSubcontractor !== 'DPD'"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/004-graphic.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>PERFORMANCE</q-item-label>
      </q-item-section>
    </q-item>
    <q-item
      clickable
      to="/dashboard/dpd-driver-performance"
      active-class="router--active"
      v-if="(userLevel==='driver') && userSubcontractor === 'DPD'"
      style="border-bottom: solid 1px white;"
    >
      <q-item-section avatar>
        <q-icon>
          <img width="22px" :src="require('../../assets/images/icons/004-graphic.svg')">
        </q-icon>
      </q-item-section>
      <q-item-section class="text-white">
        <q-item-label>PERFORMANCE</q-item-label>
      </q-item-section>
    </q-item>
  </div>
</template>

<style lang="stylus">
.router--active
  color: var(--q-color-blue-7)
</style>

<script>
export default {
  name: 'BaseDrawer',
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    userLevel: {
      get () {
        return this.$store.state.auth.userLevel
      }
    },
    userRoles: {
      get () {
        return this.$store.state.auth.userRoles
      }
    },
    userSubcontractor: {
      get () {
        return this.$store.state.auth.userSubcontractor
      }
    }
  },
  methods: {
    checkUserRoles (pageURL) {
      if (this.userRoles)
        return this.userRoles.includes(pageURL)
      else return false
    }
  }
}
</script>

<style>
</style>
