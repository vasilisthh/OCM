<template>
    <div v-if="isOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit User' : 'Add New User' }}</h2>
          <button class="close-button" @click="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitForm">
            <div class="form-group">
              <label for="name">Name*</label>
              <input 
                id="name" 
                v-model="form.name" 
                type="text" 
                required
                :class="{ 'is-invalid': errors.name }"
              >
              <div v-if="errors.name" class="error-message">{{ errors.name }}</div>
            </div>
  
            <div class="form-group">
              <label for="email">Email*</label>
              <input 
                id="email" 
                v-model="form.email" 
                type="email" 
                required
                :class="{ 'is-invalid': errors.email }"
              >
              <div v-if="errors.email" class="error-message">{{ errors.email }}</div>
            </div>
  
            <div class="form-group">
              <label for="username">Username*</label>
              <input 
                id="username" 
                v-model="form.username" 
                type="text" 
                required
                :class="{ 'is-invalid': errors.username }"
              >
              <div v-if="errors.username" class="error-message">{{ errors.username }}</div>
            </div>
  
            <div class="form-group">
              <label for="phone">Phone</label>
              <input 
                id="phone" 
                v-model="form.phone" 
                type="text"
              >
            </div>
  
            <div class="form-group">
              <label for="website">Website</label>
              <input 
                id="website" 
                v-model="form.website" 
                type="text"
              >
            </div>
  
            <div class="form-actions">
              <button 
                type="submit" 
                class="btn btn--primary btn--sm" 
                :disabled="isSubmitting"
              >
                {{ isSubmitting ? 'Saving...' : (isEditing ? 'Update User' : 'Add User') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, reactive, watch } from 'vue';
  import { userService } from '../services/api';
  
  const props = defineProps({
    isOpen: Boolean,
    isEditing: Boolean,
    userData: Object
  });
  
  const emit = defineEmits(['close', 'user-added', 'user-updated']);
  
  const form = reactive({
    name: '',
    email: '',
    username: '',
    phone: '',
    website: ''
  });
  
  const errors = reactive({
    name: '',
    email: '',
    username: ''
  });
  
  const isSubmitting = ref(false);
  
  watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
      if (props.isEditing && props.userData) {
        form.name = props.userData.name || '';
        form.email = props.userData.email || '';
        form.username = props.userData.username || '';
        form.phone = props.userData.phone || '';
        form.website = props.userData.website || '';
      } else {
        resetForm();
      }
      clearErrors();
    }
  });
  
  const validateForm = () => {
    let valid = true;
    clearErrors();
  
    if (!form.name.trim()) {
      errors.name = 'Name is required';
      valid = false;
    }
  
    if (!form.email.trim()) {
      errors.email = 'Email is required';
      valid = false;
    }
  
    if (!form.username.trim()) {
      errors.username = 'Username is required';
      valid = false;
    }
  
    return valid;
  };
  
  const submitForm = async () => {
    if (!validateForm()) return;
  
    isSubmitting.value = true;
  
    try {
      let response;
  
      if (props.isEditing && props.userData) {
        response = await userService.updateUser(props.userData.id, form);
        emit('user-updated', response.data.data);
      } else {
        response = await userService.createUser(form);
        emit('user-added', response.data.data);
      }
  
      closeModal();
    } catch (error) {
      alert('Error saving user. Please check your inputs and try again.');
    } finally {
      isSubmitting.value = false;
    }
  };
  
  const closeModal = () => {
    resetForm();
    clearErrors();
    emit('close');
  };
  
  const resetForm = () => {
    form.name = '';
    form.email = '';
    form.username = '';
    form.phone = '';
    form.website = '';
  };
  
  const clearErrors = () => {
    errors.name = '';
    errors.email = '';
    errors.username = '';
  };
  </script>
  