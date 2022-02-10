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
          :data="dpdDataList"
          :columns="columns"
          row-key="dpdDate"
          :filter="filter"
          @request="getDpdDataList"
          binary-state-sort
          :pagination.sync="pagination"
          virtual-scroll
          :virtual-scroll-item-size="48"
          :virtual-scroll-sticky-size-start="48"
          :rows-per-page-options="[0]"
          @virtual-scroll="onScroll"
        >
          <template v-slot:top>
            <div class="col-6 row justify-start q-pr-sm">
              <q-btn
                color="blue-7"
                :label="dpdSid ? 'UPDATE':'LOGIN'"
                no-caps
                dense
                rounded
                @click="showDpdLoginDlg"
                style="width: 130px; height: 40px"
              />
            </div>
            <div class="col-6 row justify-end q-pl-sm">
              <q-select
                dense
                v-model="selectedPeriod"
                :options="periods"
                :option-value="opt => opt == null ? null : opt"
                :option-label="opt => opt == null ? '- Null -' : opt.duration"
                bg-color="transparent"
                color="blue-7"
                label-color="whtie"
                behavior="menu"
                @input="onChangePeriod"
                class="q-select-list"
              />
            </div>
          </template>

          <template v-slot:body="props">
            <q-tr
              :props="props"
              @click.native="goToDetail(props.row.dpdDate)"
            >
              <q-td key="no" :props="props">{{ props.row.index }}</q-td>
              <q-td key="dpdData" :props="props" class="text-uppercase">{{
                changeDateFormat(props.row.dpdDate)
              }}</q-td>
              <q-td key="route_count" :props="props"
                >{{
                  props.row.route_count
                }}</q-td
              >
              <q-td key="buttons" :props="props">
                <q-btn
                  rounded
                  flat
                  :icon="'fas fa-pen-alt'"
                  @click.native.stop
                  @click="goToDetail(props.row.dpdDate)"
                />
              </q-td>
            </q-tr>
          </template>

          <template v-slot:bottom>
            <div class="col-12 row justify-between">
              <div class="q-my-auto"></div>
              <div class="row justify-end items-center">
                Total Records: {{ pagination.rowsNumber }}
              </div>
            </div>
          </template>
        </q-table>
      </div>
      <q-dialog
        v-model="showLoginDlg"
        persistent
        transition-show="scale"
        transition-hide="scale"
      >
        <q-card
          style="
            background-color: #3e444e;
            max-width: 500px;
            min-width: 352px;
            min-height: 300px;
          "
        >
          <q-bar style="background-color: #272b33">
            <q-btn dense flat icon="close" color="white" v-close-popup>
              <q-tooltip content-class="bg-white text-black">Close</q-tooltip>
            </q-btn>
          </q-bar>

          <q-separator />

          <div>
            <q-card-section>
              <q-form @submit="dpdLogin" :model="dpdUser" ref="dpdUserForm">
                <div class="text-h6 text-white text-center">ODF LOGIN</div>
                <span class="text-white">Username</span>
                <q-input
                  required
                  dense
                  outlined
                  bg-color="blue-grey-4"
                  class="q-pb-md"
                  input-class="text-white q-pr-xl"
                  label-color="grey-3"
                  color="blue-7"
                  v-model="dpdUser.email"
                ></q-input>
                <span class="text-white">Password</span>
                <base-text-field
                  required
                  dense
                  outlined
                  bg-color="blue-grey-4"
                  class="q-pb-md"
                  input-class="text-white q-pr-xl"
                  label-color="grey-3"
                  color="blue-7"
                  v-model="dpdUser.password"
                  type="password"
                  hide-show-password
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-account-key" />
                  </template>
                </base-text-field>
                
                <q-card-actions align="center">
                  <q-btn
                    label="DPD Login"
                    color="blue-7"
                    no-caps
                    dense
                    rounded
                    class="q-mt-xs"
                    style="width: 100px; height:40px;"
                    type="submit"
                  />
                </q-card-actions>
              </q-form>
            </q-card-section>
          </div>
        </q-card>
      </q-dialog>
    </template>
  </q-page>
</template>

<style lang="stylus">
.q-btn__wrapper {
  padding: 4px 6px;
}

