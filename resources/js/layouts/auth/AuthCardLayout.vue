<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();

// Language map (key => locale)
const languageMap = {
  1: 'en',
  2: 'pt',
  3: 'jp',
}

const handleMenuClick = (info: { key: string }) => {
  const locale = languageMap[info.key]
  if (locale) {
    router.visit(`/lang/${locale}`, {
      method: 'get',
    })
  }
}

const { props } = usePage();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <Link :href="route('home')" class="flex justify-center items-center gap-2 self-center font-medium">
                <div class="flex w-[50%] items-center justify-center">
                    <AppLogoIcon class="size-9 fill-current text-black dark:text-white" />
                </div>
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-xl">
                    <CardHeader class="px-10 pb-0 pt-8 text-center">
                        <CardTitle class="text-xl">{{ title }}</CardTitle>
                        <CardDescription>
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <!-- Language Switcher -->
                        <div class="mb-4 flex justify-center">
                            <a-dropdown>
                                <template #overlay>
                                    <a-menu @click="handleMenuClick">
                                        <a-menu-item key="1">EN</a-menu-item>
                                        <a-menu-item key="2">PT</a-menu-item>
                                        <a-menu-item key="3">JP</a-menu-item>
                                    </a-menu>
                                </template>
                                <a-button>
                                    Select Language <DownOutlined />
                                </a-button>
                            </a-dropdown>
                        </div>
                        
                        <!-- Translation Display -->
                        <div class="mb-4 text-center">
                            <p class="text-sm text-muted-foreground">Current language: {{ props.currentLocale }}</p>
                            <p class="mt-2">{{ props.translations.welcome }}</p>
                        </div>
                        
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
