<template>
    <div class="container mt-4">
      <h2 class="mb-3">Управление группами</h2>
      <button class="btn btn-secondary mb-3" @click="goToDashboard">← На Dashboard</button>

      <p class="text-muted">Здесь отображаются академические группы и их параметры.</p>
  
      <button class="btn btn-primary mb-3" @click="openGroupModal()">Добавить группу</button>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Название</th>
            <th>Год</th>
            <th>Кафедра</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="group in groups" :key="group.id">
            <td v-if="editingGroupId !== group.id">{{ group.name }}</td>
            <td v-else><input v-model="editingGroupName" class="form-control" /></td>
  
            <td v-if="editingGroupId !== group.id">{{ group.year }}</td>
            <td v-else><input v-model.number="editingGroupYear" type="number" class="form-control" /></td>
  
            <td v-if="editingGroupId !== group.id">{{ group.department?.name || '-' }}</td>
            <td v-else>
              <select v-model.number="editingGroupDepartmentId" class="form-select" required>
                <option value="" disabled>Выберите кафедру</option>
                <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
              </select>
            </td>
  
            <td>
              <button v-if="editingGroupId !== group.id" class="btn btn-sm btn-warning me-2" @click="startEditGroup(group)">Редактировать</button>
              <button v-else class="btn btn-sm btn-primary me-2" @click="saveEditGroup(group)" :disabled="loadingSave">Сохранить</button>
              <button v-if="editingGroupId === group.id" class="btn btn-sm btn-secondary me-2" @click="cancelEditGroup">Отмена</button>
              <button class="btn btn-sm btn-danger" @click="deleteGroup(group.id)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Модальное окно для добавления группы -->
      <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true" ref="groupModalRef">
        <div class="modal-dialog">
          <form @submit.prevent="saveNewGroup" class="modal-content" novalidate>
            <div class="modal-header">
              <h5 class="modal-title">Добавить группу</h5>
              <button type="button" class="btn-close" @click="closeGroupModal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="groupName" class="form-label">Название</label>
                <input id="groupName" v-model="newGroup.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label for="groupYear" class="form-label">Год</label>
                <input id="groupYear" v-model.number="newGroup.year" type="number" class="form-control" required />
              </div>
              <div class="mb-3">
                <label for="groupDepartment" class="form-label">Кафедра</label>
                <select id="groupDepartment" v-model.number="newGroup.department_id" class="form-select" required>
                  <option value="" disabled>Выберите кафедру</option>
                  <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeGroupModal">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="loadingSave">{{ loadingSave ? 'Сохраняю...' : 'Добавить' }}</button>
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
  
  const groups = ref([])
  const departments = ref([])
  
  const editingGroupId = ref(null)
  const editingGroupName = ref('')
  const editingGroupYear = ref(new Date().getFullYear())
  const editingGroupDepartmentId = ref(null)
  
  const newGroup = ref({
    name: '',
    year: new Date().getFullYear(),
    department_id: null,
  })
  
  const loadingSave = ref(false)
  
  const groupModalRef = ref(null)
  let groupModal = null
  
  function getToken() {
    return localStorage.getItem('token')
  }
  
  async function fetchGroups() {
    try {
      const token = getToken()
      const res = await axios.get('/api/admin/groups', {
        headers: { Authorization: `Bearer ${token}` },
      })
      groups.value = res.data.data || res.data
    } catch {
      alert('Ошибка загрузки групп')
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
  
  function openGroupModal() {
    newGroup.value = { name: '', year: new Date().getFullYear(), department_id: null }
    if (!groupModal) groupModal = new bootstrap.Modal(groupModalRef.value)
    groupModal.show()
  }
  
  function closeGroupModal() {
    if (groupModal) groupModal.hide()
  }
  
  async function saveNewGroup() {
    if (!newGroup.value.name.trim()) {
      alert('Название группы обязательно')
      return
    }
    if (!newGroup.value.department_id) {
      alert('Выберите кафедру')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.post(
        '/api/admin/groups',
        { ...newGroup.value },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchGroups()
      closeGroupModal()
    } catch (err) {
      alert('Ошибка добавления группы')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  function startEditGroup(group) {
    editingGroupId.value = group.id
    editingGroupName.value = group.name
    editingGroupYear.value = group.year
    editingGroupDepartmentId.value = group.department_id
  }
  
  function cancelEditGroup() {
    editingGroupId.value = null
    editingGroupName.value = ''
    editingGroupYear.value = new Date().getFullYear()
    editingGroupDepartmentId.value = null
  }
  
  async function saveEditGroup(group) {
    if (!editingGroupName.value.trim()) {
      alert('Название группы обязательно')
      return
    }
    if (!editingGroupDepartmentId.value) {
      alert('Выберите кафедру')
      return
    }
    loadingSave.value = true
    try {
      const token = getToken()
      await axios.put(
        `/api/admin/groups/${group.id}`,
        {
          name: editingGroupName.value,
          year: editingGroupYear.value,
          department_id: editingGroupDepartmentId.value,
        },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      await fetchGroups()
      cancelEditGroup()
    } catch (err) {
      alert('Ошибка сохранения группы')
      console.error(err)
    } finally {
      loadingSave.value = false
    }
  }
  
  async function deleteGroup(id) {
    if (!confirm('Удалить группу?')) return
    try {
      const token = getToken()
      await axios.delete(`/api/admin/groups/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      await fetchGroups()
    } catch {
      alert('Ошибка удаления группы')
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
    fetchGroups()
  })
  </script>
  