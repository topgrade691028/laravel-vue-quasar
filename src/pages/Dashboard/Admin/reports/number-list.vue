<template>
  <q-page>
    <template>
      <div>
        <q-table
          :class="
            is_mobile === 'ios'
              ? 'my-sticky-dynamic table-top-ios'
              : is_mobile === 'android'
              ? 'my-sticky-dynamic table-top-android'
              : 'my-sticky-dynamic'
          "
          title="ROUTES"
          :data="routeList"
          :columns="userLevel === 'admin' ? columns_admin : columns"
          row-key="route_number"
          :pagination.sync="pagination"
          :filter="filter"
          @request="getRoutes"
          binary-state-sort
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top-left>
            <div class="items-center">
              <q-btn
                rounded
                dense
                no-caps
                label="+Add Route"
                color="blue-7"
                style="width: 130px; height: 40px"
                @click="goToDetail()"
              />
            </div>
          </template>
          <template v-slot:top-right>
            <q-select
              dense
              v-model="selectedRoute"
              :options="routeNumbers"
              :option-value="opt => opt == null ? null : opt"
              :option-label="opt => opt == null ? '- Null -' : opt.route_number"
              bg-color="transparnet"
              color="blue-7"
              label="ROUTE"
              label-color="white"
              behavior="menu"
              @input="onChangeRoute"
              style="min-width: 130px"
              class="q-select-list"
            >
              <template v-if="selectedRoute" v-slot:append>
                <q-icon name="cancel" @click.stop="selectedRoute = null" @click="onChangeRoute" class="cursor-pointer text-white" />
              </template>
            </q-select>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" v-if="props.row.user" @click.native="goToDetail(props.row)">
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <q-td key="route_number" :props="props">{{ props.row.route_number }}</q-td>
              <q-td key="route_type" :props="props">{{ !props.row.route_type ? "DAILY" : "EXTRA" }}</q-td>
              <q-td v-if="userLevel === 'admin'" key="user_name" :props="props">{{ props.row.user?props.row.user.name : '' }}</q-td>
              <!-- <q-td key="buttons" :props="props">
                <q-btn
                  flat
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="remove(props.row)"
                />
              </q-td> -->
            </q-tr>
          </template>

          <template v-slot:bottom="props">
            <div class="col-12 row justify-end items-center">
              Total Records: {{ pagination.rowsNumber }}
              <q-btn
                icon="chevron_left"
                color="grey-8"
                round
                dense
                flat
                :disable="props.isFirstPage"
                @click="props.prevPage"
              />
              <span
                >{{ props.pagination.page }} /
                {{
                  Math.ceil(
                    props.pagination.rowsNumber / props.pagination.rowsPerPage
                  )
                }}</span
              >
              <q-btn
                icon="chevron_right"
                color="grey-8"
                round
                dense
                flat
                :disable="props.isLastPage"
                @click="props.nextPage"
              />
            </div>
          </template>
        </q-table>
      </div>
      <q-dialog
        v-model="showDetail"
        persistent
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card
          style="background-color: #3e444e; max-width: 500px; min-height: 500px"
        >
          <q-bar style="background-color: #272b33">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
            </q-btn>
            <div class="text-h6 text-white">{{ dialogTitle }}</div>
          </q-bar>

          <q-separator />

          <q-card-section style="max-height: 50vh" class="scroll">
            <q-form
              @submit="onSubmit"
              ref="selectedNumber"
              :model="selectedNumber"
              style="width: 320px; margin: auto"
            >
              <div class="row justify-between q-col-gutter-md">
                <div class="col-12">
                  <span class="text-white">Route</span>
                  <q-input
                    dense
                    outlined
                    required
                    v-model="selectedNumber.route_number"
                    color="blue-7"
                    bg-color="white"
                    input-class="text-black"
                  ></q-input>
                  <q-separator class="q-my-md" color="grey-4" />
                  <span class="text-white">Type</span>
                  <q-select
                    dense
                    required
                    outlined
                    v-model="selectedNumber.route_type"
                    :options="route_types"
                    :option-value="(opt) => (opt === null ? null : opt.value)"
                    :option-label="(opt) => (opt === null ? '- Null -' : opt.label)"
                    emit-value
                    map-options
                    class="q-mb-xs"
                    behavior="menu"
                    color="blue-7"
                    bg-color="white"
                    input-class="text-black"
                  >
                  </q-select>
                </div>
              </div>
              <q-card-actions align="center">
                <q-btn
                  no-caps
                  dense
                  rounded
                  v-if="!isNewRecord"
                  label="Delete"
                  color="blue-7"
                  @click="remove"
                  style="width: 100px; height: 40px"
                />
                <q-btn
                  :label="isNewRecord ? 'Add' : 'Update'"
                  color="blue-7"
                  no-caps
                  dense
                  rounded
                  style="width: 100px; height: 40px"
                  type="submit"
                />
              </q-card-actions>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </template>
  </q-page>
</template>

<script>
import { api } from "src/boot/api";
import { Loading } from "quasar";

