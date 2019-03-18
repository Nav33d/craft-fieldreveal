<template>
  <div>
    <div class="text-center" v-if="loadingFields">
      <div class="spinner big"></div>
    </div>

    <table class="data fullwidth" v-if="fields.length">
      <thead>
        <tr>
          <th>Name</th>
          <th>Handle</th>
          <th>Type</th>
          <th>Group</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(field, index) in fields" :key="index">

          <td class="font-bold" v-if="unusedFieldIds.includes(field.id)">{{ field.name }} <span class="text-xs text-green">[Not being used]</span> </td>
          <td class="font-bold" v-else><a class="go" href="#" @click.prevent="revealField(field.id)">{{ field.name }}</a></td>

          <td>{{ field.handle }}</td>
          <td>{{ field.displayName }}</td>
          <td>{{ field.groupName }}</td>
          <td><a class="icon delete" href="#" title="Delete field" @click.prevent="confirmDelete(field)"></a></td>
        </tr>
      </tbody>
    </table>

    <div class="mt-4" v-if="( !loadingFields && !fields.length )">
      <p class="text-center font-bold text-lg">No fields found!</p>
    </div>

    <field-modal :modal-content="modalContent"></field-modal>
  </div>
</template>

<script>
import axios from 'axios';
import FieldModal from './FieldModal';

export default {
  components: {
    FieldModal,
  },

  props: {
    fields: Array,
    unusedFieldIds: Array,
    loadingFields: Boolean,
  },

  data () {
    return {
      modalContent: null,
    };
  },

  methods: {
    revealField (fieldId) {
      this.$store.commit('setShowModal', true);
      
      this.modalContent = null;
      
      axios.get('/actions/fieldreveal/default/get-field-data?fieldId=' + fieldId)
      .then((response) => {
        this.modalContent = response.data;
        this.modalSpinner = false;
      })
      .catch((error) => {
        Craft.cp.displayError("Failed to load field data");
      });
    },

    confirmDelete (field) {
      this.$swal({
        title: 'Are you sure?',
        html: "<span class='font-bold'>" + field.name + "</span> field will be deleted. You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#a2a2a2',
        confirmButtonText: 'Yes, Delete!'
      }).then((result) => {
        if (result.value) {
          this.deleteField(field.id);
        }
      });
    },

    deleteField (fieldId) {
      let headers = {
        'X-CSRF-Token': Craft.csrfTokenValue,
      };

      let data = 'fieldId=' + fieldId;

      axios.post(Craft.getActionUrl('fieldreveal/default/delete-field'), data, {'headers': headers})
      .then((response) => {
        Craft.cp.displayNotice("Field deleted");
        this.$emit('field-deleted');
      })
      .catch((error) => {
        Craft.cp.displayError("Failed to delete the field");
      });
    },

  },
}
</script>
