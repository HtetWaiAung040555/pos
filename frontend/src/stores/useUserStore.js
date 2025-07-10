import { defineStore } from "pinia";
import axios from "axios";
import { API_URL } from "@/utils/config";

const api_url = API_URL;

export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        loginUser: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchAllUsers() {
            this.loading = true
            this.error = null
            try {
                const response = await axios.get(`${api_url}/users`);
                this.users = JSON.stringify(response.data);
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async loginUser(formData) {
            this.loading = true
            this.error = null
            try {
                const response = await axios.post(`${api_url}/login`, formData);
                this.loginUser = JSON.stringify(response.data);
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        }
    }
})