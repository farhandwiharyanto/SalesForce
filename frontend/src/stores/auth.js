import { defineStore } from 'pinia';
import api from '../api/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    role: (state) => state.user?.role || null,
  },
  actions: {
    async login(username, password) {
      const response = await api.post('/login', { username, password });
      this.token = response.data.token;
      this.user = response.data.user;
      localStorage.setItem('token', this.token);
      api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    },
    async fetchUser() {
      if (this.token) {
        try {
          api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
          const response = await api.get('/me');
          this.user = response.data;
        } catch (error) {
          this.logout();
        }
      }
    },
    async logout() {
      try {
        await api.post('/logout');
      } catch (error) {
        // ignore if network error or unauthenticated
      }
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
      delete api.defaults.headers.common['Authorization'];
    }
  }
});
