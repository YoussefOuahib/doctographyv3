import { createWebHistory, createRouter } from "vue-router";

import Home from '../Pages/Home.vue'
import Authenticated from '../Layouts/Authenticated.vue'
import Guest from '../Layouts/Guest.vue'
import Login from '../Components/Auth/Login.vue'
import Patients from '../Components/Patients.vue'
import Conditions from '../Components/Conditions.vue'
import Settings  from '../Components/Settings.vue'
import Sessions from '../Components/Sessions.vue'

import Appointments from '../Components/Appointments.vue'

function auth(to, from, next) {
    if(JSON.parse(localStorage.getItem('loggedIn'))) {
        next()
        return
    }
    next('/login')
    return
}

const routes = [
    {
        path: '/',
        redirect: { name: 'login' },
        component: Guest,
        children: [
          
            {
        
                path: '/login',
                name: 'login',
                component: Login
            },
           
           
            
           
        ]
    },
    {
        component: Authenticated,
        beforeEnter: auth,
        children: [
            {
        
                path: '/',
                name: 'home',
                component: Home
            },
            {
                path: '/patients',
                name: 'patients',
                component: Patients,
            },
         
            {
                path: '/conditions',
                name: 'conditions',
                component: Conditions,
            },
            {
                path: '/appointments',
                name: 'appointments',
                component: Appointments,
            },
            {
                path: '/settings',
                name: 'settings',
                component: Settings,
            },
        
            {
                path: '/sessions',
                name: 'sessions',
                component: Sessions,
            },
            
           
           
        ]
    },
  
   
]

export default createRouter({
    history: createWebHistory(),
    routes
})