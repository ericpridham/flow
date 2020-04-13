import Vue from 'vue';

import FlowPage from "./components/FlowPage";

new Vue({
    el: '#flow',
    components: {
        'flow-page': FlowPage,
    },
    data() {
        return {
            message: "Message",
        }
    }
});
