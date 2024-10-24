<template>
  <div class="map-container relative flex">
    <div class="map-wrapper flex-grow">
      <LMap
        class="custom-map"
        :zoom="14"
        :center="[6.9271, 79.8612]"
        :bounds="mapBounds"
        @ready="onMapReady"
        :min-zoom="7"
      >
        <LTileLayer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          attribution="&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors"
        />
        <LMarker
          v-for="(marker, index) in markers"
          :key="index"
          :lat-lng="marker.latlng"
          :icon="marker.icon"
          @click="navigateToMarkerDetail(marker,default_data)"
        >
          <LPopup>{{ marker.title }}</LPopup>
        </LMarker>
      </LMap>
      <!-- @click="showModal(marker)" -->
      <div id="modal" class="modal" v-if="isModalOpen" @click.self="closeModal">
        <div class="modal-content rounded-lg max-w-[32rem]">
          <!-- <h2 id="modal-title">{{ modalData.title }} Data</h2> -->
<!--          <div class="flex justify-end">-->
<!--            <div class="flex space-x-2">-->
<!--              <SelectParameterDropdown />-->
<!--              <SelectParameterDropdown />-->
<!--            </div>-->
<!--          </div>-->
          <div class="w-full flex justify-center items-center mt-4 mb-2">
            <img src="/images/pinicon.png" class="h-4 w-auto mr-2" />
            <!-- <p class="font-bold font-[20px]">Pannipitiya Road, Colombo</p> -->
            <p class="font-bold font-[20px]">
              {{ modalData.sensor_location.location.name }}
            </p>
          </div>
          <div class="grid grid-cols-7 gap-4 mt-4">
            <div class="col-span-2">
              <div class="w-full text-center">
                <div class="inline-flex items-center space-x-1">
                  <p class="text-4xl font-bold">   {{modalData?.weather_records?.temperature }}</p>
                  <div class="flex flex-col">
                    <p class="text-sm" v-text="modalData?.weather_records?.temperature ? '°C':'&nbsp;'"></p>
                    <p class="text-xs" v-text="modalData?.weather_records?.cloud ? modalData?.weather_records?.cloud:'&nbsp;'"> </p>
                  </div>
                </div>
              </div>

              <div class="w-full text-center">
                <img
                  :src="weatherIcons(modalData?.weather_records?.cloud)"
                  class="h-10 mt-4 inline-block"
                />
              </div>

              <div class="flex justify-center items-center">
                <div
                  class="w-fit py-3 px-7 flex-1 custom_bg_gray custom_border_color border rounded-lg mt-4 flex flex-col items-center justify-center"
                >
                  <img
                    src="/images/pressure.png"
                    class="h-4"
                    alt="Sunny weather"
                  />
                  <p class="text-xs">{{ $t("Pressure") }}</p>
                  <p class="text-sm font-bold">{{modalData?.weather_records?.pressure }}mb</p>
                </div>
              </div>
            </div>
            <div class="col-span-3">
              <Chart3 :data="modalData" />
            </div>
            <div class="col-span-2">
              <div class="flex items-end" >
                <span class="text-4xl font-bold leading-none">{{modalData?.weather_records?.humidity }}</span>
                <div class="flex flex-col items-start mb-1">
                  <span class="text-lg font-bold leading-none" v-text="modalData?.weather_records?.humidity ? '%':'&nbsp;'"></span>
                  <span class="text-xs ml-2" v-text="modalData?.weather_records?.humidity ? $t('Humidity'):'&nbsp;'"></span>
                </div>
              </div>
              <div class="w-full text-center" >
                <img
                    :src="modalData?.weather_records?.precipitation > 50 ? '/images/drops.png':'/images/sun.png'"
                  class="h-10 mt-4 inline-block"
                  alt="Drops"
                />
              </div>

              <div class="flex justify-center items-center">
                <div
                  class="w-fit py-3 px-7 flex-1 border custom_bg_gray custom_border_color rounded-lg mt-4 flex flex-col items-center justify-center"
                >
                  <img src="/images/wind.png" class="h-4" alt="Sunny weather" />
                  <p class="text-xs">{{ $t("Wind") }}</p>
                  <p class="text-sm font-bold">{{modalData?.weather_records?.wind }}km/h</p>
                </div>
              </div>
            </div>
          </div>
          <div>
            <h3 class="mt-5 mb-2 font-[20px] font-bold">
              AQI Trend in Last 24 Hours
            </h3>
            <Chart5 :data="modalData" />
          </div>
          <div class="bg-white rounded-lg md:col-span-3">
            <AirPollutantParameters :data="modalData" />
          </div>
        </div>
      </div>
      <div
        class="info-panel absolute right-0 top-0 bottom-0 w-1/4 bg-none flex items-center lg:hidden"
      >
        <div class="p-4 z-[2000]">
          <div class="absolute right-0 top-1/2 transform -translate-y-1/2">
            <div class="flex flex-col w-[70px] rounded-md overflow-hidden">
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#B10007]"
              >
                500
              </div>
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#832a81]"
              >
                400
              </div>
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#9E37F2]"
              >
                300
              </div>
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#EEB530]"
              >
                200
              </div>
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#CFEA2B]"
              >
                100
              </div>
              <div
                class="h-9 flex items-center justify-center text-white text-sm font-bold bg-[#19AF54]"
              >
                50
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import {faces} from "@/Utils/Ranking.js";
import {weatherIcons} from "@/Utils/WeatherData.js";
import {useStore} from "vuex";
const store = useStore();
const { t } = useI18n();
</script>


