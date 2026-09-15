<template>
  <div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-linear-to-r from-slate-50 to-white">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FiLock class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Security</h3>
          <p class="text-xs text-slate-400">Change your account password</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="p-6 flex flex-col gap-5">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Current Password <span class="text-red-400">*</span></label>
          <Password v-model="form.current_password" fluid :feedback="false" toggleMask required placeholder="Enter current password" class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">New Password <span class="text-red-400">*</span></label>
          <Password v-model="form.password" fluid toggleMask required placeholder="At least 7 characters" class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Confirm New Password <span class="text-red-400">*</span></label>
          <Password v-model="form.password_confirmation" fluid :feedback="false" toggleMask required placeholder="Re-enter new password" class="text-sm" />
        </div>
        <div class="flex justify-end pt-1">
          <Button type="submit" label="Update Password" :loading="submitting" class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import Password from "primevue/password";
import { FiLock } from "vue-icons-plus/fi";
import { useAuthStore } from "@/store/patientchart/AuthStore";
import { useAppToast } from "@/composables/toast";

const auth = useAuthStore();
const toast = useAppToast();
const submitting = ref(false);

const defaultForm = () => ({ current_password: "", password: "", password_confirmation: "" });
const form = reactive(defaultForm());

const submit = async () => {
  if (form.password !== form.password_confirmation) {
    toast.error("New password and confirmation do not match");
    return;
  }
  submitting.value = true;
  try {
    await auth.changePassword({ ...form });
    toast.success("Password updated successfully");
    Object.assign(form, defaultForm());
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update password");
  } finally {
    submitting.value = false;
  }
};
</script>
