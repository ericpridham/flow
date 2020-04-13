<template>
    <div id="flow-saved">
        <div id="flow-snapshots">
            <div v-for="(snapshot, index) in snapshots" class="btn-group" :id="'snapshot' + index">
                <button class="btn btn-flow-snapshot" :class="{active: index === selectedSnapshot}" @click="selectSnapshot(index)">{{ snapshot.name }} ({{ snapshot.events.length }})</button>
                <button class="btn btn-flow-snapshot" @click="deleteSnapshot(index)">X</button>
            </div>
        </div>
        <div v-if="selectedSnapshot !== null">
            <flow-event-viewer :events="selectedSnapshotEvents()"></flow-event-viewer>
        </div>
    </div>
</template>

<script>
    import FlowEventViewer from "./FlowEventViewer";
    export default {
        name: "FlowSaved",
        components: {FlowEventViewer},
        props: {
            snapshots: Array
        },
        data() {
            return {
                selectedSnapshot: null
            }
        },
        methods: {
            selectSnapshot(index) {
                this.selectedSnapshot = index;
            },
            selectedSnapshotEvents() {
                // slice() to get a copy
                return this.snapshots[this.selectedSnapshot].events.slice().reverse();
            },
            deleteSnapshot(index) {
                let name = this.snapshots[index].name;
                if (confirm('Are you sure you want to delete "' + name + '"?')) {
                    if (this.selectedSnapshot === index) {
                        this.selectedSnapshot = null;
                    }
                    this.$emit('snapshot-deleted', index);
                }
            }
        }
    }
</script>

<style scoped>
    #flow-snapshots {
        padding: 1rem 0;
        margin-bottom: 1rem;
    }
    .btn-group {
        margin-right: 1rem;
    }
    .btn-flow-snapshot {
        border: 2px solid #f93822;
        background-color: white;
    }
    .btn-flow-snapshot.active {
        background-color: #f93822;
        color: white;
    }
</style>