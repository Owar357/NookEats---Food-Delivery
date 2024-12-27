/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';


//* importaciones de axios
import axios from 'axios';
import VueAxios from 'vue-axios';


//*importacion para sweetalert
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';


//*Importaciones de vuetify
import { createVuetify } from 'vuetify';
import 'vuetify/styles'; // Importa los estilos de Vuetify
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { VTimePicker } from 'vuetify/labs/VTimePicker';



import '@mdi/font/css/materialdesignicons.css'; // Iconos


const vuetify = createVuetify({
    components,
    directives,
    components: {
      VTimePicker,
    },
  });
 
/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

 Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
 });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */


app.config.globalProperties.axios = axios;



 app.use(vuetify);
 app.use(VueAxios, axios);
 app.use(VueSweetalert2);
 

app.mount('#app');
