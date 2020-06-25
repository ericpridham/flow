<template>
    <div id="fow-live">
        <div id="flow-controls">
            <button type="button" class="btn btn-flow btn-toggle" :class="{active: paused}" @click="togglePause">
                <span v-if="paused">Resume</span>
                <span v-else>Pause</span>
            </button>
            <button type="button" class="btn btn-flow" @click="clearEventsPrompt">Clear</button>
            <button type="button" class="btn btn-flow" @click="saveSnapshot">Save</button>
        </div>
        <div v-if="loading">Loading ...</div>
        <flow-event-viewer v-else :events="events"></flow-event-viewer>
    </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';

    export default {
        name: "FlowLive",
        components: {
            'flow-event-viewer': require('./FlowEventViewer').default
        },
        data() {
            return {
                paused: false,
                events: [],
                loading: true
            }
        },
        mounted() {
            axios.get('/flow/events')
                .then((response) => {
                    this.events = response.data;
                    this.loading = false;
                });
        },
        methods: {
            clearEventsPrompt() {
                if (confirm('Clear all events?')) {
                    this.clearEvents();
                }
            },
            clearEvents() {
                this.events = [];
                this.$emit('dirty', false);
            },
            togglePause() {
                if (this.paused) {
                    this.resumeFlow();
                } else {
                    this.pauseFlow();
                }
            },
            pauseFlow() {
                if (!this.paused) {
                    this.paused = true;
                    this.throwMetaEvent('Paused');
                }
            },
            resumeFlow() {
                if (this.paused) {
                    this.paused = false;
                    this.throwMetaEvent('Resumed');
                }
            },
            throwMetaEvent(message) {
                this.events.unshift({
                    type: 'meta',
                    unique_id: moment().unix(),
                    created_at: moment().unix(),
                    message: message
                });
            },
            addEvent(e) {
                if (this.paused) {
                    return;
                }
                this.events.unshift(e);
                this.$emit('dirty', true);
            },
            saveSnapshot() {
                let snapshotEvents = this.events.slice();
                this.pauseFlow();
                if (name = prompt('Name', moment().format('MMMM Do YYYY h:mm:ss a'))) {
                    this.$emit('snapshot-saved', name, snapshotEvents);
                    this.clearEvents();
                }
                this.resumeFlow();
            },
        }
    }
</script>

<style scoped>
    #flow-controls {
        /*border: 2px solid grey;*/
        padding: 1rem 0;
        margin-bottom: 1rem;
    }

    .btn.btn-flow {
        border: 2px solid #f93822;
        width: 10rem;
    }

    .btn.btn-flow.btn-toggle {
        background-color: white;
        color: black;
    }

    .btn.btn-flow, .btn.btn-toggle.active {
        background-color: #f93822;
        color: white;
    }
</style>
