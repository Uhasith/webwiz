<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AddUserModel from "@/Components/Custom/Models/AddUserModel.vue";
import EditUserModel from "@/Components/Custom/Models/EditUserModel.vue";
import AddRoleModel from "@/Components/Custom/Models/AddRoleModel.vue";
import FiltersModel from "@/Components/Custom/Models/FiltersModel.vue";
import AdminUserMgmentDropdown from "@/Components/Custom/Dropdowns/AdminUserMgmentDropdown.vue";
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import { onMounted, ref, watch, nextTick, createApp, computed } from "vue";
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/Components/ui/accordion';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Switch } from '@/Components/ui/switch';
import { toast } from 'vue-sonner';
import { usePage } from '@inertiajs/vue3';
import { debounce } from "lodash";
import { useGlobalStore } from '@/store/global';
import useAuth from "@/Utils/useAuth";
import axios from 'axios';

const page = usePage();
const props = defineProps({ rolesWithPermissions: Array, groupedPermissions: Array, roles: Array });
const { hasPermission } = useAuth();

const globalStore = useGlobalStore();

const roles = ref(props.roles);
const groupedPermissions = ref(props.groupedPermissions);
const rolesWithPermissions = ref(props.rolesWithPermissions);
const checkedStatus = ref({});
const hasUsers = ref(true);

const showAddUserModal = ref(false);
const showAddRoleModal = ref(false);
const showFiltersModal = ref(false);
const showConfirmDelete = ref(false);

const deleteRoleId = ref(null);
const successMessage = ref('');
const errorMessage = ref('');

const currentPage = ref(1);
const totalPages = ref(1);
const selectedRoles = ref([]);
const dataTable = ref(null);
const searchQuery = ref("");

const tabs = ref([{ name: "User Lists" }]);
const currentTab = ref("User Lists");


// Conditionally add "User Permissions" tab if user has 'View Permission'
if (hasPermission('View Permission')) {
    tabs.value.push({ name: "User Permissions" });
}

const filteredRoles = computed(() => {
    return rolesWithPermissions.value.filter(role => role.name !== 'superadmin');
});

onMounted(() => {
    initializeToggles();
    loadScript("https://code.jquery.com/jquery-3.6.0.min.js")
        .then(() => loadScript("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"))
        .then(() => initializeDataTable());
});

watch(rolesWithPermissions, (newValue, oldValue) => {
    initializeToggles();
});

watch(showConfirmDelete, (newValue, oldValue) => {
    if (newValue === false) {
        showConfirmDelete.value = false;
        deleteRoleId.value = null;
    }
});

// Reload the table when the refreshTable event is emitted
watch(() => globalStore.refreshTable, (newValue) => {
    reloadTable();
});

const initializeToggles = () => {
    rolesWithPermissions.value.forEach(role => {
        const permissionIds = role.permissions.map(p => p.id);

        checkedStatus.value[role.id] = {};
        Object.entries(props.groupedPermissions).forEach(([category, permissions]) => {
            checkedStatus.value[role.id][category] = {};
            permissions.forEach(permission => {
                checkedStatus.value[role.id][category][permission.id] = permissionIds.includes(permission.id);
            });
        });
    });
};

const togglePermission = (roleId, category, permissionId) => {
    const newValue = checkedStatus.value[roleId][category][permissionId];
    updatePermissionInBackend(roleId, permissionId, newValue);
};

const deleteRoleConfirm = (roleId, name) => {
    if (name === 'superadmin') {
        toast.error('Super admin role cannot be deleted', {
            description: 'Super admin role cannot be deleted.',
        });
        return;
    }

    showConfirmDelete.value = true;
    deleteRoleId.value = roleId;
};

const confirmRoleDelete = () => {
    deleteRoleInBackend(deleteRoleId.value);
};

const deleteRoleInBackend = async (roleId) => {
    await axios.delete(route('role.destroy', roleId)).then((res) => {
        if (res.data.status === 'success') {
            rolesWithPermissions.value = res.data.rolesWithPermissions;
            roles.value = res.data.roles;
            showConfirmDelete.value = false;
            toast.success('Role Deleted', {
                description: 'Role deleted successfully.',
            });
        }
    }).catch((error) => {
        console.error("Failed to delete role:", error);
        if (error.response && error.response.data && error.response.data?.error === 'Role has associated users') {
            toast.error('This role cannot be deleted', {
                description: 'There are users assigned to it. Please unassign all users from this role before proceeding with deletion',
            });
            return;
        }
        toast.error('Failed to delete role', {
            description: 'Please contact support if this error persists.',
        });
    });
};

