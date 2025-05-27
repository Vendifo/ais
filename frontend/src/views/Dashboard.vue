<template>
  <div>
    <!-- Хедер -->
    <header class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-4 py-3 mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4" href="#">Мое приложение</a>
        <div class="d-flex align-items-center">
          <span class="me-3 text-muted fst-italic">Добро пожаловать, {{ user?.name || 'Гость' }}</span>
          <button @click="logout" class="btn btn-outline-danger btn-sm">Выйти</button>
        </div>
      </div>
    </header>

    <!-- Основной контент -->
    <div class="container-fluid px-4 py-5">
     
      <!-- Лоадер -->
      <div v-if="loading" class="d-flex justify-content-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Загрузка...</span>
        </div>
      </div>

      <!-- Блоки отчетов -->
      <div v-else>
        <div class="row g-4">
          <div class="col-md-6">
            <CompareLoads />
          </div>
          <div class="col-md-6">
            <ReportWorkloadsTeachers />
          </div>
          <div class="col-md-6">
            <ReportWorkloadsDisciplines />
          </div>
          <div class="col-md-6">
            <ReportWorkloadsDepartments />
          </div>
          <div class="col-md-6">
            <ReportWorkloadsTypes />
          </div>
        </div>

        <!-- Админский блок -->
        <div v-if="roles.includes('admin')" class="mt-5">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold">
              Админская панель
            </div>
            <div class="card-body">
              <AdminDepartments />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import CompareLoads from '../components/CompareLoads.vue'
import ReportWorkloadsTeachers from '../components/ReportWorkloadsTeachers.vue'
import ReportWorkloadsDisciplines from '../components/ReportWorkloadsDisciplines.vue'
import ReportWorkloadsDepartments from '../components/ReportWorkloadsDepartments.vue'
import ReportWorkloadsTypes from '../components/ReportWorkloadsTypes.vue'
import AdminDepartments from '../components/AdminDepartments.vue'

export default {
  components: {
    CompareLoads,
    ReportWorkloadsTeachers,
    ReportWorkloadsDisciplines,
    ReportWorkloadsDepartments,
    ReportWorkloadsTypes,
    AdminDepartments,
  },
  data() {
    return {
      user: null,
      roles: [],
      loading: true,
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token')
      this.$router.push('/login')
    },
  },
  async mounted() {
    try {
      const { data } = await axios.get('/api/user', {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      })
      this.user = data.user
      this.roles = data.roles
    } catch (e) {
      this.$router.push('/login')
    } finally {
      this.loading = false
    }
  },
}
</script>

<style scoped>
.card {
  min-height: 100%;
  border-radius: 0.5rem;
}

.card-header {
  font-size: 1rem;
}
</style>
