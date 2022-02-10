<template>
  <q-layout view="lHh lpr lFf" class="shadow-2 rounded-borders">
    <q-header bordered class="bg-white text-primary">
      <q-toolbar reveal >
        <router-link to="/">
          <div class="q-py-sm q-mx-lg">
            <q-img :src="require('../assets/images/ukcourier_logo.svg')" style="max-height:60px; width: 100%" />
          </div>
        </router-link>
        <q-toolbar-title> </q-toolbar-title>
      </q-toolbar>
    </q-header>
  </q-layout>
</template>

<script>
// import { Loading } from 'quasar'
// import { api } from 'src/boot/api'
export default {
  name: 'PageIndex',
  data () {
    return {
      color: 'primary',
      response: '',
      loading: false,
      userData: {
        email: '',
        password: ''
      },
      user: {},
      accept: false,
      remember: false,
      social: {
        facebook: 'https://www.facebook.com/faevaaenterprises/',
        twitter: 'https://twitter.com/Faevaa/',
        instagram: 'https://www.instagram.com/faevaaenterprises/',
        linkedin: '#',
        youtube: 'https://www.youtube.com/faevaaenterprises/'
      }
    }
  },
  created () {
    console.log('hey index.vue')
  },
  methods: {
    // async makeRequest () {
    //   let response
    //   let color = 'negative'
    //   this.loading = true

    //   try {
    //     response = ''
    //     let req = await fetch(process.env.api + '/test')

    //     if (!req.ok) throw new Error('error request')

    //     let {data} = await req.json()
    //     response = data
    //     color = 'positive'
    //   } catch (err) {
    //     console.log(err)
    //     response = err.message
    //   }

    //   setTimeout(() => {
    //     this.response = response
    //     this.color = color
    //     this.loading = false
    //   }, 700)
    // }
    // async login () {
    //   Loading.show()
    //   try {
    //     let res = await api.login(this.userData)
    //     console.log('res', res.data[0])
    //     await this.$store.dispatch('auth/login', { token: res.data.token, user: res.data.user })
    //     Loading.hide()
    //     this.$router.push('/axios-node-test')
    //   } catch (error) {
    //     Loading.hide()
    //   }
    // },
    socialNavigate (link) {
      window.open(link)
    },
    showVideo () {
      this.$refs.introVideo.show()
    },

    // following method is REQUIRED
    // (don't change its name --> "hide")
    hideVideo () {
      this.$refs.introVideo.hide()
    },

    onHideVideo () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hideVideo')
    },

    hideVideoDlg () {
      this.hideVideo()
    },

    makeRequest () {
      //   let response
      //   let color = 'negative'
      this.loading = true
      this.$axios
        .get('/test')
        .then(res => {
          console.log('res', res)
          this.loading = false
        })
        .catch(err => {
          this.loading = false
          console.log('err', err)
        })

      //   try {
      //     response = ''
      //     let req = await fetch(process.env.api + '/test')

      //     if (!req.ok) throw new Error('error request')

      //     let {data} = await req.json()
      //     response = data
      //     color = 'positive'
      //   } catch (err) {
      //     console.log(err)
      //     response = err.message
      //   }

      //   setTimeout(() => {
      //     this.response = response
      //     this.color = color
      //     this.loading = false
      //   }, 700)
    }
  }
}
</script>
