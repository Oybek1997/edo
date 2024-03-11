// Frontend qismi boshlandi

<template>
    <div>
      <v-card class="ma-1 pa-1">
        <v-card-title class="pa-1">
          <span>{{ $t('permission.index') }}</span>
          <v-spacer></v-spacer>
  
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            class="mr-2"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            single-line
            dense
            hide-details
          ></v-text-field>
          <v-btn
              v-if="$store.getters.checkPermission('permission-create')"
              @click="newItem()"
              color="success"
              class="ml-8 pl-1 pr-5"
              dark
              small
          >
            <v-icon class="mr-3" text>mdi-plus-box</v-icon>
            {{ $t("new") }}
          </v-btn>
        </v-card-title>
        <!-- :height="screenHeight" -->
        <v-data-table
          dense
          fixed-header
          :loading-text="$t('loadingText')"
          :no-data-text="$t('noDataText')"
          
          :loading="loading"
          :headers="headers"
          :items="items"
          :search="search"
          class="ma-1"
          style="border: 1px solid #aaa;"
          :server-items-length="server_items_length"
          :options.sync="dataTableOptions"
          :disable-pagination="true"
          :footer-props="{
            itemsPerPageOptions: [20, 50, 100, -1],
            itemsPerPageAllText:$t('itemsPerPageAllText'),
            itemsPerPageText: $t('itemsPerPageText'),
            showFirstLastPage: true,
            firstIcon: 'mdi-arrow-collapse-left',
            lastIcon: 'mdi-arrow-collapse-right',
            prevIcon: 'mdi-arrow-left',
            nextIcon: 'mdi-arrow-right'
          }"
          @update:page="updatePage"
          @update:items-per-page="updatePerPage"
          @update:expanded="toggleExpand"
          @click:row="rowClick"
        >
          <template
              v-slot:item.id="{ item }"
          >{{items.map(function(x) {return x.id; }).indexOf(item.id) + 1}}</template>
          <template
            v-slot:item.id="{ item }"
          >{{items.map(function(x) {return x.id; }).indexOf(item.id) + from}}</template>
          <template v-slot:item.actions="{ item }">
            <v-row>
              <v-icon
                  v-if="$store.getters.checkPermission('permission-update')"
                  color="blue"
                  @click="editItem(item)"
              >
                mdi-pencil
              </v-icon>
              <v-icon
                  v-if="$store.getters.checkPermission('permission-delete')"
                  color="red"
                  @click="deleteItem(item)"
              >
                mdi-delete
              </v-icon>
            </v-row>
          </template>
        </v-data-table>
      </v-card>
      <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
        <v-card>
          <v-card-title class="headline grey lighten-2" primary-title>
            <span class="headline">{{ dialogHeaderText }}</span>
            <v-spacer></v-spacer>
            <v-btn color="red" outlined x-small fab class @click="dialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-form @keyup.native.enter="save" ref="dialogForm">
              <v-row>
                <v-col cols="12">
                  <label for>{{ $t('permission.name') }}</label>
                  <v-text-field
                    v-model="form.name"
                    :rules="[v => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <label for>{{ $t('permission.display_name') }}</label>
                  <v-text-field
                    v-model="form.display_name"
                    :rules="[v => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <label for>{{ $t('permission.description') }}</label>
                  <v-text-field
                    v-model="form.description"
                    :rules="[v => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="save">{{ $t('save') }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="loading" width="300" hide-overlay>
        <v-card color="primary" dark>
          <v-card-text>
            {{ $t('loadingText') }}
            <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </template>
  <script>
  // const axios = require("axios").default;
  import Swal from "sweetalert2";
  export default {
    data() {
      return {
        loading: false,
        search: "",
        dialog: false,
        editMode: null,
        page: 1,
        from: 0,
        server_items_length: -1,
        dataTableOptions: { page: 1, itemsPerPage: 20 },
        page: 1,
        items: [],
        form: {},
        dialogHeaderText: ""
      };
    },
  
    computed: {
      screenHeight() {
        return window.innerHeight - 170;
      },
      headers() {
        return [
          { text: "#", value: "id", align: "center", width: 30 },
          { text: this.$t("permission.name"), value: "name" },
          { text: this.$t("permission.display_name"), value: "display_name" },
          { text: this.$t("permission.description"), value: "description" },
          {
            text: this.$t("actions"),
            value: "actions",
            width: 50,
            align: "center"
          }
        ].filter(
          v =>
            v.value != "actions" ||
            this.$store.getters.checkPermission("permission-update") ||
            this.$store.getters.checkPermission("permission-delete")
        );
      }
    },
    methods: {
      toggleExpand($event) {},
      rowClick(item, row) {
        row.expand(!row.isExpanded);
        // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
        // else this.expanded = [item];
        this.employee = item;
        this.employeeStaff = item.employee_staff;
        this.employeeCoefficients = item.employee_coefficients;
        this.employeeAddresses = item.employee_addresses;
      },
      role() {
        this.myrole = this.$store.getters.getUser().roles;
      },
      updatePage($event) {
        this.getList();
      },
      updatePerPage($event) {
        this.getList();
      },
      getList() {
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/permissions", {
            pagination: this.dataTableOptions,
            search: this.search
          })
          .then(response => {
            this.items = response.data.data;
            this.from = response.data.from;
            this.server_items_length = response.data.total;
            this.loading = false;
          })
          .catch(error => {
            console.log(error);
            this.loading = false;
          });
      },
      newItem() {
        if (this.$store.getters.checkPermission("permission-create")) {
          this.dialogHeaderText = this.$t("permission.dialogHeaderText");
          this.form = {
            id: Date.now(),
            name: "",
            display_name: "",
            description: ""
          };
          this.dialog = true;
          this.editMode = false;
          if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
        }
      },
      editItem(item) {
        if (this.$store.getters.checkPermission("permission-update")) {
          this.dialogHeaderText = this.$t("permission.edit");
          this.formIndex = this.items.indexOf(item);
          this.form = Object.assign({}, item);
          this.dialog = true;
          this.editMode = true;
          if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
        }
      },
      save() {
        if (this.$refs.dialogForm.validate())
          axios
            .post(
              this.$store.state.backend_url + "api/permissions/update",
              this.form
            )
            .then(res => {
              this.getList();
              this.dialog = false;
              const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                onOpen: toast => {
                  toast.addEventListener("mouseenter", Swal.stopTimer);
                  toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
              });
  
              Toast.fire({
                icon: "success",
                title: this.$t("create_update_operation")
              });
            })
            .catch(err => {
              console.log(err);
            });
      }, //document-types
      deleteItem(item) {
        if (this.$store.getters.checkPermission("permission-delete")) {
          const index = this.items.indexOf(item);
          Swal.fire({
            title: this.$t("swal_title"),
            text: this.$t("swal_text"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: this.$t("swal_delete")
          }).then(result => {
            if (result.value) {
              axios
                .delete(
                  this.$store.state.backend_url +
                    "api/permissions/delete/" +
                    item.id
                )
                .then(res => {
                  this.getList(this.page, this.itemsPerPage);
                  this.dialog = false;
                  Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
                })
                .catch(err => {
                  Swal.fire({
                    icon: "error",
                    title: this.$t("swal_error_title"),
                    text: this.$t("swal_error_text")
  
                  });
                  console.log(err);
                });
            }
          });
        }
      }
    },
    mounted() {
      this.getList();
    },
    created() {}
  };
  </script>
  
  <style>
  .text-start {
    border: 1px solid #96B4D8;
  }
  .text-right {
    border: 1px solid #96B4D8;
  }
  .text-center {
    border: 1px solid #96B4D8;
  }
  
  .v-data-table-header th {
    background-color: #CEE2F7 !important;
    color: black !important;
  }
  </style>

