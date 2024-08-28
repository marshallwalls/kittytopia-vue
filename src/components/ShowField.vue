<template>
  <h3 v-if="labels[this.field]">
    {{ labels[this.field] }}
    <a v-if="labels[this.field]" @click.prevent="edit(this.field)" href="#"
      ><span class="material-symbols-outlined"> edit </span></a
    >
  </h3>
  <p v-html="formattedText(this.data)" v-if="this.data != '' || this.data == null"></p>
  <p v-else style="color: #777">TBA</p>
</template>

<script>
export default {
  data() {
    return {
      labels: {
        personality: 'Purrsonality',
        hobbies: 'Hobbies & Interests',
        sayings: 'Sayings & Such',
        childhood: 'Kittenhood',
        adulthood: 'Cathood',
        history: 'Events',
        timeline: 'Daily Schedule'
      }
    }
  },
  emits: ['close', 'edit'],
  methods: {
    close() {
      this.$emit('close')
    },
    edit(field) {
      this.$emit('edit', field)
    },
    // Format text to properly display on the screen
    formattedText(text) {
      if (text == null) {
        return "<span style='color:#777'>TBA</span>"
      } else if(text.includes('\n')) {
        var t = text
          .split('\n')
          .map((line) => `<p>${line}</p>`)
          .join('')
          if(this.field == 'spouses') console.log(t)
          return t
      } else {
        return text
      }
    },
    uppercase(str) {
      return str.charAt(0).toUpperCase() + str.slice(1)
    }
  },
  props: {
    data: {
      type: String,
      required: true
    },
    field: {
      type: String,
      required: true
    }
  }
}
</script>
