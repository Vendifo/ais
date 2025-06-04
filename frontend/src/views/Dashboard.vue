<template>
  <div>
    <!-- Хедер -->
    <header class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-4 py-3 mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4" href="#">Мое приложение</a>
        <div class="d-flex align-items-center">
          <!-- Админские ссылки -->
          <template v-if="roles.includes('admin')">
            <router-link to="/admin/users" class="btn btn-outline-secondary btn-sm me-2">👥 Пользователи</router-link>
            <router-link to="/admin/departments" class="btn btn-outline-secondary btn-sm me-2">🏢 Кафедры</router-link>
            <router-link to="/admin/disciplines" class="btn btn-outline-secondary btn-sm me-2">📘 Дисциплины</router-link>
            <router-link to="/admin/groups" class="btn btn-outline-secondary btn-sm me-2">🎓 Группы</router-link>
          </template>
          <template v-if="roles.includes('admin') || roles.includes('methodist')">
            <router-link to="/loads/planned" class="btn btn-outline-secondary btn-sm me-2">🧾 Плановая нагрузка</router-link>
          </template>
          <template v-if="roles.includes('admin') || roles.includes('teacher')">
            <router-link to="/loads/actual" class="btn btn-outline-secondary btn-sm me-2">📊 Фактическая нагрузка</router-link>
          </template>

          <!-- Приветствие и выход -->
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

      <!-- Контент -->
      <div v-else>
        <!-- Кнопки переключения аналитики -->
        <div class="mb-4">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            class="btn btn-outline-primary btn-sm me-2"
            :class="{ active: activeTab === tab.key }"
            @click="activeTab = tab.key"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Отображение выбранной аналитики -->
        <div class="row g-4">
          <div class="col-12">
            <CompareLoads v-if="activeTab === 'compare'" />
            <ReportWorkloadsTeachers v-if="activeTab === 'teachers'" />
            <ReportWorkloadsDisciplines v-if="activeTab === 'disciplines'" />
            <ReportWorkloadsDepartments v-if="activeTab === 'departments'" />
            <ReportWorkloadsTypes v-if="activeTab === 'types'" />
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
      activeTab: 'compare',
      tabs: [
        { key: 'compare', label: 'Сравнение нагрузки' },
        { key: 'teachers', label: 'По преподавателям' },
        { key: 'disciplines', label: 'По дисциплинам' },
        { key: 'departments', label: 'По кафедрам' },
        { key: 'types', label: 'По типам работ' },
      ],
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

/* Подсветка активной кнопки */
.btn.active {
  background-color: #0d6efd;
  color: white;
}
</style>
