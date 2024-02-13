<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3'

const flashMessage = ref({message: usePage().props.value.flash.error})

onMounted(() => {
    setTimeout(() => {
        usePage().props.value.flash.error = ''
    }, 3000)
});
</script>
    
<template>
    <div class="fixed top-20 md:right-12 right-4 bg-white shadow shadow-zinc-200 drop-shadow-md text-[13px] p-4 rounded-xl flex items-center space-x-4">
        <div class="p-1 rounded-full bg-red-200 text-red-500 bg-opacity-50">
            <svg class="w-5 h-5 flex-none" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 1a9 9 0 019 9v0a9 9 0 01-9 9v0a9 9 0 01-9-9v0A9 9 0 0110 1zm0 3a1 1 0 00-.993.883L9 5v5a1 1 0 001 1h1a1 1 0 001-1V5l-.007-.117A1 1 0 0010 4zm0 7a1 1 0 00-1 1v1a1 1 0 102 0v-1a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        </div>
        <div>
            <span class="font-medium block">Error</span>
            <span class="text-zinc-500">{{ flashMessage.message }}</span>
        </div>
    </div>
</template>