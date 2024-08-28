<template>
  <div class="row p-4" style="flex-grow: 1">
    <div class="information p-4">
      <div class="tabs">
        <ul class="nav nav-fill nav-tabs" id="myTab" role="tablist">
          <li v-for="(tab, index) in tabs" :key="index" class="nav-item" role="presentation">
            <a
              :aria-controls="`${tab.key}-tab-pane`"
              :aria-selected="tab.selected"
              :class="['nav-link', { active: tab.selected }]"
              :data-bs-target="`#${tab.key}-tab-pane`"
              :id="`${tab.key}-tab`"
              data-bs-toggle="tab"
              href="#"
              role="tab"
              >{{ tab.label }}</a
            >
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <TabContent
            v-for="(tab, index) in tab_contents"
            :key="index"
            :tabId="tab.id"
            :data="data"
            :fields="tab.fields"
            :isActive="tab.active"
            @edit="edit"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TabContent from './TabContent.vue'

export default {
  components: {
    TabContent
  },
  computed: {
    tab_contents() {
      return [
        { id: 'character', fields: this.fields?.character || {}, active: true },
        { id: 'friends', fields: this.fields?.friends || {}, active: false },
        { id: 'relations', fields: this.fields?.relations || {}, active: false },
        { id: 'history', fields: this.fields?.history || {}, active: false },
        { id: 'timeline', fields: this.fields?.timeline || {}, active: false }
      ]
    }
  },
  data() {
    return {
      fields: {
        character: ['personality', 'hobbies', 'sayings'],
        friends: ['friends'],
        relations: ['relations'],
        history: ['childhood', 'adulthood', 'history'],
        timeline: ['timeline']
      },
      tabs: [
        { key: 'character', label: 'Cattitude', selected: true },
        { key: 'friends', label: 'Furrends', selected: false },
        { key: 'relations', label: 'Pawsociations', selected: false },
        { key: 'history', label: 'Purrsonal Hisstory', selected: false },
        { key: 'timeline', label: 'Daily Schedule', selected: false },
      ]
    }
  },
  methods: {
    edit(field) {
      this.$emit('edit', field, 'info')
    },
    // Format text to properly display on the screen
    formattedText(text, two = null) {
      if (!this.data[text] || this.data[text] == '') return 'TBA'
      var a = this.data[text]
      if (two != null) {
        a = this.data[text][two]
      } else if (two == '') {
        return 'TBA'
      }
      return a
        .split('\n')
        .map((line) => `<p>${line}</p>`)
        .join('')
    }
  },
  name: 'InformationBox',
  props: {
    data: {
      type: Object,
      required: true
    },
    labels: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.information {
  background-color: #131313;
  border: 1px #2b2a2a solid;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  padding: 25px;
  text-align: left;
}

#myTab {
  margin-bottom: 20px;
}

.nav-link {
  color: #d19aff;
}

.nav-tabs .nav-link.active {
  background-color: #d19aff;
  color: black;
  font-weight: 700;
}
</style>
