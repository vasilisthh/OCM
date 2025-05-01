<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div id="app">
    <header class="app-header">
      <div class="header-container">
        <h1 class="app-logo">OCM Users App</h1>
        <nav class="app-nav">
          <RouterLink to="/" class="nav-link">Home</RouterLink>
          <RouterLink to="/users" class="nav-link">Users</RouterLink>
        </nav>
      </div>
    </header>

    <main class="app-main">
      <RouterView />
    </main>

    <footer class="app-footer">
      <p>&copy; {{ new Date().getFullYear() }} OCM Users App. All rights reserved.</p>
    </footer>
  </div>
</template>
