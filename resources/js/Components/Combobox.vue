<template>
  <div class="top-16">
    <Combobox v-model="selectedItems" multiple>
      <div class="relative mt-1">
        <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
          <ComboboxInput
              :placeholder="placeholder"
              class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
              :displayValue="(item) => item.name"
              @change="query = $event.target.value"
              @click="handleInputFocus"
          />
          <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2" ref="comboBtn">
            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </ComboboxButton>
        </div>
        <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="query = ''">
          <ComboboxOptions class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
            <div v-if="filteredItems.length === 0 && query !== ''" class="relative cursor-default select-none py-2 px-4 text-gray-700">
              Nothing found.
            </div>

            <ComboboxOption v-for="item in filteredItems" :key="item.id" :value="item" v-slot="{ selected, active }">
              <li class="relative cursor-default select-none py-2 pl-10 pr-4" :class="{'bg-teal-600 text-white': active, 'text-gray-900': !active}">
                <span class="block truncate" :class="{'font-medium': selected, 'font-normal': !selected}">
                  {{ item.name }}
                </span>
                <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3" :class="{'text-white': active, 'text-teal-600': !active}">
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </div>
    </Combobox>
  </div>
</template>

<script setup>
import {ref, computed, watch} from 'vue'
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'



// variables
const props = defineProps({
  placeholder: {
    type: String,
    default: "Select"
  },
  item: Array,
  modelValue: Array,
  limit: {
    type: Number,
    default: 3
  }
});
let comboBtn = ref(null);
const handleInputFocus = () => comboBtn.value?.$el.click();
const placeholder = ref(props.placeholder);
const items = ref(props.item);
const selectedItems = ref(props.modelValue.map(id =>
    props.item.find(item => item.id === id)
));
let query = ref('');
let emit = defineEmits(['update:modelValue']);
// variables
if (selectedItems.value.length > 0) {
    placeholder.value = `${selectedItems.value.length} selected`;
} else {
    placeholder.value = props.placeholder;
}
// functionalities
const filteredItems = computed(() => {
  const selected = selectedItems.value.filter(item => item.name.toLowerCase().includes(query.value.toLowerCase()));
  const unselected = items.value.filter(item => !selectedItems.value.includes(item) && item.name.toLowerCase().includes(query.value.toLowerCase()));
  if (selectedItems.value.length === props.limit) {
    return selected;
  }

  return selected.concat(unselected);
});
// functionalities

// watchers

watch(selectedItems, (newValue) => {
  emit('update:modelValue',  newValue.map(item => item.id));
  if (newValue.length > 0) {
    placeholder.value = `${newValue.length} selected`;
  } else {
    placeholder.value = props.placeholder;
  }
});
// watchers

</script>
