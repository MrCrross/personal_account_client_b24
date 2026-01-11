<script setup lang="ts">
import { Button, Checkbox, Input, Label } from '@/components';
import { AuthLayout } from '@/layouts';
import { Head, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import { API } from "@/services";
import { APIResponse, JwtToken, LoginFormInput} from "@/types";
import { AxiosResponse } from "axios";
import { LogIn } from "lucide-vue-next";
import {TokenManager} from "@/lib/TokenManager";
import {useLocalStorage} from "@/composables";

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const isProcessing = ref(false);
const loginForm = ref({
    email: "",
    password: "",
    remember: false
});

const loginSubmit = async () => {
    useLocalStorage.clear();
    isProcessing.value = true;

    const data: LoginFormInput = loginForm.value;

    try {
        const response: AxiosResponse<APIResponse<JwtToken>> = await API.auth.authLogin(data);
        const jwtToken = response.data.data;
        TokenManager.setToken(jwtToken.token_type, jwtToken.access_token, jwtToken.expires_on);

        router.visit('/tasks');
    } finally {
        isProcessing.value = false;
        loginForm.value.password = '';
    }
};
</script>

<template>
    <AuthLayout title="Личный кабинет" description="Войдите в систему FlashFormat">
        <Head title="Вход" />

        <form
            @submit.prevent="loginSubmit"
            class="flex flex-col gap-6"
        >
            <div v-if="status" class="text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Почта</Label>
                    <Input
                        v-model="loginForm.email"
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Пароль</Label>
<!--                        <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5"> Забыли пароль? </TextLink>-->
                    </div>
                    <Input
                        v-model="loginForm.password"
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Пароль"
                    />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox v-model="loginForm.remember" id="remember" name="remember" :tabindex="3" />
                        <span>Запомнить меня</span>
                    </Label>
                </div>

                <Button type="submit" variant="destructive" class="mt-4 w-full" :tabindex="4" :disabled="isProcessing">
                    <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                    <LogIn class="mr-2 text-gray-100"/>
                    Войти
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
