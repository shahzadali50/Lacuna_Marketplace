<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';

const { props } = usePage();
const isOpen = ref(false);

const currentLocale = computed(() => props.currentLocale);
const availableLocales = computed(() => {
  return [
    { code: 'en', name: 'English' },
    { code: 'es', name: 'Español' },
    { code: 'fr', name: 'Français' },
    { code: 'de', name: 'Deutsch' },
    { code: 'pt', name: 'Português' },
    { code: 'jp', name: '日本語' }
  ];
});

const switchLanguage = (locale: string) => {
  window.location.href = route('language.switch', { locale });
};
</script>

<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center gap-1 rounded-md px-2 py-1 text-sm text-gray-600 hover:bg-gray-100"
      aria-label="Select language"
    >
      <Globe class="h-4 w-4" />
      <span class="uppercase">{{ currentLocale }}</span>
    </button>

    <div v-if="isOpen" class="absolute right-0 mt-1 w-40 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
      <div class="py-1">
        <button
          v-for="locale in availableLocales"
          :key="locale.code"
          @click="switchLanguage(locale.code)"
          class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
          :class="{ 'bg-gray-50': locale.code === currentLocale }"
        >
          {{ locale.name }}
        </button>
      </div>
    </div>
  </div>
</template>
