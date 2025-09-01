<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import BaseCard from '@/components/BaseCard.vue';
    import SubTitle from '@/components/SubTitle.vue';
    import { useRouter } from 'vue-router';
    import BaseInput from '@/components/BaseInput.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import { onMounted, ref, computed } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import BaseSwitch from '@/components/BaseSwitch.vue';
    import BaseLabel from '@/components/BaseLabel.vue';
    import { useUserRoleStore } from '@/stores/useUserRoleStore';
    import { usePermissionStore } from '@/stores/usePermissionStore';
    import BaseCheckbox from '@/components/BaseCheckbox.vue';
    
    const router = useRouter();
    const toast = useToast();
    const useRole = useUserRoleStore();
    const usePermission = usePermissionStore();
    const formData = ref(
      {
        name: "",
        desc: "",
        status_id: "1",
        created_by: "1",
        updated_by: "",
        permissions: [],
      }
    )
    const roleStatus = ref(true);
    const permissionList = ref([]);
    const collapsed = ref({}); // which permission groups are collapsed
    const selectedPermissions = ref([]); // store selected permission IDs
    const userData = ref({});

    onMounted(async() => {
        await usePermission.fetchAllPermission();
        permissionList.value = usePermission.permissionList;
        userData.value = JSON.parse(localStorage.getItem('user'));
    });

    const groupedPermissions = computed(() => {
        const grouped = {};
        permissionList.value.forEach((item) => {
            if (!grouped[item.name]) {
                grouped[item.name] = [];
            }
            grouped[item.name].push(item);
        });
        return grouped;
    });

    function toggleCollapse(name) {
        collapsed.value[name] = !collapsed.value[name];
    }

    function togglePermission(id, checked) {
        if (checked) {
            if (!selectedPermissions.value.includes(id)) {
                selectedPermissions.value.push(id);
            }
        } else {
            selectedPermissions.value = selectedPermissions.value.filter(p => p !== id);
        }
    }

    // Check if all actions in a group are selected
    function isAllSelected(groupName) {
        const actions = groupedPermissions.value[groupName] || [];
        if (!actions.length) return false;
        return actions.every(a => selectedPermissions.value.includes(a.id));
    }

    // Toggle all actions in a group
    function toggleAllGroup(groupName, checked) {
        const actions = groupedPermissions.value[groupName] || [];
        if (!actions.length) return;
        if (checked) {
            // Add all if not already included
            actions.forEach(a => {
                if (!selectedPermissions.value.includes(a.id)) {
                    selectedPermissions.value.push(a.id);
                }
            });
            collapsed.value[groupName] = true;
        } else {
            // Remove all
            selectedPermissions.value = selectedPermissions.value.filter(id => !actions.some(a => a.id === id));
            collapsed.value[groupName] = false;
        }
    }

    // Check if all permissions across ALL groups are selected
    const isAllSelectedGlobal = computed(() => {
        const allIds = Object.values(groupedPermissions.value).flat().map(p => p.id);
        return allIds.every(id => selectedPermissions.value.includes(id));
    });

    // Toggle ALL permissions across ALL groups
    function toggleAllGlobal(checked) {
        const allIds = Object.values(groupedPermissions.value).flat().map(p => p.id);
        if (checked) {
            // Add all permissions
            selectedPermissions.value = Array.from(new Set([...selectedPermissions.value, ...allIds]));
            for (const groupName in groupedPermissions.value) {
                collapsed.value[groupName] = true;
            }
        } else {
            // Clear all
            selectedPermissions.value = [];
        }
    }

    function changeRoute(pathname) {
        router.push(pathname);
    }

    async function formSubmit() {
        formData.value = {
            ...formData.value,
            status_id: roleStatus.value? '1' : '2',
            created_by: userData.value.id,
            permissions: [...selectedPermissions.value]
        };
        await useRole.addRole(formData.value);
        if(useRole.error) {
            Object.values(useRole.error).forEach((err) => {
                err.forEach((msg) => {
                    toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
                })
            })
            return
        }
        if (useRole.roleList) {
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Role created successfully.', life: 3000 });

            router.push('/role');
        }
    }

</script>

<template>
    <div class="p-4">
        <PageTitle title="Create Role">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-chevron-left" label="Back" severity="secondary" @click="changeRoute('/role')"  />
                </div>
            </template>
        </PageTitle>
        <BaseCard class="mt-3">
            <template #cardElements>
                <SubTitle label="Basic Info" />
                <div class="flex gap-x-4 mt-6">
                    <BaseInput
                        size="sm"
                        v-model="formData.name"
                        label="Name"
                        placeholder="Role Name"
                        width="300px"
                        height="h-[35px]"
                    />
                    <div class="flex flex-col gap-y-1 w-[200px]">
                        <BaseLabel label="Status" />
                        <BaseSwitch v-model="roleStatus" />

                    </div>
                </div>
                <div class="flex gap-x-4 mt-4 pb-4 border-b border-b-gray-200">
                    <BaseTextarea
                        v-model="formData.desc"
                        label="Description"
                        placeholder=""
                        autoResize
                    />
                </div>
                <div class="mt-4 flex items-center gap-x-2">
                    <SubTitle label="Permission" />
                    <BaseCheckbox
                        :label="isAllSelectedGlobal ? 'Deselect All' : 'Select All'"
                        :model-value="isAllSelectedGlobal"
                        @update:modelValue="checked => toggleAllGlobal(checked)"
                    />
                </div>
                <div v-if="usePermission.loading" class="w-full rounded-md p-4">
                    <div class="flex animate-pulse space-x-4">
                        <div class="flex-1 space-y-6 py-1">
                            <div class="h-2 rounded bg-gray-200"></div>
                            <div class="space-y-3">
                                <div class="h-2 rounded bg-gray-200"></div>
                                <div class="h-2 rounded bg-gray-200"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="mt-4 space-y-2 w-[700px]">
                    <div
                        v-for="(actions, groupName) in groupedPermissions"
                        :key="groupName"
                        class="border-b border-b-gray-200 rounded p-2"
                    >
                        <!-- Header with collapse toggle -->
                        <div
                        class="flex justify-between items-center"
                        >
                            <div class="flex items-center gap-x-4">
                                <span class="font-semibold">{{ groupName }}</span>
                                <!-- Select All / Deselect All -->
                                <BaseCheckbox
                                    :label="isAllSelected(groupName) ? 'Deselect All' : 'Select All'"
                                    :model-value="isAllSelected(groupName)"
                                    @update:modelValue="checked => toggleAllGroup(groupName, checked)"
                                />
                            </div>
                            <i 
                                :class="collapsed[groupName] ? 'fa fa-chevron-down' : 'fa fa-chevron-right'"
                                class="cursor-pointer"
                                @click="toggleCollapse(groupName)"
                            >
                            </i>
                        </div>
                        <!-- Collapsible actions -->
                        <div v-if="collapsed[groupName]" class="flex mt-3 ml-4 gap-x-10">
                            <!-- Individual permissions -->
                            <BaseCheckbox
                                v-for="perm in actions"
                                :key="perm.id"
                                :label="perm.action"
                                :model-value="selectedPermissions.includes(perm.id)"
                                @update:modelValue="checked => togglePermission(perm.id, checked)"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <BaseButton label="Save" :isLoading="useRole.loading" :icon="useRole.loading? 'fa fa-spinner' : 'fa fa-floppy-disk'" severity="primary" @click="formSubmit" :disabled="useRole.loading"  />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
