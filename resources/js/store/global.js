import { defineStore } from "pinia";

export const useGlobalStore = defineStore("GlobalStore", {
    state: () => ({
        refreshTable: 0, // Initial state for table refresh counter
    }),

    actions: {
        refreshTableAction() {
            this.refreshTable++; // Increment the refreshTable counter
        },
    },
});
