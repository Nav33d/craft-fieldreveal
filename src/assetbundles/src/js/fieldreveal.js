import Vue from 'vue';
import Vuex from 'vuex'
import Moment from 'vue-moment';
import VueSweetalert2 from 'vue-sweetalert2';
import VueConfetti from 'vue-confetti';
import VConfetti from './components/Confetti.vue';
import VFields from './components/Fields.vue';

Vue.use(Vuex);
Vue.use(Moment);
Vue.use(VueSweetalert2);
Vue.use(VueConfetti);

import store from './store';

const vm = new Vue({
  el: '#fieldreveal-app',
  store,
  delimiters: ["<%","%>"],
  components: {
    'v-confetti': VConfetti,
    'v-fields': VFields,
  },
});