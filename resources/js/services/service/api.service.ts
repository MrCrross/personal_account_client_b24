import { axiosInstance } from "@/services/lib/api.instance"
import {AxiosRequestConfig, AxiosResponse} from "axios";
import {TokenManager} from "@/lib/TokenManager";

interface dataParams {
    [key: string]: string | Blob
}

function dataFunc (sendingData: string | dataParams, method = '') {
    // Создаем пустой объект
    if (sendingData instanceof FormData) {
        return sendingData;
    } else if (typeof sendingData !== 'string') {
        const data = new FormData()
        // Добавляем в созданный объект поля
        // Если obj не null добавляем в data ключ-значени obj
        if (sendingData) {
            Object.entries(sendingData).forEach(([key, value]) =>
                data.append(key, value)
            )
        }

        if (method) data.append('_method', method)

        return data
    } else {
        return sendingData
    }
}

function getConfig(config: AxiosRequestConfig = {}): AxiosRequestConfig
{
    const token = TokenManager.getToken();
    const headers: Record<string, string> = {};

    if (token) {
        headers.Authorization = token;
    }

    return {
        ...config,
        headers: {
            ...headers,
            ...(config?.headers || {})
        }
    }
}

const ApiService = {
    get (url: string, params?: object, optional?: object): Promise<AxiosResponse<any, any>> {
        return axiosInstance.get(url, getConfig({ params, ...optional }));
    },
    post (url: string = '', data: any, params?: object, responseType: string = ''): Promise<AxiosResponse<any, any>> {
        const formData = dataFunc(data)
        let config: object = {
            cache: false,
            params,
            responseType
        }

        if (typeof data === 'string') {
            config = {
                ...config,
                headers: { 'Content-Type': 'application/json' }
            }
        }

        return axiosInstance.post(url, formData, getConfig(config))
    },
    patch (url: string = '', data: any, params?: object, responseType: string = ''): Promise<AxiosResponse<any, any>> {
        const formData = dataFunc(data, 'PATCH')
        let config: object = {
            cache: false,
            params,
            responseType
        }

        if (typeof data === 'string') {
            config = {
                ...config,
                headers: { 'Content-Type': 'application/json' }
            }
        }

        return axiosInstance.post(url, formData, getConfig(config))
    },
    delete (url: string = '', params?: object): Promise<AxiosResponse<any, any>> {
        return axiosInstance.delete(url, getConfig(params))
    }
}

export default ApiService
