<template>
  <div>
    <fields-filter 
      :field-groups="fieldGroups"
      @filter-fields="filterFields"
      @clear-filters="clearFilters"
    >
    </fields-filter>

    <fields-table 
      :fields="fields"
      :loading-fields="loadingFields"
      :unused-field-ids="unusedFieldIds"
      @field-deleted="onFieldDelete()"
    >
    </fields-table>
  </div>
</template>

<script>
import axios from 'axios';
import FieldsTable from './FieldsTable';
import FieldsFilter from './FieldsFilter';

export default {
  components: {
    FieldsTable,
    FieldsFilter,
  },

  data() {
    return {
      fields: [],
      unusedFieldIds: [],
      fieldGroups: [],

      loadingFields: true,
    };
  },

  mounted() {
    this.loadFields();
  },

  methods: {
    loadFields (params) {
      this.fields = [];
      axios.get('/actions/fieldreveal/default/get-fields', {params})
      .then((response) => {
        this.fields = response.data.fields;
        this.unusedFieldIds = response.data.unusedFieldIds;
        this.fieldGroups = response.data.fieldGroups;
        this.loadingFields = false;
      })
      .catch((error) => {
        Craft.cp.displayError("Failed to load the fields");
        this.loadingFields = false;
      });
    },

    onFieldDelete () {
      this.loadFields();
    },

    filterFields (filters) {
      if ( !(Object.keys(filters).length === 0 && filters.constructor === Object) )
      {
        this.loadFields(filters);
      }
    },

    clearFilters () {
      this.loadFields();
    },

  }
}
</script>

