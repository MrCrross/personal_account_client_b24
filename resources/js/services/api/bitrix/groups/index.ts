import ApiService from "@/services/service";
import {AxiosResponse} from "axios";
import {AutocompleteResponse} from "@/types";

export default {
    autocomplete: async (term: string): Promise<AxiosResponse<AutocompleteResponse>> => {
        return await ApiService.get('/api/bitrix/groups/autocomplete', {term});
    }
}
