<template>
  <q-page padding>
    <template>
      <div class="q-pa-md">
        <q-table
          title="Time Slots"
          :data="statuscodeList"
          :columns="columns"
          row-key="name"
          :pagination.sync="pagination"
          :loading="loading"
          :filter="filter"
          @request="getStatuscodeList"
          binary-state-sort
        >
          <template v-slot:top-right>
            <q-input dense debounce="300" v-model="filter" placeholder="Search">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
            <div class="items-center q-ml-xl">
              <q-btn class="bg-white text-primary shadow-3 q-btn--push" @click="createNew()">
                Create New StatusCode
              </q-btn>
            </div>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props" @click.native="goToDetail(props.row.accountID, props.row.statusCode)">
              <q-td key="accountID" :props="props" v-show="isAdmin">{{ props.row.accountID }}</q-td>
              <q-td key="statusCode" :props="props">{{ props.row.statusCode }}</q-td>
              <q-td key="statusName" :props="props">{{ props.row.statusName }}</q-td>
              <q-td key="description" :props="props">{{ props.row.description }}</q-td>
              <q-td key="buttons" :props="props">
                <q-btn
                  :icon=" 'fas fa-trash-alt' "
                  @click.native.stop
                  @click="removeStatuscode(props.row)"
                />
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </div>
    </template>
  </q-page>
</template>

<script>
export default {
  // name: 'PageName',
}
</script>