.layout-center {
  position: absolute;
  width: 100%;
  top: 50%;
  left: 50%;
  transform: translateY(-50%) translateX(-50%);
  padding-left: 25px;
  padding-right: 25px;
}
</style>

<script>
import { api } from "src/boot/api";
import { Loading, date, exportFile } from "quasar";

function wrapCsvValue(val, formatFn) {
  console.log("csv", val);
  let formatted = formatFn !== void 0 ? formatFn(val) : val;

  formatted =
    formatted === void 0 || formatted === null ? "" : String(formatted);

  formatted = formatted.split('"').join('""');

  return `"${formatted}"`;
}

export default {
  name: "DPD",
  data() {
    return {
      filter: "",
      showLoginDlg: false,
      dpdUser: {
        email: "",
        password: "",
      },
      selectedPeriod: {},
      periods: [],
      pagination: {
        sortBy: "dpdDate",
        descending: true,
        page: 1,
        rowsPerPage: 100,
        rowsNumber: 20,
      },
      columns: [
        { name: "no", required: true, label: "NO", align: "left", field: "no" },
        {
          name: "dpdData",
          required: true,
          label: "DATE",
          align: "left",
          field: "dpdData",
        },
        {
          name: "route_count",
          required: true,
          label: "COUNT",
          align: "left",
          field: "route_count",
        },
        { name: "buttons", label: "", field: "buttons" },
      ],
      dpdDataList: [],
      is_mobile: "web",
    };
  },
  computed: {
    dpdSid: {
      get() {
        return this.$store.state.auth.dpdSid;
      }
    }
  },
  async mounted() {
    this.checkPlatform();
    this.$store.commit("auth/pageTitle", this.$router.currentRoute.meta.title);
    await this.getPeriods();
    // await this.getDpdDataList();
  },
  methods: {
    // createNew () {
    //   this.$router.push('/dashboard/schedules/new')
    // },
    goToDetail (dpdDate) {
      this.$router.push({ name: 'Edit DPD Modules', params: { dpdDate: dpdDate } })
    },
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
    changeDateFormat(reportDate) {
      let convertedDate = date.formatDate(date.addToDate(date.extractDate(reportDate, "YYYY-MM-DD"), {hours: 5}),"DD-MM-YY ddd");
      return convertedDate;
    },
    showDpdLoginDlg() {
      if (!this.periods.length) {
          this.showLoginDlg = true;
        // this.dpdLogin();
      } else {
        if (this.dpdSid) {
          this.updateDpdData();
        } else {
          this.showLoginDlg = true;
        }
      }
    },
    async getPeriods () {
      Loading.show();
      try {
        let currentYear = new Date().getFullYear();
        let currentDate = date.formatDate(new Date(), "YYYY-MM-DD");
        let res = await api.getPeriods(currentDate);
        Loading.hide();
        this.periods = res.data.data;
        if (this.periods.length) {
          this.selectedPeriod = this.periods.filter(period => {
            if (period.firstTransactionDate <= currentDate && period.lastTransactionDate >= currentDate) {
              return period
            }
          })[0]
        }
        if (!this.periods.length) {
          this.showLoginDlg = true;
        } else {
          await this.getDpdDataList({
            pagination: this.pagination,
            filter: this.filter,
            isScroll: false,
          });
        }
      } catch (error) {
        console.log("get periods error", error);
        Loading.hide();
      }
    },
    exportTable() {
      // naive encoding to csv format
      let columns = this.columns;
      const content = [columns.map((col) => wrapCsvValue(col.label))]
        .concat(
          this.dpdDataList.map((row) =>
            columns
              .map((col) => {
                if (col.field === "is_group") {
                  return wrapCsvValue(
                    row.is_group === 1 ? "DAILY" : "EXTRA",
                    col.format
                  );
                } else if (col.field === "user_name") {
                  return wrapCsvValue(row.user.full_name, col.format);
                } else if (col.field === "report_date") {
                  return wrapCsvValue(row.report_date, col.format);
                } else {
                  return wrapCsvValue(row[col.field], col.format);
                }
              })
              .join(",")
          )
        )
        .join("\r\n");

      const status = exportFile("table-export.csv", content, "text/csv");

      if (status !== true) {
        this.$q.notify({
          message: "Browser denied file download...",
          color: "negative",
          icon: "warning",
        });
      }
    },

    async updateDpdData() {
      Loading.show();
      try {
        let params = {
          sid: this.dpdSid,
          period: {
            startDate: this.selectedPeriod.firstTransactionDate,
            endDate: this.selectedPeriod.lastTransactionDate,
            previousCutOffDate: this.selectedPeriod.previousCutOffDate,
            cutOffDate: this.selectedPeriod.cutOffDate
          }
        }
        await api.updateDpdData(params);
        await this.getDpdDataList({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: false,
        });
        Loading.hide();
        
      } catch (error) {
        console.log("get periods error", error);
        Loading.hide();
      }
    },
    async dpdLogin() {
      Loading.show();
      try {
        let res = {};
        if (this.periods.length) {
          let params = {
            email: this.dpdUser.email,
            password: this.dpdUser.password,
            period: {
              startDate: this.selectedPeriod.firstTransactionDate,
              endDate: this.selectedPeriod.lastTransactionDate,
              previousCutOffDate: this.selectedPeriod.previousCutOffDate,
              cutOffDate: this.selectedPeriod.cutOffDate
            }
          }
          res = await api.dpdLogin(params);
          this.$store.commit("auth/setDpdSid", res.data.sid);
        } else {
          let currentDate = date.formatDate(new Date(), "YYYY-MM-DD");
          let params = {
            email: this.dpdUser.email,
            password: this.dpdUser.password,
            currentDate: currentDate
          }
          res = await api.getDpdPeriods(params);
          this.$store.commit("auth/setDpdSid", res.data.sid);
          this.periods = res.data.data;
          if (this.periods.length) {
            this.selectedPeriod = this.periods.filter(period => {
              if (period.firstTransactionDate <= currentDate && period.lastTransactionDate >= currentDate) {
                return period
              }
            })[0];
          }
        }
        this.showLoginDlg = false;
        Loading.hide();
      } catch (error) {
        console.log("dpd login error", error);
        Loading.hide();
        this.showLoginDlg = false;
      }
    },
    async onScroll({ index, from, to, ref }) {
      let { page, rowsPerPage, rowsNumber } = this.pagination;
      const lastIndex = this.dpdDataList.length - 1;
      const lastPage = Math.ceil(rowsNumber / rowsPerPage);
      if (index > 0 && page < lastPage && index === lastIndex) {
        this.pagination.page++;
        await this.getDpdDataList({
          pagination: this.pagination,
          filter: this.filter,
          isScroll: true,
        });
      }
    },
    async onChangePeriod () {
      await this.getDpdDataList({
        pagination: this.pagination,
        filter: this.filter,
        isScroll: false,
      });
    },
    getDpdDataList: async function (props) {
      if (this.selectedPeriod.firstTransactionDate) {
        let { page, rowsPerPage, rowsNumber, sortBy, descending } =
          props.pagination;
        let filter = props.filter;
        let isScroll = props.isScroll

        // get all rows if "All" (0) is selected
        let fetchCount = rowsPerPage === 0 ? rowsNumber : rowsPerPage;

        // calculate starting row of driverList
        let startRow = (page - 1) * rowsPerPage;

        const params = {
          start: startRow,
          numPerPage: fetchCount,
          sortBy: sortBy,
          descending: descending,
          fromDate: this.selectedPeriod.firstTransactionDate,
          endDate: this.selectedPeriod.lastTransactionDate,
        };
        if (filter) {
          params.conditions.filter = filter;
        }

        // fetch vehicleList from "server"
        Loading.show();
        try {
          let res = await api.getDpdDatas(params);
          Loading.hide();

          // clear out existing vehicleList and add new
          res.data.data.forEach((row, index) => {
            row.index = (page - 1) * 100 + index + 1
          })
          if (isScroll) {
            this.dpdDataList = this.dpdDataList.concat(res.data.data)
          } else {
            this.dpdDataList = res.data.data
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
        }
      } else {
        this.$q.notify({
          message: "Please choose the period first...",
          color: "negative",
          icon: "warning",
        });
        return;
      }
    },
  },
  created() {},
};
</script>
