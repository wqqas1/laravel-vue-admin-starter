<template>
  <section>
    <div class="modal fade" id="createUser" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <create-user :create_success="createSuccess"></create-user>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editUser" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <edit-user :id="editId"></edit-user>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">assignment</i>
              </div>
              <h4 class="card-title">Users Table</h4>
            </div>
            <div class="card-body">
              <button
                class="btn btn-primary"
                v-if="$can(xprops.permission_prefix + 'create')"
                @click="showCreate"
              >
                <i class="material-icons">
                  add
                </i>
                Add new
              </button>
              <button
                type="button"
                class="btn btn-default"
                @click="fetchIndexData"
                :disabled="loading"
                :class="{ disabled: loading }"
              >
                <i class="material-icons" :class="{ 'fa-spin': loading }">
                  refresh
                </i>
                Refresh
              </button>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="table-overlay" v-show="loading">
                    <div class="table-overlay-container">
                      <material-spinner></material-spinner>
                      <span>Loading...</span>
                    </div>
                  </div>
                  <datatable
                    :tblClass="'table'"
                    :columns="columns"
                    :data="data"
                    :total="total"
                    :query="query"
                    :xprops="xprops"
                    :pageSizeOptions="[10, 25, 50, 100]"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import CreateUser from './Create'
import EditUser from './Edit'
import DatatableActions from '@components/Datatables/DatatableActions'
import DatatableSingle from '@components/Datatables/DatatableSingle'
import DatatableList from '@components/Datatables/DatatableList'
import DatatableCheckbox from '@components/Datatables/DatatableCheckbox'
import DatatableEnum from '@components/Datatables/DatatableEnum'
import DatatablePictures from '@components/Datatables/DatatablePictures'
import thFilter from '@components/Datatables/thFilter'


export default {
  components:{
    CreateUser,
    EditUser
  },
  data() {
    return {
      editId: null,
      columns: [
        { title: 'ID', field: 'id', sortable: true, colStyle: 'width: 50px;' },
        { title: 'Name', field: 'name', thComp: thFilter, sortable: true },
        { title: 'Email', field: 'email', thComp: thFilter, sortable: true },
        {
          title: 'Email verified at',
          field: 'email_verified_at',
          sortable: true
        },
        { title: 'Roles', field: 'roles.title', tdComp: DatatableList },
        { title: 'Photo', field: 'photo', tdComp: DatatablePictures },
        {
          title: 'Actions',
          tdComp: DatatableActions,
          visible: true,
          thClass: 'text-right',
          tdClass: 'text-right td-actions',
          colStyle: 'width: 150px;'
        }
      ],
      query: { sort: 'id', order: 'desc', limit: 100 },
      xprops: {
        module: 'Users',
        route: 'users',
        permission_prefix: 'user_'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('UsersIndex', ['data', 'total', 'loading'])
  },
  mounted(){
    const self = this
    self.$eventHub.$on(['UsersEdit'], async (id) =>{
      self.showEdit(id);
    });
    self.$eventHub.$on(['UsersDelete'], async (id) =>{
      self.deleteData(id);
    });
    self.$eventHub.$on(['UsersEditSuccess'], async () =>{
      self.editSuccess();
    });
    self.$eventHub.$on(['UsersCreateSuccess'], async () =>{
      self.createSuccess();
    });
  },
  watch: {
    query: {
      handler(query) {
        query.page = (query.offset + query.limit) / query.limit
        this.setQuery(query)
        this.fetchIndexData()
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('UsersIndex', ['fetchIndexData', 'destroyData', 'setQuery', 'resetState']),
    showCreate(){
      $('#createUser').modal('show')
    },
    createSuccess(){
      $('#createUser').modal('hide')
      this.fetchIndexData()
    },
    showEdit(id){
      this.editId = id
      $('#editUser').modal('show')
    },
    editSuccess(){
      $('#editUser').modal('hide')
      this.fetchIndexData()
    },
    deleteData(id) {
      const self = this
      this.$swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#dd4b39',
        focusCancel: true,
        reverseButtons: true
      }).then(async result => {
        if (result.value) {
          await self.destroyData(id)
            .then(result => {
              self.$eventHub.$emit('delete-success')
            })
        }
      })
    }
  }
}
</script>
