<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title mb-4 text-center">Вход в систему</h3>

            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" required />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input v-model="password" type="password" class="form-control" id="password" required />
              </div>

              <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>

            <div class="text-center mt-3">
              <router-link to="/register">Нет аккаунта? Зарегистрируйтесь</router-link>
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
      email: '',
      password: '',
    }
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          email: this.email,
          password: this.password,
        })
        // Сохраняем именно api_token из ответа
        localStorage.setItem('token', response.data.api_token)
        this.$router.push('/dashboard')
      } catch (error) {
        alert('Ошибка входа: ' + (error.response?.data?.message || 'Неизвестная ошибка'))
      }
    }

  },
}
</script>
