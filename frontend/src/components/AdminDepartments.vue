<template>
    <div class="card shadow-sm border-0">
      <div class="card-header bg-dark text-white fw-semibold">
        <h5 class="mb-0">Отделы</h5>
      </div>
      <div class="card-body">
        <button
          @click="fetchDepartments"
          class="btn btn-outline-primary mb-4 d-flex align-items-center justify-content-center gap-2"
          :disabled="loading"
          type="button"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          <span>{{ loading ? 'Загрузка...' : 'Обновить' }}</span>
        </button>
  
        <ul class="list-group list-group-flush">
          <li
            v-for="dep in departments"
            :key="dep.id"
            class="list-group-item d-flex align-items-center gap-3 py-3"
          >
            <i class="bi bi-building fs-5 text-secondary"></i>
            <span class="flex-grow-1 fw-semibold text-truncate">{{ dep.name }}</span>
          </li>
          <li v-if="departments.length === 0 && !loading" class="list-group-item text-muted text-center">
            Отделы не найдены
          </li>
        </ul>
  
        <div v-if="error" class="alert alert-danger mt-4" role="alert" aria-live="polite">
          Ошибка загрузки отделов. Попробуйте еще раз.
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        departments: [],
        loading: false,
        error: false,
      }
    },
    methods: {
      async fetchDepartments() {
        this.loading = true
        this.error = false
        try {
          const response = await axios.get('/api/admin/departments', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
          })
          this.departments = response.data
        } catch {
          this.error = true
        } finally {
          this.loading = false
        }
      },
    },
    mounted() {
      this.fetchDepartments()
    },
  }
  </script>
  
  <style scoped>
  .card-header {
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    font-size: 1rem;
  }
  
  .list-group-item {
    cursor: default;
    transition: background-color 0.2s ease;
    border-left: 4px solid transparent;
  }
  
  .list-group-item:hover {
    background-color: #f1f3f5;
    border-left-color: #0d6efd;
  }
  
  .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  </style>
  