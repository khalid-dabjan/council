<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
        </div>
        <paginator :dataSet="dataSet" @changed="fetch"></paginator>
        <p v-if="$parent.locked">
            The thread is locked, no more replies can be added.
        </p>
        <new-reply :endpoint="endpoint" @created='add' v-else></new-reply>
    </div>

</template>

<script>
    import Reply from './Reply.vue';
    import NewReply from '../components/NewReply.vue';

    export default {
        components: {Reply, NewReply},
        data() {
            return {
                dataSet: false,
                items: [],
                endpoint: location.pathname + '/replies'
            };
        },
        created() {
            this.fetch();
        },
        methods: {
            fetch(page) {
                if (!page) {
                    let query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : 1;
                }
                axios.get(this.url(page))
                    .then(this.refresh);
            },
            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;

                window.scroll(0, 0);
            },
            url(page) {
                return location.pathname + '/replies?page=' + page;
            },
            add(reply) {
                this.items.push(reply);
                this.$emit('added');
            },
            remove(index) {
                this.items.splice(index, 1);

                flash('Reply was deleted');

                this.$emit('remove');
            }
        },
    }
</script>