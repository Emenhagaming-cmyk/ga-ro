<template>
  <div class="filter-chips">
    <button
      v-for="chip in chips"
      :key="chip"
      class="chip"
      :class="{ active: modelValue === chip }"
      @click="$emit('update:modelValue', modelValue === chip ? '' : chip)"
    >
      {{ chip }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  chips: { type: Array, required: true },
  modelValue: { type: String, default: '' }
})
defineEmits(['update:modelValue'])
</script>

<style scoped>
.filter-chips {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  overflow-x: auto;
  padding-bottom: 4px;
  scrollbar-width: thin;
  scrollbar-color: var(--border) transparent;
}
.chip {
  padding: 8px 18px;
  border-radius: var(--radius-pill, 999px);
  border: 1.5px solid var(--primary, #3a6450);
  background: transparent;
  color: var(--primary, #3a6450);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  font-family: inherit;
}
.chip:hover {
  background: rgba(58, 100, 80, 0.06);
}
.chip.active {
  background: var(--primary, #3a6450);
  color: #fff;
  border-color: var(--primary, #3a6450);
}
</style>
