<template>
    <div>
        <div id="flow-filters">
            Filters
            <input v-model="searchString" placeholder="Search"/><i class="fa fa-search"></i>
        </div>
        <div>
            <input v-model="groupByRequest" type="checkbox" id="groupByRequest"> <label for="groupByRequest">Group by Request</label>
        </div>
        <div v-for="event in filteredEvents" :key="event.unique_id" :class="'event event-' + event.type">
            <div class="event-summary" data-toggle="collapse" :data-target="'#details-' + event.unique_id">
                <span>{{ eventTitle(event) }}</span>
                <span>{{ niceDate(event.created_at) }}</span>
            </div>
            <div :id="'details-' + event.unique_id" class="collapse event-details" v-if="eventDetails(event)">
                <hr/>
                <pre v-html="eventDetails(event)"></pre>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "FlowEventViewer",
        props: {
            events: Array
        },
        data() {
            return {
                searchString: null,
                groupByRequest: true
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
                    flattened.push({'type': 'requestHeader', 'request_id': requestId});
                    flattened.push(...events);
                }

                return flattened;
            },
            niceDate(time) {
                if (! time) {
                    return '';
                }
                return moment.unix(time).format('h:mm:ss a');
            },
            eventTitle(event) {
                if (event.type === 'meta') {
                    return event.message;
                } else if (event.type === 'model') {
                    return this.ucfirst(event.payload.type) + ' ' + event.payload.model_name;
                } else if (event.type === 'stripeWebhook') {
                    return 'Stripe Webhook: ' + event.payload.type;
                } else if (event.type === 'stripeHttp') {
                    return 'Stripe HTTP: ' + event.payload.method.toUpperCase() + ' ' + event.payload.url;
                } else if (event.type === 'exception') {
                    return this.ucfirst(event.type) + ': ' + event.payload.message;
                } else if (event.type === 'log') {
                    return this.ucfirst(event.payload.level) + ': ' + event.payload.message;
                } else if (event.type === 'lcms') {
                    return 'LCMS: ' + event.payload.method + ' ' + event.payload.url;
                } else if (event.type === 'request') {
                    return 'HTTP Request: ' + event.payload.method + ' ' + event.payload.url;
                } else if (event.type === 'requestHeader') {
                    return '';
                }

                return this.ucfirst(event.type);
            },
            eventDetails(event) {
                if (event.type === 'meta') {
                    return null;
                } else if (event.type === 'log') {
                    return this.prettyJson(event.payload.context);
                } else if (event.type === 'model') {
                    if (event.payload.type === 'created') {
                        return this.prettyJson(event.payload.record);
                    }
                    if (event.payload.type === 'updated') {
                        return this.prettyJson({
                            changes: event.payload.changes,
                            record: event.payload.record
                        });
                    }
                } else if (event.type === 'stripeWebhook') {
                    return event.payload.data;
                } else if (event.type === 'exception') {
                    return event.payload.file + ':' + event.payload.line + "\n\n"
                        + event.payload.trace;
                } else if (event.type === 'lcms') {
                    return this.prettyJson({
                        request: event.payload.request,
                        response: event.payload.response,
                    });
                } else if (event.type === 'stripeHttp') {
                    return this.prettyJson({
                        params: event.payload.params,
                        response: event.payload.response,
                        responseCode: event.payload.responseCode
                    });
                } else if (event.type === 'request') {
                    return this.prettyJson({
                        contents: event.payload.contents,
                        response: event.payload.response,
                    });
                }
                return this.prettyJson(event.payload);
            },
            ucfirst(str) {
                return str[0].toUpperCase() + str.slice(1);
            },
            prettyJson(any) {
                return JSON.stringify(any, null, 2);
            }
        }
    }
</script>

<style scoped>
    #flow-filters {
        border: 2px solid #ccc;
        padding: 1rem 0.5rem;
    }

    .event-summary {
        padding: 0 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
    }

    .event-meta .event-summary {
        cursor: default;
    }

    .event-details {
        padding: 0 1rem;
    }

    .event {
        color: white;
        margin: 0.25rem;
        padding: 1rem 0;
        background-color: #747538;
    }

    .event.event-meta {
        background-color: white;
        border: 2px solid #f93822;
        color: #f93822;
    }

    .event.event-generic { background-color: #757575; }

    .event.event-model { background-color: #56bb8d; }

    .event.event-exception { background-color: #f93822; }

    .event.event-stripeWebhook { background-color: #586ada; }

    .event.event-stripeHttp { background-color: #586ada; }

    .event.event-log { background-color: #999; }

    .event.event-lcms { background-color: #daa25e; }

    .event.event-request { background-color: #955eda; }

    .event.event-requestHeader { background-color: white; color: black; }
</style>