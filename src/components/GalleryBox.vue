<template>
  <div class="col-xs-12 col-lg-4 p-4">
    <div class="gallery">
      <h3>{{ data.first }}'s Gallery</h3>
      <p>
        <img :src="`src/assets/${data.screenshots[currentImage - 1]}.jpg`" />
      </p>
      <p>
        <a v-if="currentImage > 1" @click="prevImage" href="#">&lt;</a
        ><span v-else style="color: grey"><b>&lt;&nbsp;</b></span> {{ currentImage }} /
        {{ data.screenshots.length }}
        <a v-if="currentImage < data.screenshots.length" @click="nextImage" href="#">&gt;</a>
        <span v-else style="color: grey"><b>&nbsp;&gt;</b></span>
      </p>
      <p>
        <a @click="show" href="#"
          ><span class="material-symbols-outlined align-bottom"> apps </span> View Gallery</a
        >
        ({{ data.total }})
      </p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentImage: 1
    }
  },
  emits: ['show'],
  props: {
    data: {
      type: Object,
      required: true
    },
    isVisible: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    nextImage() {
      this.currentImage += 1
    },
    prevImage() {
      this.currentImage -= 1
    },
    show() {
      this.$emit('show')
    }
  }
}
</script>

<style scoped>
.gallery {
  background-color: #131313;
  border: 1px #2b2a2a solid;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  height: 100%;
  justify-content: space-evenly;
  padding: 15px;
  text-align: center;
}

.gallery img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  max-height: 200px;
  max-width: 100%;
}
</style>
