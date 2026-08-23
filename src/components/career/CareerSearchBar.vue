<template>
  <div class="search-bar">
    <i class="fas fa-search search-icon"></i>
    <input
      type="text"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      placeholder="Cari perusahaan, posisi, atau jurusan..."
      class="search-input"
    />
    <button class="search-btn" @click="$emit('search')">
      <i class="fas fa-magnifying-glass"></i> Cari
    </button>
  </div>
</template>

<script setup>
defineProps({ modelValue: { type: String, default: '' } })
defineEmits(['update:modelValue', 'search'])
</script>

<style scoped>
.search-bar {
  display: flex;
  align-items: center;
  gap: 0;
  background: #fff;
  border-radius: var(--radius-lg, 20px);
  border: 1px solid var(--border, #dfe4dd);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  margin-bottom: 20px;
  position: relative;
}
.search-icon {
  position: absolute;
  left: 20px;
  color: var(--text-secondary, #647067);
  font-size: 14px;
}
.search-input {
  flex: 1;
  padding: 16px 20px 16px 48px;
  border: none;
  background: transparent;
  font-size: 14px;
  color: var(--text, #1c2a23);
  outline: none;
  font-family: inherit;
}
.search-input::placeholder {
  color: var(--text-muted, #96a098);
}
.search-btn {
  padding: 16px 28px;
  border: none;
  background: var(--primary, #3a6450);
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.2s;
  white-space: nowrap;
}
.search-btn:hover {
  background: var(--primary-dark, #2a5238);
}
</style>
