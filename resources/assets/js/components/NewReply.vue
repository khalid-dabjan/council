<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <wysiwyg v-model="body" name="body" placeholder="Have something to say?"
                         :shouldClear="completed"></wysiwyg>
            </div>

            <button type="submit" class="btn btn-default" @click="addReply">Post</button>
        </div>
        <div v-else>
            <p class="text-center">You can <a href="/login">sign in</a> here.</p>
        </div>
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';

    export default {
        props: ['endpoint'],
        data() {
            return {
                body: '',
                completed: false
            };
        },
        mounted() {
            $('#body').atwho({
                at: '@',
                delay: 750,
                callbacks: {
                    remoteFilter(query, callback) {
                        $.getJSON('/api/users', {name: query}, (usernames) => {
                            callback(usernames);
                        });
                    }
                }
            });
        },
        methods: {
            addReply() {
                axios.post(this.endpoint, {
                    body: this.body
                }).catch(error => {
                    flash(error.response.data, 'danger')
                }).then(({data}) => {
                    this.body = '';
                    this.completed = true;
                    flash('Reply was added');
                    this.$emit('created', data);
                });

            }
        }
    }
</script>