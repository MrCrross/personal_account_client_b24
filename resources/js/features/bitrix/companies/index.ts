import {API} from "@/services";
import {Autocomplete} from "@/types";

export const BCompanies = {
    autocomplete: async (term: string): Promise<Autocomplete[]> => {
        const response = await API.b24.companies.autocomplete(term)

        return response.data.data;
    }
}
