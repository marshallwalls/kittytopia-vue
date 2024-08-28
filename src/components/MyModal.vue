<template>
  <div class="modal-overlay" v-if="isVisible" @mousedown.self="close">
    <div class="modal-content" @click.stop>
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  emits: ['close', 'save'],
  methods: {
    close() {
      this.$emit('close')
    },
    save() {
      this.$emit('save')
    }
  },
  props: {
    isVisible: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  /* DARKENED BACKGROUND */
  align-items: center;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  height: 100%;
  justify-content: center;
  left: 0;
  position: fixed;
  top: 0;
  width: 100%;
}

.modal-content {
  background-color: #131313;
  border: 1px #2b2a2a solid;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  max-width: 75%;
  min-width: 30%;
  padding: 25px;
  width: max-content;
}
</style>
