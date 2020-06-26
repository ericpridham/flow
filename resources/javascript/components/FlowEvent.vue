<template>
    <div :class="'event event-' + event.type">
        <div class="event-summary" @click.prevent="showDetails = !showDetails">
            <span>{{ event.title }}</span>
            <span>{{ event.timestamp }}</span>
        </div>
        <div :id="'details-' + event.event_id" class="mx-2 my-1 p-2 bg-white text-black" v-show="showDetails">
            <pre v-html="event.details"></pre>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FlowEvent",
        props: ['event'],
        data() {
            return {
                'showDetails': false
            }
        },
        methods: {
            eventTitle(event) {
                if (event.type === 'meta') {
                return event.message;
                } else if (event.type === 'model') {
                    return this.ucfirst(event.payload.type) + ' ' + event.payload.model_name;
                } else if (event.type === 'stripeWebhook') {
                    return 'Stripe Webhook: ' + event.payload.type;
                } else if (event.type === 'stripeHttp') {
                    return 'Stripe HTTP: ' + event.payload.method.toUpperCase() + ' ' + event.payload.url;
                } else if (event.type === 'lcms') {
                    return 'LCMS: ' + event.payload.method + ' ' + event.payload.url;
                } else if (event.type === 'requestHeader') {
                    return '';
                }

                return this.ucfirst(event.type);
            },
            eventDetails(event) {
                if (event.type === 'meta') {
                    return null;
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

    .event-summary {
        padding: 0 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
    }

    .event-meta .event-summary {
        cursor: default;
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
