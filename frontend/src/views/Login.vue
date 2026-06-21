<template>
  <div class="login-container">
    <!-- Animated Background Elements -->
    <div class="ambient-bg">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
    </div>
    
    <div class="login-card-wrapper">
      <div class="login-card">
        <div class="logo-area">
          <div class="icon-wrapper">
            <svg class="w-10 h-10 text-white icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
          <h1>SalesForce CRM</h1>
          <p>Secure pipeline management</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label>Username</label>
            <div class="input-wrapper">
              <span class="input-icon">👤</span>
              <input 
                type="text" 
                v-model="username" 
                placeholder="Enter username" 
                required 
              />
            </div>
          </div>
          
          <div class="form-group">
            <label>Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔒</span>
              <input 
                type="password" 
                v-model="password" 
                placeholder="••••••••" 
                required 
              />
            </div>
          </div>

          <div v-if="error" class="text-sm text-red-400 text-center font-medium bg-red-900/20 py-2 rounded-lg border border-red-500/20 mb-4 shadow-sm backdrop-blur-md">
            {{ error }}
          </div>

          <button type="submit" :disabled="loading" class="login-btn">
            <span v-if="loading" class="loader"></span>
            <span v-else class="btn-text">Authenticate <span class="arrow">→</span></span>
          </button>
        </form>
        
        <div class="mt-8 text-center border-t border-white/10 pt-6">
          <p class="text-xs text-gray-400 font-medium mb-3">Quick Login (Password: password)</p>
          <div class="flex flex-wrap justify-center gap-2">
            <button @click="username='admin'; password='password'" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all text-gray-300 font-medium cursor-pointer">Admin</button>
            <button @click="username='pimpinan_sales'; password='password'" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all text-gray-300 font-medium cursor-pointer">Pimpinan</button>
            <button @click="username='sales'; password='password'" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all text-gray-300 font-medium cursor-pointer">Sales</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const username = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  try {
    loading.value = true;
    error.value = '';
    await authStore.login(username.value, password.value);
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid username or password.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

.login-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #09090b;
  position: relative;
  overflow: hidden;
  font-family: 'Outfit', sans-serif;
}

/* Premium Ambient Background */
.ambient-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  overflow: hidden;
  z-index: 0;
  background: radial-gradient(circle at 50% 50%, #18181b 0%, #09090b 100%);
}

.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.5;
  mix-blend-mode: screen;
}

.orb-1 {
  width: 600px; height: 600px;
  background: #4f46e5;
  top: -200px; left: -200px;
  animation: float 15s ease-in-out infinite alternate;
}

.orb-2 {
  width: 500px; height: 500px;
  background: #8b5cf6;
  bottom: -100px; right: -100px;
  animation: float 12s ease-in-out infinite alternate-reverse;
}

.orb-3 {
  width: 400px; height: 400px;
  background: #3b82f6;
  top: 40%; left: 30%;
  animation: pulse 10s ease-in-out infinite alternate;
  opacity: 0.3;
}

@keyframes float {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(50px, 100px) scale(1.1); }
  100% { transform: translate(-50px, -50px) scale(0.9); }
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.3; }
  100% { transform: scale(1.5); opacity: 0.5; }
}

.login-card-wrapper {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 440px;
  padding: 20px;
  perspective: 1000px;
}

.login-card {
  background: rgba(24, 24, 27, 0.6);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 32px;
  padding: 3.5rem 3rem;
  box-shadow: 0 30px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
  animation: cardEntrance 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transform-origin: center;
}

@keyframes cardEntrance {
  0% { opacity: 0; transform: translateY(40px) scale(0.95) rotateX(-5deg); }
  100% { opacity: 1; transform: translateY(0) scale(1) rotateX(0); }
}

.logo-area {
  text-align: center;
  margin-bottom: 3rem;
}

.icon-wrapper {
  width: 72px;
  height: 72px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, rgba(79, 70, 229, 0.2), rgba(139, 92, 246, 0.2));
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(139, 92, 246, 0.3);
  box-shadow: 0 0 30px rgba(139, 92, 246, 0.2);
  position: relative;
}

.icon-wrapper::after {
  content: '';
  position: absolute;
  inset: -2px;
  border-radius: 22px;
  background: linear-gradient(135deg, #4f46e5, #8b5cf6);
  z-index: -1;
  opacity: 0.5;
  filter: blur(10px);
}

.icon {
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

.logo-area h1 {
  font-size: 2rem;
  font-weight: 800;
  margin: 0 0 0.5rem;
  background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: -0.5px;
}

.logo-area p {
  color: #a1a1aa;
  font-size: 1rem;
  margin: 0;
  font-weight: 400;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #e4e4e7;
  margin-bottom: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 1.25rem;
  font-size: 1.1rem;
  color: #71717a;
  transition: color 0.3s;
}

.input-wrapper input {
  width: 100%;
  padding: 1.1rem 1.25rem 1.1rem 3rem;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  color: white;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-family: inherit;
}

.input-wrapper input::placeholder {
  color: #52525b;
}

.input-wrapper input:focus {
  outline: none;
  background: rgba(0, 0, 0, 0.4);
  border-color: #8b5cf6;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15), inset 0 0 0 1px #8b5cf6;
}

.input-wrapper input:focus + .input-icon,
.input-wrapper input:not(:placeholder-shown) ~ .input-icon {
  color: #a78bfa;
}

.login-btn {
  width: 100%;
  padding: 1.1rem;
  margin-top: 1rem;
  background: linear-gradient(135deg, #4f46e5, #8b5cf6);
  color: white;
  border: none;
  border-radius: 16px;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4);
}

.login-btn::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
  opacity: 0;
  transition: opacity 0.3s;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px -5px rgba(139, 92, 246, 0.5);
}

.login-btn:hover::before {
  opacity: 1;
}

.login-btn:active:not(:disabled) {
  transform: translateY(1px);
}

.btn-text {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 1;
}

.arrow {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.login-btn:hover .arrow {
  transform: translateX(4px);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loader {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
