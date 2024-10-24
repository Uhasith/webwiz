<template>
  <UserLayout>
    <div
      class="bg-[url('/images/aboutheader.png')] bg-cover bg-center relative h-[400px] flex items-center justify-center"
    >
      <div class="text-center z-10">
        <h1 class="mainheading font-bold">{{ $t('About Us') }}</h1>
        <p class="text-xl">
          {{ $t('Real-Time Air Quality Data and Insights at Your Fingertips') }}
        </p>
      </div>
    </div>

    <div class="px-6 lg:px-[50px] bg-white pt-9">
      <div class="w-full text-center">
        <h2 class="text-black1 aboutheading1 font-semibold mb-6">{{ $t('Who We Are') }}</h2>
        <p class="text-lg text-justify">
          {{ $t('We are a team of passionate') }}
        </p>
      </div>

      <div class="w-full text-center">
        <h2 class="text-black1 aboutheading1 font-semibold mt-[90px] mb-[70px]">
          {{ $t('What We Do') }}
        </h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="relative aboutbox">
          <div class="absolute -top-8 left-1/2 -translate-x-1/2">
            <img
              src="/images/realairqualityicon.svg"
              alt="Historical Data Icon"
              class="h-16 w-16"
            />
          </div>
          <h2 class="mb-4 font-bold text-center aboutheading2">
            Real-Time Air Quality Monitoring
          </h2>
          <p class="text-gray-700 text-md text-justify">
            {{ $t('Our application offers') }}
          </p>
        </div>

        <div class="relative aboutbox">
          <div class="absolute -top-8 left-1/2 -translate-x-1/2">
            <img
              src="/images/historicaldataicon.svg"
              alt="Historical Data Icon"
              class="h-16 w-16"
            />
          </div>
          <h2 class="mb-4 font-bold text-center aboutheading2">
            Historical Data Analysis
          </h2>
          <p class="text-gray-700 text-md text-justify">
            {{ $t('We provide detailed') }}
          </p>
        </div>

        <div class="relative aboutbox">
          <div class="absolute -top-8 left-1/2 -translate-x-1/2">
            <img
              src="/images/comparativeanalysisicon.svg"
              alt="Historical Data Icon"
              class="h-16 w-16"
            />
          </div>
          <h2 class="mb-4 font-bold text-center aboutheading2">
            {{ $t('Comparative Analysis') }}
          </h2>
          <p class="text-gray-700 text-md text-justify">
            {{ $t('Our platform allows') }}
          </p>
        </div>

        <div class="relative aboutbox">
          <div class="absolute -top-8 left-1/2 -translate-x-1/2">
            <img
              src="/images/alertnotificicon.svg"
              alt="Historical Data Icon"
              class="h-16 w-16"
            />
          </div>
          <h2 class="mb-4 font-bold text-center aboutheading2">
            {{ $t("Alerts & Notifications") }}
          </h2>
          <p class="text-gray-700 text-md text-justify">
            {{ $t('We keep our users') }}
          </p>
        </div>
      </div>

      <div class="mx-auto flex mt-[90px]">
        <div class="flex-shrink-0">
          <img
            src="images/aboutusvm.png"
            alt="Illustrative globe with tree"
            class="w-full h-auto hidden lg:block"
          />
        </div>

        <div class="w-20 flex-shrink-0"></div>

        <div class="flex-grow flex flex-col">
          <div
            class="flex-grow mb-6 p-6 bg-[url('/images/missionbg.png')] bg-cover bg-right-bottom bg-no-repeat relative overflow-hidden"
          >
            <h2 class="aboutheading1 font-semibold mb-4">{{ $t('Our Mission') }}</h2>
            <p class="text-[18px]">
              {{ $t('Our mission is to empower') }}
            </p>
          </div>
          <div
            class="flex-grow mb-6 p-6 bg-[url('/images/visionbg.png')] bg-cover bg-right-bottom bg-no-repeat relative overflow-hidden"
          >
            <h2 class="aboutheading1 font-semibold mb-4">{{ $t('Our Vision') }}</h2>
            <p class="text-[18px]">
              {{ $t('We envision a future') }}
            </p>
          </div>
        </div>
      </div>

      <div class="w-full text-center mb-16 mt-[90px]">
        <h2 class="text-black1 aboutheading1 font-semibold">{{ $t('Contact Us') }}</h2>
        <p class="text-lg">
          {{ $t('If you have any questions') }}
        </p>
      </div>

      <div class="md:flex gap-8 mb-[90px]">
        <div
          class="mb-4 md:mb-0 w-full lg:w-1/3 p-6 border border-green1 rounded-3xl flex flex-col items-center text-center"
        >
          <img
            src="images/mailicon.svg"
            alt="Email Icon"
            class="w-8 h-8 object-contain"
          />
          <h3 class="mt-4 text-xl font-semibold">{{ $t('Email') }}</h3>
          <p class="mt-2">info@[companyname].com</p>
        </div>

        <div
          class="mb-4 md:mb-0 w-full lg:w-1/3 p-6 border border-green1 rounded-3xl flex flex-col items-center text-center"
        >
          <img
            src="images/phoneicon.svg"
            alt="Email Icon"
            class="w-8 h-8 object-contain"
          />
          <h3 class="mt-4 text-xl font-semibold">{{ $t('Phone') }}</h3>
          <p class="mt-2">+94 123 456 789</p>
        </div>

        <div
          class="mb-4 md:mb-0 w-full lg:w-1/3 p-6 border border-green1 rounded-3xl flex flex-col items-center text-center"
        >
          <img
            src="images/addressicon.svg"
            alt="Email Icon"
            class="w-8 h-8 object-contain"
          />
          <h3 class="mt-4 text-xl font-semibold">{{ $t('Address') }}</h3>
          <p class="mt-2">[Company Address]</p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
</script>

<style scoped>
</style>
