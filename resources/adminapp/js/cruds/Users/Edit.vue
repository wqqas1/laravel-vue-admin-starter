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
              <h4 class="card-title">Edit User</h4>
            </div>
            <div class="card-body">
              <bootstrap-alert />
              <div class="row">
                <div class="col-md-12">
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.name,
                      'is-focused': activeField == 'name'
                    }"
                  >
                    <label class="bmd-label-floating required">Name</label>
                    <input
                      class="form-control"
                      type="text"
                      v-model="form.name"
                      @focus="focusField('name')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.email,
                      'is-focused': activeField == 'email'
                    }"
                  >
                    <label class="bmd-label-floating required">Email</label>
                    <input
                      class="form-control"
                      type="text"
                      v-model="form.email"
                      @focus="focusField('email')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.password,
                      'is-focused': activeField == 'password'
                    }"
                  >
                    <label class="bmd-label-floating">Password</label>
                    <input
                      class="form-control"
                      type="password"
                      v-model="form.password"
                      @focus="focusField('password')"
                      @blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': form.roles.length !== 0,
                      'is-focused': activeField == 'roles'
                    }"
                  >
                    <label class="bmd-label-floating required">Roles</label>
                    <v-select
                      name="roles"
                      label="title"
                      :key="'roles-field'"
                      v-model="form.roles"
                      :options="lists.roles"
                      :closeOnSelect="false"
                      multiple
                      @search.focus="focusField('roles')"
                      @search.blur="clearFocus"
                    />
                  </div>
                  <div class="form-group">
                    <label>Logo</label>
                    <attachment
                        :route="getRoute('users')"
                        :collection-name="'user_photo'"
                        :media="form.photo"
                        :model-id="form.id"
                        :max-file-size="2"
                        :component="'pictures'"
                        :accept="'image/*'"
                        @file-uploaded="insertPhotoFile"
                        @file-removed="removePhotoFile"
                        :max-files="1"
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
import Attachment from '@components/Attachments/Attachment'

export default {
  props: ['id'],
  name: 'EditUser',
  components: {
    Attachment
  },
  data() {
    return {
      status: '',
      form: {
        roles: [],
        photo: []
      },
      activeField: ''
    }
  },
  computed: {
    ...mapGetters('UsersSingle', ['entry', 'loading', 'lists'])
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
    ...mapActions('UsersSingle', [
      'fetchEditData',
      'updateData',
      'resetState',
      'setEntry'
    ]),
    insertPhotoFile(file) {
      this.form.photo.push(file)
      this.setEntry(_.cloneDeep(this.form))
    },
    removePhotoFile( file) {
      this.form.photo = this.form.photo.filter(item => {
        return item.id !== file.id
      })
      this.setEntry(_.cloneDeep(this.form))
    },
    getRoute(name) {
      return `${axios.defaults.baseURL}${name}/media`
    },
    submitForm() {
      this.setEntry(_.cloneDeep(this.form))
      this.updateData()
        .then(() => {
          this.$eventHub.$emit('update-success')
          this.$eventHub.$emit('UsersEditSuccess')
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
