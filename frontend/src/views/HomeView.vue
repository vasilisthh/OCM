<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToUsers = () => {
  router.push('/users');
};
</script>

<template>
  <div>
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">OCM Users Management App</h1>
        <p class="hero-subtitle">A full-stack application with Vue.js 3, TypeScript, Laravel, and MySQL to manage users fetched from external APIs.</p>
        <button class="btn btn--primary btn--lg" @click="navigateToUsers">Manage Users</button>
      </div>
    </section>
    
    <div class="container container--xl">
      <div class="features-grid">
        <div class="feature-card">
          <h2 class="feature-title">Frontend Implementation</h2>
          <p class="feature-text">Built with Vue.js 3 and TypeScript using the Composition API, featuring responsive design and real-time search.</p>
        </div>
        
        <div class="feature-card">
          <h2 class="feature-title">Backend Integration</h2>
          <p class="feature-text">RESTful API built with Laravel PHP framework, using MySQL to store user data fetched from an external public API.</p>
        </div>
        
        <div class="feature-card">
          <h2 class="feature-title">User Management</h2>
          <p class="feature-text">Users can be fetched from API, viewed, edited, updated and deleted via a clean user interface with search functionality.</p>
        </div>
      </div>
    </div>
  </div>
</template>
