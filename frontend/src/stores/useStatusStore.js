import { defineStore } from "pinia";
import axios from "axios";
import { API_URL } from "@/utils/config";

const api_url = API_URL;

export const useStatusStore = defineStore('status', {
    state: () => ({
        statusList: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchAllStatus() {
            this.loading = true
            this.error = null
            try {
                const response = await axios.get(`${api_url}/status`);
                this.statusList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
                console.log("store/status:"+ this.statusList )
            }
        },
    }
})