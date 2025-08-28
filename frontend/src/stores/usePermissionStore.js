import axios from "axios";
import { defineStore } from "pinia";

export const usePermissionStore = defineStore('permission', {
    state: () => ({
        permissionList: [],
        loading: false,
        error: null,
    }), 
    actions: {
        async fetchAllPermission() {
            this.loading = true;
            try {
                const response = await axios.get(`/permissions`);
                this.permissionList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        }
    }
});