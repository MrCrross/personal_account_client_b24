import {API} from "@/services";
import {Autocomplete} from "@/types";

export const BGroups = {
    autocomplete: async (term: string): Promise<Autocomplete[]> => {
        const response = await API.b24.groups.autocomplete(term)

        return response.data.data;
    }
}
