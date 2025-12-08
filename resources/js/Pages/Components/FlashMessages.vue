<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

let flashMessage = ref("");
let show = ref(false);

function flash(message) {
    flashMessage.value = message;
    show.value = true;

    setTimeout(() => (show.value = false), 4000);
}

watch(() => usePage().props.flash.success, flash);
</script>
<template>
    <Transition
        enter-active-class="transition duration-300 transform ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 transform ease-out"
        leave-from-class="opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div
            v-if="flashMessage && show"
            class="bg-pixl fixed right-4 bottom-4 rounded-md px-3 py-2 text-white"
            v-text="flashMessage"
        />
    </Transition>
</template>
