import {API} from "@/services";

export class TokenManager {
    private static refreshTimer: number | null = null;

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

    private static scheduleRefresh(expiresAt: number): void {
        // Очищаем предыдущий таймер
        if (this.refreshTimer) {
            clearTimeout(this.refreshTimer);
            this.refreshTimer = null;
        }

        // Обновляем во время истечения срока
        const refreshTime = expiresAt * 1000;
        const currentTime = Date.now();
        const timeUntilRefresh = refreshTime - currentTime;

        if (timeUntilRefresh > 0) {
            this.refreshTimer = setTimeout(async () => {
                await this.refreshToken();
            }, timeUntilRefresh);
        } else {
            // Если время уже прошло, сразу обновляем
            this.refreshToken();
        }
    }

    public static async refreshToken(): Promise<string> {
        try {
            // Получаем текущий токен для отправки на сервер
            const currentToken = this.getToken();

            if (!currentToken) {
                throw new Error('No token available for refresh');
            }

            // Отправляем запрос на обновление токена
            const response = await API.auth.refreshToken();
            const { token_type, access_token, expires_on } = response.data.data;
            // Сохраняем новый токен
            this.setToken(token_type, access_token, expires_on);

            return access_token;
        } catch (error) {
            console.error('Token refresh failed:', error);
            this.logout();

            throw error;
        }
    }

    private static clearTokens(): void {
        localStorage.removeItem('access_token');
        localStorage.removeItem('token_issued_at');
        localStorage.removeItem('token_expired_at');

        if (this.refreshTimer) {
            clearTimeout(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    // Восстановление таймера при загрузке приложения
    public static init(): void {
        const expiresAt = localStorage.getItem('token_expired_at');
        const token = localStorage.getItem('access_token');

        if (token && expiresAt) {
            const expiresAtNum = parseInt(expiresAt);
            const currentTime = Math.floor(Date.now() / 1000);

            if (expiresAtNum > currentTime) {
                // Токен еще валиден, планируем обновление
                this.scheduleRefresh(expiresAtNum);
            } else {
                this.forceRefresh();
            }
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
