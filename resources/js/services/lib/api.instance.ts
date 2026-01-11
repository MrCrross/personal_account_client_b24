import axios, {AxiosError, AxiosResponse} from 'axios';
import { useToast } from '@/composables'
import type {APIError, APIValidationError} from "@/types";

const toast = useToast()
const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_ENDPOINT,
})

axiosInstance.interceptors.response.use(
    (responseAccess: AxiosResponse) => {
        const hasError: undefined | boolean = responseAccess.data?.error
        const textError: string = responseAccess.data?.message

        if (hasError && textError) {
            toast.error({content: textError})
            throw {
                message: textError,
                errors: {},
                status: responseAccess.status
            } as APIError;
        }

        return responseAccess
    }, async (responseError: AxiosError<APIValidationError>) => {
        if (
            responseError
            && typeof responseError === 'object'
            && typeof responseError.response === 'object'
            && typeof responseError.response.data === 'object'
            && responseError.response.data?.errors
        ) {
            const { message, errors, status } = responseError.response.data;

            toast.error({content: message})
            throw {
                message: message || 'Ошибка сервера',
                errors: errors || {},
                status: status
            } as APIError;
        }

        const errorMsg = 'Серверная ошибка, попробуйте повторить запрос позже'
        toast.error({
            title: 'Ошибка сервера',
            content: errorMsg,
        })

        throw {
            message: errorMsg,
            errors: {},
            status: 500
        } as APIError;
    })

export { axiosInstance }
