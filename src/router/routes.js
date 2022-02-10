// TODO:: separate route definitions by modules and just import here
// import routeModules from 'features/routes-index.js'
// import store from 'src/store'
// function authGuard (to, from, next) {
//   // console.log('to', store.getters['auth/token'])
//   // next()
//   if (LocalStorage.has('token')) {
//     // or however you store your logged in state
//     next() // allow to enter route
//   } else {
//     next('/login') // go to '/login';
//   }
// }
const routes = [
  {
    path: '/',
    name: 'Home',
    meta: {
      title: 'Report Management',
      needAuth: true
    },
    component: () => import('pages/Home.vue')
  },
  {
    path: '/',
    component: () => import('layouts/EmptyLayout.vue'),
    children: [
      {
        path: '/login',
        name: 'Login',
        meta: {
          title: 'DPD Login'
        },
        component: () => import('pages/Authentication/login.vue')
      },
      {
        path: '/signup',
        name: 'Signup',
        meta: {
          title: 'DPD Sign Up'
        },
        component: () => import('pages/Authentication/signup.vue')
      },
      {
        path: '/forgot-password',
        name: 'ForgotPassword',
        meta: {
          title: 'Forgot Password'
        },
        component: () => import('pages/Authentication/forgot-password.vue')
      },
      {
        path: '/reset-password',
        name: 'ResetPassword',
        meta: {
          title: 'Reset Password'
        },
        component: () => import('pages/Authentication/reset-password.vue'),
        props: (route) => ({ query: route.query.token })
      },
      {
        path: '/confirm-user',
        name: 'ConfirmUser',
        meta: {
          title: 'Confirm User'
        },
        component: () => import('pages/Authentication/confirm-user.vue'),
        props: (route) => ({ query: route.query.token })
      },
      {
        path: '/dashboard',
        name: 'Dashboard',
        meta: {
          title: 'Dashboard',
          needAuth: true
        },
        component: () => import('layouts/MyLayout.vue'),
        children: [
          { path: '/', component: () => import('pages/Dashboard/Index.vue') },
          {
            path: 'admin-profile',
            name: 'AdminProfile',
            meta: {
              title: 'Profile'
            },
            component: () => import('pages/Authentication/admin-profile.vue')
          },
          {
            path: 'client-profile',
            name: 'ClientProfile',
            meta: {
              title: 'Profile'
            },
            component: () => import('pages/Authentication/client-profile.vue')
          },
          // {
          //   path: 'user-profile',
          //   name: 'UserProfile',
          //   meta: {
          //     title: 'Profile'
          //   },
          //   component: () => import('pages/Authentication/user-profile.vue')
          // },
          {
            path: 'driver-profile',
            name: 'DriverProfile',
            meta: {
              title: 'Profile'
            },
            component: () => import('pages/Authentication/driver-profile.vue')
          },
          {
            path: 'clients',
            name: 'Clients',
            meta: {
              title: 'CLIENTS',
              roles: ['admin']
            },
            component: () => import('pages/Dashboard/Admin/client/list.vue')
          },
          {
            path: 'clients/new',
            name: 'New Client',
            meta: {
              title: 'NEW CLIENT',
              roles: ['admin']
            },
            component: () => import('pages/Dashboard/Admin/client/detail.vue')
          },
          {
            path: 'clients/:id/detail',
            name: 'Client Detail',
            meta: {
              title: 'CLIENT DETAIL',
              roles: ['admin']
            },
            component: () => import('pages/Dashboard/Admin/client/detail.vue')
          },
          // {
          //   path: 'users',
          //   name: 'Users',
          //   meta: {
          //     title: 'USERS',
          //     roles: ['admin', 'client']
          //   },
          //   component: () => import('pages/Dashboard/Admin/user/list.vue')
          // },
          // {
          //   path: 'users/new',
          //   name: 'New User',
          //   meta: {
          //     title: 'New User',
          //     roles: ['admin', 'client']
          //   },
          //   component: () => import('pages/Dashboard/Admin/user/detail.vue')
          // },
          // {
          //   path: 'users/:id/detail',
          //   name: 'User Detail',
          //   meta: {
          //     title: 'User Detail',
          //     roles: ['admin', 'client']
          //   },
          //   component: () => import('pages/Dashboard/Admin/user/detail.vue')
          // },
          {
            path: 'schedules',
            name: 'Schedules',
            meta: {
              title: 'SCHEDULE',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/reports/list.vue')
          },
          {
            path: 'schedules/daily-new',
            name: 'New Daily Schedule',
            component: () => import('pages/Dashboard/Admin/reports/daily-detail.vue'),
            meta: {
              title: 'Create New Schedule',
              roles: ['admin', 'user', 'client']
            }
          },
          {
            path: 'schedules/:reportNo/daily-detail',
            name: 'Edit Daily Schedule',
            component: () => import('pages/Dashboard/Admin/reports/daily-detail.vue'),
            meta: {
              title: 'Schedule Detail',
              roles: ['admin', 'user', 'client']
            }
          },
          {
            path: 'schedules/extra-new',
            name: 'New Extra Schedule',
            component: () => import('pages/Dashboard/Admin/reports/extra-detail.vue'),
            meta: {
              title: 'Create New Schedule',
              roles: ['admin', 'user', 'client']
            }
          },
          {
            path: 'schedules/:reportNo/extra-detail',
            name: 'Edit Extra Schedule',
            component: () => import('pages/Dashboard/Admin/reports/extra-detail.vue'),
            meta: {
              title: 'Schedule Detail',
              roles: ['admin', 'user', 'client']
            }
          },
          {
            path: 'performance',
            name: 'Performance',
            meta: {
              title: 'PERFORMANCE',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/reports/monthly-list.vue')
          },
          {
            path: 'driver-performance',
            name: 'Performance',
            meta: {
              title: 'PERFORMANCE',
              roles: ['driver']
            },
            component: () => import('pages/Dashboard/Admin/drivers/monthly-list-driver.vue')
          },
          {
            path: 'driver-services',
            name: 'Driver-Services',
            meta: {
              title: 'DRIVER SERVICES',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/driver_services/list.vue')
          },
          {
            path: 'driver-services/new',
            name: 'New Driver Service',
            meta: {
              title: 'NEW DRIVER SERVICE',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/driver_services/detail.vue')
          },
          {
            path: 'driver-services/:id/details',
            name: 'Driver Service Details',
            meta: {
              title: 'DRIVER SERVICE DETAILS',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/driver_services/detail.vue')
          },
          {
            path: 'fleets',
            name: 'Fleets',
            meta: {
              title: 'FLEET',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fleets/list.vue')
          },
          {
            path: 'fleets/new',
            name: 'New Fleet',
            meta: {
              title: 'NEW FLEET',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fleets/detail.vue')
          },
          {
            path: 'fleets/:id/details',
            name: 'Fleet Details',
            meta: {
              title: 'FLEET DETAILS',
              roles: ['admin', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fleets/detail.vue')
          },
          {
            path: 'odometer-records',
            name: 'Odometer Records',
            meta: {
              title: 'ODOMETER RECORDS',
              roles: ['driver', 'client', 'admin']
            },
            component: () => import('pages/Dashboard/Admin/driver_mileages/list.vue')
          },
          {
            path: 'odometer-records/new',
            name: 'New Odometer Record',
            meta: {
              title: 'NEW ODOMETER RECORD',
              roles: ['driver']
            },
            component: () => import('pages/Dashboard/Admin/driver_mileages/detail.vue')
          },
          {
            path: 'odometer-records/:id/details',
            name: 'Odometer Record Details',
            meta: {
              title: 'ODOMETER RECORD DETAILS',
              roles: ['driver']
            },
            component: () => import('pages/Dashboard/Admin/driver_mileages/detail.vue')
          },
          {
            path: 'fuels',
            name: 'Fuels',
            meta: {
              title: 'FUEL',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fuels/list.vue')
          },
          {
            path: 'fuels/new',
            name: 'New Fuel',
            meta: {
              title: 'NEW FUEL',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fuels/detail.vue')
          },
          {
            path: 'fuels/:id/details',
            name: 'Fuel Details',
            meta: {
              title: 'FUEL DETAILS',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/fuels/detail.vue')
          },
          {
            path: 'drivers',
            name: 'Drivers',
            meta: {
              title: 'DRIVERS',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/drivers/driver-list.vue')
          },
          {
            path: 'drivers/new',
            name: 'New Driver',
            meta: {
              title: 'NEW DRIVER',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/drivers/detail.vue')
          },
          {
            path: 'drivers/:id/details',
            name: 'Driver Details',
            meta: {
              title: 'DRIVER DETAILS',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/drivers/detail.vue')
          },
          {
            path: 'routes',
            name: 'Routes',
            meta: {
              title: 'ROUTES',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/reports/number-list.vue')
          },
          {
            path: 'dpd-modules',
            name: 'DPD Modules',
            meta: {
              title: 'SCHEDULE',
              roles: ['admin', 'client', 'user']
            },
            component: () => import('pages/Dashboard/Admin/dpd/list.vue')
          },
          {
            path: 'dpd-modules/:dpdDate/details',
            name: 'Edit DPD Modules',
            component: () => import('pages/Dashboard/Admin/dpd/detail.vue'),
            meta: {
              title: 'Schedule Detail',
              roles: ['admin', 'client', 'user']
            }
          },
          {
            path: 'dpd-performance',
            name: 'DPD Performance',
            meta: {
              title: 'PERFORMANCE',
              roles: ['admin', 'user', 'client']
            },
            component: () => import('pages/Dashboard/Admin/dpd/performance-list.vue')
          },
          {
            path: 'dpd-driver-performance',
            name: 'DPD Driver Performance',
            meta: {
              title: 'PERFORMANCE',
              roles: ['driver']
            },
            component: () => import('pages/Dashboard/Admin/dpd/driver-performance-list.vue')
          },
          // {
          //   path: 'locator-search',
          //   name: 'LOCATOR',
          //   meta: {
          //     title: 'LOCATOR',
          //     roles: ['admin', 'user', 'client']
          //   },
          //   component: () => import('pages/Dashboard/Admin/locator/locator_search.vue')
          // }
        ]
      }
    ]
  }
  // add other routes
  // ...Object.keys(routeModules).map(v => routeModules[v])
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
