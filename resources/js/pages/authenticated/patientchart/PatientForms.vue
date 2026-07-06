<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-linear-to-r from-slate-50 to-white">
            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                <BsJournalMedical class="text-white" size="18" />
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Patient Forms</h3>
                <p class="text-xs text-slate-400">Select a form to view or record patient assessment data</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <router-link
                :to="{ name: 'HistoryAndPhysicalExaminationFormOne' }"
                class="group flex items-center gap-4 p-5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all duration-200 bg-white"
            >
                <div class="w-12 h-12 shrink-0 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                    <FaBookMedical class="text-white" size="20" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700">Physical Examination Form 1</p>
                    <p class="text-xs text-slate-400 mt-0.5">History taking &amp; review of systems</p>
                </div>
                <BiChevronRight class="text-slate-300 group-hover:text-emerald-500 transition-colors" size="20" />
            </router-link>

            <router-link
                :to="{ name: 'HistoryAndPhysicalExaminationFormTwo' }"
                class="group flex items-center gap-4 p-5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all duration-200 bg-white"
            >
                <div class="w-12 h-12 shrink-0 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                    <FaBookMedical class="text-white" size="20" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700">Physical Examination Form 2</p>
                    <p class="text-xs text-slate-400 mt-0.5">Physical examination findings &amp; assessment</p>
                </div>
                <BiChevronRight class="text-slate-300 group-hover:text-emerald-500 transition-colors" size="20" />
            </router-link>
        </div>
    </div>
</template>

<script setup lang="ts">
import { BsJournalMedical } from 'vue-icons-plus/bs';
import { FaBookMedical } from 'vue-icons-plus/fa';
import { BiChevronRight } from 'vue-icons-plus/bi';
</script>
