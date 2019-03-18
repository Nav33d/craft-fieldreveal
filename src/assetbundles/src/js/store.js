import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    showModal: false,
  },
  mutations: {
    setShowModal (state, value) {
      state.showModal = value;
    }
  }
});

export default store;