<template>
  <div class="dt-action-container">
    <router-link
      v-if="$can(xprops.permission_prefix + 'show')"
      :to="{ name: xprops.route + '.show', params: { id: row.id } }"
      class="btn btn-just-icon btn-round btn-link text-azure"
    >
      <i class="material-icons">remove_red_eye</i>
    </router-link>

    <button
      class="btn btn-just-icon btn-round btn-link text-success"
      v-if="$can(xprops.permission_prefix + 'edit')"
      @click="emitEdit(row.id)"
    >
      <i class="material-icons">edit</i>
    </button>

    <button
      class="btn btn-just-icon btn-round btn-link text-rose"
      v-if="$can(xprops.permission_prefix + 'delete')"
      @click.prevent="emitDelete(row.id)"
    >
      <i class="material-icons">delete</i>
    </button>
  </div>
</template>

<script>
export default {
  props: ['row', 'xprops'],
  data() {
    return {
      // Code...
    }
  },
  created() {
    // Code...
  },
  methods: {
    emitDelete(id){
      this.$eventHub.$emit(this.xprops.module + 'Delete', id)
    },
    emitEdit(id){
      this.$eventHub.$emit(this.xprops.module + 'Edit', id)
    }
  }
}
</script>
