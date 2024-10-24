import { createStore } from 'vuex';

const store = createStore({
    state() {
        return {
            markarData: {},
            markarDefaultData: {},
            aqiRegion: "AQI - SL",
            equipmentType: 'All',
            message: '',
            sensor: ''
        };
    },
    actions: {
        async updateSensorAction({ commit, state }, sensor) {
            // if (state.sensor !== sensor) {
                commit('updateSensor', sensor);
            // }
        },
        async updateDateRangeAction({ commit, state }, dateRange) {
            if (state.dateRange !== dateRange) {
                commit('updateDateRange', dateRange);
            }
        },
        async updateSelectedLocationAction({ commit, state }, location) {
            if (state.location !== location) {
                commit('updateSelectedLocation', location);
            }
        },
        async pollutantParamChangedAction({ commit, state }, pollutantParam) {
            if (state.pollutantParam !== pollutantParam) {
                commit('pollutantParamChanged', pollutantParam);
            }
        },
        async comparativePollutantParamChangedAction({ commit, state }, pollutantParam) {
            if (state.pollutantParam !== pollutantParam) {
                commit('comparativePollutantParamChanged', pollutantParam);
            }
        },
        async updateComparativePollutantSensorAction({ commit, state }, sensor) {
                commit('updateComparativePollutantSensor', sensor);
        },
        async updateAnalysisPollutantSensor_1Action({ commit, state }, sensor) {
            if (state.sensor !== sensor) {
                commit('updateAnalysisPollutantSensor_1', sensor);
            }
        },
        async updateAnalysisPollutantSensor_2Action({ commit, state }, sensor) {
                commit('updateAnalysisPollutantSensor_2', sensor);
        },
        async updateComparativeLevelChart3Action({ commit, state }, data) {
            commit('updateComparativeLevelChart3', data);
        },
        async updateComparativeLevelChart4Action({ commit, state }, data) {
            commit('updateComparativeLevelChart4', data);
        }
    },
    mutations: {
        updateMessage(state, newMessage) {
            state.message = newMessage;
        },
        updateEquipmentType(state, newType) {
            state.equipmentType = newType;
        },

        updateSensor(state, sensor) {
            if (state.sensor !== sensor) {  // Only commit if the sensor is different
                state.sensor = sensor;
            }
        },
        historicalData(state, historicalData) {
            state.historicalData = historicalData;
            if (state.historicalData !== historicalData) {
                state.historicalData = historicalData;
            }
        },
        updateDateRange(state, dateRange) {
            state.dateRange = dateRange;
            if (state.dateRange !== dateRange) {
                state.dateRange = dateRange;
            }
        },
        updateSelectedLocation(state, location) {
            state.location = location;
            if (state.location !== location) {
                state.location = location;
            }
        },
        updateComparativePollutantSensor(state, sensor) {
            state.sensor = sensor;
        },
        updateAnalysisPollutantSensor_1(state, sensor) {
            state.sensor = sensor;
        },
        updateAnalysisPollutantSensor_2(state, sensor) {
            state.sensor = sensor;
        },
        updateMarkarData(state, newMarkarData) {
            state.markarData = newMarkarData;
        },
        updateMarkarDefaultData(state, newMarkarData) {
            state.markarDefaultData = newMarkarData;
        },
        updateAqiRegion(state, newAqiRegion) {
            state.aqiRegion = newAqiRegion;
        },
        pollutantParamChanged(state, param) {
            state.pollutantParam = param;
        },
        comparativePollutantParamChanged(state, param) {
            state.pollutantParam = param;
        },
        updateComparativeLevelChart3(state, param) {
            state.comparativeData = param;
        },
        updateComparativeLevelChart4(state, param) {
            state.comparativeData = param;
        }
    },
    getters: {
        getSelectedSensor(state) {
            return state.sensor;
        }
    }
});

export default store;
