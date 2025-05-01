import axios from 'axios';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export const userService = {
  getUsers(search: string = '') {
    return apiClient.get('/users', {
      params: { search }
    });
  },

  getUser(id: string | number) {
    return apiClient.get(`/users/${id}`);
  },

  createUser(user: Record<string, any>) {
    return apiClient.post('/users', user);
  },

  updateUser(id: string | number, user: Record<string, any>) {
    return apiClient.put(`/users/${id}`, user);
  },

  deleteUser(id: string | number) {
    return apiClient.delete(`/users/${id}`);
  },

  fetchFromApi() {
    return apiClient.post('/users/fetch');
  }
};

export default apiClient;
