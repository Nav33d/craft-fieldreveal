<template>
  <div id="fieldreveal-modal" class="modal fieldrevealmodal" style="display: none;">
    <div id="modal" class="body">
      <div class="text-center" v-if="modalSpinner">
        <div class="spinner big"></div>
      </div>

      <div class="content" v-if="modalContent">  
        <div class="mb-6" v-for="(item, index) in modalContent" :key="index">
          <div class="mb-4">
            <h2 class="mb-1">{{ item.shortName }}</h2>
            <p class="text-grey-dark">{{ item.name }}</p>
          </div>

          <div v-if="item.sections.length">
            <h3 class="text-grey-dark">{{ item.sectionTitle }}</h3>
            <ul class="fieldreveal-list mt-2 pl-8">
              <li class="mt-2" v-for="(section, index) in item.sections" :key="index">
                <span class="font-bold">{{ section.name }}</span>
              </li>
            </ul>
          </div>

          <hr>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="float-right">
        <button class="btn" @click.prevent="closeModal()">Close</button>
      </div>
    </div>
    
  </div>
</template>

<script>
export default {
  props: {
    modalContent: Object,
  },

  data() {
    return {
      modal: null,
      modalSpinner: true,
    };
  },

  computed: {
    showModal () {
      return this.$store.state.showModal;
    }
  },

  watch: {
    showModal (newValue, oldValue) {
      if ( newValue )
      {
        this.modal.show();
      }
      else
      {
        this.modal.hide();
      }
    },

    modalContent (newValue, oldValue) {
      if ( newValue )
      {
        this.modalSpinner = false;
      }
      else
      {
        this.modalSpinner = true;
      }
    }
  },

  mounted () {
    let self = this;

    var GarnishModal = Garnish.Modal.extend({
      hide: function(){
        self.closeModal();
        this.base();
      } 
    });
    
    this.modal = new GarnishModal('#fieldreveal-modal', {
      autoShow: false,
    });
  },

  methods: {
    closeModal () {
      this.$store.commit('setShowModal', false);
    },

  },
}
</script>

