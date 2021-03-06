<template>
    <div id="fow-live">
        <div id="flow-controls">
            <date-picker v-model="from" type="datetime" :minuteStep="10" :secondStep="10" :shortcuts="dateShortcuts"></date-picker>
            <date-picker v-model="to" type="datetime" :minuteStep="10" :secondStep="10" :shortcuts="dateShortcuts"></date-picker>
            <button type="button" class="btn btn-flow" @click.prevent="getEvents">Get</button>
            <button type="button" class="btn btn-flow" @click="saveSnapshot">Save</button>
        </div>
        <div v-if="loading">Loading ...</div>
        <flow-event-viewer v-else :events="events"></flow-event-viewer>
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: "FlowLive",
    components: {
        'flow-event-viewer': require('./FlowEventViewer').default,
        DatePicker
    },
    data() {
        return {
            paused: false,
            events: [],
            loading: true,
            from: moment().toDate(),
            to: false,
            dateShortcuts: [
                { text: 'Now', onClick: () => moment().toDate() },
                { text: '30m Ago', onClick: () => moment().subtract(30, 'm').toDate() },
                { text: '60m Ago', onClick: () => moment().subtract(60, 'm').toDate() },
                { text: '1d Ago', onClick: () => moment().subtract(1, 'd').toDate() },
            ]
        }
    },
    mounted() {
        this.getEvents();
    },
    methods: {
        getEvents() {
            this.loading = true;
            let base = window.location.href;
            let url = base + (base.endsWith('/') ? '' : '/') + 'events';
            let params = []
            if (this.from) {
                params.push('from=' + moment(this.from).unix());
            }
            if (this.to) {
                params.push('to=' + moment(this.to).unix());
            }
            if (params.length) {
                url += '?' + params.join('&');
            }
            axios.get(url)
                .then((response) => {
                    this.events = response.data;
                    this.loading = false;
                });
        },
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
            let nameSnapshot;
            if (nameSnapshot = prompt('Name', moment().format('MMMM Do YYYY h:mm:ss a'))) {
                this.$emit('snapshot-saved', nameSnapshot, snapshotEvents);
            }
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
