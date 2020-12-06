<template>
    <div>
        <div id="flow-filters">
            Filters
            <input v-model="searchString" v-on:keyup.enter="storeFilter" placeholder="Search"/><i class="fa fa-search"></i>
            <div>
                <span v-for="(filter, index) in savedFilters" class="inline-block p-2 mr-2 mt-4 border border-black">
                    <button class="text-red-500" @click="deleteFilter(index)">X</button>
                    {{ filter }}
                </span>
            </div>
        </div>
        <div>
            <input v-model="groupByRequest" type="checkbox" id="groupByRequest"> <label for="groupByRequest">Group by Request</label>
        </div>
        <flow-event v-for="event in filteredEvents" :key="event.event_id" :event="event"></flow-event>
    </div>
</template>

<script>
    import FlowEvent from "./FlowEvent";
    import ObjSearch from "../lib/ObjSearch";

    export default {
        name: "FlowEventViewer",
        components: {FlowEvent},
        props: {
            events: Array
        },
        data() {
            return {
                searchString: '',
                savedFilters: [],
                groupByRequest: true,
                loading: true,
            }
        },
        mounted() {
            let storageFilters = window.localStorage.getItem('flow-filters')
            if (storageFilters) {
                this.savedFilters = JSON.parse(storageFilters);
            }
        },
        computed: {
            fullFilterString() {
                return this.savedFilters.join(' ') + ' ' + this.searchString;
            },
            filteredEvents() {
                let filtered = this.events.slice();
                filtered = filtered.map((event) => {
                    this.$set(event, 'faded', false);
                    return event;
                });
                if (this.fullFilterString) {
                    filtered = this.filterBySearch(filtered, this.fullFilterString);
                }
                if (this.groupByRequest) {
                    filtered = this.groupListByRequest(filtered);
                }
                return filtered;
            }
        },
        methods: {
            filterBySearch(list, searchString) {
                let matchingEvents = list.filter((event) => {
                    return searchString.split(" ").every((searchTerm) => {
                        if (!searchTerm) {
                            return true;
                        }
                        return this.searchMatches(searchTerm, event);
                    });
                });

                if (!this.groupByRequest) {
                    return matchingEvents;
                }

                let matchingRequestIds = matchingEvents.map((event) => {
                    return event.request_id;
                });

                let matchingEventIds = matchingEvents.map((event) => {
                    return event.event_id;
                });

                return list.filter((event) => {
                    if (matchingRequestIds.includes(event.request_id)) {
                        if (matchingEventIds.includes(event.event_id)) {
                            event.faded = false;
                        } else {
                            event.faded = true;
                        }
                        return true;
                    }
                    return false;
                })
            },
            searchMatches(searchTerm, ev) {
                if (searchTerm.charAt(0) === '.') {
                    let accessor, value;
                    accessor = searchTerm
                    if (searchTerm.includes('=')) {
                        [accessor, value] = searchTerm.split('=');
                    }
                    if (typeof value === 'undefined') {
                        return ObjSearch.hasKey(ev, 'details' + accessor);
                    } else {
                        return ObjSearch.get(ev, 'details' + accessor) == value;
                    }
                }

                let eventString = JSON.stringify(ev).toLowerCase();
                if (searchTerm.charAt(0) === "-") {
                    return !eventString.includes(searchTerm.slice(1).toLowerCase());
                } else {
                    return eventString.includes(searchTerm.toLowerCase());
                }
            },
            storeFilter() {
                this.savedFilters.push(this.searchString);
                this.searchString = '';
                window.localStorage.setItem('flow-filters', JSON.stringify(this.savedFilters));
            },
            deleteFilter(index) {
                this.savedFilters.splice(index, 1);
                window.localStorage.setItem('flow-filters', JSON.stringify(this.savedFilters));
            },
            groupListByRequest(list) {
                let grouped = new Map();
                let flattened = [];

                for (event of list) {
                    if (!grouped.has(event.request_id)) {
                        grouped.set(event.request_id, []);
                    }
                    grouped.set(event.request_id, grouped.get(event.request_id).concat(event))
                }

                for (let [requestId, events] of grouped.entries()) {
                    flattened.push({'type': 'requestHeader', 'request_id': requestId, 'color': 'white'});
                    flattened.push(...events);
                }

                return flattened;
            },
        }
    }
</script>

<style scoped>
    #flow-filters {
        border: 2px solid #ccc;
        padding: 1rem 0.5rem;
    }
</style>
