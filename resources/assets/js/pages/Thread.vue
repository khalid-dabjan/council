<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],
        components: {Replies, SubscribeButton},
        data() {
            return {
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked,
                editing: false,
                title: this.thread.title,
                body: this.thread.body,
                form: {
                    title: this.thread.title,
                    body: this.thread.body,
                }
            };
        },
        methods: {
            toggleLock() {
                axios[this.locked ? 'delete' : 'post']('/locked-thread/' + this.thread.slug);
                this.locked = !this.locked;
            },
            resetForm() {
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body,
                };
                this.editing = false;
            },
            update() {
                let uri = `/threads/${this.thread.channel.name}/${this.thread.slug}`;
                axios.patch(uri, this.form).then(() => {
                    this.title = this.form.title;
                    this.body = this.form.body;
                    this.editing = false;
                });
            }
        },
    }
</script>