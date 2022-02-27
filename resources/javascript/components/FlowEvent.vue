<template>
    <div class="event" :style="'background-color:' + event.color" >
        <div class="px-4 cursor-pointer flex justify-between" @click.prevent="showDetails = !showDetails">
            <span v-html="event.title"></span>
            <span>{{ event.timestamp }} [{{ event.duration_ms }}ms]</span>
        </div>
        <collapse-transition dimension="height" :duration="300">
            <div :id="'details-' + event.event_id" class="mx-2 bg-white text-black overflow-hidden" v-show="showDetails">
                <pre class="p-4" v-html="event.details"></pre>
            </div>
        </collapse-transition>
    </div>
</template>

<script>
    export default {
        name: "FlowEvent",
        props: ['event'],
        components: {
            'CollapseTransition': require('@ivanv/vue-collapse-transition').default
        },
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

    .event.event-model { background-color: #56bb8d; }

</style>
