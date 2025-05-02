<template>
  <Transition name="notification-fade">
    <div v-if="isVisible" class="notification" :class="notificationClass">
      {{ message }}
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';

const props = defineProps({
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 3500
  },
  visible: {
    type: Boolean,
    default: true
  },
  type: {
    type: String,
    default: 'success',
    validator: (value: string) => ['success', 'error', 'warning', 'info'].includes(value)
  }
});

const notificationClass = computed(() => {
  return `notification--${props.type}`;
});

const emit = defineEmits(['hide']);
const isVisible = ref(props.visible);
let timer: number | null = null;

watch(() => props.visible, (newVal) => {
  isVisible.value = newVal;
  if (newVal && props.duration > 0) {
    if (timer) clearTimeout(timer);
    timer = window.setTimeout(() => {
      isVisible.value = false;
      emit('hide');
    }, props.duration);
  }
});

onMounted(() => {
  if (isVisible.value && props.duration > 0) {
    timer = window.setTimeout(() => {
      isVisible.value = false;
      emit('hide');
    }, props.duration);
  }
});

onUnmounted(() => {
  if (timer) {
    clearTimeout(timer);
  }
});
</script> 