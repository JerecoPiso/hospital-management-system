<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="max-w-md w-full bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
      <div class="w-16 h-16 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
        <FiLock class="text-red-500" size="28" />
      </div>
      <h1 class="text-lg font-bold text-slate-900">Access Restricted</h1>
      <p class="text-sm text-slate-500 mt-2">You don't have permission to view this page. Contact your administrator if you believe this is a mistake.</p>
      <button
        type="button"
        @click="router.push({ name: 'Dashboard' })"
        class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
      >
        Back to Dashboard
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { FiLock } from "vue-icons-plus/fi";

const router = useRouter();
</script>
