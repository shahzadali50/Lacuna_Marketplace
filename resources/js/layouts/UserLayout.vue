<script setup lang="ts">
import { useFlashMessages } from '@/composables/useFlashMessages';
import Header from '@/components/frontend/Header.vue';
useFlashMessages();
</script>
<template>

    <Header />
    <main>
        <slot />
    </main>
</template>

<style scoped></style>
