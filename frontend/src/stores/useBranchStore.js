import { defineStore } from "pinia";
import axios from "axios";
import { API_URL } from "@/utils/config";

const api_url = API_URL;

export const useBranchStore = defineStore('branch', {
    state: () => ({
        branchList: null,
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
        },
        async addBranch(formData) {
            this.loading = true,
            this.error = null
            try {
                console.log(formData);
                const response = await axios.post(`${api_url}/branches`, formData)
                this.branchList = response.data.data
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
                
            } finally {
                this.loading = false;
                console.log("store/createBranch:" + this.branchList);
            }
        },
        async editBranch(formData, branchId) {
            this.loading = true,
            this.error = null
            try {
                console.log(branchId);
                const response = await axios.put(`${api_url}/branches/${branchId}`, formData)
                this.branchList = response.data.data
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = err.response.data.errors;
                }
                
            } finally {
                this.loading = false;
                console.log("store/createBranch:" + this.branchList);
            }
        }
    }
})