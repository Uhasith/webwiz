<script setup lang="ts">
// import { CommandEmpty, CommandGroup, CommandItem, CommandList } from '@/Components/ui/command'
// import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/Components/ui/tags-input'
// import { ComboboxAnchor, ComboboxContent, ComboboxInput, ComboboxPortal, ComboboxRoot } from 'radix-vue'
// import { computed, ref } from 'vue'
// import { ChevronDownIcon } from 'lucide-vue-next'

// const frameworks = [
//     { value: 'next.js', label: 'Next.js' },
//     { value: 'sveltekit', label: 'SvelteKit' },
//     { value: 'nuxt', label: 'Nuxt' },
//     { value: 'remix', label: 'Remix' },
//     { value: 'astro', label: 'Astro' },
// ]

// const modelValue = ref<string[]>([])
// const open = ref(false)
// const searchTerm = ref('')

// const filteredFrameworks = computed(() => frameworks.filter(i => !modelValue.value.includes(i.label)))

// // Toggle function for the popover
// const toggleOpen = () => {
//     open.value = !open.value
// }
</script>

<template>
    <!-- <TagsInput class="px-0 gap-0 w-80 cursor-pointer relative" :model-value="modelValue">
        <div v-if="modelValue.length > 0" class="flex gap-2 flex-wrap items-center px-3 py-3">
            <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
                <TagsInputItemText />
                <TagsInputItemDelete />
            </TagsInputItem>
        </div>

        <ChevronDownIcon class="cursor-pointer size-4 mr-3 absolute right-0" @click="toggleOpen" />

        <ComboboxRoot v-model="modelValue" v-model:open="open" v-model:search-term="searchTerm" class="w-full">
            <ComboboxAnchor as-child>
                <div class="flex items-center justify-end w-full">
                    <ComboboxInput placeholder="Framework..." as-child>
                        <TagsInputInput class="w-full px-3 border-0 focus:ring-0 py-0"
                            :class="modelValue.length > 0 ? 'mt-2' : ''" @keydown.enter.prevent />
                    </ComboboxInput>
                </div>
            </ComboboxAnchor>

            <ComboboxPortal>
                <ComboboxContent>
                    <CommandList position="popper"
                        class="w-[--radix-popper-anchor-width] rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2">
                        <CommandEmpty />
                        <CommandGroup>
                            <CommandItem v-for="framework in filteredFrameworks" :key="framework.value"
                                :value="framework.label" @select.prevent="(ev) => {
                                    if (typeof ev.detail.value === 'string') {
                                        searchTerm = ''
                                        modelValue.push(ev.detail.value)
                                    }

                                    if (filteredFrameworks.length === 0) {
                                        open = false
                                    }
                                }">
                                {{ framework.label }}
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </ComboboxContent>
            </ComboboxPortal>
        </ComboboxRoot>
    </TagsInput> -->
</template>