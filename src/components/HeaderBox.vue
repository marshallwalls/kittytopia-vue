<template>
  <div class="header row">
    <div class="col-9 d-flex align-items-center">
      <span class="lime me-4">Select Profile</span>
      <VueDatePicker
        v-model="date"
        :enable-time-picker="false"
        :start-date="startDate"
        @update:model-value="handleDate"
        auto-apply
      ></VueDatePicker>
      &nbsp;
      <div class="list input-group me-4">
        <label class="input-group-text" for=""
          ><span class="material-symbols-outlined">person</span></label
        >
        <select
          class="form-select"
          onchange="window.location = 'http://localhost:5173/?name='+this.options[this.selectedIndex].value"
        >
          <option
            v-for="char in data"
            :key="char.id"
            :value="char.first.toLowerCase() + '-' + char.last.toLowerCase()"
            :selected="char.first === data.selected"
          >
            {{ char.first }} {{ char.last }}
          </option>
        </select>
      </div>
      <a href="#" @click="randomKitty"
        ><span class="align-bottom material-symbols-outlined"> shuffle </span> Random</a
      >
    </div>
    <div class="col-3 search search-container">
      <input
        class="search-input"
        id="searchTextbox"
        placeholder="Search..."
        type="text"
        v-model="searchTerm"
        @keyup.enter="handleEnter"
      />
      <a href="#" id="searchLink"
        ><span @click="showSearch" class="material-symbols-outlined"> search </span></a
      >
    </div>
  </div>
  <div class="breadcrumbs">
    <p>
      <a href="">Kitties</a> > {{ data.selected }} {{ data.last }} <b>{{ test }}</b>
    </p>
  </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {
  components: { VueDatePicker },
  data() {
    return {
      date: null,
      searchTerm: '',
      startDate: new Date(),
      test: null
    }
  },
  emits: ['enter'],
  methods: {
    getRandomInt(min, max) {
      min = Math.ceil(min)
      max = Math.floor(max)
      return Math.floor(Math.random() * (max - min + 1) + min)
    },
    handleDate() {
      var curr = new Intl.DateTimeFormat().format(this.date)
      if (curr != '12/31/1969') {
        this.test = ''
        var bir = this.data.dob.split('-')
        var birth = bir[1] + '/' + bir[2] + '/' + bir[0]
        const d1 = new Date(birth)
        const d2 = new Date(curr)
        var years = Math.floor((d2.getTime() / 1000 - d1.getTime() / 1000) / 31556926)
        var s = ''
        if (years != 1) {
          s = 's'
        }
        years += ' year' + s
        var months = Math.floor(
          ((d2.getTime() / 1000 - d1.getTime() / 1000) % 31556926) / 2629743.83
        )
        if (months != 1) {
          s = 's'
        } else {
          s = ''
        }
        months = months == 0 ? '' : ', ' + months + ' month' + s
        this.test = '(' + years + months + ' old)'
      } else {
        this.test = ''
      }
    },
    handleEnter() {
      this.$emit('enter', this.searchTerm)
    },
    randomKitty() {
      var num = this.getRandomInt(1, this.data.length)
      var nam = (this.data[num].first + '-' + this.data[num].last).toLowerCase()
      window.location = 'http://localhost:5173/?name=' + nam
    },
    showSearch() {
      document.querySelector('.search-container').classList.toggle('active')
      document.getElementById('searchLink').style.display = 'none'
      document.getElementById('searchTextbox').focus()
    }
  },
  props: {
    data: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.header {
  align-items: center;
  padding: 25px 15px 20px 15px;
}

.lime {
  color: #80c78f;
}

.material-symbols-outlined {
  vertical-align: middle;
}

.cur-date {
  border: 0px;
  border-radius: 2rem;
  padding-left: 12px;
}

.dp__main {
  max-width: fit-content;
  min-width: 140px;
}

.form-select {
  --bs-border-radius: 2rem;
  width: auto;
}

.input-group {
  --bs-border-radius: 2rem;
  max-width: 213px;
  min-width: 213px;
}

.input-group-text {
  padding-right: 0px;
}

.search {
  text-align: right;
}

.breadcrumbs p {
  margin: 10px 0 0 0;
  padding-left: 10px;
}

.search-container {
  position: relative;
}

.search-input {
  border: 1px solid #777;
  border-radius: 25px;
  box-sizing: border-box;
  cursor: default;
  opacity: 0;
  padding: 8px;
  right: 0;
  transition: width 0.3s ease;
  width: 0;
}

.search-container.active .search-input {
  cursor: text;
  width: 100%;
  opacity: 1;
}

.search-container.active .search-icon {
  transform: translateX(-100%);
}

input:focus {
  border: 1px solid #80c78f;
  outline: none;
}
</style>
