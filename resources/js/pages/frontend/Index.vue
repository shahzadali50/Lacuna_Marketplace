<script setup lang="ts">
<<<<<<< HEAD
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import { DownOutlined } from '@ant-design/icons-vue'
import { usePage} from '@inertiajs/vue3';
const { props } = usePage()
=======
import { Head, Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
>>>>>>> 8e1a82d (implement the Eng | Português | 日本語  langauge accurate)

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
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                    Log in
                    </Link>
                    <Link :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Register
                    </Link>

                </template>
                <a href="/lang/jp">JP</a>
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
            </nav>
        </header>
        <div
            class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">

                <p  class="mb-1 font-medium">{{ props.translations.welcome }}</p>


            </main>
        </div>

    </div>
</template>
