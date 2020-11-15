<template>
  <div class="container-fluid">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">add</i>
              </div>
              <h4 class="card-title">Create Role</h4>
            </div>
            <div class="card-body">
              <bootstrap-alert />
              <div class="row">
                <div class="col-md-12">
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.title,
                      'is-focused': activeField == 'title'
                    }"
                  >
                    <label class="bmd-label-floating required">Title</label>
                    <input
                      class="form-control"
                      type="text"
                      v-model="form.title"
                      @focus="focusField('title')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.permissions.length !== 0,
                      'is-focused': activeField == 'permissions'
                    }"
                  >
                    <label class="bmd-label-floating required"
                      >Permissions</label
                    >
                    <v-select
                      name="permissions"
                      label="title"
                      :key="'permissions-field'"
                      v-model="form.permissions"
                      :options="lists.permissions"
                      :closeOnSelect="false"
                      multiple
                      @search.focus="focusField('permissions')"
                      @search.blur="clearFocus"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <vue-button-spinner
                class="btn-primary"
                :status="status"
                :isLoading="loading"
                :disabled="loading"
              >
                Save
              </vue-button-spinner>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: "CreateRole",
  data() {
    return {
      status: '',
      form:{
        permissions: []
      },
      activeField: ''
    }
  },
  computed: {
    ...mapGetters('RolesSingle', ['entry', 'loading', 'lists'])
  },
  async mounted() {
    await this.fetchCreateData()
    this.form = _.cloneDeep(this.entry)
  },
  beforeDestroy() {
    this.resetState()
  },
  methods: {
    ...mapActions('RolesSingle', [
      'storeData',
      'resetState',
      'setEntry',
      'fetchCreateData'
    ]),
    submitForm() {
      this.setEntry(_.cloneDeep(this.form))
      this.storeData()
        .then(() => {
          this.$eventHub.$emit('create-success')
          this.$eventHub.$emit('RolesCreateSuccess')
        })
        .catch(error => {
          this.status = 'failed'
          _.delay(() => {
            this.status = ''
          }, 3000)
        })
    },
    focusField(name) {
      this.activeField = name
    },
    clearFocus() {
      this.activeField = ''
    }
  }
}
</script>
