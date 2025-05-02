<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { userService } from '../services/api';
import UserModal from '../components/UserModal.vue';

const users = ref<any[]>([]);
const isLoading = ref(false);
const error = ref('');
const searchQuery = ref('');
const currentPage = ref(1);
const usersPerPage = 15;

const isModalOpen = ref(false);
const isEditing = ref(false);
const selectedUser = ref<any>(null);

const openAddUserModal = () => {
  selectedUser.value = null;
  isEditing.value = false;
  isModalOpen.value = true;
};

const openEditUserModal = (user: any) => {
  selectedUser.value = user;
  isEditing.value = true;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const handleUserAdded = (user: any) => {
  users.value.unshift(user);
  alert('User added successfully!');
};

const handleUserUpdated = (updatedUser: any) => {
  const index = users.value.findIndex(u => u.id === updatedUser.id);
  if (index !== -1) {
    users.value[index] = updatedUser;
  }
  alert('User updated successfully!');
};

const fetchUsers = async () => {
  isLoading.value = true;
  error.value = '';

  try {
    const response = await userService.getUsers(searchQuery.value);
    users.value = response.data.data;
  } catch (err: any) {
    error.value = err.message || 'Failed to load users.';
  } finally {
    isLoading.value = false;
  }
};

const fetchFromApi = async () => {
  isLoading.value = true;
  error.value = '';

  try {
    const response = await userService.fetchFromApi();
    await fetchUsers();
    alert('Users fetched from external API successfully!');
  } catch (err: any) {
    let errorMessage = 'Failed to fetch users from external API.';
    if (err.response) {
      errorMessage += ` Status: ${err.response.status}. ${err.response.data?.message || ''}`;
    } else if (err.request) {
      errorMessage += ' No response from server. Check if backend is running.';
    } else {
      errorMessage += ` Error: ${err.message}`;
    }
    
    alert(errorMessage);
    error.value = errorMessage;
  } finally {
    isLoading.value = false;
  }
};

const deleteUser = async (id: number) => {
  if (!confirm('Are you sure you want to delete this user?')) return;

  try {
    await userService.deleteUser(id);
    users.value = users.value.filter(user => user.id !== id);
    alert('User deleted successfully!');
  } catch (err: any) {
    alert('Failed to delete user.');
  }
};

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;

  const query = searchQuery.value.toLowerCase();

  return users.value.filter(user =>
    user.name.toLowerCase().includes(query) ||
    user.username.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  );
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * usersPerPage;
  return filteredUsers.value.slice(start, start + usersPerPage);
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / usersPerPage);
});

const goToPage = (page: number) => {
  currentPage.value = page;
};

const previousPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

onMounted(() => {
  fetchUsers();
});
</script>

<template>
  <div class="container container--xl p-3">
    <div class="d-flex justify-between align-center mb-4">
      <h1>User Management</h1>
      <div class="d-flex gap-2">
        <button class="btn btn--primary btn--sm" @click="fetchFromApi">Fetch From External API</button>
        <button class="btn btn--primary btn--sm" @click="openAddUserModal">Add User</button>
      </div>
    </div>

    <div class="mb-3">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search by name, username or email..."
        class="form-input"
      />
    </div>

    <div class="table-container">
      <div class="d-flex flex-column align-center p-4 w-100" v-if="isLoading">
        <div class="loader"></div>
        <p class="mt-2">Loading users...</p>
      </div>

      <div class="p-3 text-center w-100" v-else-if="error">
        <p class="error-message mb-2">{{ error }}</p>
        <button class="btn btn--primary" @click="fetchUsers">Retry</button>
      </div>

      <table class="table" v-else>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Website</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody v-if="paginatedUsers.length > 0">
          <tr v-for="user in paginatedUsers" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.username }}</td>
            <td>{{ user.phone }}</td>
            <td>{{ user.website }}</td>
            <td>
              <button class="btn btn--error btn--sm" @click="deleteUser(user.id)">Delete</button>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="6" class="text-center p-4">
              {{ searchQuery ? 'No users match your search criteria.' : 'No users found.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination" v-if="totalPages > 1">
      <button class="pagination-button" @click="previousPage" :disabled="currentPage === 1">
        &laquo;
      </button>

      <button
        v-for="page in totalPages"
        :key="page"
        class="pagination-button mx-1"
        :class="{ 'btn--primary': currentPage === page }"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>

      <button class="pagination-button" @click="nextPage" :disabled="currentPage === totalPages">
        &raquo;
      </button>
    </div>
  </div>

  <UserModal
  :is-open="isModalOpen"
  :is-editing="isEditing"
  :user-data="selectedUser"
  @close="closeModal"
  @user-added="handleUserAdded"
  @user-updated="handleUserUpdated"
/>

</template>
