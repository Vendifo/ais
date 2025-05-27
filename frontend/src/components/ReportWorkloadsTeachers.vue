<template>
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-info text-white fw-semibold">
        <h5 class="mb-0">Нагрузка по преподавателям</h5>
      </div>
      <div class="card-body p-3">
        <div v-if="loading" class="d-flex justify-content-center my-5">
          <div class="spinner-border text-info" role="status" aria-hidden="true"></div>
        </div>
  
        <div v-else-if="error" class="alert alert-danger" role="alert">
          Ошибка: {{ error }}
        </div>
  
        <div v-else>
          <div
            v-for="(teacher, index) in reports"
            :key="teacher.teacher"
            class="mb-3 border rounded"
          >
            <button
              class="btn btn-light w-100 d-flex justify-content-between align-items-center px-3 py-2"
              @click="toggleCollapse(index)"
              :aria-expanded="isOpen(index)"
              :aria-controls="'collapse-' + index"
              aria-haspopup="true"
            >
              <span class="fw-bold text-truncate">{{ teacher.teacher }}</span>
              <div class="d-flex gap-3 align-items-center flex-wrap" style="min-width: 220px;">
                <div>План: <span class="fw-semibold">{{ teacher.planned_total_hours }}</span></div>
                <div>Факт: <span class="fw-semibold">{{ teacher.actual_total_hours }}</span></div>
                <div
                  :class="{
                    'text-success': teacher.difference > 0,
                    'text-danger': teacher.difference < 0,
                    'text-muted': teacher.difference === 0
                  }"
                >
                  Разница: <strong>{{ teacher.difference }}</strong>
                </div>
                <i
                  class="bi"
                  :class="isOpen(index) ? 'bi-chevron-up' : 'bi-chevron-down'"
                  aria-hidden="true"
                ></i>
              </div>
            </button>
  
            <transition name="fade">
              <div
                v-show="isOpen(index)"
                class="px-3 pb-3"
                :id="'collapse-' + index"
                role="region"
                :aria-labelledby="'heading-' + index"
              >
                <table class="table table-sm table-hover table-striped mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Дисциплина</th>
                      <th>Группа</th>
                      <th>Тип</th>
                      <th class="text-end">План</th>
                      <th class="text-end">Факт</th>
                      <th class="text-end">Разница</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="teacher.details.length === 0">
                      <td colspan="6" class="text-center fst-italic text-muted">
                        Нет данных по нагрузке
                      </td>
                    </tr>
                    <tr v-for="(detail, i) in teacher.details" :key="i">
                      <td>{{ detail.discipline }}</td>
                      <td>{{ detail.group }}</td>
                      <td>{{ detail.type }}</td>
                      <td class="text-end">{{ detail.planned_hours }}</td>
                      <td class="text-end">{{ detail.actual_hours }}</td>
                      <td
                        class="text-end"
                        :class="{
                          'text-success': detail.diff > 0,
                          'text-danger': detail.diff < 0,
                          'text-muted': detail.diff === 0
                        }"
                      >
                        {{ detail.diff }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </transition>
          </div>
        </div>
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
        openIndexes: [],
      }
    },
    methods: {
      toggleCollapse(index) {
        if (this.openIndexes.includes(index)) {
          this.openIndexes = this.openIndexes.filter(i => i !== index)
        } else {
          this.openIndexes.push(index)
        }
      },
      isOpen(index) {
        return this.openIndexes.includes(index)
      },
    },
    async mounted() {
      try {
        const { data } = await api.get('/reports/workloads/teachers')
        this.reports = data
      } catch (e) {
        this.error = e.message || 'Неизвестная ошибка'
      } finally {
        this.loading = false
      }
    },
  }
  </script>
  
  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  .btn-light {
    text-align: left;
    user-select: none;
  }
  </style>
  