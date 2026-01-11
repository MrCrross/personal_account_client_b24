import {CalendarDate} from '@/types'

export const useDateFormat = () => {
    const formatDate = (dateString: string | null): string => {
        if (!dateString) return 'Не указано'

        const date = new Date(dateString)
        try {
            return new Intl.DateTimeFormat('ru-RU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(date)
        } catch {
            return 'Не указано'
        }
    }

    const formatDateTime = (dateString: string | null): string => {
        if (!dateString) return 'Не указано'

        const date = new Date(dateString)
        try {
            return new Intl.DateTimeFormat('ru-RU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date)
        } catch {
            return 'Не указано'
        }
    }

    const formatDuration = (minutes: number | null): string => {
        if (!minutes) return 'Не указано'

        const hours = Math.floor(minutes / 60)
        const mins = minutes % 60

        if (hours > 0) {
            return `${hours}ч ${mins}м`
        }
        return `${mins}м`
    }

    const formatFromCalendar = (calendar: CalendarDate): string => {
        let day = calendar.day.toString()
        let month = calendar.month.toString()
        if (calendar.day < 10) {
            day = '0' + day;
        }
        if (calendar.month < 10) {
            month = '0' + month;
        }

        return `${calendar.year}-${month}-${day}`
    }


    return {
        formatDate,
        formatDateTime,
        formatDuration,
        formatFromCalendar,
    }
}
