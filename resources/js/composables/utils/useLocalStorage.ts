/**
 * lock - заблокировать получение
 */
export const useLocalStorage = {
    lock: true,

    get: (key:string): string|null => {
        return localStorage.getItem(key);
    },

    setJson: (key:string, data: any): void => {
        localStorage.setItem(key, JSON.stringify(data));
    },

    clear: (): void => {
        const keys = [
            'tasks_problem_types_options',
            'tasks_problem_types',
            'tasks_priorities_options',
        ];
        if (useLocalStorage.lock) {
            keys.forEach((key:string) => {
                localStorage.removeItem(key);
            })
        }
    }
}
