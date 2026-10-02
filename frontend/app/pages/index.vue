<script setup lang="ts">
// Biar kalo ada tamu masuk ke domain utama, langsung diarahkan ke katalog kamar
await navigateTo({ path: '/rooms' })
</script>