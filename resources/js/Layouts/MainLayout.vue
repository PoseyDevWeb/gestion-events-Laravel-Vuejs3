<template>
<div class="bg-gray-800  p-4 mb-10">
  <nav class="flex items-center justify-between">
    <div class="text-white font-bold text-xl">
        <Link href="/">Mon Site</Link>
    </div>
    <div class="space-x-4">
        <Link :href="route('events.list')" class="text-white hover:text-blue-500" :class="{'inline-block border border-blue-500 rounded py-1 px-3 bg-blue-500 text-white hover:text-white' : $page.component =='Home'}">Accueil</Link>
        <Link href="/About" class="text-white hover:text-blue-500"  :class="{'inline-block border border-blue-500 rounded py-1 px-3 bg-blue-500 text-white hover:text-white' : $page.component =='About'}">A propos</Link>
        <Link href="/Contact" class="text-white hover:text-blue-500" :class="{'inline-block border border-blue-500 rounded py-1 px-3 bg-blue-500 text-white hover:text-white' : $page.component =='Contact'}">Contact</Link>
    </div>
  </nav>
</div>


<div class="content-wrappers">
<slot />
</div>


</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>


