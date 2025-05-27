<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title mb-4 text-center">Регистрация</h3>

            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input v-model="name" type="text" class="form-control" id="name" required />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" required />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input v-model="password" type="password" class="form-control" id="password" required />
              </div>

              <div class="mb-3">
                <label for="role" class="form-label">Роль</label>
                <select v-model="role" id="role" class="form-select" required>
                  <option disabled value="">Выберите роль</option>
                  <option value="admin">Администратор</option>
                  <option value="methodist">Методист</option>
                  <option value="teacher">Преподаватель</option>
                </select>
              </div>

              <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
            </form>

            <div class="text-center mt-3">
              <router-link to="/login">Уже есть аккаунт? Войти</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      role: '',
    }
  },
  methods: {
    async register() {
      try {
        const response = await axios.post('http://localhost:8000/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          role: this.role,
        })
        localStorage.setItem('token', response.data.token)
        this.$router.push('/dashboard')
      } catch (error) {
        alert('Ошибка регистрации: ' + (error.response?.data?.message || 'Неизвестная ошибка'))
      }
    },
  },
}
</script>
