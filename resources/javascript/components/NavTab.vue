<template>
    <li v-if="active" class="-mb-px mr-1">
        <a class="bg-white inline-block py-2 px-4 font-semibold text-blue-700 border-l border-t border-r rounded-t">
            {{ text }}
        </a>
    </li>
    <li v-else class="mr-1 cursor-pointer" @click="$emit('activate')">
        <a class="bg-white inline-block py-2 px-4 font-semibold text-blue-500 hover:text-blue-800">{{ text }}</a>
    </li>
</template>

<script>
    export default {
        name: "NavTab",
        props: ['text', 'active'],
        mounted() {
            console.log('fuck');
        },
        methods: {
            activate() {
                this.emit('activate');
            }
        }
    }
</script>

<style scoped>

</style>
