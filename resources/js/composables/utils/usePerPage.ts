import type { SelectOption } from '@/types'

const STORAGE_KEY = 'per_page_selection'

export const perPageOptions: SelectOption[] = [
    { value: 10, label: '10' },
    { value: 15, label: '15' },
    { value: 25, label: '25' },
    { value: 50, label: '50' },
    { value: 100, label: '100' },
]

const DEFAULT_OPTION = perPageOptions[1]

/**
 * Источник правды для выбора количества элементов на странице.
 * Чтение из localStorage происходит синхронно, чтобы родительский
 * компонент мог инициализировать значение до первого запроса.
 */
export const usePerPage = () => {
    const read = (): SelectOption => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY)
            if (stored) {
                const parsed = JSON.parse(stored)
                const found = perPageOptions.find(opt => opt.value === parsed.value)
                if (found) {
                    return found
                }
            }
        } catch (error) {
            console.warn('Failed to load perPage from localStorage:', error)
        }

        return DEFAULT_OPTION
    }

    const save = (value: SelectOption): void => {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
        } catch (error) {
            console.warn('Failed to save perPage to localStorage:', error)
        }
    }

    return { perPageOptions, read, save }
}
