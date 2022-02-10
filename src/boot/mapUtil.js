const tileProviders = [
  {
    name: 'Google Satellite',
    visible: false,
    attribution: 'Map data &copy; contributors, <a href=\\"https://www.google.com/intl/en_sg/help/terms_maps.html\\">Google</a>',
    url: 'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
    options: {
      id: 'Google Satellite',
      subdomains: ['mt1', 'mt2', 'mt3'],
      maxZoom: 18
    }
  },
  {
    name: 'Google Standard',
    visible: false,
    attribution: 'Map data &copy; contributors, <a href=\\"https://www.google.com/intl/en_sg/help/terms_maps.html\\">Google</a>',
    url: 'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
    options: {
      id: 'Google Standard',
      subdomains: ['mt1', 'mt2', 'mt3'],
      maxZoom: 18
    }
  },
  {
    name: 'Google Hybrid',
    visible: false,
    attribution: 'Map data &copy; contributors, <a href=\\"https://www.google.com/intl/en_sg/help/terms_maps.html\\">Google</a>',
    url: 'https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
    options: {
      id: 'Google Hybrid',
      subdomains: ['mt1', 'mt2', 'mt3'],
      maxZoom: 18
    }
  },
  {
    name: 'HERE Fleet',
    visible: false,
    attribution: '&copy;2019 HERE | <a href=\\"https://here.com/terms\\" target=\\"_blank\\">Terms Of Use</a>',
    url: 'https://{s}.base.maps.api.here.com/maptile/2.1/maptile/newest/normal.day/{z}/{x}/{y}/256/png8?lg=ENG' +
        '&style=fleet&app_id=PEnznTAPH2gDPRHpwcQY&app_code=nx29uH7u71jJU16yDMuxUQ',
    options: {
      id: 'HERE Fleet',
      subdomains: '1234',
      maxZoom: 19
    }
  },
  {
    name: 'HERE Terrain',
    visible: false,
    attribution: '&copy;2019 HERE | <a href=\\"https://here.com/terms\\" target=\\"_blank\\">Terms Of Use</a>',
    url: ' https://{s}.aerial.maps.api.here.com/maptile/2.1/maptile/newest/terrain.day/{z}/{x}/{y}/256/png8?lg=ENG' +
        '&app_id=PEnznTAPH2gDPRHpwcQY&app_code=nx29uH7u71jJU16yDMuxUQ',
    options: {
      id: 'HERE Terrain',
      subdomains: '1234',
      maxZoom: 19
    }
  },
  {
    name: 'HERE Hybrid',
    visible: false,
    attribution: '&copy;2019 HERE | <a href=\\"https://here.com/terms\\" target=\\"_blank\\">Terms Of Use</a>',
    url: 'https://{s}.aerial.maps.api.here.com/maptile/2.1/maptile/newest/hybrid.day/{z}/{x}/{y}/256/png8?lg=ENG' +
        '&app_id=PEnznTAPH2gDPRHpwcQY&app_code=nx29uH7u71jJU16yDMuxUQ',
    options: {
      id: 'HERE Hybrid',
      subdomains: '1234',
      maxZoom: 19
    }
  },
  {
    name: 'HERE Traffic',
    visible: false,
    attribution: '&copy;2019 HERE | <a href=\\"https://here.com/terms\\" target=\\"_blank\\">Terms Of Use</a>',
    url: 'https://{s}.traffic.maps.api.here.com/maptile/2.1/traffictile/newest/normal.day/{z}/{x}/{y}/256/png8?' +
        '&app_id=PEnznTAPH2gDPRHpwcQY&app_code=nx29uH7u71jJU16yDMuxUQ',
    options: {
      id: 'HERE Traffic',
      subdomains: '1234',
      maxZoom: 19
    }
  },
  // {
  //   name: 'Waze',
  //   attribution: '&copy;2019 Waze <a href="https://www.waze.com/legal/notices" target="_blank">Notices</a>',
  //   url: 'https://worldtiles{s}.waze.com/{z}/{x}/{y}.png',
  //   options: {
  //     subdomains: '1234',
  //     maxZoom: 18
  //   }
  // },
  {
    name: 'Whereto 2D',
    visible: false,
    attribution: '<a href=\\"https://www.openstreetmap.org/copyright\\">OpenStreetMap</a>',
    url: 'https://tile{s}.f4map.com/tiles/f4_2d/{z}/{x}/{y}.png',
    options: {
      id: 'Whereto 2D',
      subdomains: '0123',
      maxZoom: 18
    }
  },
  {
    name: 'Whereto 3D',
    visible: false,
    attribution: '<a href=\\"https://www.openstreetmap.org/copyright\\">OpenStreetMap</a>',
    url: 'https://tile{s}.f4map.com/tiles/f4_3d/{z}/{x}/{y}.png',
    options: {
      id: 'Whereto 3D',
      subdomains: '0123',
      maxZoom: 18
    }
  },
  {
    name: 'Gothere.sg',
    visible: false,
    attribution: '&copy; <a href=\\"https://gothere.sg\\">gothere.sg</a><a href=\\"https://www.openstreetmap.org/copyright\\">OpenStreetMap</a>',
    url: 'https://t0.gothere.sg/tiles/v21h/{x}/{y}/{z}',
    options: {
      // minZoom: 12,
      id: 'Gothere.sg',
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  },
  {
    name: 'OpenStreetMap',
    visible: true,
    attribution: 'Map data &copy; contributors, <a href=\\"https://www.openstreetmap.org/copyright\\">OpenStreetMap</a>',
    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    options: {
      id: 'OpenStreetMap'
    }
  },
  {
    name: 'OpenStreetMap Fr',
    visible: false,
    attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    url: 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
    options: {
      id: 'OpenStreetMap Fr'
    }
  },
  {
    name: 'OneMap',
    visible: false,
    attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>',
    url: 'https://maps-{s}.onemap.sg/v3/Default/{z}/{x}/{y}.png',
    options: {
      id: 'OneMap',
      // minZoom: 12,
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  },
  {
    name: 'Onemap Night',
    visible: false,
    attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>',
    url: 'https://maps-{s}.onemap.sg/v3/Night/{z}/{x}/{y}.png',
    options: {
      id: 'Onemap Night',
      // minZoom: 12,
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  },
  {
    name: 'Onemap Grey',
    visible: false,
    attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>',
    url: 'https://maps-{s}.onemap.sg/v3/Night/{z}/{x}/{y}.png',
    options: {
      id: 'Onemap Grey',
      // minZoom: 12,
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  },
  {
    name: 'Onemap Original',
    visible: false,
    attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>',
    url: 'https://maps-{s}.onemap.sg/v3/Original/{z}/{x}/{y}.png',
    options: {
      id: 'Onemap Original',
      // minZoom: 12,
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  },
  {
    name: 'Onemap Landlot',
    visible: false,
    attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>',
    url: 'https://maps-{s}.onemap.sg/v3/LandLot/{z}/{x}/{y}.png',
    options: {
      id: 'Onemap Landlot',
      // minZoom: 12,
      maxZoom: 18,
      bounds: [[1.56073, 104.1147], [1.16, 103.502]]
    }
  }
]

const timeZoneList = [
  { value: 'US/Hawaii', desc: 'US/Hawaii', offset: -64800 },
  { value: 'US/Alaska', desc: 'US/Alaska', offset: -61200 },
  { value: 'US/Pacific', desc: 'US/Pacific', offset: -57600 },
  { value: 'US/Arizona', desc: 'US/Arizona', offset: -54000 },
  { value: 'US/Mountain', desc: 'US/Mountain', offset: -54000 },
  { value: 'US/Central', desc: 'US/Central', offset: -50400 },
  { value: 'US/Eastern', desc: 'US/Eastern', offset: -46800 },
  { value: 'Canada/Pacific', desc: 'Canada/Pacific', offset: -57600 },
  { value: 'Canada/Mountain', desc: 'Canada/Mountain', offset: -54000 },
  { value: 'Canada/Central', desc: 'Canada/Central', offset: -50400 },
  { value: 'Canada/Eastern', desc: 'Canada/Eastern', offset: -46800 },
  { value: 'Canada/Atlantic', desc: 'Canada/Atlantic', offset: -43200 },
  { value: 'Mexico/BajaNorte', desc: 'Mexico/BajaNorte', offset: -57600 },
  { value: 'Mexico/BajaSur', desc: 'Mexico/BajaSur', offset: -54000 },
  { value: 'Mexico/General', desc: 'Mexico/General', offset: -50400 },
  { value: 'Europe/Athens', desc: 'Europe/Athens', offset: -18000 },
  { value: 'Europe/Berlin', desc: 'Europe/Berlin', offset: -14400 },
  { value: 'Europe/Dublin', desc: 'Europe/Dublin', offset: -10800 },
  { value: 'Europe/Helsinki', desc: 'Europe/Helsinki', offset: -18000 },
  { value: 'Europe/Kiev', desc: 'Europe/Kiev', offset: -18000 },
  { value: 'Europe/Lisbon', desc: 'Europe/Lisbon', offset: -25200 },
  { value: 'Europe/London', desc: 'Europe/London', offset: -25200 },
  { value: 'Europe/Madrid', desc: 'Europe/Madrid', offset: -21600 },
  { value: 'Europe/Moscow', desc: 'Europe/Moscow', offset: -18000 },
  { value: 'Europe/Oslo', desc: 'Europe/Oslo', offset: -21600 },
  { value: 'Europe/Paris', desc: 'Europe/Paris', offset: -21600 },
  { value: 'Europe/Rome', desc: 'Europe/Rome', offset: -21600 },
  { value: 'Europe/Stockholm', desc: 'Europe/Stockholm', offset: -21600 },
  { value: 'Europe/Zurich', desc: 'Europe/Zurich', offset: -21600 },
  { value: 'Pacific/Auckland', desc: 'Pacific/Auckland', offset: 14400 },
  { value: 'Pacific/Chatham', desc: 'Pacific/Chatham', offset: 17700 },
  { value: 'GMT', desc: 'GMT', offset: -28800 },
  { value: 'GMT+00:00', desc: 'GMT+00:00', offset: -28800 },
  { value: 'GMT+01:00', desc: 'GMT+01:00', offset: -25200 },
  { value: 'GMT+02:00', desc: 'GMT+02:00', offset: -21600 },
  { value: 'GMT+03:00', desc: 'GMT+03:00', offset: -18000 },
  { value: 'GMT+04:00', desc: 'GMT+04:00', offset: -14400 },
  { value: 'GMT+05:00', desc: 'GMT+05:00', offset: -10800 },
  { value: 'GMT+05:30', desc: 'GMT+05:30', offset: -9000 },
  { value: 'GMT+06:00', desc: 'GMT+06:00', offset: -7200 },
  { value: 'GMT+07:00', desc: 'GMT+07:00', offset: -3600 },
  { value: 'GMT+08:00', desc: 'GMT+08:00', offset: 0 },
  { value: 'GMT+09:00', desc: 'GMT+09:00', offset: 3600 },
  { value: 'GMT+10:00', desc: 'GMT+10:00', offset: 7200 },
  { value: 'GMT+11:00', desc: 'GMT+11:00', offset: 10800 },
  { value: 'GMT+12:00', desc: 'GMT+12:00', offset: 14400 },
  { value: 'GMT+13:00', desc: 'GMT+13:00', offset: 18000 },
  { value: 'GMT+14:00', desc: 'GMT+14:00', offset: 21600 },
  { value: 'GMT-01:00', desc: 'GMT-01:00', offset: -32400 },
  { value: 'GMT-02:00', desc: 'GMT-02:00', offset: -36000 },
  { value: 'GMT-03:00', desc: 'GMT-03:00', offset: -39600 },
  { value: 'GMT-04:00', desc: 'GMT-04:00', offset: -43200 },
  { value: 'GMT-05:00', desc: 'GMT-05:00', offset: -46800 },
  { value: 'GMT-06:00', desc: 'GMT-06:00', offset: -50400 },
  { value: 'GMT-07:00', desc: 'GMT-07:00', offset: -54000 },
  { value: 'GMT-08:00', desc: 'GMT-08:00', offset: -57600 },
  { value: 'GMT-09:00', desc: 'GMT-09:00', offset: -61200 },
  { value: 'GMT-10:00', desc: 'GMT-10:00', offset: -64800 },
  { value: 'GMT-11:00', desc: 'GMT-11:00', offset: -68400 },
  { value: 'GMT-12:00', desc: 'GMT-12:00', offset: -72000 }
]

const HEADING = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW']

const statusCodes = [
  { code: 0, desc: 'All Codes' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },

  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },

  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61713, desc: 'Motion Start' },
  { code: 61714, desc: 'In Motion' },
  { code: 61715, desc: 'Motion Stop' },
  { code: 61716, desc: 'Dormant' },
  { code: 61718, desc: 'Idle' },
  { code: 61722, desc: 'Over Speed' },
  { code: 62465, desc: 'Ignition On' },
  { code: 62467, desc: 'Ignition Off' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' },
  { code: 61472, desc: '' }
]

const arrivalCodes = [
  { value: 0, label: 'No Code' },
  { value: 61968, label: 'Arrive' },
  { value: 62064, label: 'Geofence_Active' },
  { value: 62112, label: 'Boundary_Enter' },
  { value: 61973, label: 'Job_Arrive' },
  { value: 61831, label: 'Job_Start' },
  { value: 61832, label: 'Job_Stop' },
  { value: 61825, label: 'Track_Start' },
  { value: 61827, label: 'Track_Stop' },
  { value: 63543, label: 'Duty_On' },
  { value: 63544, label: 'Duty_Off' },
  { value: 63617, label: 'Intrusion_On' }
]

const departureCodes = [
  { value: 0, label: 'No Code' },
  { value: 62000, label: 'Depart' },
  { value: 62080, label: 'Geofence_Inactive' },
  { value: 62128, label: 'Boundary_Exit' },
  { value: 62005, label: 'Job_Depart' },
  { value: 61832, label: 'Job_Stop' },
  { value: 61831, label: 'Job_Start' },
  { value: 61827, label: 'Track_Stop' },
  { value: 61825, label: 'Track_Start' },
  { value: 63544, label: 'Duty_Off' },
  { value: 63543, label: 'Duty_On' },
  { value: 63618, label: 'Intrusion_Off' }
]

const shapeColors = [
  { value: '', label: 'Default' },
  { value: '#000000', label: 'Black' },
  { value: '#a52a2a', label: 'Brown' },
  { value: '#dd0000', label: 'Red' },
  { value: '#ffa500', label: 'Orange' },
  { value: '#ffd700', label: 'Yellow' },
  { value: '#00cc00', label: 'Green' },
  { value: '#0000ee', label: 'Blue' },
  { value: '#9400d3', label: 'Purple' },
  { value: '#505050', label: 'Gray' },
  { value: '#ffffff', label: 'White' },
  { value: '#00ffff', label: 'Cyan' },
  { value: '#ff1493', label: 'Pink' }
]

const pushpinList = [
  { name: '', desc: 'default', isEval: false, image: '', width: 24, height: 0, index: -1 },
  { name: 'black', desc: 'black', isEval: false, image: 'statics/pp/pin30_black.png', width: 18, height: 30, index: 0 },
  { name: 'brown', desc: 'brown', isEval: false, image: 'statics/pp/pin30_brown.png', width: 18, height: 30, index: 1 },
  { name: 'red', desc: 'red', isEval: false, image: 'statics/pp/pin30_red.png', width: 18, height: 30, index: 2 },
  { name: 'orange', desc: 'orange', isEval: false, image: 'statics/pp/pin30_orange.png', width: 18, height: 30, index: 3 },
  { name: 'yellow', desc: 'yellow', isEval: false, image: 'statics/pp/pin30_yellow.png', width: 18, height: 30, index: 4 },
  { name: 'green', desc: 'green', isEval: false, image: 'statics/pp/pin30_green.png', width: 18, height: 30, index: 5 },
  { name: 'blue', desc: 'blue', isEval: false, image: 'statics/pp/pin30_blue.png', width: 18, height: 30, index: 6 },
  { name: 'purple', desc: 'purple', isEval: false, image: 'statics/pp/pin30_purple.png', width: 18, height: 30, index: 7 },
  { name: 'gray', desc: 'gray', isEval: false, image: 'statics/pp/pin30_gray.png', width: 18, height: 30, index: 8 },
  { name: 'white', desc: 'white', isEval: false, image: 'statics/pp/pin30_white.png', width: 18, height: 30, index: 9 },
  { name: 'reddot', desc: 'reddot', isEval: false, image: 'statics/pp/pin30_red_dot.png', width: 18, height: 30, index: 10 },
  { name: 'greendot', desc: 'greendot', isEval: false, image: 'statics/pp/pin30_green_dot.png', width: 18, height: 30, index: 11 },
  { name: 'bluedot', desc: 'bluedot', isEval: false, image: 'statics/pp/pin30_blue_dot.png', width: 18, height: 30, index: 12 },
  { name: 'clear', desc: 'clear', isEval: false, image: 'statics/pp/clear16.png', width: 16, height: 16, index: 13 },
  { name: 'statusCode', desc: 'status Code', isEval: false, image: 'statics/pp/pin30_yellow.png', width: 18, height: 30, index: 14 },
  { name: 'greenh', desc: 'greenh', isEval: true, image: 'statics/pp/pin30_green_h1.png', width: 18, height: 30, index: 15 },
  { name: 'yellowh', desc: 'yellowh', isEval: true, image: 'statics/pp/pin30_yellow_h1.png', width: 18, height: 30, index: 16 },
  { name: 'heading', desc: 'heading', isEval: true, image: 'statics/pp/pin30_green_h1.png', width: 18, height: 30, index: 17 },
  { name: 'heading_eu', desc: 'heading  eu', isEval: true, image: 'statics/pp/pin30_green_h1.png', width: 18, height: 30, index: 18 },
  { name: 'heading_ca', desc: 'heading  ca', isEval: true, image: 'statics/pp/pin30_green_h1.png', width: 18, height: 30, index: 19 },
  { name: 'head_clear', desc: 'head  clear', isEval: false, image: 'statics/pp/clear16.png', width: 16, height: 16, index: 20 },
  { name: 'last', desc: 'last', isEval: true, image: 'statics/pp/CrosshairRed.gif', width: 18, height: 30, index: 21 },
  { name: 'lastlabel', desc: 'lastlabel', isEval: true, image: 'statics/pp/label47_fill.png', width: 47, height: 24, index: 22 },
  { name: 'lastage', desc: 'lastage', isEval: true, image: 'statics/pp/CrosshairRed.gif', width: 18, height: 30, index: 23 },
  { name: 'devlabel', desc: 'devlabel', isEval: true, image: 'statics/pp/label47_fill.png', width: 47, height: 24, index: 24 },
  { name: 'devlbl_r', desc: 'devlbl  r', isEval: true, image: 'statics/pp/label47_fill_red.png', width: 47, height: 24, index: 25 },
  { name: 'devlbl_g', desc: 'devlbl  g', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 26 },
  { name: 'devlbl_b', desc: 'devlbl  b', isEval: true, image: 'statics/pp/label47_fill_blue.png', width: 47, height: 24, index: 27 },
  { name: 'devlbl_y', desc: 'devlbl  y', isEval: true, image: 'statics/pp/label47_fill_yellow.png', width: 47, height: 24, index: 28 },
  { name: 'devlabel2', desc: 'devlabel 2', isEval: true, image: 'statics/pp/label67_fill.png', width: 67, height: 24, index: 29 },
  { name: 'sp_slow', desc: 'sp  slow', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 30 },
  { name: 'sp_moving', desc: 'sp  moving', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 31 },
  { name: 'sp_greenh', desc: 'sp  greenh', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 32 },
  { name: 'sp_yellowh', desc: 'sp  yellowh', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 33 },
  { name: 'sp_heading', desc: 'sp  heading', isEval: true, image: 'statics/pp/label47_fill_green.png', width: 47, height: 24, index: 34 },
  { name: 'pp_arrow_c', desc: 'pp  arrow  c', isEval: true, image: 'statics/pp/pin30_cyan_arrow.png', width: 18, height: 30, index: 35 },
  { name: 'pp_arrow_y', desc: 'pp  arrow  y', isEval: true, image: 'statics/pp/pin30_yellow_arrow.png', width: 18, height: 30, index: 36 },
  { name: 'arrow_spd', desc: 'arrow  spd', isEval: true, image: 'statics/pp/arrow_red.png', width: 20, height: 20, index: 37 },
  { name: 'arrow_grn', desc: 'arrow  grn', isEval: true, image: 'statics/pp/arrow_green.png', width: 20, height: 20, index: 38 },
  { name: 'indexed', desc: 'indexed', isEval: true, image: 'statics/pp/pin30_blue_fill.png', width: 18, height: 30, index: 39 },
  { name: 'custom', desc: 'custom', isEval: true, image: 'statics/pin30_green_h1.png', width: 18, height: 30, index: 40 }
]

import { date } from 'quasar'

export default {
  convertTimestamp (val, tmz, showTimeZone = false) {
    const timeOffset = timeZoneList.find(zone => zone.desc === tmz).offset
    const offset = showTimeZone ? timeOffset : 0

    const timeStamp = new Date((val + offset) * 1000)
    return date.formatDate(timeStamp, 'DD/MM/YYYY HH:mm:ss')
  },
  getStatusCode: function (code) {
    return statusCodes.find(el => el.code === code).desc
  },

  getCompass: function (heading) {
    return HEADING[Math.round(heading / 45.0) % 8]
  },
  getMarkerIcon: function (e) {
    const pushpinID = e.pushpinID
    console.log('GET ICON', pushpinID)

    if (pushpinID === '' || pushpinID === null || pushpinID === 'statusCode' || pushpinID === 'heading' ||
        pushpinID === 'heading_eu' || pushpinID === 'heading_ca') {
      return {
        iconUrl: this.getHeadingMarkerURL(e),
        iconSize: [18, 30],
        iconOffset: [9, 30]
      }
    }

    const pushPinImg = 'statics/pp/pin30'
    const x = Math.round(e.heading / 45.0) % 8
    const text = e.displayName ? e.displayName : e.deviceID
    const color = this.getSpeedColor(e.speedKPH)

    let iconUrl = ''
    let iconSize = [18, 30]
    let iconOffset = [9, 30]
    let iconHeight = 30
    switch (pushpinID) {
      case 'black':
      case 'brown':
      case 'red':
      case 'orange':
      case 'yellow':
      case 'green':
      case 'blue':
      case 'purple':
      case 'gray':
      case 'white':
        iconUrl = pushPinImg + '_' + pushpinID + '.png'
        break
      case 'reddot':
        iconUrl = pushPinImg + '_red_dot.png'
        break
      case 'greendot':
        iconUrl = pushPinImg + '_green_dot.png'
        break
      case 'bluedot':
        iconUrl = pushPinImg + '_blue_dot.png'
        break
      case 'clear':
      case 'head_clear':
        iconUrl = 'statics/pp/clear16.png'
        iconOffset = [8, 16]
        break
      case 'greenh':
        iconUrl = pushPinImg + '_green_h' + x + '.png'
        break
      case 'yellowh':
        iconUrl = pushPinImg + '_yellow_h' + x + '.png'
        break
      case 'last':
      case 'lastage':
        iconUrl = pushPinImg + '/CrosshairRed.gif'
        break
      case 'lastlabel':
      case 'devlabel':
      case 'devlabel2':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|FFFFFF|000000'
        iconOffset = [0, 30]
        break
      case 'devlbl_r':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|FF8888|000000'
        iconOffset = [0, 30]
        break
      case 'devlbl_g':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|88FF88|000000'
        iconOffset = [0, 30]
        break
      case 'devlbl_b':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|8888FF|000000'
        iconOffset = [0, 30]
        break
      case 'devlbl_y':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|FFFF88|000000'
        iconOffset = [0, 30]
        break
      case 'sp_slow':
      case 'sp_moving':
      case 'sp_greenh':
      case 'sp_yellowh':
      case 'sp_heading':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|' + color + '|000000'
        iconOffset = [0, 30]
        break
      case 'pp_arrow_c':
        iconUrl = pushPinImg + '_cyan.png'
        break
      case 'pp_arrow_y':
        iconUrl = pushPinImg + '_yellow.png'
        break
      case 'arrow_spd':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|FFFF88|000000'
        iconOffset = [10, 10]
        iconHeight = 20
        break
      case 'arrow_grn':
        iconUrl = 'https://chart.googleapis.com/chart?chst=d_bubble_text_small&chld=bb|' + text + '|FFFF88|000000'
        iconOffset = [10, 10]
        iconHeight = 20
        break
      default:
        iconUrl = pushPinImg + '_red.png'
    }

    const iconInfo = {
      iconUrl: iconUrl,
      iconSize: iconSize,
      iconOffset: iconOffset,
      iconHeight,
      pushpin: pushpinID,
      heading: e.heading
    }

    const arrowColor = pushpinID === 'arrow_spd' ? 'red' : pushpinID === 'arrow_grn' ? '88FF88' : ''

    if (arrowColor) {
      iconInfo.color = arrowColor
    }

    return iconInfo
  },
  getHeadingMarkerURL: function (e) {
    const pushPinImg = 'statics/pp/pin30' // "extra/images/pp/circle"
    // if ((MAX_CREATION_AGE_SEC > 0) && (e.createAge > MAX_CREATION_AGE_SEC)) {
    //   if ((e.stopped > 0) || (e.speedKPH < 5.0)) {
    //     return ppImg + '_black.png'
    //   } else {
    //     var x = Math.round(e.heading / 45.0) % 8
    //     return ppImg + '_black_h' + x + '.png'
    //   }
    // } else
    if (e.stopStatus > 0) {
    // stopped
      if (e.stopStatus === 2) {
      // stop-event
        return pushPinImg + '_red_dot.png'
      } else {
      // general stopped
        return pushPinImg + '_red.png'
      }
    } else if (e.speedKPH < 5.0/* km/h */) { // 3.107 mph
    // probably not moving
      return pushPinImg + '_red_dot.png'
    } else if (e.speedKPH < 32.0/* km/h */) { // 19.884 mph
    // 5 <= X < 32
      const x = Math.round(e.heading / 45.0) % 8
      return pushPinImg + '_yellow_h' + x + '.png'
    } else {
    // 32 <= X
      const x = Math.round(e.heading / 45.0) % 8
      return pushPinImg + '_green_h' + x + '.png'
    }
  },
  getSpeedColor: function (val) {
    if (val <= 5.0) {
      return 'FF8888'
    } else if (val < 32.0) {
      return 'FFFF88'
    } else {
      return '88FF88'
    }
  },
  getMarkerInfo (d, tmz) {
    const TEXT_INFO_DATE = 'Date'
    const TEXT_INFO_GPS = 'GPS'
    const TEXT_INFO_SATS = '#Sats'
    const TEXT_INFO_SPEED = 'Speed'
    const TEXT_INFO_HEADING = 'Heading'
    const TEXT_INFO_ALTITUDE = 'Altitude'
    // const TEXT_INFO_STOP_TIME = 'Stop Time'
    const TEXT_INFO_ADDR = 'Address'

    const dev = d.deviceID.toUpperCase()
    const shortname = d.displayName ? ' (' + d.displayName.toUpperCase() + ')' : ''
    const flat = d.latitude.toFixed(5)
    const flon = d.longitude.toFixed(5)
    const dtime = this.convertTimestamp(d.timestamp, tmz, true)
    const spdfmt = d.speedKPH.toFixed(1) + ' km/h'
    const addr = d.address
    const code = this.getStatusCode(d.statusCode)
    const sats = d.satelliteCount
    const compass = this.getCompass(d.heading)

    let gpsStr = ''
    if (sats > 0) {
      // eslint-disable-next-line no-undef
      gpsStr += TEXT_INFO_SATS + ' ' + sats
    }
    if (gpsStr !== '') {
      gpsStr = '[' + gpsStr + ']'
    }

    let posLink = '<a href="https://maps.google.com/?q=' + parseFloat(flat) + ',' + parseFloat(flon) + '" target="_blank">'
    posLink += flat + ' / ' + flon + '</a>'

    let str = ''
    str += "<table class='infoBoxTable' cellspacing='1' cellpadding='1' border='0' style='width: 350px'>"
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'>[#" + d.id + '] &nbsp; <b>' + dev + shortname + ' : ' + code + '</b></td></tr>'
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_DATE + ':</b> ' + dtime + ' [' + tmz + ']</td></tr>'
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_GPS + ':</b> ' + posLink + ' ' + gpsStr + '</td></tr>'
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_SPEED + ':</b> ' + spdfmt + '</td></tr>'
    if (d.speedKPH > 0) {
      const head = d.heading.toFixed(0) + '&deg; &nbsp;(' + compass + ')'
      str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_HEADING + ':</b> ' + head + '</td></tr>'
    }
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_ALTITUDE + ':</b> ' + d.altitude + ' meter' + '</td></tr>'
    str += "<tr class='infoBoxRow'><td class='infoBoxCell'><b>" + TEXT_INFO_ADDR + ':</b> ' + addr + '</td></tr>'
    str += '</table>'
    return str
  },

  tileProviders,
  timeZoneList,
  arrivalCodes,
  departureCodes,
  shapeColors,
  pushpinList
}