<script>
import "leaflet/dist/leaflet.css";
import {LMap, LTileLayer, LMarker, LPopup, Utilities} from "@vue-leaflet/vue-leaflet";
import L from "leaflet";
import SelectParameterDropdown from "@/Components/Custom/Dropdowns/SelectParameterDropdown.vue";
import AirPollutantParameters from "@/Components/Custom/Widgets/AirPollutantParameters.vue";
import Chart3 from "@/Components/Custom/Charts/Chart3.vue";
import Chart5 from "@/Components/Custom/Charts/Chart5.vue";
import { router } from "@inertiajs/vue3";
import store from "@/store/index.js";
import {useStore} from "vuex";
import {data} from "autoprefixer";
import {colours, markerIcons} from "@/Utils/Ranking.js";

const isMobile = window.innerWidth <= 768;
// Define the custom div icon
const customDivIcon = new L.DivIcon({
  className: "custom-div-icon",
  html: '<div class="my-custom-marker"></div>',
  iconSize: [30, 30],
  iconAnchor: [15, 15],
});

export default {
  name: "MapWithMarkers",
  props: {
    data: Object,
      default_data: Object
  },
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    SelectParameterDropdown,
    Chart3,
    Chart5,
    AirPollutantParameters,
  },
  data() {
    return {
      map: null,
      isModalOpen: false,
      modalData: {},
      mapBounds: [
        [6.825, 79.7954], // Southwest corner
        [7.014, 80.1442], // Northeast corner
      ]
    };
  },
  computed: {
    markers() {
      if (!this.data || !this.data.length){
          if(this.default_data == null){
              return [];
          }
          this.data[0] = this.default_data;
      }
      const latLngMap = {};
      return this.data.map((item) => {
          const lat = item.sensor_location.latitude;
          const lng = item.sensor_location.longitude;

          return {
          latlng: [
              lat,
              lng
          ],
          title: item.name,
          data: item,
          icon: this.createCustomDivIcon(item),
        };
      });
    },
  },
  methods: {
    onMapReady(map) {
      this.map = map;
    },
    showModal(marker) {
        this.modalData = marker.data;
      this.isModalOpen = true;
      document.body.classList.add("overflow-hidden");
    },
    closeModal() {
      this.isModalOpen = false;
      document.body.classList.remove("overflow-hidden");
    },
    createCustomDivIcon(item) {

        let parameterName = "PM2.5";
        let value = 0;
        let aqi = 0;
        aqi = item.aqi.AQI;

        if(item.pm2_5_status !== 'ACTIVE' ){
            if(item.pm10_status === 'ACTIVE' ){
                parameterName = "PM10";
                value = item.pm10;
            }
        }else{
            parameterName = "PM2.5";
            value = item.pm2_5;
        }
      if (!isMobile) {
        return new L.DivIcon({
          className: "custom-div-icon",
          html: `
            <div class="${item?.sensor_location?.sensor_id === 1? 'outercircle flex items-center justify-center rounded-full relative bg-[rgba(71,255,25,0.2)]':'outercircle flex items-center justify-center rounded-full relative bg-[rgba(255,255,255,0.2)]'}">
              <div class="relative modelcircle rounded-full bg-white shadow">
                  <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center mt-3">
                      <div class="flex items-center mb-0.5">
                          <span class="text-black text-[12px] font-semibold mr-1">${parameterName}</span>
                          <span class="bg-gray-300 text-black text-[10px] font-bold px-2 py-0.5 rounded-full">${value}</span>
                      </div>
                      <p style="color:${colours(item?.aqi?.SL)}" class="text-[20px] font-bold leading-tight">${aqi}</p>
                      <p class="text-black text-[12px]">AQI</p>
                  </div>
              </div>
          </div>`,
          iconSize: [30, 30],
          iconAnchor: [15, 15],
        });
      } else {
        return new L.DivIcon({
          className: "custom-div-icon",
          html: `
            <div>
                <img src="${markerIcons(item)}" class="w-full h-auto" />
            </div>`,
          iconSize: [30, 30],
          iconAnchor: [15, 15],
        });
      }
    },

    navigateToMarkerDetail(marker,defaultData) {
        let previousSelectedMarker = null;
        previousSelectedMarker = this.data.find(item => item.sensor_location_id === marker.data.sensor_location_id);
        marker.data = previousSelectedMarker;

        store.commit("updateMarkarData", previousSelectedMarker);
        if (window.innerWidth < 768) {
        router.visit(`/public/latest-aqi`);
      } else {
        this.showModal(marker);
      }
    },
  },
};
</script>

<style>
#map {
  height: 400px;
}

.custom-div-icon {
  background: transparent; /* Ensures the background is transparent so the inner div can show */
}

.my-custom-marker {
  background-color: white;
  /* border: 2px solid black; */
  border-radius: 50%;
  width: 40px;
  height: 40px;
  padding: 10px;
}

.modal {
  display: block;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}

@media screen and (max-height: 768px) {
  .modal {
    align-items: flex-start;
  }
}

.modal-content {
  background-color: #fefefe;
  /* margin: 5% auto; */
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  height: fit-content;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.leaflet-popup-pane {
  display: none;
}

.leaflet-container {
  font-family: Figtree, ui-sans-serif;
}

.leaflet-bottom {
  display: none;
}
</style>
