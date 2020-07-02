<template>
    <div>
        <div id="flow-filters">
            Filters
            <input v-model="searchString" placeholder="Search"/><i class="fa fa-search"></i>
        </div>
        <div>
            <input v-model="groupByRequest" type="checkbox" id="groupByRequest"> <label for="groupByRequest">Group by Request</label>
        </div>
        <flow-event v-for="event in filteredEvents" :key="event.event_id" :event="event"></flow-event>
    </div>
</template>

<script>
    import FlowEvent from "./FlowEvent";

    export default {
        name: "FlowEventViewer",
        components: {FlowEvent},
        props: {
            events: Array
        },
        data() {
            return {
                searchString: null,
                groupByRequest: true,
                loading: true,
            }
        },
        computed: {
            filteredEvents() {
                let filtered = this.events.slice();
                if (this.searchString) {
                    filtered = this.filterBySearch(filtered, this.searchString);
                }
                if (this.groupByRequest) {
                    filtered = this.groupListByRequest(filtered);
                }
                return filtered;
            }
        },
        methods: {
            filterBySearch(list, searchString) {
                return list.filter((event) => {
                    let eventString = JSON.stringify(event).toLowerCase();

                    return searchString.split(" ").every((searchTerm) => {
                        if (!searchTerm) {
                            return true;
                        }

                        if (searchTerm.charAt(0) === "-") {
                            return ! eventString.includes(searchTerm.slice(1).toLowerCase());
                        } else {
                            return eventString.includes(searchTerm.toLowerCase());
                        }
                    });
                });
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
