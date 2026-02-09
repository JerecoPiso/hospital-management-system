<template>
    <ConfirmDialog></ConfirmDialog>

    <div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
            <div class="flex items-center justify-between px-4 md:px-8 py-4">
                <!-- Logo & Title -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div
                            class="w-10 h-10 bg-linear-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">HealthBase</h1>
                            <p class="text-xs text-slate-500">Admin Dashboard</p>
                        </div>
                    </div>
                </div>

                <!-- Right Section with User Profile Popover -->
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative p-2 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Profile Popover -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen"
                            class="flex items-center gap-2 p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" alt="User"
                                class="w-8 h-8 rounded-full border-2 border-emerald-500" />
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-semibold text-slate-900">Dr. Sarah</p>
                                <p class="text-xs text-slate-500">Administrator</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-600 transition-transform"
                                :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>

                        <!-- Popover Menu -->
                        <transition enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                            <div v-if="profileOpen"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden z-50">
                                <!-- Profile Header -->
                                <div class="bg-linear-to-r from-emerald-500 to-teal-600 px-6 py-4">
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" alt="User"
                                        class="w-12 h-12 rounded-full border-3 border-white mb-3" />
                                    <p class="text-white font-semibold">Dr. Sarah Johnson</p>
                                    <p class="text-emerald-100 text-sm">Administrator</p>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-2">
                                    <button @click="handleMenuClick('profile')"
                                        class="w-full px-6 py-3 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-medium">My Profile</span>
                                    </button>

                                    <button @click="handleMenuClick('settings')"
                                        class="w-full px-6 py-3 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="font-medium">Settings</span>
                                    </button>

                                    <button @click="handleMenuClick('security')"
                                        class="w-full px-6 py-3 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="font-medium">Security</span>
                                    </button>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-slate-200"></div>

                                <!-- Logout -->
                                <button @click="handleMenuClick('logout')"
                                    class="w-full px-6 py-3 text-left text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </transition>

                        <!-- Backdrop -->
                        <div v-if="profileOpen" @click="profileOpen = false" class="fixed inset-0 z-40"></div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside
                class="fixed min-h-[90vh] md:static inset-y-0 left-0 z-30 bg-white border-r border-slate-200 transform transition-all duration-300 mt-16 md:mt-0"
                :class="{ 'w-64': sidebarExpanded, 'md:w-20': !sidebarExpanded }">
                <!-- '-translate-x-full': !sidebarOpen,  -->
                <!-- Added sidebar header with toggle button -->
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 v-if="sidebarExpanded" class="text-sm font-bold text-slate-900 uppercase tracking-wider">Menu
                    </h2>
                    <button @click="sidebarExpanded = !sidebarExpanded"
                        class="p-2 hover:bg-slate-100 rounded-lg transition-colors hidden md:flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-700 transition-transform"
                            :class="{ 'rotate-180': !sidebarExpanded }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
                <nav class="p-4 space-y-2">
                    <router-link v-for="item in navItems" :key="item.name" :to="{ name: item.name }"
                        @click="activeNav = item.name; sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all" :class="activeNav === item.name
                            ? 'bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md'
                            : 'text-slate-700 hover:bg-slate-100'
                            " :title="!sidebarExpanded ? item.icon : ''">
                        <component :is="item.icon" class="text-xl text-gray-600"
                            :class="activeNav === item.name ? 'text-white' : 'text-gray-600'" />
                        <span v-if="sidebarExpanded" class="font-medium">{{ item.name }}</span>
                    </router-link>

                </nav>
                <!-- Added sidebar footer with additional info -->
                <div class="fixed bottom-0 left-0 right-0 p-4 border-t border-slate-200 bg-slate-50">
                    <p class="text-xs text-slate-600 text-center">HealthBase v1.0</p>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-8">
                <RouterView />

            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { useRouter } from 'vue-router';
import { MdDashboard } from 'vue-icons-plus/md';
import { FiUsers } from 'vue-icons-plus/fi';
import { GiMedicines } from 'vue-icons-plus/gi';
import { FaUsers } from 'vue-icons-plus/fa';
const confirm = useConfirm();
const toast = useToast();
const router = useRouter();
const sidebarOpen = ref(true)
const profileOpen = ref(false)
const activeNav = ref('dashboard')
const sidebarExpanded = ref(true)
const navItems = [

    {
        "name": "Dashboard",
        "icon": MdDashboard
    },
    {
        "name": "Patients",
        "icon": FiUsers
    },
    {
        "name": "Medicines",
        "icon": GiMedicines
    },
    {
        "name": "Users",
        "icon": FaUsers
    }
]
const handleMenuClick = (action) => {
    profileOpen.value = false
    if (action === 'logout') {
        confirm.require({
            message: 'Are you sure you want to proceed?',
            header: 'Confirmation',
            icon: 'pi pi-exclamation-triangle',
            rejectProps: {
                label: 'Cancel',
                severity: 'secondary',
                outlined: true
            },
            acceptProps: {
                label: 'Save'
            },
            accept: () => {
                toast.add({ severity: 'info', summary: 'Confirmed', detail: 'You have accepted', life: 3000 });
                router.push({ name: "Login" })
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
            }
        });
    }
}
</script>

<style scoped>
* {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
