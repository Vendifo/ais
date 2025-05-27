<template>
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Нагрузка по типам занятий</h5>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="text-center my-3">
          <div class="spinner-border text-danger" role="status" aria-hidden="true"></div>
        </div>
  
        <div v-else-if="error" class="alert alert-danger m-3" role="alert">
          Ошибка: {{ error }}
        </div>
  
        <table v-else class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Тип занятия</th>
              <th class="text-end">План (ч)</th>
              <th class="text-end">Факт (ч)</th>
              <th class="text-end">Разница</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in reports" :key="index">
              <td>{{ item.type }}</td>
              <td class="text-end">{{ item.planned_total_hours }}</td>
              <td class="text-end">{{ item.actual_total_hours }}</td>
              <td
                class="text-end"
                :class="{
                  'text-success': item.difference > 0,
                  'text-danger': item.difference < 0,
                  'text-muted': item.difference === 0
                }"
              >
                {{ item.difference }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script>
  import api from '../axios'
  
  export default {
    data() {
      return {
        reports: [],
        loading: true,
        error: null,
      }
    },
    async mounted() {
      try {
        const { data } = await api.get('/reports/workloads/types')
        this.reports = data
      } catch (e) {
        this.error = e.message || 'Неизвестная ошибка'
      } finally {
        this.loading = false
      }
    },
  }
  </script>
  