<template>
    <div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-50 mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Hospital Portal</h1>
                    <p class="text-slate-600 text-sm">Secure access for healthcare professionals</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <input id="email" v-model="loginForm.email" type="email" placeholder="name@hospital.com"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
                            <svg class="absolute right-3 top-3.5 w-5 h-5 text-slate-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" v-model="loginForm.password" :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••" required
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-3.5 text-slate-400 hover:text-slate-600 transition">
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-4.753 4.753m4.754-4.753L3.596 3.596m16.807 16.807L9.404 9.404m0 0L6.643 6.643" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="rememberMe" type="checkbox"
                                class="w-4 h-4 rounded border-slate-200 text-blue-600 focus:ring-blue-500" />
                            <span class="text-sm text-slate-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" :disabled="isLoading"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ isLoading ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>



                <!-- Footer Links -->
                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-600">
                        Need help?
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-700 transition">Contact Support</a>
                    </p>
                </div>

                <!-- Security Notice -->
                <div class="mt-4 p-3 bg-blue-50 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-blue-700">
                        This system is for authorized personnel only. All access is monitored and logged.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-slate-500">
                <p>© 2024 Hospital Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
interface LoginForm {
    email: string,
    password: string
}
const loginForm = ref<LoginForm>({
    email: "",
    password: ""
})
const router = useRouter()
const isLoading = ref<boolean>(false)
const rememberMe = ref<boolean>(false)
const showPassword = ref<boolean>(false)
const handleLogin = async () => {
    if (!loginForm.value.email || !loginForm.value.password) {
        alert('Please fill in all fields')
        return
    }
    isLoading.value = true
    setTimeout(() => {
        isLoading.value = false
        alert(`Welcome back! Login would process for ${loginForm.value.email}`)
        // Reset form
        loginForm.value.email = ''
        loginForm.value.password = ''
        router.push({ name: 'Authenticated' })
    }, 1500)
}
</script>

<style scoped>
input[type='checkbox'] {
    accent-color: rgb(37, 99, 235);
}
</style>
