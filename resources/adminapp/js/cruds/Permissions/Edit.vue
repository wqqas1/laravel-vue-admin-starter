<template>
  <div class="container-fluid">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">edit</i>
              </div>
              <h4 class="card-title">Edit Permission</h4>
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
  props: ['id'],
  name: "EditPermission",
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
  beforeDestroy() {
    this.resetState()
  },
  watch: {
    id: {
      immediate: true,
      async handler(value) {
        this.resetState()
        if(value == null)
          return
        await this.fetchEditData(value)
        this.form = _.cloneDeep(this.entry)
      }
    }
  },
  methods: {
    ...mapActions('PermissionsSingle', [
      'fetchEditData',
      'updateData',
      'resetState',
      'setEntry'
    ]),
    submitForm() {
      const self = this;
      self.setEntry(_.cloneDeep(self.form))
      self.updateData()
        .then(() => {
          self.$eventHub.$emit('update-success')
          self.$eventHub.$emit('PermissionsEditSuccess')
          self.resetState()
        })
        .catch(error => {
          self.status = 'failed'
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
