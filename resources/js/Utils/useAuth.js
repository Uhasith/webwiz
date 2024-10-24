import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

export default function useAuth() {
    const page = usePage();

    // Access the roles and permissions from the page props
    const roles = computed(() => page.props.roles || []);
    const permissions = computed(() => page.props.permissions || []);

    // Method to check if user has a specific role
    function hasRole(role) {
        return roles.value.includes(role);
    }

    // Method to check if user has a specific permission
    function hasPermission(permission) {
        return permissions.value.includes(permission);
    }

    // Expose the methods and properties
    return {
        roles,
        permissions,
        hasRole,
        hasPermission,
    };
}