const updatePermissionInBackend = async (roleId, permissionId, isGranted) => {

    await axios.post(route('sync.permission.to.role', roleId), {
        permission_id: permissionId,
        granted: isGranted,
    }).then((res) => {
        if (res.data.status === 'success') {
            toast.success('Permission Updated', {
                description: 'Role permissions updated successfully.',
            });
        }
    }).catch((error) => {
        console.error("Failed to update permission:", error);
        toast.error('Failed to update permission', {
            description: 'Please contact support if this error persists.',
        });
    });

};

// Format category names
const formatCategory = (category) => {
    return category
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const loadScript = (src) => {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) {
            resolve();
            return;
        }
        const script = document.createElement("script");
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

const initializeDataTable = () => {
    dataTable.value = $("#users-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/admin/user-details",
            data: function (d) {
                d.roles = selectedRoles.value;
                d.search = searchQuery.value;
            },

           
            
        },
        columns: [
            { data: "name", name: "name" },
            { data: "roles", name: "roles" },
            { data: "email", name: "email" },
            { data: "contact", name: "contact" },
            {
                data: "status", name: "status", render: function (data) {
                    return data === 'PENDING' ? '<span style="color: orange;">Pending</span>'
                        : data === 'ACTIVE' ? '<span style="color: #019301;">Active</span>' : '<span style="color: red;">Inactive</span>';
                }
            },
            // { data: null, render: function() { return "Colombo"; }},
            {
                data: "last_login_at", name: "last_login_at", render: function (data) {
                    return data ? data : '<span style="color: red;">No Logged Records</span>';
                }
            },
            {
                data: null, orderable: false, searchable: false, render: function (data, type, row) {
                    return `<div id="dropdown-container-${row.id}"></div>`;
                }
            },
        ],
        language: {
            paginate: {
                previous: "Previous",
                next: "Next",
            },
            info: "Page _PAGE_ of _PAGES_",
            emptyTable: "No data available in table"
        
        },
        dom: "rt",
        pagingType: "simple",
        pageLength: 10,
        lengthChange: false,
        searching: false,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        drawCallback: function (settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            hasUsers.value = api.rows().count() > 0;

            $(".dataTables_info").html(
                "Page " + (pageInfo.page + 1) + " of " + pageInfo.pages
            );
            updateCustomPagination(this.api());

            // Mount AdminUserMgmentDropdown components
            api.rows().every(function () {
                const rowData = this.data();
                const container = document.getElementById(`dropdown-container-${rowData.id}`);
                if (container) {
                    const app = createApp(AdminUserMgmentDropdown, {
                        itemId: rowData.id,
                        status: rowData.status,
                        data: rowData
                    });
                    app.mount(container);
                }
            });
          
        },
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
        initComplete: function () {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var min = $("#min").val();
                var max = $("#max").val();
                var age = parseFloat(data[3]) || 0;

                if (
                    (isNaN(min) && isNaN(max)) ||
                    (isNaN(min) && age <= max) ||
                    (min <= age && isNaN(max)) ||
                    (min <= age && age <= max)
                ) {
                    return true;
                }
                return false;
            });

            $("#min, #max").keyup(function () {
                dataTable.value.draw();
            });
        },
    });

    window.editUser = function (userId) {
        console.log("Edit user with ID:", userId);
    };
};

const reloadTable = () => {
    if (dataTable.value) {
        dataTable.value.ajax.reload();
    }
};

watch(selectedRoles, () => {
    reloadTable();
});

const handleSearch = debounce(() => {
    reloadTable();
}, 300);

watch(searchQuery, () => {
    handleSearch();
});

const handleApplyFilters = (filters) => {
    selectedRoles.value = filters.roles;
    reloadTable();
};

