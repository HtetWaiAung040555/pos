import { defineStore } from "pinia";
import axios from "axios";
import { API_URL } from "@/utils/config";

const api_url = API_URL;

export const useReceivableStore = defineStore('receivable', {
    state: () => ({
        receivableList: null,
        loading: false,
        deleteLoading: false,
        data: null,
        error: null,
    }),

    actions: {
        async fetchAllReceivable() {
            this.loading = true
            try {
                const response = await axios.get(`/customers_transactions`);
                this.receivableList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async fetchReceivable(receivableId) {
            this.loading = true;
            try {
                const response = await axios.get(`/customers_transactions/${receivableId}`);
                this.receivableList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async addReceivable(formData) {
            this.loading = true;
            try {
                const response = await axios.post(`/customers_transactions`, formData);
                this.receivableList = response.data.data;
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
            } finally {
                this.loading = false;
            }
        },
        async editReceivable(formData, receivableId) {
            this.loading = true;
            try {
                const response = await axios.put(`/customers_transactions/${receivableId}`, formData);
                this.receivableList = response.data.data;
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
            } finally {
                this.loading = false;
            }
        },
        async deleteReceivable(receivableId) {
            this.deleteLoading = true;
            try {
                const response = await axios.delete(`/customers_transactions/${receivableId}`);
                this.data = response;
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data;
                } else if (err.response && err.response.status === 400) {
                    this.error = err.response.data.error;
                } 
            } finally {
                this.deleteLoading = false;
            }
        }
    }
});