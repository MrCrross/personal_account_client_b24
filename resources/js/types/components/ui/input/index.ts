export type CalendarDate = {
    month: number,
    day: number,
    year: number,
}

interface DateRangePickerCalendarType {
    identifier: string
}

interface DateRangePickerValue extends CalendarDate{
    calendar: DateRangePickerCalendarType,
    era: string,
}

export interface DateRangePickerInterface {
    end: DateRangePickerValue,
    start: DateRangePickerValue,
}