const removeRole = (roleToRemove) => {
    selectedRoles.value = selectedRoles.value.filter(
        (role) => role !== roleToRemove
    );
    reloadTable();
};

// Pagination
const updateCustomPagination = (api) => {
    const info = api.page.info();
    currentPage.value = info.page + 1;
    totalPages.value = info.pages;
};

const previousPage = () => {
    if (currentPage.value > 1) {
        dataTable.value.page("previous").draw("page");
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        dataTable.value.page("next").draw("page");
    }
};

watch(currentTab, (newTab, oldTab) => {
    if (newTab === "User Lists") {
        nextTick(() => {
            initializeDataTable();
        });
    }
});

const exportUsers = async () => {
    const params = {
        search: searchQuery.value,
        roles: selectedRoles.value,
    };

    axios.get('/admin/export-users', { params, responseType: 'blob' })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Users.xlsx');
            document.body.appendChild(link);
            link.click();
        })
        .catch(error => {
            console.error("Error exporting data:", error);
        });

};
</script>

<template>
    <AdminLayout>
        <AddUserModel v-model:showAddUserModal="showAddUserModal" v-model:roles="roles"
            @close="(message) => { showAddUserModal = false; successMessage = message; }" />
        <AddRoleModel v-model:showAddRoleModal="showAddRoleModal" v-model:groupedPermissions="groupedPermissions"
            v-model:rolesWithPermissions="rolesWithPermissions" v-model:roles="roles" />
        <EditUserModel @reloadTable="reloadTable" />
        <FiltersModel :show="showFiltersModal" @close="showFiltersModal = false" :availableRoles="roles"
            @applyFilters="handleApplyFilters" :initialSelectedRoles="selectedRoles" />

        <div v-if=successMessage class="fixed top-4 right-4">
            <SuccessAlert :value="successMessage" />
        </div>
        <div v-if=errorMessage class="fixed top-4 right-4">
            <ErrorAlert :value="errorMessage" />
        </div>
        <div class="p-4">
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-gray-600">
                A summary of the user management features and current user statistics.
            </p>
        </div>

        <div class="w-full">
            <div class="bg-white">
                <!-- Tab navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex">
                        <a v-for="tab in tabs" :key="tab.name" @click="currentTab = tab.name" :class="[
                            currentTab === tab.name
                                ? 'border-green-500 text-green-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-4 px-8 border-b-2 font-medium text-md cursor-pointer',
                        ]">
                            {{ tab.name }}
                        </a>
                    </nav>
                </div>

                <!-- Tab content -->
                <div class="p-6">
                    <div v-if="currentTab === 'User Lists'">
                        <div class="flex items-center justify-between bg-white">
                            <div class="flex items-center">
                                <div class="relative">
                                    <input v-model="searchQuery" type="text" placeholder="Search"
                                        class="text-green-700 border border-gray-300 placeholder-gray-300 rounded-full pl-10 pr-4 py-2 w-60 focus:outline-none focus:ring-2 focus:ring-green-500" />
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-700 w-5 h-5"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35m3.23-4.88a6.375 6.375 0 11-12.75 0 6.375 6.375 0 0112.75 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-shrink-0 items-center">
                                    <button @click="showFiltersModal = true"
                                        class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold">
                                        <img src="/images/filterlinesicon.svg" alt="Filter Icon" class="w-4 h-4 mr-2" />
                                        filters
                                    </button>
                                </div>
                                <div class="ml-4 flex items-center flex-wrap ">
                                    <button v-for="role in selectedRoles" :key="role"
                                        class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold mr-2 mb-2 mt-2">
                                        <span>{{ role }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" @click.stop="removeRole(role)">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <button 
                                    @click="exportUsers" :disabled="!hasUsers"
                                    class="flex items-center justify-center w-40 h-10 border border-gray-300 rounded-full text-green-700 font-semibold text-center truncate hover:bg-green-100 transition duration-200 ease-in-out">
                                    <img src="/images/exportdataicon.svg" alt="Export Icon" class="w-4 h-4 mr-2" />
                                    Export Data
                                </button>
                                <button v-if="hasPermission('Create Sub Admin')" @click="showAddUserModal = true"
                                    class="w-40 h-10 ml-4 bg-green1 text-white rounded-full text-center truncate">
                                    + Add New User
                                </button>
                            </div>

                        </div>

                        <!-- Table -->
                        <div class="container mx-auto my-4">
                            <table id="users-table" class="min-w-full">
                                <thead>
                                    <tr
                                        class="w-full text-left text-sm  text-gray-400 font-medium  tracking-wider">
                                        <th class="py-3 px-6 flex items-center">
                                            <img src="/images/checkboxicon.svg" alt="Icon" class="mr-2 w-4 h-4" />
                                            <span>Username</span>
                                        </th>
                                        <th class="py-3 px-6">Roles</th>
                                        <th class="py-3 px-6">Email</th>
                                        <th class="py-3 px-6">Contact Number</th>
                                        <!-- <th class="py-3 px-6">Location</th> -->
                                        <th class="py-3 px-6">Status</th>
                                        <th class="py-3 px-6">Last Login</th>
                                        <th class="py-3 px-6"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>


                        <!-- Pagination -->
                        <div class="flex items-center justify-between w-full mt-4">
                            <button @click="previousPage" :disabled="currentPage === 1"
                                class="px-4 py-1 rounded-lg border border-gray-300"
                                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                Previous
                            </button>

                            <span class="text-sm">
                                <span class="text-sm font-medium">
                                    <span class="text-green1">Page {{ currentPage }}</span> of
                                    {{ totalPages }}
                                </span>
                            </span>

                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                class="px-3 py-1 rounded-lg border border-gray-300" :class="{
                                    'opacity-50 cursor-not-allowed': currentPage === totalPages,
                                }">
                                Next
                            </button>
                        </div>
                    </div>

                    <div v-else-if="currentTab === 'User Permissions'">
                        <!-- User Permissions content -->
                        <div class="flex justify-end">
                            <button v-if="hasPermission('Create Role')" @click="showAddRoleModal = true"
                                class="px-4 py-2 bg-green1 text-white rounded-full">
                                + Add New Role
                            </button>
                        </div>

                        <div v-if="roles.length === 0">
                            <p>No roles available.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                            <div v-for="(role, roleIndex) in filteredRoles" :key="role.id" class="col-span-1">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-md capitalize font-semibold">{{ role.name }}</h3>
                                        <DropdownMenu v-if="hasPermission('Update Role')">
                                            <DropdownMenuTrigger>
                                                <div class="text-md cursor-pointer font-bold p-4">&#8942;</div>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent>
                                                <DropdownMenuItem @click="deleteRoleConfirm(role.id, role.name)"
                                                    class="text-red-500">Delete</DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                    <!-- Group permissions by category with fixed order -->
                                    <Accordion v-for="(permission, category) in groupedPermissions" :key="permission.id"
                                        type="single" collapsible class="border border-gray-300 rounded-md mb-3 px-3">
                                        <AccordionItem value="item-1">
                                            <AccordionTrigger>{{ formatCategory(category) }}</AccordionTrigger>
                                            <AccordionContent>
                                                <div v-for="item in permission" :key="item.id"
                                                    class="flex items-center justify-between w-auto px-4 py-2 ml-4">
                                                    <p class="text-sm">{{ item.name }}</p>

                                                    <Switch v-model:checked="checkedStatus[role.id][category][item.id]"
                                                        :disabled="!hasPermission('Update Permission')"
                                                        @update:checked="togglePermission(role.id, category, item.id)" />
                                                </div>
                                            </AccordionContent>
                                        </AccordionItem>
                                    </Accordion>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Dialog v-model:open="showConfirmDelete">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Are you sure?</DialogTitle>
                    <DialogDescription> You will not be able to undo this action. </DialogDescription>
                </DialogHeader>
                <div class="flex items-center justify-end gap-4">
                    <Button variant="secondary" @click="showConfirmDelete = false;">
                        Cancel
                    </Button>
                    <Button @click="confirmRoleDelete()">
                        Yes
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>

<style scoped>
@import "https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css";

table.dataTable thead th,
table.dataTable thead td {
    border-bottom: none;
}

table.dataTable.no-footer {
    border-bottom: none;
}
</style>
<style>
.custpadding {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    padding-left: 1.1rem !important;
    padding-right: 1.5rem !important;
}
</style>