export default {
  name: "RouteList",
  data() {
    return {
      filter: "",
      showDetail: false,
      pagination: {
        sortBy: "route_number",
        descending: false,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20,
      },
      route_types: [
        { label: "DAILY", value: 0 },
        { label: "EXTRA", value: 1 },
      ],
      columns: [
        { name: "no", required: true, label: "NO", align: "left", field: "no" },
        { name: "route_number", required: true, label: "ROUTE", align: "left", field: "route_number", },
        { name: "route_type", required: true, label: "TYPE", align: "left", field: "route_type", },
      ],
      columns_admin: [
        { name: "no", required: true, label: "NO", align: "left", field: "no" },
        { name: "route_number", required: true, label: "ROUTE", align: "left", field: "route_number", },
        { name: "route_type", required: true, label: "TYPE", align: "left", field: "route_type", },
        { name: "user_name", required: true, label: "USER", align: "left", field: "user_name", },
      ],
      routeList: [],
      selectedNumber: {
        route_number: "",
        route_type: "",
      },
      selectedRoute: null,
      routeNumbers: [],
      dialogTitle: "",
      is_mobile: "web",
      isNewRecord: true
    };
  },
  async created() {
    this.checkPlatform();
    // get initial vehicleList from server (1st page)
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    await this.getRouteAll();
    this.getRoutes({
      pagination: this.pagination,
      filter: undefined,
    });
  },
  computed: {
    // ...mapFields('commons', ['pageMeta'])
    user: {
      get() {
        return this.$store.state.auth.user;
      },
    },
    userLevel: {
      get() {
        return this.$store.state.auth.userLevel;
      },
    },
  },
  methods: {
    // createNew () {
    //   this.$router.push({ name: 'NewNumber' })
    // },
    checkPlatform() {
      if (this.$q.platform.is.mobile) {
        if (this.$q.platform.is.ios) {
          this.is_mobile = "ios";
        } else {
          this.is_mobile = "android";
        }
      } else {
        this.is_mobile = "web";
      }
    },
    goToDetail(data) {
      if (data) {
        this.isNewRecord = false;
        this.dialogTitle = "Edit Route";
        this.selectedNumber.id = data.id;
        this.selectedNumber.route_number = data.route_number;
        this.selectedNumber.route_type = data.route_type;
      } else {
        this.isNewRecord = true;
        this.dialogTitle = "Add Route";
        this.selectedNumber = {
          route_number: "",
          route_type: "",
        };
      }
      // this.$router.push({ name: 'BuyerDetail', params: { id: id } })
      this.showDetail = true;
    },
    getRouteAll: async function () {
      try {
        let res = await api.getRouteAll()
        this.routeNumbers = res.data.data
      } catch (e) {
      }
    },
    onChangeRoute () {
      this.getRoutes({
        pagination: this.pagination,
        isScroll: false,
      });
    },
    async onScroll({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination;
      const lastIndex = this.routeList.length - 1;
      const lastPage = Math.ceil(rowsNumber / rowsPerPage);
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++;
        await this.getRoutes({
          pagination: this.pagination,
          isScroll: true,
        });
      }
    },
    getRoutes: async function (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } =
        props.pagination;
      let filter = props.filter;
      let isScroll = props.isScroll;

      // get all rows if "All" (0) is selected
      let fetchCount = rowsPerPage === 0 ? rowsNumber : rowsPerPage;

      // calculate starting row of driverList
      let startRow = (page - 1) * rowsPerPage;

      const params = {
        conditions: {},
        start: startRow,
        numPerPage: fetchCount,
        sortBy: sortBy,
        descending: descending,
      };
      if (this.selectedRoute) {
        params.conditions.route_number = this.selectedRoute.route_number
      }

      // fetch vehicleList from "server"
      Loading.show();
      try {
        let res = await api.getRoutes(params);
        Loading.hide();

        // clear out existing vehicleList and add new
        res.data.data.forEach((row, index) => {
          row.index = (page - 1) * 10 + index + 1;
        });
        if (isScroll) {
          this.routeList = this.routeList.concat(res.data.data);
        } else {
          this.routeList = res.data.data;
        }
        // update rowsCount with appropriate value
        this.pagination.rowsNumber = res.data.totalCount;

        // don't forget to update local pagination object
        this.pagination.page = page;
        this.pagination.rowsPerPage = rowsPerPage;
        this.pagination.sortBy = sortBy;
        this.pagination.descending = descending;

        // ...and turn of loading indicator
      } catch (e) {
        Loading.hide();
        console.log("errorrrrrrrrrr", e);
      }
    },
    cancelDetail() {
      this.showDetail = false;
      this.selectedNumber = {};
    },
    async onSubmit() {
      const params = {
        data: this.selectedNumber,
      };
      if (this.selectedNumber.id) {
        params.conditions = {
          id: this.selectedNumber.id,
        };
        Loading.show();
        try {
          let res = await api.updateRoute(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.cancelDetail();
      } else {
        Loading.show();
        try {
          let res = await api.createRoute(params);
          Loading.hide();
          console.log("result", res.data);
        } catch (error) {
          Loading.hide();
          console.log("error", error);
        }
        this.cancelDetail();
      }
      this.getRoutes({
        pagination: this.pagination,
        filter: undefined,
      });
    },
    remove() {
      // Confirm Remove Vehicle
      this.$q
        .dialog({
          title: "Confirm",
          message:
            "Are you surely remove " + this.selectedNumber.route_number + "?",
          cancel: true,
          persistent: true,
          color: "blue-7",
        })
        .onOk(async () => {
          const params = {
            conditions: {
              id: this.selectedNumber.id,
            },
          };
          Loading.show();
          try {
            let res = await api.removeRoute(params);
            Loading.hide();
            console.log("remove result", res);
            this.$q.notify({
              color: "positive",
              position: "top",
              message:
                this.selectedNumber.route_number + " is removed successfully !",
            });
            this.cancelDetail();
            this.getRoutes({
              pagination: this.pagination,
              filter: undefined,
            });
          } catch (e) {
            Loading.hide();
          }
        })
        .onCancel(() => {})
        .onDismiss(() => {});
    }
  }
};
</script>
