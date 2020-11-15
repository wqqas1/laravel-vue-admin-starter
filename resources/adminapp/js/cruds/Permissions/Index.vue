<template>
  <section>
    <div class="modal fade" id="createPermission" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <create-permission :create_success="createSuccess"></create-permission>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editPermission" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-content">
          <div class="modal-body">
            <edit-permission :id="editId"></edit-permission>
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
              <h4 class="card-title">Permissions Table</h4>
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
  import CreatePermission from './Create'
  import EditPermission from './Edit'
  import DatatableActions from '@components/Datatables/DatatableActions'
  import thFilter from '@components/Datatables/thFilter'

export default {
  components:{
    CreatePermission,
    EditPermission
  },
  data() {
    return {
      editId: null,
      columns: [
        { title: 'ID', field: 'id', sortable: true, colStyle: 'width: 50px;' },
        { title: 'Title', field: 'title', thComp: thFilter, sortable: true },
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
        module: 'Permissions',
        route: 'permissions',
        permission_prefix: 'permission_'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('PermissionsIndex', ['data', 'total', 'loading'])
  },mounted(){
    const self = this
    self.$eventHub.$on(['PermissionsEdit'], async (id) =>{
      self.showEdit(id);
    });
    self.$eventHub.$on(['PermissionsDelete'], async (id) =>{
      self.deleteData(id);
    });
    self.$eventHub.$on(['PermissionsEditSuccess'], async (id) =>{
      self.editSuccess();
    });
    self.$eventHub.$on(['PermissionsCreateSuccess'], async (id) =>{
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
    ...mapActions('PermissionsIndex', ['fetchIndexData', 'destroyData', 'setQuery', 'resetState']),
    showCreate(){
      $('#createPermission').modal('show')
    },
    createSuccess(){
      $('#createPermission').modal('hide')
      this.fetchIndexData()
    },
    showEdit(id){
      this.editId = id
      $('#editPermission').modal('show')
    },
    editSuccess(){
      $('#editPermission').modal('hide')
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