// Frontend qismi tugadi


<!-- Route qismi boshlandi -->

Route::post('permissions', 'PermissionController@index');
Route::get('permissions/show', 'PermissionController@show');
Route::delete('permissions/delete/{id}', 'PermissionController@destroy');
Route::post('permissions/update', 'PermissionController@update');

<!-- Route qismi tugadi -->




<!-- Controller qismi boshaladi -->

<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Permission::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $search = $request->input('search');
        
        $user = auth('api')->user();
        if ( $user->hasRole(['superadmin']) ){
            $permission = Permission::select();
            if (isset($search)) {
                $permission->where(function ($query) use ($search) {
                    return $query
                        ->where('name', 'like', "%" . $search . "%")
                        ->orWhere('display_name', 'like', "%" . $search . "%")
                        ->orWhere('description', 'like', "%" . $search . "%");
                });
            }
            return $permission->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
        }else{

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
        $model = Permission::find($request->input('id'));
        if (!$model) {
            $model = new Permission();
            //$model->created_by= Auth::id();
        } else {
            //$model->updated_by = Auth::id();
        }
        $model->name = $request['name'];
        $model->display_name = $request['display_name'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Permission::find($id);
        $model->delete();
    }

    public function checkPermission(Request $request)
    {
        $permission = Permission::where('name', 'report_'.$request['permission'])->first();
        if ($permission) {
            return 1;
        } else {
            return 0;
        }
    }
}


<!-- Controller qismi tugadi -->