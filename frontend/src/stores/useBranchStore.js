import { defineStore } from "pinia";
import axios from "axios";
import { API_URL } from "@/utils/config";

const api_url = API_URL;

export const useBranchStore = defineStore('branch', {
    state: () => ({
        branchList: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchAllBranch() {
            this.loading = true
            this.error = null
            try {
                const response = await axios.get(`${api_url}/branches`);
                this.branchList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
                console.log("store/branch:"+ this.branchList )
            }
        },
        async fetchBranch(branchId) {
            this.loading = true
            this.error = null
            try {
                const response = await axios.get(`${api_url}/branches/${branchId}`);
                this.branchList = response.data.data;
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
                console.log("store/updatebranch:"+ this.branchList )
            }
        }
    }
})