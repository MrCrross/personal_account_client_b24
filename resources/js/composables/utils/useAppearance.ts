import { onMounted, ref } from 'vue';
import { Appearance } from "@/types";
import {API} from "@/services";
import {usePage} from "@inertiajs/vue3";

export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') {
        return;
    }

    if (value === 'system') {
        const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
        const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem('appearance') as Appearance | null;
};

const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();

    updateTheme(currentAppearance || 'system');
};

export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Initialize theme from saved preference or default to system...
    const savedAppearance = getStoredAppearance();
    updateTheme(savedAppearance || 'light');

    // Set up system theme change listener...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');

export function useAppearance() {
    onMounted(() => {
        let savedAppearance = localStorage.getItem('appearance') as Appearance | null;
        if (!savedAppearance) {
            const page = usePage();
            savedAppearance = page.props.auth.user.theme as Appearance;
            localStorage.setItem('appearance', page.props.auth.user.theme)
        }

        if (savedAppearance) {
            appearance.value = savedAppearance;
        }
        initializeTheme()
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem('appearance', value);

        updateTheme(value);
        API.users.updateTheme({theme: value});
    }

    return {
        appearance,
        updateAppearance,
    };
}
