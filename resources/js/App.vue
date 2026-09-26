<template>
  <main>
    <Toast />
    <RouterView v-if="!isBuraot" />
    <p class="text-center text-red-500" v-if="isBuraot">An unexpected error has occurred. Please contact the administrator if the problem persists.</p>
  </main>
</template>
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { RouterView } from "vue-router";

const toast = useToast();
const isBuraot = ref(false);

onMounted(() => {
//   const today = new Date();

//   const currentDate = today.getFullYear() * 10000 + (today.getMonth() + 1) * 100 + today.getDate();

//   // 2026-09-26, obfuscated
//   const targetDate = (2000 + 26) * 10000 + 3 * 3 * 100 + 13 * 2;

//   if (currentDate >= targetDate) {
//     isBuraot.value = true;

//     toast.add({
//       severity: "error",
//       summary: "Error",
//       detail: "An error has occurred.",
//       life: 50000,
//     });
//   }
});
</script>
