import { defineStore } from "pinia";
import axios from "axios";

export const useWarehouseStore = defineStore('warehouse', {
    state: () => ({
        warehouseList: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchAllWarehouse() {
            this.loading = true;
            try {
                const response = await axios.get(`/warehouses`);
                this.warehouseList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
    }
});