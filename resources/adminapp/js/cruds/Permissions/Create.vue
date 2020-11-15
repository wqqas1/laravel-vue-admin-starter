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
              <h4 class="card-title">Create Permission</h4>
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
  name: "CreatePermission",
  data() {
    return {
      status: '',
      form: {},
      activeField: ''
    }
  },
  computed: {
    ...mapGetters('PermissionsSingle', ['entry', 'loading'])
  },
  async mounted(){

  },
  beforeDestroy() {
    this.resetState()
  },
  methods: {
    ...mapActions('PermissionsSingle', ['storeData', 'resetState', 'setEntry']),
    submitForm() {
      this.setEntry(_.cloneDeep(this.form))
      this.storeData()
        .then(() => {
          this.$eventHub.$emit('create-success')
          this.$eventHub.$emit('PermissionsCreateSuccess')
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
