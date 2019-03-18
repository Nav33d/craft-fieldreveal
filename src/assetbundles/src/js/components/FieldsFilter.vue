<template>
  <div class="mb-6">
    <form class="flex justify-end" @submit.prevent="filterFields()">
      <div>
        <span class="status" :class="{'on': filterActive}" title="Filter status"></span>
      </div>

      <div>
        <label class="hidden">Name</label>
        <div>
          <input class="text" type="text" name="field_name" placeholder="Search by field name" v-model="filters.fieldName" />
        </div>
      </div>
      
      <div v-if="fieldGroups.length">
        <label class="hidden">Filter by field group</label>
        <div class="select">
          <select v-model="filters.fieldGroup">
            <option value="" selected disabled>Filter by field group</option>
            <option v-for="(item, index) in fieldGroups" :key="index" :value="item.name">{{ item.name }}</option>
          </select>
        </div>
      </div>

      <div>
        <button class="btn submit">Filter</button>
        <button class="btn" @click.prevent="clearFilters()">Clear filters</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    fieldGroups: Array,
  },

  data () {
    return {
      filters: {},
      filterActive: false,
    };
  },

  mounted () {
    this.resetFiltersObject();
  },

  methods: {
    filterFields () {
      if ( !this.isEmptyObject(this.filters)  )
      {
        this.filterActive = true;
        this.$emit('filter-fields', this.filters);
      }
    },

    clearFilters () {
      if ( !this.isEmptyObject(this.filters) || this.filterActive )
      {
        this.resetFiltersObject();
        this.filterActive = false;
        this.$emit('clear-filters');
      }
    },

    isEmptyObject (object) {
      return Object.keys(object).every(function(x) {
          return object[x]===''||object[x]===null;
      });
    },

    resetFiltersObject () {
      this.filters = { fieldGroup: "" };
    },

  },
}
</script>

