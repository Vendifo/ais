<template>
    <div class="container mt-4">
      <h2 class="mb-3">👨‍🏫 Фактическая нагрузка</h2>
      <p class="text-muted">Управление фактической учебной нагрузкой преподавателей.</p>
  
      <!-- Форма добавления -->
      <form @submit.prevent="createLoad" class="row g-3 mb-4 border rounded p-3">
        <div class="col-md-3">
          <label class="form-label">Преподаватель</label>
          <select v-model="form.teacher_id" class="form-select" required>
            <option value="" disabled>Выберите преподавателя...</option>
            <option v-for="t in teachers" :key="t.id" :value="t.id">
              {{ t.name }} — {{ t.department?.name || 'Кафедра не указана' }}
            </option>
          </select>
        </div>
  
        <div class="col-md-3">
          <label class="form-label">Группа</label>
          <select v-model="form.group_id" class="form-select" required>
            <option value="" disabled>Выберите группу...</option>
            <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>
        </div>
  
        <div class="col-md-3">
          <label class="form-label">Дисциплина</label>
          <select v-model="form.discipline_id" class="form-select" required>
            <option value="" disabled>Выберите дисциплину...</option>
            <option v-for="d in disciplines" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
  
        <div class="col-md-2">
          <label class="form-label">Часы</label>
          <input type="number" v-model.number="form.hours" class="form-control" min="1" required />
        </div>
  
        <div class="col-md-2">
          <label class="form-label">Тип</label>
          <input type="text" v-model="form.type" class="form-control" required />
        </div>
  
        <div class="col-md-2">
          <label class="form-label">Семестр</label>
          <input type="text" v-model="form.semester" class="form-control" required />
        </div>
  
        <div class="col-md-2">
          <label class="form-label">Год</label>
          <input type="number" v-model.number="form.year" class="form-control" required />
        </div>
  
        <div class="col-md-4">
          <label class="form-label">Комментарий</label>
          <input type="text" v-model="form.comment" class="form-control" />
        </div>
  
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Добавить нагрузку</button>
        </div>
      </form>
  
      <!-- Таблица фактической нагрузки -->
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Преподаватель</th>
            <th>Группа</th>
            <th>Дисциплина</th>
            <th>Часы</th>
            <th>Тип</th>
            <th>Семестр</th>
            <th>Год</th>
            <th>Комментарий</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="load in actualLoads" :key="load.id">
            <td v-if="editingId !== load.id">{{ load.teacher?.name || '—' }}</td>
            <td v-else>
              <select v-model="editForm.teacher_id" class="form-select form-select-sm" required>
                <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
            </td>
  
            <td v-if="editingId !== load.id">{{ load.group?.name || '—' }}</td>
            <td v-else>
              <select v-model="editForm.group_id" class="form-select form-select-sm" required>
                <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
              </select>
            </td>
  
            <td v-if="editingId !== load.id">{{ load.discipline?.name || '—' }}</td>
            <td v-else>
              <select v-model="editForm.discipline_id" class="form-select form-select-sm" required>
                <option v-for="d in disciplines" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </td>
  
            <td v-if="editingId !== load.id">{{ load.hours }}</td>
            <td v-else>
              <input type="number" v-model.number="editForm.hours" min="1" class="form-control form-control-sm" required />
            </td>
  
            <td v-if="editingId !== load.id">{{ load.type }}</td>
            <td v-else>
              <input type="text" v-model="editForm.type" class="form-control form-control-sm" required />
            </td>
  
            <td v-if="editingId !== load.id">{{ load.semester }}</td>
            <td v-else>
              <input type="text" v-model="editForm.semester" class="form-control form-control-sm" required />
            </td>
  
            <td v-if="editingId !== load.id">{{ load.year }}</td>
            <td v-else>
              <input type="number" v-model.number="editForm.year" class="form-control form-control-sm" required />
            </td>
  
            <td v-if="editingId !== load.id">{{ load.comment || '—' }}</td>
            <td v-else>
              <input type="text" v-model="editForm.comment" class="form-control form-control-sm" />
            </td>
  
            <td>
              <button
                v-if="editingId !== load.id"
                @click="startEdit(load)"
                class="btn btn-sm btn-outline-primary me-1"
              >
                Редактировать
              </button>
              <button
                v-if="editingId !== load.id"
                @click="deleteLoad(load.id)"
                class="btn btn-sm btn-outline-danger"
              >
                Удалить
              </button>
  
              <span v-else>
                <button @click="saveEdit(load.id)" class="btn btn-sm btn-success me-1">Сохранить</button>
                <button @click="cancelEdit" class="btn btn-sm btn-secondary">Отмена</button>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from '../axios'
  
  export default {
    data() {
      return {
        actualLoads: [],
        teachers: [],
        groups: [],
        disciplines: [],
        form: {
          teacher_id: '',
          group_id: '',
          discipline_id: '',
          type: '',
          hours: 1,
          semester: '',
          year: new Date().getFullYear(),
          comment: '',
        },
        editingId: null,
        editForm: {
          teacher_id: '',
          group_id: '',
          discipline_id: '',
          type: '',
          hours: 1,
          semester: '',
          year: new Date().getFullYear(),
          comment: '',
        },
      }
    },
  
    methods: {
      async fetchData() {
        try {
          const [loadsRes, teachersRes, groupsRes, disciplinesRes] = await Promise.all([
            axios.get('/loads/actual'),
            axios.get('/admin/teachers'),
            axios.get('/admin/groups'),
            axios.get('/admin/disciplines'),
          ])
          this.actualLoads = loadsRes.data
          this.teachers = teachersRes.data
          this.groups = groupsRes.data
          this.disciplines = disciplinesRes.data
        } catch (error) {
          alert('Ошибка загрузки данных: ' + (error.response?.data?.message || error.message))
        }
      },
  
      resetForm() {
        this.form = {
          teacher_id: '',
          group_id: '',
          discipline_id: '',
          type: '',
          hours: 1,
          semester: '',
          year: new Date().getFullYear(),
          comment: '',
        }
      },
  
      async createLoad() {
        try {
          const res = await axios.post('/loads/actual', this.form)
          this.actualLoads.push(res.data)
          this.resetForm()
        } catch (error) {
          alert('Ошибка при добавлении нагрузки: ' + (error.response?.data?.message || error.message))
        }
      },
  
      startEdit(load) {
        this.editingId = load.id
        this.editForm = {
          teacher_id: load.teacher_id,
          group_id: load.group_id,
          discipline_id: load.discipline_id,
          type: load.type,
          hours: load.hours,
          semester: load.semester,
          year: load.year,
          comment: load.comment || '',
        }
      },
  
      cancelEdit() {
        this.editingId = null
        this.editForm = {
          teacher_id: '',
          group_id: '',
          discipline_id: '',
          type: '',
          hours: 1,
          semester: '',
          year: new Date().getFullYear(),
          comment: '',
        }
      },
  
      async saveEdit(id) {
        try {
          const payload = {
            teacher_id: this.editForm.teacher_id,
            group_id: this.editForm.group_id,
            discipline_id: this.editForm.discipline_id,
            type: this.editForm.type,
            hours: this.editForm.hours,
            semester: this.editForm.semester,
            year: this.editForm.year,
            comment: this.editForm.comment,
          }
  
          const res = await axios.put(`/loads/actual/${id}`, payload)
  
          const idx = this.actualLoads.findIndex(l => l.id === id)
          if (idx !== -1) {
            this.actualLoads[idx] = res.data
          }
  
          this.cancelEdit()
        } catch (error) {
          alert('Ошибка при сохранении изменений: ' + (error.response?.data?.message || error.message))
        }
      },
  
      async deleteLoad(id) {
        if (!confirm('Вы уверены, что хотите удалить эту нагрузку?')) return
  
        try {
          await axios.delete(`/loads/actual/${id}`)
  
          this.actualLoads = this.actualLoads.filter(l => l.id !== id)
        } catch (error) {
          alert('Ошибка при удалении нагрузки: ' + (error.response?.data?.message || error.message))
        }
      },
    },
  
    mounted() {
      this.fetchData()
    },
  }
  </script>
  
  <style scoped>
  /* Добавьте стили по необходимости */
  </style>
  