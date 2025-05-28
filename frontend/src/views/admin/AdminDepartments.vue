<template>
    <div class="container mt-4">
      <h2 class="mb-3">Управление кафедрами</h2>
      <p class="text-muted">Управление списком кафедр и их описаниями.</p>
        
      <button class="btn btn-secondary mb-3" @click="goToDashboard">← На Dashboard</button>

      <button class="btn btn-primary mb-3" @click="openDepartmentModal()">Добавить кафедру</button>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Название</th>
            <th>Описание</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dep in departments" :key="dep.id">
            <td v-if="editingDepartmentId !== dep.id">{{ dep.name }}</td>
            <td v-else>
              <input v-model="editingDepartmentName" class="form-control" />
            </td>
  
            <td v-if="editingDepartmentId !== dep.id">{{ dep.description || '-' }}</td>
            <td v-else>
              <input v-model="editingDepartmentDescription" class="form-control" />
            </td>
  
            <td>
              <button
                v-if="editingDepartmentId !== dep.id"
                class="btn btn-sm btn-warning me-2"
                @click="startEditDepartment(dep)"
              >
                Редактировать
              </button>
              <button
                v-else
                class="btn btn-sm btn-primary me-2"
                @click="saveEditDepartment(dep)"
                :disabled="loadingSave"
              >
                Сохранить
              </button>
              <button
                v-if="editingDepartmentId === dep.id"
                class="btn btn-sm btn-secondary me-2"
                @click="cancelEditDepartment"
              >
                Отмена
              </button>
              <button class="btn btn-sm btn-danger" @click="deleteDepartment(dep.id)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Модальное окно для добавления кафедры -->
      <div
        class="modal fade"
        id="departmentModal"
        tabindex="-1"
        aria-hidden="true"
        ref="departmentModalRef"
      >
        <div class="modal-dialog">
          <form @submit.prevent="saveNewDepartment" class="modal-content" novalidate>
            <div class="modal-header">
              <h5 class="modal-title">Добавить кафедру</h5>
              <button type="button" class="btn-close" @click="closeDepartmentModal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="depName" class="form-label">Название</label>
                <input
                  id="depName"
                  v-model="newDepartment.name"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="depDescription" class="form-label">Описание</label>
                <textarea
                  id="depDescription"
                  v-model="newDepartment.description"
                  class="form-control"
                  rows="3"
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeDepartmentModal">Отмена</button>
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
  
  const departments = ref([])
  
  const editingDepartmentId = ref(null)
  const editingDepartmentName = ref('')
  const editingDepartmentDescription = ref('')
  
  const newDepartment = ref({
    name: '',
    description: '',
  })
  
  const loadingSave = ref(false)
  
  const departmentModalRef = ref(null)
  let departmentModal = null
  
  function getToken() {
    return localStorage.getItem('token')
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
  
  function openDepartmentModal() {
    newDepartment.value = { name: '', description: '' }
    if (!departmentModal) departmentModal = new bootstrap.Modal(departmentModalRef.value)
    departmentModal.show()
  }
  
  function closeDepartmentModal() {
    if (departmentModal) departmentModal.hide()
  }
  
  async function saveNewDepartment() {
    if (!newDepartment.value.name.trim()) {
      alert('Название кафедры обязательно')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.post(
        '/api/admin/departments',
        { ...newDepartment.value },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchDepartments()
      closeDepartmentModal()
    } catch (err) {
      alert('Ошибка добавления кафедры')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  function startEditDepartment(dep) {
    editingDepartmentId.value = dep.id
    editingDepartmentName.value = dep.name
    editingDepartmentDescription.value = dep.description || ''
  }
  
  function cancelEditDepartment() {
    editingDepartmentId.value = null
    editingDepartmentName.value = ''
    editingDepartmentDescription.value = ''
  }
  
  async function saveEditDepartment(dep) {
    if (!editingDepartmentName.value.trim()) {
      alert('Название кафедры обязательно')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.put(
        `/api/admin/departments/${dep.id}`,
        {
          name: editingDepartmentName.value,
          description: editingDepartmentDescription.value,
        },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchDepartments()
      cancelEditDepartment()
    } catch (err) {
      alert('Ошибка сохранения кафедры')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  async function deleteDepartment(id) {
    if (!confirm('Удалить кафедру?')) return
    try {
      const token = getToken()
      await axios.delete(`/api/admin/departments/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      await fetchDepartments()
    } catch {
      alert('Ошибка удаления кафедры')
    }
  }
  function goToDashboard() {
  // если используется Vue Router
  if (typeof window !== 'undefined') {
    // например, если маршрут дашборда "/admin/dashboard"
    window.location.href = '/dashboard'
  }
}

  
  onMounted(() => {
    fetchDepartments()
  })
  </script>
  