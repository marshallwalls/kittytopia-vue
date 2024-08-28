<template>
  <div v-if="isVisible" class="modal-overlay" @mousedown.self="close">
    <div class="modal-content" @click.stop>
      <div class="stats">
        <div class="row">
          <h2>Edit {{ this.labels[data.field] }}</h2>
          <textarea :style="computedStyle" class="text-area" v-model="data.value"></textarea>
        </div>
        <div class="line"><hr /></div>
        <div class="row">
          <p><button @click="saveInfo" type="button">SAVE</button></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    computedStyle() {
      return {
        height: this.data.height + 'px',
        width: this.data.width + '%'
      };
    }
  },
  methods: {
    capitalize(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    close() {
      this.$emit('close');
    },
    saveInfo() {
      this.$emit('saveInfo', this.data.which, this.data.field, this.data.value);
    }
  },
  name: 'CustomBox',
  props: {
    data: {
      type: Object,
      required: true
    },
    isVisible: {
      type: Boolean,
      default: false
    },
    labels: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  border-radius: 20px;
  max-width: 90%;
  padding: 20px;
  width: 600px;
}

.stats {
  background-color: #131313;
  border: 1px #2b2a2a solid;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  padding: 25px;
}

.text-area {
  height: 500px;
  width: 100%;
}
</style>
