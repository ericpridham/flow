<script lang="babel" type="text/ecmascript-6">
    import FlowLive from "./FlowLive";
    import FlowSaved from "./FlowSaved";
    import NavTab from "./NavTab";

    export default {
        name: "FlowPage",
        components: {
            NavTab,
            FlowLive,
            FlowSaved
        },
        data() {
            return {
                selectedTab: 'live',
                snapshots: []
            }
        },
        mounted() {
            let flowSnapshots = window.localStorage.getItem('flow-snapshots');
            if (flowSnapshots) {
                this.snapshots = JSON.parse(flowSnapshots);
            }
        },
        methods: {
            selectLive() {
                this.selectedTab = 'live';
            },
            selectSaved() {
                this.selectedTab = 'saved';
            },
            saveSnapshot(name, events) {
                this.snapshots.push({name, events});
                window.localStorage.setItem('flow-snapshots', JSON.stringify(this.snapshots));
            },
            deleteSnapshot(index) {
                this.snapshots.splice(index, 1);
                window.localStorage.setItem('flow-snapshots', JSON.stringify(this.snapshots));
            },
            setLiveDirty(isDirty) {
                this.liveDirty = isDirty;
            }
        },
    }
</script>

<style>
    .container {
        min-height: calc(100vh - 350px);
    }
</style>

<template>
    <div id="flow-page" class="container mx-auto">
        <h1>Flow View</h1>
        <ul class="flex border-b">
            <nav-tab text="Live" :active="selectedTab === 'live'" @activate="selectedTab = 'live'"></nav-tab>
            <nav-tab text="Saved" :active="selectedTab === 'saved'" @activate="selectedTab = 'saved'"></nav-tab>
        </ul>
        <div v-if="selectedTab === 'live'">
            <flow-live @snapshot-saved="saveSnapshot"></flow-live>
        </div>
        <div v-if="selectedTab === 'saved'">
            <flow-saved :snapshots="snapshots" @snapshot-deleted="deleteSnapshot"></flow-saved>
        </div>
    </div>
</template>
