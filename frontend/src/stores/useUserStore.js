import { defineStore } from "pinia";
import axios from "axios";



export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        userData: [],
        token: localStorage.getItem("token") || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token
    },

    actions: {
        async fetchAllUsers() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/users`);
                this.users = JSON.stringify(response.data);
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async loginUser(formData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/login`, formData);
                this.userData = JSON.stringify(response.data);
                if (response.data.isSuccess) {
                    console.log(response.data)
                    this.token = response.data.token;
                    localStorage.setItem('token', response.data.token);
                    localStorage.setItem('user', JSON.stringify({
                        id: response.data.user.id,
                        name: response.data.user.name
                    }));
                }
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/logout`);
                if (response.data.isSuccess) {
                    this.token = null;
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                }
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        }
    }
});