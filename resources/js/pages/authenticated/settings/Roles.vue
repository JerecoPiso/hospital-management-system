<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- ROLE MODAL -->
    <Dialog v-model:visible="roleModal" modal :style="{ width: '32vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiShield class="text-white" size="17" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? "Edit Role" : "New Role" }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? "Update the role details" : "Fill in the role details below" }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Name <span class="text-red-400">*</span></label>
          <InputText v-model="info.name" placeholder="e.g. Nurse" required fluid class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Description</label>
          <Textarea v-model="info.description" rows="3" placeholder="Optional description" fluid class="text-sm" />
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="roleModal = false" />
          <Button type="submit" :label="isUpdate ? 'Update Role' : 'Save Role'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- PERMISSIONS MODAL -->
    <Dialog v-model:visible="permissionsModal" modal :style="{ width: '64vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiShield class="text-white" size="17" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Permissions — {{ activeRole?.name }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Toggle which CRUD operations this role can perform per module</p>
          </div>
        </div>
      </template>
      <div class="flex flex-col gap-4 pt-2">
        <div class="max-h-[55vh] overflow-y-auto border border-slate-200 rounded-lg">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 sticky top-0">
              <tr>
                <th class="text-left px-4 py-2.5 font-semibold text-slate-600">Module</th>
                <th v-for="ability in abilities" :key="ability.key" class="px-3 py-2.5 font-semibold text-slate-600 text-center">
                  <div class="flex flex-col items-center gap-1">
                    <span>{{ ability.label }}</span>
                    <Checkbox :modelValue="isColumnChecked(ability.key)" binary @update:modelValue="(val: boolean) => toggleColumn(ability.key, val)" />
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in accesses" :key="row.module" class="border-t border-slate-100 hover:bg-slate-50">
                <td class="px-4 py-2 text-slate-700">{{ row.label }}</td>
                <td v-for="ability in abilities" :key="ability.key" class="px-3 py-2 text-center">
                  <Checkbox v-model="(row as any)[ability.field]" binary />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="permissionsModal = false" />
          <Button type="button" label="Save Permissions" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" @click="saveAccess" />
        </div>
      </div>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FiShield class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Roles & Permissions</h3>
          <p class="text-xs text-slate-400">Control which CRUD actions each role can perform</p>
        </div>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <div class="relative flex-1 sm:w-64">
          <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
          <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
        </div>
        <button
          v-if="can('roles', 'create')"
          type="button"
          @click="roleModal = true"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0"
        >
          <BsPlusCircle size="16" />
          Add Role
        </button>
      </div>
    </div>

    <DataTable
      :value="roles"
      lazy
      paginator
      :rows="rows"
      :first="first"
      :totalRecords="total"
      :loading="loading"
      @page="onPage"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 50rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <FiShield size="36" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No roles found</p>
        </div>
      </template>
      <Column field="name" header="Name">
        <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.name }}</span></template>
      </Column>
      <Column header="Description">
        <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.description || "—" }}</span></template>
      </Column>
      <Column header="Users" class="w-24">
        <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.users_count ?? 0 }}</span></template>
      </Column>
      <Column header="Actions" class="w-32">
        <template #body="{ data }">
          <div class="flex items-center gap-1">
            <button
              v-if="can('roles', 'update')"
              type="button"
              title="Manage permissions"
              @click="openPermissions(data)"
              class="p-1.5 rounded-md text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 cursor-pointer"
            >
              <FiShield size="18" />
            </button>
            <button v-if="can('roles', 'update')" type="button" title="Edit" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
              <BiEdit size="18" />
            </button>
            <button v-if="can('roles', 'delete')" type="button" title="Delete" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
              <BiTrash size="18" />
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import Checkbox from "primevue/checkbox";
import { BsPlusCircle } from "vue-icons-plus/bs";
import { BiEdit, BiTrash } from "vue-icons-plus/bi";
import { FiSearch, FiShield } from "vue-icons-plus/fi";
import { useRoleStore } from "@/store/Role";
import { Role, RoleAccess } from "@/interface/Interfaces";
import { useApiTable } from "@/composables/apiTable";
import { useConfirmToast } from "@/composables/confirm";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const roleStore = useRoleStore();
const { can } = usePermission();

const roles = computed<Role[]>(() => roleStore.roles);
const roleModal = ref<boolean>(false);
const permissionsModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const activeRole = ref<Role | null>(null);
const defaultInfo = (): Role => ({ pid: "", name: "", description: "" });
const info = reactive<Role>(defaultInfo());

const abilities = [
  { key: "view", label: "View", field: "can_view" },
  { key: "create", label: "Create", field: "can_create" },
  { key: "update", label: "Update", field: "can_update" },
  { key: "delete", label: "Delete", field: "can_delete" },
] as const;

const accesses = ref<RoleAccess[]>([]);

watch(roleModal, (open) => {
  if (!open) {
    Object.assign(info, defaultInfo());
    isUpdate.value = false;
  }
});

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
  async (params) => {
    try {
      await roleStore.read(params);
    } catch (err: any) {
      toast.error(err.response?.data?.message || "Failed to retrieve roles");
    }
  },
  () => roleStore.meta
);

const create = async () => {
  try {
    await roleStore.create(info);
    toast.success("Role created successfully");
    roleModal.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to create role");
  }
};

const edit = async (pid: string) => {
  try {
    await roleStore.view(pid);
    Object.assign(info, { pid: roleStore.role.pid, name: roleStore.role.name, description: roleStore.role.description });
    isUpdate.value = true;
    roleModal.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve role");
  }
};

const update = async () => {
  try {
    await roleStore.update(info);
    toast.success("Role updated successfully");
    roleModal.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update role");
  }
};

const archive = (pid: string) => {
  showConfirm({
    message: "Are you sure you want to delete this role?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await roleStore.archive(pid);
        toast.success("Role deleted successfully");
        await reload();
      } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to delete role");
      }
    },
  });
};

const openPermissions = async (data: Role) => {
  try {
    await roleStore.view(data.pid!);
    activeRole.value = roleStore.role;
    accesses.value = (roleStore.role.accesses ?? []).map((a) => ({ ...a }));
    permissionsModal.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve role permissions");
  }
};

const isColumnChecked = (ability: string) => {
  const field = `can_${ability}` as keyof RoleAccess;
  return accesses.value.length > 0 && accesses.value.every((row) => row[field]);
};

const toggleColumn = (ability: string, value: boolean) => {
  const field = `can_${ability}` as keyof RoleAccess;
  accesses.value.forEach((row) => ((row as any)[field] = value));
};

const saveAccess = async () => {
  try {
    await roleStore.updateAccess(activeRole.value!.pid!, accesses.value);
    toast.success("Permissions updated successfully");
    permissionsModal.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update permissions");
  }
};
</script>
