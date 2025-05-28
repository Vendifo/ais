<template>
    <div class="container mt-4">
      <h2 class="mb-3">Управление дисциплинами</h2>
      <p class="text-muted">Создание, редактирование и удаление дисциплин.</p>
      
      <button class="btn btn-secondary mb-3" @click="goToDashboard">← На Dashboard</button>
  
      <button class="btn btn-primary mb-3" @click="openDisciplineModal()">Добавить дисциплину</button>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Название</th>
            <th>Описание</th>
            <th>Кафедра</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="disc in disciplines" :key="disc.id">
            <td v-if="editingDisciplineId !== disc.id">{{ disc.name }}</td>
            <td v-else>
              <input v-model="editingDisciplineName" class="form-control" />
            </td>
  
            <td v-if="editingDisciplineId !== disc.id">{{ disc.description || '-' }}</td>
            <td v-else>
              <input v-model="editingDisciplineDescription" class="form-control" />
            </td>
  
            <td v-if="editingDisciplineId !== disc.id">{{ disc.department?.name || '-' }}</td>
            <td v-else>
              <select v-model="editingDisciplineDepartmentId" class="form-select" required>
                <option value="">Выберите кафедру</option>
                <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
              </select>
            </td>
  
            <td>
              <button
                v-if="editingDisciplineId !== disc.id"
                class="btn btn-sm btn-warning me-2"
                @click="startEditDiscipline(disc)"
              >
                Редактировать
              </button>
              <button
                v-else
                class="btn btn-sm btn-primary me-2"
                @click="saveEditDiscipline(disc)"
                :disabled="loadingSave"
              >
                Сохранить
              </button>
              <button
                v-if="editingDisciplineId === disc.id"
                class="btn btn-sm btn-secondary me-2"
                @click="cancelEditDiscipline"
              >
                Отмена
              </button>
              <button class="btn btn-sm btn-danger" @click="deleteDiscipline(disc.id)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Модальное окно для добавления дисциплины -->
      <div
        class="modal fade"
        id="disciplineModal"
        tabindex="-1"
        aria-hidden="true"
        ref="disciplineModalRef"
      >
        <div class="modal-dialog">
          <form @submit.prevent="saveNewDiscipline" class="modal-content" novalidate>
            <div class="modal-header">
              <h5 class="modal-title">Добавить дисциплину</h5>
              <button type="button" class="btn-close" @click="closeDisciplineModal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="discName" class="form-label">Название</label>
                <input
                  id="discName"
                  v-model="newDiscipline.name"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="discDescription" class="form-label">Описание</label>
                <textarea
                  id="discDescription"
                  v-model="newDiscipline.description"
                  class="form-control"
                  rows="3"
                ></textarea>
              </div>
              <div class="mb-3">
                <label for="discDepartment" class="form-label">Кафедра</label>
                <select
                  id="discDepartment"
                  v-model="newDiscipline.department_id"
                  class="form-select"
                  required
                >
                  <option value="">Выберите кафедру</option>
                  <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeDisciplineModal">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="loadingSave">
                {{ loadingSave ? 'Сохраняю...' : 'Добавить' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import * as bootstrap from 'bootstrap'
  
  const disciplines = ref([])
  const departments = ref([]) // Список кафедр
  
  const editingDisciplineId = ref(null)
  const editingDisciplineName = ref('')
  const editingDisciplineDescription = ref('')
  const editingDisciplineDepartmentId = ref('')
  
  const newDiscipline = ref({
    name: '',
    description: '',
    department_id: '',
  })
  
  const loadingSave = ref(false)
  
  const disciplineModalRef = ref(null)
  let disciplineModal = null
  
  function getToken() {
    return localStorage.getItem('token')
  }
  
  async function fetchDisciplines() {
    try {
      const token = getToken()
      const res = await axios.get('/api/admin/disciplines', {
        headers: { Authorization: `Bearer ${token}` },
      })
      disciplines.value = res.data.data || res.data
    } catch {
      alert('Ошибка загрузки дисциплин')
    }
  }
  
  async function fetchDepartments() {
    try {
      const token = getToken()
      const res = await axios.get('/api/admin/departments', {
        headers: { Authorization: `Bearer ${token}` },
      })
      departments.value = res.data.data || res.data
    } catch {
      alert('Ошибка загрузки кафедр')
    }
  }
  
  function openDisciplineModal() {
    newDiscipline.value = { name: '', description: '', department_id: '' }
    if (!disciplineModal) disciplineModal = new bootstrap.Modal(disciplineModalRef.value)
    disciplineModal.show()
  }
  
  function closeDisciplineModal() {
    if (disciplineModal) disciplineModal.hide()
  }
  
  async function saveNewDiscipline() {
    if (!newDiscipline.value.name.trim()) {
      alert('Название дисциплины обязательно')
      return
    }
    if (!newDiscipline.value.department_id) {
      alert('Кафедра обязательна')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.post(
        '/api/admin/disciplines',
        { ...newDiscipline.value },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchDisciplines()
      closeDisciplineModal()
    } catch (err) {
      alert('Ошибка добавления дисциплины')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  function startEditDiscipline(disc) {
    editingDisciplineId.value = disc.id
    editingDisciplineName.value = disc.name
    editingDisciplineDescription.value = disc.description || ''
    editingDisciplineDepartmentId.value = disc.department_id || ''
  }
  
  function cancelEditDiscipline() {
    editingDisciplineId.value = null
    editingDisciplineName.value = ''
    editingDisciplineDescription.value = ''
    editingDisciplineDepartmentId.value = ''
  }
  
  async function saveEditDiscipline(disc) {
    if (!editingDisciplineName.value.trim()) {
      alert('Название дисциплины обязательно')
      return
    }
    if (!editingDisciplineDepartmentId.value) {
      alert('Кафедра обязательна')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.put(
        `/api/admin/disciplines/${disc.id}`,
        {
          name: editingDisciplineName.value,
          description: editingDisciplineDescription.value,
          department_id: editingDisciplineDepartmentId.value,
        },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchDisciplines()
      cancelEditDiscipline()
    } catch (err) {
      alert('Ошибка сохранения дисциплины')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  async function deleteDiscipline(id) {
    if (!confirm('Удалить дисциплину?')) return
    try {
      const token = getToken()
      await axios.delete(`/api/admin/disciplines/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      await fetchDisciplines()
    } catch {
      alert('Ошибка удаления дисциплины')
    }
  }
  
  function goToDashboard() {
    if (typeof window !== 'undefined') {
      window.location.href = '/dashboard'
    }
  }
  
  onMounted(() => {
    fetchDepartments()
    fetchDisciplines()
  })
  </script>
  