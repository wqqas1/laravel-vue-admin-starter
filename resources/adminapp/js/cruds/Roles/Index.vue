<template>
  <section>
    <div class="modal fade" id="createRole" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <create-role :create_success="createSuccess"></create-role>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editRole" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <edit-role :id="editId"></edit-role>
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
              <h4 class="card-title">Roles</h4>
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
import CreateRole from './Create'
import EditRole from './Edit'
import DatatableActions from '@components/Datatables/DatatableActions'
import DatatableList from '@components/Datatables/DatatableList'
import thFilter from '@components/Datatables/thFilter'

export default {
  components:{
    CreateRole,
    EditRole
  },
  data() {
    return {
      editId: null,
      columns: [
        { title: 'ID', field: 'id', sortable: true, colStyle: 'width: 50px;' },
        { title: 'Title', field: 'title', thComp: thFilter, sortable: true },
        {
          title: 'Permissions',
          field: 'permissions.title',
          tdComp: DatatableList
        },
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
        module: 'Roles',
        route: 'roles',
        permission_prefix: 'role_'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('RolesIndex', ['data', 'total', 'loading'])
  },
  mounted(){
    const self = this
    self.$eventHub.$on(['RolesEdit'], async (id) =>{
      self.showEdit(id);
    });
    self.$eventHub.$on(['RolesDelete'], async (id) =>{
      self.deleteData(id);
    });
    self.$eventHub.$on(['RolesEditSuccess'], async (id) =>{
      self.editSuccess();
    });
    self.$eventHub.$on(['RolesCreateSuccess'], async (id) =>{
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
    ...mapActions('RolesIndex', ['fetchIndexData', 'destroyData', 'setQuery', 'resetState']),
    showCreate(){
      $('#createRole').modal('show')
    },
    createSuccess(){
      $('#createRole').modal('hide')
      this.fetchIndexData()
    },
    showEdit(id){
      this.editId = id
      $('#editRole').modal('show')
    },
    editSuccess(){
      $('#editRole').modal('hide')
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
