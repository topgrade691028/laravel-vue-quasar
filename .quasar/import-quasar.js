/**
 * THIS FILE IS GENERATED AUTOMATICALLY.
 * DO NOT EDIT.
 *
 * You are probably looking on adding startup/initialization code.
 * Use "quasar new boot <name>" and add it there.
 * One boot file per concern. Then reference the file(s) in quasar.conf.js > boot:
 * boot: ['file', ...] // do not add ".js" extension to it.
 *
 * Boot files are your "main.js"
 **/

import iconSet from 'quasar/icon-set/fontawesome-v5'


import Vue from 'vue'

import {Quasar,QBtn,Ripple,Notify,Dialog,LocalStorage,SessionStorage,LoadingBar,Loading,exportFile} from 'quasar'


Vue.use(Quasar, { config: {"loadingBar":{"color":"accent"},"loading":{}},iconSet: iconSet,components: {QBtn},directives: {Ripple},plugins: {Notify,Dialog,LocalStorage,SessionStorage,LoadingBar,Loading,exportFile} })
