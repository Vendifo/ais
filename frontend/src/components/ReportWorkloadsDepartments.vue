<template>
    <div class="card mb-4 shadow-sm rounded">
      <div class="card-header bg-light border-0">
        <h5 class="mb-0 text-secondary fw-semibold">Нагрузка по кафедрам</h5>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center align-items-center py-5">
          <div class="spinner-border text-secondary" role="status" aria-hidden="true"></div>
        </div>
  
        <div v-else-if="error" class="alert alert-danger m-3" role="alert">
          {{ error }}
        </div>
  
        <table v-else class="table table-hover align-middle mb-0">
          <thead class="table-light text-secondary text-uppercase small">
            <tr>
              <th scope="col" class="ps-4">Кафедра</th>
              <th scope="col" class="text-end pe-4">План (ч)</th>
              <th scope="col" class="text-end pe-4">Факт (ч)</th>
              <th scope="col" class="text-end pe-4">Разница</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in reports" :key="idx" class="border-top">
              <td class="ps-4 fw-medium">{{ item.department }}</td>
              <td class="text-end pe-4">{{ item.planned_total_hours }}</td>
              <td class="text-end pe-4">{{ item.actual_total_hours }}</td>
              <td
                class="text-end pe-4 fw-semibold"
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
        const { data } = await api.get('/reports/workloads/departments')
        this.reports = data
      } catch (e) {
        this.error = e.message || 'Ошибка при загрузке данных'
      } finally {
        this.loading = false
      }
    },
  }
  </script>
  