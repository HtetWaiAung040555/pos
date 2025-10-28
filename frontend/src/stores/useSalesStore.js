import axios from "axios";
import { defineStore } from "pinia";

export const useSaleStore = defineStore('sales', {
    state: () => ({
        salesList: [],
        loading: false,
        deleteLoading: false,
        error: null,
        data: null

    }),
    actions: {
        async fetchAllSales() {
            this.loading = true;
            try {
                const response = await axios.get(`/sales`);
                this.salesList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async fetchSales(id) {
            this.loading = true;
            try {
                const response = await axios.get(`/sales/${id}`);
                this.salesList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
        async addSales(formData) {
            this.loading = true;
            try {
                const response = await axios.post(`/sales`, formData);
                this.salesList = response.data.data;
            } catch(err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
            } finally {
                this.loading = false;
            }
        },
        async editSales(id, formData) {
            this.loading = true;
            try {
                const response = await axios.put(`/sales/${id}`, formData)
                this.salesList = response.data.data
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
                
            } finally {
                this.loading = false;
            }
        },
        async deleteSales(id) {
            this.deleteLoading = true;
            try {
                const response = await axios.delete(`/sales/${id}`);
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