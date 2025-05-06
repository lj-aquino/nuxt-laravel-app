// frontend/lib/api.js
const API_BASE_URL = 'http://localhost:8000/api'; // Adjust if your Laravel runs on a different port

export const api = {
  async get(endpoint) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`);
    if (!response.ok) {
      const error = await response.json().catch(() => ({ 
        message: `Server responded with status ${response.status}` 
      }));
      throw { error };
    }
    return await response.json();
  },
  
  async post(endpoint, data) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
      credentials: 'include'
    });
    if (!response.ok) {
      const error = await response.json().catch(() => ({ 
        message: `Server responded with status ${response.status}` 
      }));
      throw { error };
    }
    return await response.json();
  },
  
  // Auth methods
  auth: {
    async signInWithPassword(credentials) {
      return await api.post('auth/login', credentials);
    },
    async signOut() {
      return await api.post('auth/logout');
    },
    async getSession() {
      return await api.get('auth/session');
    }
  }
};