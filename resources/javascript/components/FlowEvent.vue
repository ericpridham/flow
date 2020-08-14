<template>
    <div class="event py-3 m-1 text-white" :style="'background-color:' + event.color" >
        <div class="px-4 cursor-pointer flex justify-between" @click.prevent="showDetails = !showDetails">
            <span v-html="event.title"></span>
            <span class="whitespace-no-wrap">{{ event.timestamp }}</span>
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

    .event.event-meta {
        background-color: white;
        border: 2px solid #f93822;
        color: #f93822;
    }

</style>
