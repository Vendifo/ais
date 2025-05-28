<template>
    <div class="container mt-4">
      <h2 class="mb-3">Управление пользователями</h2>

      <button class="btn btn-secondary mb-3" @click="goToDashboard">← На Dashboard</button>

      <p class="text-muted">
        Здесь вы можете добавлять, редактировать и удалять пользователей, а также назначать им роли.
      </p>
  
      <button class="btn btn-primary mb-3" @click="openUserModal()">Добавить пользователя</button>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Имя</th>
            <th>Email</th>
            <th>Кафедра</th>
            <th>Роли</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.department?.name || '-' }}</td>
            <td>
              <span
                v-for="role in user.roles"
                :key="role.id"
                class="badge bg-secondary me-1"
                >{{ role.name }}</span
              >
            </td>
            <td>
              <button class="btn btn-sm btn-warning me-2" @click="openUserModal(user)">Редактировать</button>
              <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <nav v-if="users.meta && users.meta.last_page > 1">
        <ul class="pagination">
          <li :class="['page-item', { disabled: !users.links.prev }]">
            <a
              class="page-link"
              href="#"
              @click.prevent="fetchUsers(users.meta.current_page - 1)"
              >Назад</a
            >
          </li>
          <li
            v-for="page in users.meta.last_page"
            :key="page"
            :class="['page-item', { active: users.meta.current_page === page }]"
          >
            <a class="page-link" href="#" @click.prevent="fetchUsers(page)">{{ page }}</a>
          </li>
          <li :class="['page-item', { disabled: !users.links.next }]">
            <a
              class="page-link"
              href="#"
              @click.prevent="fetchUsers(users.meta.current_page + 1)"
              >Вперед</a
            >
          </li>
        </ul>
      </nav>
  
      <!-- Модальное окно пользователя -->
      <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" ref="userModalRef">
        <div class="modal-dialog">
          <form @submit.prevent="saveUser" class="modal-content" novalidate>
            <div class="modal-header">
              <h5 class="modal-title">{{ form.id ? 'Редактировать пользователя' : 'Добавить пользователя' }}</h5>
              <button type="button" class="btn-close" @click="closeUserModal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label" for="userName">Имя</label>
                <input id="userName" v-model="form.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label" for="userEmail">Email</label>
                <input id="userEmail" v-model="form.email" type="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label" for="userPassword">
                  Пароль <small v-if="form.id">(оставьте пустым, чтобы не менять)</small>
                </label>
                <input
                  id="userPassword"
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  :required="!form.id"
                  autocomplete="new-password"
                />
              </div>
              <div class="mb-3">
                <label class="form-label" for="userDepartment">Кафедра</label>
                <select id="userDepartment" v-model="form.department_id" class="form-select">
                  <option :value="null">-</option>
                  <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label" for="userRoles">Роли</label>
                <select id="userRoles" v-model="form.roles" multiple class="form-select" size="5">
                  <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeUserModal">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="loadingSave">
                {{ loadingSave ? 'Сохраняю...' : 'Сохранить' }}
              </button>
            </div>
          </form>
        </div>
      </div>
  
      <hr class="my-5" />
  
      <h3>Управление ролями</h3>
      <div class="mb-3 d-flex gap-2">
        <input
          v-model="newRoleName"
          type="text"
          class="form-control"
          placeholder="Название новой роли"
        />
        <button class="btn btn-success" @click="createRole" :disabled="loadingRole">Добавить роль</button>
      </div>
      <table class="table table-bordered w-50">
        <thead>
          <tr>
            <th>Название роли</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in roles" :key="role.id">
            <td v-if="editingRoleId !== role.id">{{ role.name }}</td>
            <td v-else>
              <input v-model="editingRoleName" class="form-control" />
            </td>
            <td>
              <button
                v-if="editingRoleId !== role.id"
                class="btn btn-sm btn-warning me-2"
                @click="editRole(role)"
              >
                Редактировать
              </button>
              <button v-else class="btn btn-sm btn-primary me-2" @click="updateRole(role)">
                Сохранить
              </button>
              <button class="btn btn-sm btn-danger" @click="deleteRole(role.id)">Удалить</button>
              <button
                v-if="editingRoleId === role.id"
                class="btn btn-sm btn-secondary ms-2"
                @click="cancelEditRole"
              >
                Отмена
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import * as bootstrap from 'bootstrap'
  
  const users = ref({ data: [], meta: {}, links: {} })
  const roles = ref([])
  const departments = ref([])
  
  const form = ref({
    id: null,
    name: '',
    email: '',
    password: '',
    department_id: null,
    roles: [],
  })
  
  const loadingSave = ref(false)
  
  const newRoleName = ref('')
  const loadingRole = ref(false)
  
  const editingRoleId = ref(null)
  const editingRoleName = ref('')
  
  const userModalRef = ref(null)
  let userModal = null
  
  function getToken() {
    return localStorage.getItem('token')
  }
  
  async function fetchUsers(page = 1) {
    try {
      const token = getToken()
      const res = await axios.get(`/api/admin/users?page=${page}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      users.value = res.data
    } catch {
      alert('Ошибка загрузки пользователей')
    }
  }
  
  async function fetchRoles() {
    try {
      const token = getToken()
      const res = await axios.get('/api/admin/roles', {
        headers: { Authorization: `Bearer ${token}` },
      })
      roles.value = res.data
    } catch {
      alert('Ошибка загрузки ролей')
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
  
  function openUserModal(user = null) {
    if (user) {
      form.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        department_id: user.department_id,
        roles: user.roles.map((r) => r.id),
      }
    } else {
      form.value = {
        id: null,
        name: '',
        email: '',
        password: '',
        department_id: null,
        roles: [],
      }
    }
    if (!userModal) userModal = new bootstrap.Modal(userModalRef.value)
    userModal.show()
  }
  
  function closeUserModal() {
    if (userModal) userModal.hide()
  }
  
  async function saveUser() {
  loadingSave.value = true
  try {
    const token = getToken()
    
    // Данные из формы
    console.log('Данные формы:', JSON.parse(JSON.stringify(form.value)))

    const payload = {
      name: form.value.name,
      email: form.value.email,
      department_id: form.value.department_id,
      roles: form.value.roles,
    }
    
    if (form.value.password.trim() !== '') {
      payload.password = form.value.password
    }

    // Данные, которые отправляем на сервер
    console.log('Отправляем payload:', payload)

    if (form.value.id) {
      await axios.put(`/api/admin/users/${form.value.id}`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      })
    } else {
      if (!payload.password) {
        alert('Пароль обязателен для нового пользователя')
        loadingSave.value = false
        return
      }
      await axios.post('/api/admin/users', payload, {
        headers: { Authorization: `Bearer ${token}` },
      })
    }
    await fetchUsers()
    closeUserModal()
  } catch (err) {
    alert('Ошибка при сохранении пользователя')
    console.error(err)
  } finally {
    loadingSave.value = false
  }
}

  
  async function deleteUser(id) {
    if (!confirm('Удалить пользователя?')) return
    try {
      const token = getToken()
      await axios.delete(`/api/admin/users/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      await fetchUsers()
    } catch {
      alert('Ошибка удаления пользователя')
    }
  }
  
  async function createRole() {
    if (!newRoleName.value.trim()) return
    loadingRole.value = true
    try {
      const token = getToken()
      await axios.post(
        '/api/admin/roles',
        { name: newRoleName.value },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      newRoleName.value = ''
      await fetchRoles()
    } catch {
      alert('Ошибка создания роли')
    } finally {
      loadingRole.value = false
    }
  }
  
  function editRole(role) {
    editingRoleId.value = role.id
    editingRoleName.value = role.name
  }
  
  async function updateRole(role) {
    if (!editingRoleName.value.trim()) return
    try {
      const token = getToken()
      await axios.put(
        `/api/admin/roles/${role.id}`,
        { name: editingRoleName.value },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      editingRoleId.value = null
      editingRoleName.value = ''
      await fetchRoles()
    } catch {
      alert('Ошибка обновления роли')
    }
  }
  
  function cancelEditRole() {
    editingRoleId.value = null
    editingRoleName.value = ''
  }
  
  async function deleteRole(id) {
    if (!confirm('Удалить роль?')) return
    try {
      const token = getToken()
      await axios.delete(`/api/admin/roles/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      await fetchRoles()
    } catch {
      alert('Ошибка удаления роли')
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
    fetchUsers()
    fetchRoles()
    fetchDepartments()
  })
  </script>
  