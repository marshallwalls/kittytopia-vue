<template>
  <div
    class="tab-pane fade"
    role="tabpanel"
    tabindex="0"
    :aria-labelledby="tabId + '-tab'"
    :class="{ 'show active': isActive }"
    :id="tabId + '-tab-pane'"
  >
    <div class="row">
      <div class="col">
        <template v-for="(feel, index) in fields" :key="index">
          <ShowField :data="data[feel]" :field="feel" @edit="edit" />
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import ShowField from './ShowField.vue'

export default {
  components: {
    ShowField
  },
  methods: {
    edit(field) {
      this.$emit('edit', field)
    }
  },
  props: {
    data: {
      type: Object,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
    isActive: {
      type: Boolean,
      default: false
    },
    tabId: {
      type: String,
      required: true
    }
  }
}
</script>
