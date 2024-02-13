<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import pickBy from 'lodash/pickBy'
import { Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
    products: Array
})

const filters = ref({
    sortBy: ''
});

watch(filters.value, value => {
    Inertia.get(route("products.index"), pickBy(value), {
        preserveState: true,
        replace: true,
    });
});
</script>
    
<template>
    <DashboardLayout>
        <Head title="My Products" />

        <h2 class="font-medium uppercase text-2xl mb-8">My Products</h2>
        
        <header class="flex items-center justify-between mb-4">
            <Link
                :href="route('products.create')"
                class="text-white bg-c-green-300 text-xxs py-2 w-24 rounded transition flex justify-center"
            >
                <span class="uppercase">Add Product</span>
            </Link>
            
            <select v-model="filters.sortBy" class="border-none shadow shadow-zinc-200 rounded text-xs focus:border-transparent focus:ring focus:ring-zinc-300 focus:ring-opacity-80">
                <option value="">Sort by</option>
                <option value="price_desc">Price desc</option>
                <option value="price_asc">Price asc</option>
                <option value="best_selling">Best selling</option>
            </select>
        </header>

        <table class="w-full">
            <tr class="w-full bg-white border-b">
                <th class="text-left font-medium text-xs px-4 py-3 w-16">No</th>
                <th class="text-left font-medium text-xs px-4 py-3 ">Product Name</th>
                <th class="text-right font-medium text-xs px-4 py-3 ">Price</th>
                <th class="text-center font-medium text-xs px-4 py-3 ">Quantity</th>
                <th></th>
            </tr>
            <tr
                v-for="(product, index) in products"
                :key="product.id"
                class="even:bg-zinc-100 odd:bg-white text-sm border-b">
                    <td class="px-4 py-3 text-left ">
                        <span class="text-xs font-medium">#{{ index + 1 }}</span>
                    </td>
                    <td class="px-4 py-3 text-left ">
                        <Link :href="route('product.show', product.slug)">{{ product.name }}</Link>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span>{{ product.price }}</span>
                    </td>
                    <td class="px-4 py-3 text-center ">
                        <span>{{ product.available_quantity }}</span>
                    </td>
            </tr>
        </table>
    </DashboardLayout>
</template>