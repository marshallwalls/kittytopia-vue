<script setup>
import MenuBar from './components/MenuBar.vue'
import HeaderBox from './components/HeaderBox.vue'
import StatsBox from './components/StatsBox.vue'
import GalleryBox from './components/GalleryBox.vue'
import InformationBox from './components/InformationBox.vue'
import Galleria from 'primevue/galleria'
import MyModal from './components/MyModal.vue'
import CustomBox from './components/CustomBox.vue'
import { top } from '@popperjs/core'
</script>

<template>
  <div id="error"></div>
  <div class="container-fluid" v-if="loaded">
    <MenuBar />

    <div class="content col-11">
      <HeaderBox :data="headerData" @enter="searchTerm" />

      <div class="data">
        <div class="row">
          <StatsBox
            :data="statsData"
            :labels="labels"
            :totalAvatar="totalAvatar"
            @editChar="toggleModal('edit')"
            @editField="edit"
          />

          <GalleryBox
            :data="galleryData"
            :isVisible="isVisible['modal']"
            @show="toggleModal('modal')"
          />

          <Galleria
            containerStyle="max-height: 100%; max-width: 100%"
            ref="galleria"
            v-model:activeIndex="activeIndex"
            v-model:visible="isVisible['modal']"
            :circular="true"
            :fullScreen="true"
            :numVisible="6"
            :responsiveOptions="responsiveOptions"
            :value="images"
            :indicatorsPosition="top"
            :showIndicators="true"
            :showIndicatorsOnItem="true"
            :showItemNavigators="true"
            :showThumbnails="false"
          >
            <template #item="slotProps">
              <img :alt="slotProps.item.alt" :src="slotProps.item.src" :style="imageStyles" />
            </template>
            <template #thumbnail="slotProps">
              <img :alt="slotProps.item.alt" :src="slotProps.item.thumb" />
            </template>
            <template #caption="slotProps">
              <div class="text-xl mb-2 fs-4">{{ slotProps.item.title }}</div>
              <p class="text-white">{{ slotProps.item.alt }}</p>
            </template>
          </Galleria>

          <MyModal
            id="EditKittyModal"
            :isVisible="isVisible['edit']"
            @close="toggleModal('edit')"
          >
            <h2>Edit Kitty</h2>
            <div class="form-group" v-for="(input, index) in inputs" :key="index">
              <label :for="'input-' + index">{{ input.label }}</label>
              <input
                class="form-control"
                type="text"
                v-model="statsData[input.field]"
                :id="'input-' + index"
              />
            </div>
            <button class="button" @click="saveEdits">Save</button>
          </MyModal>

          <MyModal id="SearchModal" :isVisible="isVisible['search']" @close="toggleModal('search')">
            <h2>Search Results</h2>
            <p v-html="searchResults"></p>
          </MyModal>

          <CustomBox
            id="EditFieldModal"
            :data="currentStat"
            :isVisible="isVisible['custom']"
            :labels="labels"
            @close="toggleModal('custom')"
            @saveInfo="saveInfo"
          ></CustomBox>
        </div>

        <InformationBox :data="informationData" :labels="labels" @edit="edit" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {
    CustomBox,
    HeaderBox,
    MyModal
  },
  computed: {
    imageStyles() {
      return {
        maxHeight: this.pageHeight - 130 + 'px',
        objectFit: 'contain'
      }
    }
  },
  data() {
    return {
      activeIndex: 0,
      currentStat: { field: '', height: null, value: '', weight: null },
      debug: true,
      first: '',
      galleryData: {},
      genderColor: 'pink',
      headerData: {},
      images: [],
      informationData: {},
      inputs: [
        { label: 'First Name', field: 'first' },
        { label: 'Full Name', field: 'full' },
        { label: 'Nickname', field: 'nick' },
        { label: 'Last Name', field: 'last' },
        { label: 'Gender', field: 'gender' },
        { label: 'Date of Birth', field: 'birth' },
        { label: 'Occupation', field: 'occupation' },
        { label: 'Gallery Tags', field: 'gallery' }
      ],
      isVisible: { custom: false, edit: false, modal: false, search: false },
      labels: {
        first: 'First Name',
        last: 'Last Name',
        gender: 'Gender',
        birth: 'Date of Birth',
        hairLength: 'Fur Length',
        hairColor: 'Fur Color',
        eyes: 'Eye Color',
        height: 'Height',
        weight: 'Weight',
        personality: 'Purrsonality',
        hobbies: 'Hobbies & Interests',
        friends: 'Friends',
        childhood: 'Kittenhood',
        adulthood: 'Catthood',
        history: 'Events',
        relations: 'Relations'
      },
      last: '',
      loaded: false,
      pageHeight: 0,
      responsiveOptions: [
        { breakpoint: '1024px', numVisible: 3 },
        { breakpoint: '768px', numVisible: 2 },
        { breakpoint: '560px', numVisible: 1 }
      ],
      searchResults: '',
      statsData: {},
      timer: null,
      totalAvatar: 3
    }
  },
  methods: {
    addKeydownListener() {
      document.addEventListener('keydown', this.handleKeydown)
    },
    edit(field, which, height = 500, width = 100) {
      if (this.debug) console.log('%cedit() =', 'color:lime', which, field, height, width)
      var val = ''
      if (which == 'stats') {
        val = this.statsData[field]
      } else {
        val = this.informationData[field]
      }
      this.currentStat = {
        field: field,
        height: height,
        value: val,
        which: which,
        width: width
      }
      this.isVisible['custom'] = true
    },
    handleKeydown(event) {
      if (event.key == 'ArrowLeft') {
        this.swapImage(false)
      } else if (event.key == 'ArrowRight') {
        this.swapImage()
      } else if (event.key == 'Escape') {
        this.isVisible['modal'] = false
      }
    },
    removeKeydownListener() {
      if (this.debug) console.log('%cremoveKeydownListener()', 'color:pink')
      document.removeEventListener('keydown', this.handleKeydown)
    },
    saveEdits() {
      if (this.debug) console.log('%csaveEdits() =', 'color:#80c78f', this.statsData)
      this.updateImages(this.statsData.gallery)
      var t = { occupation: this.statsData.occupation, gallery: this.statsData.gallery }
      this.syncStats(t)
      // Database Sync
      this.toggleModal('edit')
    },
    saveInfo(which, type, value) {
      if (this.debug) console.log('%csaveInfo() =', 'color:lime', which, type, value)
      if (which == 'stats') {
        this.statsData[type] = value
      } else {
        this.informationData[type] = value
      }
      this.syncData(type, value)
      this.toggleModal('custom')
    },
    searchTerm(val) {
      if (this.debug) console.log('%csearchTerm()', 'color:yellow', val)
      var allData = { ...this.statsData, ...this.informationData }
      var results = ''

      // Calculate search results
      for (const key in allData) {
        if (typeof allData[key] === 'string') {
          let div = document.createElement('div')
          div.innerHTML = allData[key]
          let text = div.textContent || div.innerText || ''
          let curr = ''

          // Loop to find all occurrences
          let spot = text.indexOf(val)
          while (spot !== -1) {
            if (curr !== this.labels[key]) {
              results += "<p><strong style='color:#80c78f'>" + this.labels[key] + ':</strong></p>'
              curr = this.labels[key]
            }
            results +=
              '<p>"...' +
              text.substring(Math.max(spot - 83, 0), spot) +
              "<strong style='color:#d19aff'>"
            results +=
              val +
              '</strong>' +
              text.substring(spot + val.length, spot + val.length + 83) +
              '..."</p>'

            // Move to the next occurrence
            spot = text.indexOf(val, spot + val.length)
          }
        }
      }
      if (results == '') results = "'" + val + "' NOT FOUND"
      this.searchResults = results
      this.toggleModal('search')
    },
    swapImage(next = true) {
      if (next) {
        if (this.activeIndex < this.images.length - 1) {
          this.activeIndex += 1
        } else {
          this.activeIndex = 0
        }
      } else {
        if (this.activeIndex > 0) {
          this.activeIndex -= 1
        } else {
          this.activeIndex = this.images.length - 1
        }
      }
    },
    toggleModal(which) {
      if (this.debug) console.log('toggleModal', which)
      this.isVisible[which] = !this.isVisible[which]
    },
    updateImages(json) {
      // Set a default gallery field
      var test = '3|ONE|one|TWO|two'
      if (json != null) {
        test = json
      }
      var test2 = test.split('|')
      var total = (test2.length - 1) / 2
      var link = 'src/assets/' + this.first.toLowerCase() + '-' + this.last.toLowerCase() + '_full_'
      var list = []
      // Build the gallery list of properly labeled pictures
      for (var i = 0; i < total; i++) {
        list[i] = {
          title: test2[i * 2 + 1],
          alt: test2[i * 2 + 2],
          src: link + i + '.jpg',
          thumb: link + i + '_thumb.png'
        }
      }
      this.totalAvatar = Number(test[0])
      this.images = list
    },
    updatePageHeight() {
      this.pageHeight = window.innerHeight
    },
    async fetchData() {
      // Fetch the name of the requested kitty
      const queryString = window.location.search
      const urlParams = new URLSearchParams(queryString)
      const name = urlParams.get('name')
      // If no name is supplied, redirect to a default page
      if (name == null) window.location.replace('/index.html/?name=fred-wallace')
      var yes = name.split('-')
      var first = yes[0].substr(0, 1).toUpperCase() + yes[0].substr(1)
      var last = yes[1].substr(0, 1).toUpperCase() + yes[1].substr(1)
      // Database call to fetch this kitty's information and populate the page
      const url = '/src/kittytopiaAPI.php'
      const options = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ func: 'getKittyStats', first: first, last: last })
      }
      try {
        // Wait for the API to reply
        const res = await fetch(url, options)
        if (!res.ok) {
          //throw new Error(`HTTP error! Status: ${res.status}`)
          const text = await res.text() // Read the response as text
          throw new Error(`HTTP error! Status: ${res.status}\nResponse: ${text}`)
        }
        // Read the reply as text
        const resp = await res.text()
        let json
        // Check to see if the reply is proper JSON
        try {
          json = JSON.parse(resp)
        } catch (jsonError) {
          console.error('Failed to parse JSON:', resp)
          document.getElementById('modal').innerHTML = resp
          throw new Error(`Failed to parse JSON. Error: ${jsonError.message}`)
        }
        if(this.debug) console.log('%cfetchedData =', 'color:#80c78f', json)
        // If there is an error in the JSON, alert the user on screen
        if (json.error != false) alert(json.error)
        // Change the document title to properly display this kitty's first and last name
        document.title = json.stats.first + ' ' + json.stats.last + ' | Kittytopia'
        // Populate all the necessary variables for this page and all of its child components
        this.galleryData = {
          first: json.stats.first,
          last: json.stats.last,
          screenshots: json.screenshots,
          total: json.screenshots_total
        }
        this.headerData = json.list
        this.headerData.selected = first
        this.headerData.last = last
        this.headerData.dob = json.stats.birth
        this.informationData = json.info
        this.statsData = json.stats
        this.statsData.currAv = 0
        this.inputs[0].value = json.stats.first
        this.first = json.stats.first
        this.last = json.stats.last
        // If the kitty is male, display all headers as a pretty blue, otherwise show a pretty pink
        if (json.stats.gender == 'M') this.genderColor = 'rgb(135, 206, 250)'
        this.updateImages(json.stats.gallery)
        this.loaded = true
      } catch (error) {
        console.error('Error:', error)
        this.response = 'Failed to fetch data'
      }
    },
    // Update a specific field in the database
    async syncData(field, value) {
      const url = '/src/kittytopiaAPI.php'
      const options = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          func: 'updateKittyStats',
          first: this.first,
          field: field,
          value: value
        })
      }
      try {
        const res = await fetch(url, options)
        if (!res.ok) {
          throw new Error(`HTTP error! Status: ${res.status}`)
        }
        const contentType = res.headers.get('content-type')
        if (contentType && contentType.includes('application/json')) {
          const json = await res.json()
          // console.log('%csyncedData() =', 'color:#d19aff', json)
          if (json.error != false) alert(json.error)
          //this.statsData = json.char
        }
      } catch (error) {
        console.error('Error:', error, 'Failed to sync data')
      }
    },
    // Update kitty stats in the database
    async syncStats(values) {
      const url = '/src/kittytopiaAPI.php'
      const options = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          func: 'updateStats',
          first: this.first,
          values: values
        })
      }
      try {
        const res = await fetch(url, options)
        if (!res.ok) {
          throw new Error(`HTTP error! Status: ${res.status}`)
        }
        const contentType = res.headers.get('content-type')
        if (contentType && contentType.includes('application/json')) {
          const json = await res.json()
          // console.log('%csyncedStats', 'color:orange', json)
          if (json.error != false) alert(json.error)
        }
      } catch (error) {
        console.error('Error:', error, 'Failed to sync data')
      }
    }
  },
  watch: {
    // Add/remove a keydown listener when a modal is visible/invisible in order to control it with key presses
    'isVisible.modal': function (newVal, oldVal) {
      console.log('testing', newVal, oldVal)
      if (newVal) {
        this.addKeydownListener()
      } else {
        this.removeKeydownListener()
      }
    }
  },
  // When the page starts
  mounted() {
    this.fetchData()
    this.updatePageHeight()
    // Add event listener for any time the page is resized
    window.addEventListener('resize', this.updatePageHeight)
  },
  // When the page is closed
  beforeUnmount() {
    this.removeKeydownListener()
    // Remove event listener for any time the page was resized
    window.removeEventListener('resize', this.updatePageHeight)
  }
}
</script>

<style>
body {
  background-color: #111;
  color: #fff;
  font-family: 'Poppins';
  font-size: 10pt;
}

a {
  color: #d19aff;
  font-weight: 700;
  text-decoration: none;
}

h2,
h3 {
  color: v-bind(genderColor);
  font-weight: 700;
}

h4 {
  color: #989898;
  font-size: 13pt;
  font-weight: 300;
  padding-top: 15px;
}

.container-fluid {
  display: flex;
  flex-direction: row;
  text-align: left;
}

MyModal p {
  color: black;
}

.mid {
  align-content: center;
}

.button {
  margin-top: 50px;
}

.tiny_av {
  border-radius: 100%;
  height: 75px;
}
</style>
