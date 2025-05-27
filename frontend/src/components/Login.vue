<template>
    <div>
      <h2>Login</h2>
      <form @submit.prevent="login">
        <input type="email" v-model="email" placeholder="Email" required />
        <input type="password" v-model="password" placeholder="Password" required />
        <button type="submit">Войти</button>
      </form>
  
      <div v-if="user">
        <h3>Пользователь:</h3>
        <p>Имя: {{ user.name }}</p>
        <p>Email: {{ user.email }}</p>
      </div>
  
      <div v-if="error" style="color:red">{{ error }}</div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        apiToken: null,
        user: null,
        error: null,
      };
    },
    methods: {
      async login() {
        try {
          this.error = null;
          const response = await axios.post('http://localhost:8000/api/login', {
            email: this.email,
            password: this.password,
          });
          this.apiToken = response.data.api_token;
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.apiToken;
  
          // Получаем данные пользователя
          const userResp = await axios.get('http://localhost:8000/api/user');
          this.user = userResp.data;
        } catch (e) {
          this.error = 'Ошибка авторизации: ' + (e.response?.data?.message || e.message);
        }
      },
    },
  };
  </script>
  