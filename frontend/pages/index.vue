<script setup>
const config = useRuntimeConfig();
const { data } = await useFetch(`${config.public.apiBase}/test`);
</script>

<template>
  <div>
    <h1>{{ data }}</h1>
  </div>
</template>
