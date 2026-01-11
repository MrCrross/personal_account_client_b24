import {JwtToken, LoginFormInput} from "@/types";
import ApiService from "@/services/service";
import {AxiosResponse} from "axios";
import {APIResponse} from "@/types";
import {store} from "@/routes/login";

export default {
    authLogin(input: LoginFormInput): Promise<AxiosResponse<APIResponse<JwtToken>>> {
        return ApiService.post(store().url, input);
    },
    refreshToken(): Promise<AxiosResponse<APIResponse<JwtToken>>> {
        return ApiService.get('/api/auth/refresh');
    },
    logout(): Promise<AxiosResponse<null>> {
        return ApiService.post('/logout', null);
    },
};
