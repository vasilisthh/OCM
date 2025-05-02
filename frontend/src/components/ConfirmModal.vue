<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ title }}</h2>
        <button class="close-button" @click="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <p>{{ message }}</p>
        <div class="modal-actions">
          <button class="btn btn--error btn--sm" @click="confirm">Delete</button>
          <button class="btn btn--secondary btn--sm" @click="closeModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Confirm Delete'
  },
  message: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['close', 'confirm']);

const closeModal = () => {
  emit('close');
};

const confirm = () => {
  emit('confirm');
  emit('close');
};
</script> 