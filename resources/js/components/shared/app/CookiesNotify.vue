<template>
    <div
        v-if="!isCookieAccepted"
        class="fixed bottom-4 left-4 right-4 md:right-auto md:left-4 z-50 animate-fade-in-up"
    >
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4">
            <!-- Текст уведомления -->
            <div class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                <p>Мы используем файлы cookies для улучшения работы сайта.</p>
                <p>Оставаясь на нашем сайте, вы соглашаетесь с условиями использования файлов cookies.</p>
                <p>Вы можете ознакомиться с нашим
                    <Link
                        href="/policies/cookies"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline transition-colors"
                    >Положением об использовании файлов cookie</Link>.
                </p>
            </div>

            <!-- Кнопки действий -->
            <div class="flex md:w-1/12 gap-2">
                <button
                    @click="acceptCookies"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Принять
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {Link} from "@inertiajs/vue3";

const isCookieAccepted = ref(true) // По умолчанию скрываем, пока не проверим куки

// Ключ для хранения в куки
const COOKIE_NAME = 'cookies_accepted'
const COOKIE_DAYS = 2

// Проверяем, приняты ли уже куки
const checkCookie = (): boolean => {
    if (typeof document === 'undefined') return true // Для SSR

    const cookies = document.cookie.split(';')
    return cookies.some(cookie => {
        const [name, value] = cookie.trim().split('=')
        return name === COOKIE_NAME && value === 'true'
    })
}

// Устанавливаем куки
const setCookie = (accepted: boolean) => {
    if (typeof document === 'undefined') return

    const date = new Date()
    date.setTime(date.getTime() + (COOKIE_DAYS * 24 * 60 * 60 * 1000))
    const expires = `expires=${date.toUTCString()}`

    document.cookie = `${COOKIE_NAME}=${accepted}; ${expires}; path=/; SameSite=Lax`
}

// Принимаем куки
const acceptCookies = () => {
    setCookie(true)
    isCookieAccepted.value = true
}

// При монтировании компонента проверяем куки
onMounted(() => {
    isCookieAccepted.value = checkCookie()
})
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
