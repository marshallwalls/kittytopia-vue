<template>
  <div class="col-xs-12 col-lg-8 p-4">
    <div class="stats">
      <div class="row">
        <div class="col-10 d-flex">
          <div>
            <transition mode="out-in" name="fade">
              <img class="circ" :key="currAvatar" :src="`src/assets/${data.first}-${data.last}_${currAvatar}.jpg`" />
            </transition>
          </div>
          <div>
            <h2>
              {{ data.first }}
              <span v-if="data.full != '' || data.full == null"
                >({{ data.full }})</span
              >
              {{ data.last }}
              <span v-if="this.birthday" style="color:green;font-weight:900;"><br />HAPPY BIRTHDAY!</span>
            </h2>
            <p class="nickname">
              <i v-if="data.nick != ''">"{{ data.nick }}"</i><i v-else>"???"</i>
            </p>
            <p v-if="data.occupation !== 'NA'" class="occupation">
              {{ data.occupation }}
            </p>
            <p v-else class="occupation">Student</p>
          </div>
        </div>
        <div class="col-2 edit">
          <a @click="editChar" href="#"
            ><span class="align-bottom material-symbols-outlined"> edit </span> Edit</a
          >
        </div>
      </div>
      <div class="line"><hr /></div>
      <div class="row">
        <div class="col-7">
          <div class="row">
            <h4>Stats</h4>
            <div class="col-6">
              <p><a href="#" @click="editField('birth')"><b>Born:</b></a> {{ this.formatDOB(data.birth) }}</p>
              <p><a href="#" @click="editField('height')"><b>Height:</b></a> {{ data.height }}</p>
              <p><a href="#" @click="editField('weight')"><b>Weight:</b></a> {{ data.weight }}</p>
            </div>
            <div class="col-6">
              <p><a href="#" @click="editField('hairLength')"><b>Fur Length:</b></a> {{ data.hairLength }}</p>
              <p><a href="#" @click="editField('hairColor')"><b>Fur Color:</b></a> {{ data.hairColor }}</p>
              <p><a href="#" @click="editField('eyes')"><b>Eye Color:</b></a> {{ data.eyes }}</p>
            </div>
          </div>
        </div>
        <div class="col-5">
          <h4>Favorite Foods</h4>
          <p v-html="data.favfood"></p>
        </div>
      </div>
      <div class="line"><hr /></div>
      <div class="sub">
        <strong>ID:</strong> {{ data.id }} | <strong>Last Modified:</strong>
        {{ data.modified }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      birthday: false,
      currAvatar: 0,
      timer: null
    }
  },
  methods: {
    editChar() {
      this.$emit('editChar')
    },
    editField(field) {
      this.$emit('editField', field, 'stats', 50, 50)
    },
    // Returns formatted date of birth, for example: "Sun, Oct 2nd, 1983"
    formatDOB(text) {
      if (text != undefined) {
        var bir = text.split('-')
        var birth = bir[1] + '/' + bir[2] + '/' + bir[0]
        var d1 = new Date(birth).toString()
        var day = d1.substring(8, 10)
        switch (day) {
          case '1':
          case '21':
          case '31':
            day = 'st'
            break
          case '2':
          case '22':
            day = 'nd'
            break
          case '3':
          case '23':
            day = 'rd'
            break
          default:
            day = 'th'
            break
        }
        bir = d1.substring(0, 3) + ',' + d1.substring(3, 10) + day + ',' + d1.substring(10, 15)
        if (bir.substring(9, 10) == '0') bir = bir.substring(0, 9) + bir.substring(10)
        return bir
      }
    },
    // Start the timer to swap the avatar image every 3.5 seconds
    startTimer() {
      if(this.timer === null) {
        this.timer = setInterval(this.updateAvatar, 3500)
      }
    },
    // Swap the avatar image from the current to the next
    updateAvatar() {
      this.currAvatar = this.currAvatar > this.totalAvatar - 2 ? 0 : this.currAvatar + 1
    }
  },
  mounted() {
    this.startTimer()
    // Check for their birthday!!
    const date = new Date()
    const month = (date.getMonth() + 1).toString().padStart(2, '0')
    const day = date.getDate().toString().padStart(2, '0')
    const curr_date = `${month}-${day}`
    var [y, m, d] = this.data.birth.split("-")
    if(curr_date == m+"-"+d) this.birthday = true
  },
  beforeUnmount() {
    if(this.timer !== null) {
      clearInterval(this.timer)
    }
  },
  props: {
    data: {
      type: Object,
      required: true
    },
    labels: {
      type: Object,
      required: true
    },
    totalAvatar: {
      type: Number,
      required: true
    }
  }
}
</script>

<style scoped>
.circ {
  border-radius: 100%;
  height: 120px;
  margin-right: 25px;
  width: 120px;
}

.stats {
  background-color: #131313;
  border: 1px #2b2a2a solid;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  padding: 25px;
}

.nickname {
  font-size: 14pt;
}

.edit {
  text-align: right;
}

.sub {
  color: #989898;
  font-size: 8pt;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.6s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
