<template>
  <UserLayout>
    <div
      class="bg-[url('/images/supportheader.png')] bg-cover bg-center relative h-[400px] flex items-center justify-center"
    >
      <div class="text-center z-10">
        <h1 class="mainheading font-bold">{{ $t('How Can We Help You?') }}</h1>
        <p class="text-xl">
          {{ $t('Real-Time Air Quality Data and Insights at Your Fingertips') }}
        </p>
      </div>
    </div>
    <div class="px-4 lg:px-[50px] py-12 bg-white">
      <div class="grid grid-cols-1 lg:grid-cols-24 gap-5 first_div">
        <div class="col-span-1 lg:col-span-19">
          <div
            v-for="(section, sectionIndex) in faqSections"
            :key="sectionIndex"
            class="section mb-10"
          >
            <h2 class="faqheading1 font-semibold">{{ section.title }}</h2>

            <div
              v-for="(item, itemIndex) in section.items"
              :key="itemIndex"
              class="faqitem mt-6 flex justify-between items-start faq-container"
              :class="{ expanded: expandedItems[sectionIndex][itemIndex] }"
            >
              <div class="flex-grow pr-4">
                <h3
                  :class="[
                    'faqheading2',
                    {
                      'text-green1 font-semibold':
                        expandedItems[sectionIndex][itemIndex],
                    },
                  ]"
                >
                  {{ item.question }}
                </h3>
                <Transition name="fade">
                  <p
                    v-if="expandedItems[sectionIndex][itemIndex]"
                    class="faqparagraph mt-4"
                  >
                    {{ item.answer }}
                  </p>
                </Transition>
              </div>
              <div
                class="flex-shrink-0 cursor-pointer"
                @click="toggleExpand(sectionIndex, itemIndex)"
              >
                <img
                  :class="{
                    'rotate-180': expandedItems[sectionIndex][itemIndex],
                  }"
                  :src="
                    expandedItems[sectionIndex][itemIndex]
                      ? '/images/expandicon.svg'
                      : '/images/shrinkicon.svg'
                  "
                  alt="Arrow icon"
                />
              </div>
            </div>
          </div>
        </div>
        <div
          class="hidden lg:block col-span-1 lg:col-span-5 border border-gray-200 rounded-lg p-4 bg-white"
        >
          <h3 class="text-lg font-bold mb-4">{{ $t("Alerts & Notifications") }}</h3>
            <div style="height: 700px;overflow-y: auto;">
                <Notification/>
            </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import Notification from "@/Components/Custom/Notification.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

import { ref } from "vue";

const faqSections = ref([
  {
    title: "General",
    items: [
      {
        question: t("What is the purpose of this application?"),
        answer:
          t("This application provides real-time air quality monitoring across various locations in Sri Lanka. It offers a detailed analysis of air pollutant parameters and comparative analysis between different locations"),
      },
      {
        question: t("How is the Air Quality Index (AQI) measured?"),
        answer: "The AQI is measured using...",
      },
      // Add more items as needed
    ],
  },
  {
    title: t('Features'),
    items: [
      {
        question: t("What technologies are used in this application?"),
        answer: "This application uses...",
      },
      {
        question: t("How often is the data updated?"),
        answer: "The data is updated...",
      },
      // Add more items as needed
    ],
  },
]);

const expandedItems = ref(
  faqSections.value.map((section) =>
    new Array(section.items.length).fill(false)
  )
);

const toggleExpand = (sectionIndex, itemIndex) => {
  expandedItems.value[sectionIndex][itemIndex] =
    !expandedItems.value[sectionIndex][itemIndex];
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, max-height 0.3s ease;
  max-height: 1000px; /* Adjust based on your content */
  overflow: hidden;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  max-height: 0;
}

img {
  transition: transform 0.3s ease;
}

.rotate-180 {
  transform: rotate(180deg);
}

.faqitem {
  border: 1px solid #01565b1a;
  padding: 20px;
}

.faq-container {
  border-radius: 50px;
  transition: border-radius 0.3s ease 0.1s; /* This line is crucial */
}

.faq-container.expanded {
  border-radius: 24px;
}
</style>
