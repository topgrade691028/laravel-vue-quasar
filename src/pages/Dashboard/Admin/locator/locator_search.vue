<template>
  <q-page class="q-mt-none" style="background-color: #3e444e">
    <template>
      <div>
        <div>
          <!-- <q-bar style="background-color: #3E444E">
            <q-btn dense flat icon="close" color="white" @click="$router.push('/dashboard/clients')">
            </q-btn>
            <div class="text-h6 text-white">DPD SEARCH</div>
          </q-bar> -->
        </div>
        <div class="q-px-md" style="background-color: #3e444e">
          <div class="q-py-sm">
            <div style="max-width: 400px; position: relative;" class="q-mx-auto">
              <div style="display:flex; align-items: center;">
                <q-input
                  dense
                  debounce="300"
                  v-model="filter"
                  placeholder="Enter PostCode"
                  input-class="text-white border-white"
                  style="width: 90%"
                  color="blue-7"
                >
                  <template v-slot:append>
                    <q-btn icon="search" color="grey" @click="searchLocator(filter)" />
                  </template>
                </q-input>
                <q-btn icon="close" class="q-ml-xs" style="width: 36px; height: 36px;" size="12px" color="grey" @click="clearMarkers()" />
              </div>
              <div v-if="locations.length" style="width:100%; position: absolute; z-index: 999;" class="text-right bg-white">
                <q-scroll-area style="height: calc(100vh - 200px); max-height: 600px;">
                  <q-btn round size="6px" icon="close" color="red" class="q-mr-sm" @click="resetLocations"/>
                  <q-list separator class="text-left">
                    <q-item dense clickable v-ripple v-for="location in locations" :key="location.name" @click="showMarker(location)">
                      <q-item-section>{{location.name}}</q-item-section>
                    </q-item>
                  </q-list>
                </q-scroll-area>
              </div>
            </div>
          </div>
          <gmap-map
            :center="center"
            :zoom="12"
            style="width:100%;  height: calc(100vh - 120px);"
            id="map"
          >
            <gmap-marker
              :position="mylocation"
              @click="moveCenter(mylocation)"
              :icon="{url: require('../../../../assets/images/icons/my-location-marker.png'), size: {width: 30, height: 30, f: 'px', b: 'px',}, scaledSize: {width: 30, height: 30, f: 'px', b: 'px',}}"
            ></gmap-marker>
            <gmap-marker
              :key="index"
              v-for="(marker, index) in markers"
              :position="marker.position"
              :label="marker.label"
              @click="moveCenter(marker.position)"
            ></gmap-marker>
          </gmap-map>
        </div>
      </div>
    </template>
  </q-page>
</template>

<script>
// import { Loading } from 'quasar'
import { api } from 'src/boot/api'
// import L from 'leaflet'
// import Util from 'src/boot/mapUtil'
// import { LMap, LTileLayer } from 'vue2-leaflet'
import cheerio from 'cheerio'
import { gmapApi } from 'vue2-google-maps'

export default {
  name: 'LOCATORSEARCH',
  components: {
  },
  data () {
    return {
      filter: '',
      zoom: 5,
      center: {},
      markers: [],
      locations: [],
      mylocation: {}
    }
  },
  computed: {
    google: gmapApi
  },
  async created () {
    this.$store.commit('auth/pageTitle', this.$router.currentRoute.meta.title)
    this.getCurrentPosition()
  },
  methods: {
    async moveCenter (position) {
      this.center = position
      this.zoom = 10
    },
    async getLatLng (coordinates) {
      let res = await api.convertbng2latlong(coordinates)
      return res.data
    },
    async searchLocator (filter) {
      if (filter) {
        this.locations = []
        const params = {
          searchterm: filter
        }
        let res = await api.getLocatorResults(params)
        const $ = cheerio.load(res.data)
        this.locations = await Promise.all(
          $('a').map(async function (i, item) {
            let coordinates = $(this).attr('coords_1').split(',')[0].split(' ')
            const coords = {
              northing: Math.round(coordinates[0]),
              easting: Math.round(coordinates[1])
            }
            let latlng = await api.convertbng2latlong(coords)
            const placeInfo = {
              name: $(this).text(),
              type: $(this).attr('category'),
              lat: latlng.data.latitude,
              lng: latlng.data.longitude
            }
            return placeInfo
          })
        )
        this.zoom = 5
      }
    },
    resetLocations () {
      this.locations = []
      this.zoom = 5
    },
    containsMarker (newMarker) {
      for (let i = 0; i < this.markers.length; i++) {
        if (this.markers[i].position.lat === newMarker.lat && this.markers[i].position.lng === newMarker.lng) {
          return true
        }
      }
      return false
    },
    showMarker (location) {
      this.center = {
        lat: location.lat,
        lng: location.lng
      }
      this.zoom = 10
      if (!this.containsMarker(this.center)) {
        let newMarker = {
          position: {
            lat: location.lat,
            lng: location.lng
          },
          label: (this.markers.length + 1).toString()
        }
        this.markers.push(newMarker)
      }
      this.locations = []
    },
    getCurrentPosition () {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const pos = {
              lat: Math.round(position.coords.latitude),
              lng: Math.round(position.coords.longitude)
            }
            this.mylocation = pos
            this.center = pos
          },
          () => {
            this.handleLocationError(true, this.mylocation)
          }
        )
      } else {
        // Browser doesn't support Geolocation
        this.handleLocationError(false, this.mylocation)
      }
    },
    handleLocationError (browserHasGeolocation, pos) {
      console.log('error location')
    },
    clearMarkers () {
      this.markers = []
      this.center = this.mylocation
      this.filter = ''
    }
  }
}
</script>
