<template>
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Сравнение нагрузок</h5>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="text-center my-3">
          <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
        </div>
  
        <div v-else-if="error" class="alert alert-danger m-3" role="alert">
          Ошибка: {{ error }}
        </div>
  
        <table v-else class="table table-sm table-hover m-0">
          <thead class="table-light">
            <tr>
              <th>Дисциплина</th>
              <th>Тип</th>
              <th class="text-end">Плановые часы</th>
              <th class="text-end">Фактические часы</th>
              <th class="text-end">Разница</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in loads" :key="index">
              <td>{{ item.discipline }}</td>
              <td>{{ item.type }}</td>
              <td class="text-end">{{ item.planned_hours }}</td>
              <td class="text-end">{{ item.actual_hours }}</td>
              <td
                class="text-end"
                :class="{'text-danger': item.difference < 0, 'text-success': item.difference > 0}"
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
        loads: [],
        loading: true,
        error: null,
      }
    },
    async mounted() {
      try {
        const { data } = await api.get('/loads/compare')
        this.loads = data
      } catch (e) {
        this.error = e.message || 'Неизвестная ошибка'
      } finally {
        this.loading = false
      }
    },
  }
  </script>
  