import {API} from "@/services";

export class TokenManager {
    private static refreshTimer: number | null = null;
    // Текущий запрос на обновление — чтобы таймер и возврат на вкладку не дёргали refresh параллельно
    private static refreshing: Promise<string> | null = null;

    // Обновляем токен, когда осталось ~20% его жизни, но не позже чем за столько секунд до истечения.
    // Важно: /api/auth/refresh висит под auth:api и отклоняет уже истёкший токен — поэтому обновлять
    // нужно заранее, пока токен ещё валиден, а не впритык к моменту истечения.
    private static readonly MIN_MARGIN_SECONDS = 30;
    private static readonly FALLBACK_MARGIN_SECONDS = 300;
    private static readonly RETRY_DELAY_MS = 30000;

    public static setToken(token_type: string, access_token: string, expiresIn: number = 3600): void {
        const issuedAt = Math.floor(Date.now() / 1000);
        const expiresAt = issuedAt + expiresIn;

        localStorage.setItem('access_token', token_type + " " + access_token);
        localStorage.setItem('token_issued_at', issuedAt.toString());
        localStorage.setItem('token_expired_at', expiresAt.toString());

        this.scheduleRefresh(expiresAt);
    }

    public static getToken(): string | null {
        return localStorage.getItem('access_token');
    }

    public static isTokenValid(): boolean {
        const expiresAt = localStorage.getItem('token_expired_at');
        if (!expiresAt) return false;

        const currentTime = Math.floor(Date.now() / 1000);
        return currentTime < parseInt(expiresAt);
    }

    public static getTimeUntilExpiry(): number {
        const expiresAt = localStorage.getItem('token_expired_at');
        if (!expiresAt) return 0;

        const currentTime = Math.floor(Date.now() / 1000);
        return Math.max(0, parseInt(expiresAt) - currentTime);
    }

    private static clearTimer(): void {
        if (this.refreshTimer !== null) {
            clearTimeout(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    private static scheduleRefresh(expiresAt: number): void {
        this.clearTimer();

        const issuedAt = parseInt(localStorage.getItem('token_issued_at') ?? '', 10);
        const lifetime = Number.isFinite(issuedAt) && issuedAt > 0 ? expiresAt - issuedAt : 0;
        const margin = lifetime > 0
            ? Math.max(TokenManager.MIN_MARGIN_SECONDS, Math.floor(lifetime * 0.2))
            : TokenManager.FALLBACK_MARGIN_SECONDS;

        const timeUntilRefresh = (expiresAt - margin) * 1000 - Date.now();

        if (timeUntilRefresh > 0) {
            this.refreshTimer = window.setTimeout(() => {
                void this.refreshToken();
            }, timeUntilRefresh);
        } else {
            // Уже в окне обновления — обновляем немедленно, пока токен ещё валиден
            void this.refreshToken();
        }
    }

    private static scheduleRetry(): void {
        this.clearTimer();
        this.refreshTimer = window.setTimeout(() => {
            void this.refreshToken();
        }, TokenManager.RETRY_DELAY_MS);
    }

    public static refreshToken(): Promise<string> {
        // Дедуп: если обновление уже идёт — возвращаем тот же промис
        if (this.refreshing) {
            return this.refreshing;
        }

        this.refreshing = this.performRefresh().finally(() => {
            this.refreshing = null;
        });

        return this.refreshing;
    }

    private static async performRefresh(): Promise<string> {
        try {
            const currentToken = this.getToken();

            if (!currentToken) {
                throw new Error('No token available for refresh');
            }

            const response = await API.auth.refreshToken();
            const { token_type, access_token, expires_on } = response.data.data;
            // Сохраняем новый токен и планируем следующее обновление
            this.setToken(token_type, access_token, expires_on);

            return access_token;
        } catch (error) {
            // Токен ещё валиден (сетевой сбой/троттлинг фоновой вкладки) — не разлогиниваем,
            // а пробуем позже. Разлогиниваем только когда токен реально истёк.
            if (this.isTokenValid()) {
                this.scheduleRetry();
                throw error;
            }

            await this.logout();
            throw error;
        }
    }

    private static clearTokens(): void {
        localStorage.removeItem('access_token');
        localStorage.removeItem('token_issued_at');
        localStorage.removeItem('token_expired_at');

        this.clearTimer();
    }

    // Восстановление таймера при загрузке приложения
    public static init(): void {
        // Фоновые вкладки троттлят setTimeout, поэтому при возврате на вкладку перепроверяем токен
        // и обновляем его, пока он ещё валиден (иначе пропущенный таймер привёл бы к разлогину).
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.refreshIfNeeded();
            }
        });
        window.addEventListener('focus', () => this.refreshIfNeeded());

        this.refreshIfNeeded();
    }

    private static refreshIfNeeded(): void {
        const expiresAt = localStorage.getItem('token_expired_at');
        const token = localStorage.getItem('access_token');

        if (!token || !expiresAt) {
            return;
        }

        const expiresAtNum = parseInt(expiresAt, 10);
        const currentTime = Math.floor(Date.now() / 1000);

        if (expiresAtNum > currentTime) {
            // Токен валиден — (пере)планируем обновление; если пора, scheduleRefresh обновит сразу
            this.scheduleRefresh(expiresAtNum);
        } else {
            // Токен истёк — пробуем обновить (сработает в пределах refresh_ttl и валидной сессии)
            void this.forceRefresh();
        }
    }

    // Принудительное обновление токена (например, по кнопке)
    public static async forceRefresh(): Promise<string> {
        return await this.refreshToken();
    }

    private static async logout(): Promise<void> {
        this.clearTokens();
        await API.auth.logout();
        window.location.reload();
    }
}
