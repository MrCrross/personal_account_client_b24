<template>
    <div class="bg-muted/30 border border-muted rounded-lg p-3 mt-3">
        <div class="flex items-start gap-3">
            <ShieldCheckIcon class="w-4 h-4 text-primary mt-0.5 flex-shrink-0" />
            <div class="text-xs text-muted-foreground">
                <p class="leading-relaxed">
                    Нажимая на кнопку <strong>"{{ buttonName }}"</strong>, Вы соглашаетесь с
                    <Link
                        href="/policies/confidential"
                        class="text-primary hover:text-primary/80 underline transition-colors focus:outline-none focus:ring-1 focus:ring-primary rounded font-medium"
                    >
                        Политикой конфиденциальности
                    </Link>
                    и даете согласие на обработку Ваших персональных данных в соответствии с Федеральным законом № 152-ФЗ
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ShieldCheckIcon } from '@lucide/vue'
import {Link} from "@inertiajs/vue3";

interface Props {
    buttonName: string
}

defineProps<Props>()
</script>
